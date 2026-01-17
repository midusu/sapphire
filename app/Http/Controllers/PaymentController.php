<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\ActivityBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['booking.user', 'activityBooking.user', 'booking.room.roomType', 'activityBooking.activity'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $totalRevenue = Payment::where('status', 'completed')->sum('amount');

        return view('admin.payments.index', compact('payments', 'totalRevenue'));
    }

    public function create()
    {
        $bookings = Booking::with(['user', 'room.roomType'])
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->whereDoesntHave('payments', function ($query) {
                $query->where('status', 'completed');
            })
            ->get();

        $activityBookings = ActivityBooking::with(['user', 'activity'])
            ->where('status', 'confirmed')
            ->whereDoesntHave('payments', function ($query) {
                $query->where('status', 'completed');
            })
            ->get();

        return view('admin.payments.create', compact('bookings', 'activityBookings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_type' => 'required|in:booking,activity',
            'booking_id' => 'required_if:payment_type,booking|exists:bookings,id',
            'activity_booking_id' => 'required_if:payment_type,activity|exists:activity_bookings,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,bank_transfer,online',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'coupon_code' => 'nullable|string|exists:coupons,code',
        ]);

        DB::beginTransaction();
        try {
            $discountAmount = 0;
            $finalAmount = $request->amount;

            // Handle Coupon
            if ($request->coupon_code) {
                $coupon = \App\Models\Coupon::where('code', $request->coupon_code)->first();
                if ($coupon && $coupon->isValid()) {
                    if ($coupon->type === 'fixed') {
                        $discountAmount = $coupon->value;
                    } else {
                        $discountAmount = $finalAmount * ($coupon->value / 100);
                    }
                    // Ensure discount doesn't exceed total
                    $discountAmount = min($discountAmount, $finalAmount);

                    // The user pays the discounted amount
                    // Logic: If user enters full amount $100, and coupon is $10.
                    // We should likely record the full bill amount somewhere, but here 'amount' is usually what is PAID.
                    // Let's assume the admin enters the FINAL amount they are paying.
                    // OR, better: Admin enters original amount, and we update it? 
                    // Let's stick to: Amount is what is PAID. Discount is just recorded.
                    // Wait, if admin enters $100 and applies 10% coupon, they expect to pay $90.
                    // So we should recalculate the amount.

                    $finalAmount = $finalAmount - $discountAmount;
                }
            }

            $paymentData = [
                'amount' => $finalAmount,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'transaction_id' => $request->transaction_id,
                'notes' => $request->notes,
                'coupon_code' => $request->coupon_code,
                'discount_amount' => $discountAmount
            ];

            if ($request->payment_type === 'booking') {
                $paymentData['booking_id'] = $request->booking_id;
            } else {
                $paymentData['activity_booking_id'] = $request->activity_booking_id;
            }

            $payment = Payment::create($paymentData);

            // Send payment alert notification
            $user = $payment->user();
            if ($user && $payment->status === 'completed') {
                $user->notify(new \App\Notifications\PaymentAlertNotification($payment, 'completed'));
            } elseif ($user && $payment->status === 'pending') {
                $user->notify(new \App\Notifications\PaymentAlertNotification($payment, 'pending'));
            }

            DB::commit();

            return redirect()->route('admin.payments.index')
                ->with('success', 'Payment recorded successfully! ' . ($discountAmount > 0 ? "Discount of $$discountAmount applied." : ""));
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    public function show(Payment $payment)
    {
        $payment->load(['booking.user', 'activityBooking.user', 'booking.room.roomType', 'activityBooking.activity']);
        return view('admin.payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        if ($payment->status === 'completed') {
            return back()->with('error', 'Cannot edit completed payments.');
        }

        $payment->load(['booking.user', 'activityBooking.user', 'booking.room.roomType', 'activityBooking.activity']);
        return view('admin.payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        if ($payment->status === 'completed') {
            return back()->with('error', 'Cannot edit completed payments.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,bank_transfer,online',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $payment->update([
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.payments.show', $payment)
            ->with('success', 'Payment updated successfully!');
    }

    public function complete(Payment $payment)
    {
        if ($payment->status === 'completed') {
            return back()->with('error', 'Payment is already completed.');
        }

        $payment->update(['status' => 'completed']);

        // Send payment alert notification
        $user = $payment->user();
        if ($user) {
            $user->notify(new \App\Notifications\PaymentAlertNotification($payment, 'completed'));
        }

        return back()->with('success', 'Payment marked as completed!');
    }

    public function refund(Payment $payment)
    {
        if ($payment->status !== 'completed') {
            return back()->with('error', 'Only completed payments can be refunded.');
        }

        if ($payment->payment_method === 'online' && $payment->transaction_id) {
            try {
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                \Stripe\Refund::create([
                    'payment_intent' => $payment->transaction_id,
                ]);
            } catch (\Exception $e) {
                return back()->with('error', 'Stripe Refund Failed: ' . $e->getMessage());
            }
        }

        $payment->update(['status' => 'refunded']);

        return back()->with('success', 'Payment refunded successfully!');
    }

    public function capture(Payment $payment)
    {
        if ($payment->status !== 'authorized') {
            return back()->with('error', 'Only authorized payments can be captured.');
        }

        if ($payment->payment_method === 'online' && $payment->transaction_id) {
            try {
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                $intent = \Stripe\PaymentIntent::retrieve($payment->transaction_id);
                $intent->capture();
            } catch (\Exception $e) {
                return back()->with('error', 'Stripe Capture Failed: ' . $e->getMessage());
            }
        }

        $payment->update(['status' => 'completed']);
        return back()->with('success', 'Payment captured successfully!');
    }

    public function destroy(Payment $payment)
    {
        if ($payment->status === 'completed') {
            return back()->with('error', 'Cannot delete completed payments.');
        }

        $payment->delete();

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment deleted successfully!');
    }

    public function reports()
    {
        $startDate = request('start_date', Carbon::now()->startOfMonth());
        $endDate = request('end_date', Carbon::now()->endOfMonth());

        $payments = Payment::with(['booking.user', 'activityBooking.user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total_payments' => $payments->count(),
            'total_amount' => $payments->sum('amount'),
            'completed_amount' => $payments->where('status', 'completed')->sum('amount'),
            'pending_amount' => $payments->where('status', 'pending')->sum('amount'),
            'refunded_amount' => $payments->where('status', 'refunded')->sum('amount'),
            'by_method' => $payments->groupBy('payment_method')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('amount')
                ];
            }),
            'by_type' => [
                'bookings' => $payments->whereNotNull('booking_id')->sum('amount'),
                'activities' => $payments->whereNotNull('activity_booking_id')->sum('amount'),
            ],
        ];

        return view('admin.payments.reports', compact('payments', 'summary', 'startDate', 'endDate'));
    }

    public function generateInvoice(Payment $payment)
    {
        // Check if invoice already exists
        $payment->load(['booking.user', 'booking.room.roomType']);

        $bookings = $payment->booking;
        if (!$bookings) {
            return back()->with('error', 'Invoices can only be generated for bookings.');
        }

        // Logic to generate or retrieve Invoice
        $invoice = \App\Models\Invoice::where('booking_id', $bookings->id)->first();

        DB::beginTransaction();
        try {
            if (!$invoice) {
                // Create Invoice Header if not exists
                $invoice = \App\Models\Invoice::create([
                    'booking_id' => $bookings->id,
                    'user_id' => $bookings->user_id,
                    'invoice_number' => 'INV-' . strtoupper(uniqid()),
                    'issue_date' => now(),
                    'due_date' => now(),
                    'total_amount' => 0,
                    'status' => $payment->status === 'completed' ? 'paid' : 'unpaid',
                ]);
                // Force regenerate items even if status is paid, to ensure all latest charges are shown
                $invoice->items()->delete();
            } else {
                // Force regenerate items even if status is paid, to ensure all latest charges are shown
                $invoice->items()->delete();
            }

            // Always regenerate items
            $total = 0;

            // 1. Room Charge
            $nights = \Carbon\Carbon::parse($bookings->check_in_date)->diffInDays(\Carbon\Carbon::parse($bookings->check_out_date));
            $roomPrice = $bookings->room->roomType->base_price * $nights;

            \App\Models\InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => "Room Charge: {$bookings->room->roomType->name} ({$nights} nights)",
                'quantity' => $nights,
                'unit_price' => $bookings->room->roomType->base_price,
                'amount' => $roomPrice,
                'type' => 'room'
            ]);
            $total += $roomPrice;

            // 3. Add Activity Charges
            // Broaden search dates slightly to handle checkout day activities
            $activities = \App\Models\ActivityBooking::where('user_id', $bookings->user_id)
                ->whereBetween('scheduled_time', [
                    \Carbon\Carbon::parse($bookings->check_in_date)->startOfDay(),
                    \Carbon\Carbon::parse($bookings->check_out_date)->endOfDay()
                ])
                ->whereIn('status', ['confirmed', 'completed', 'paid'])
                ->get();

            foreach ($activities as $activity) {
                \App\Models\InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => "Activity: {$activity->activity->name} ({$activity->participants} people) - " . $activity->scheduled_time->format('M d'),
                    'quantity' => 1,
                    'unit_price' => $activity->total_price,
                    'amount' => $activity->total_price,
                    'type' => 'activity'
                ]);
                $total += $activity->total_price;
            }

            // 4. Add Food Orders
            $foodOrders = \App\Models\FoodOrder::where('booking_id', $bookings->id)
                ->whereIn('status', ['delivered', 'ready', 'paid'])
                ->get();

            foreach ($foodOrders as $order) {
                \App\Models\InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => "Food Order #{$order->kot_number}: " . ($order->food ? $order->food->name : 'Item'),
                    'quantity' => $order->quantity,
                    'unit_price' => $order->total_price / ($order->quantity ?: 1),
                    'amount' => $order->total_price,
                    'type' => 'food'
                ]);
                $total += $order->total_price;
            }

            // 5. Add Tax (e.g., 10%)
            $tax = $total * 0.10;
            \App\Models\InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => "Tax (10%)",
                'quantity' => 1,
                'unit_price' => $tax,
                'amount' => $tax,
                'type' => 'tax'
            ]);

            // 3. Apply Payment Discount
            if ($payment->discount_amount > 0) {
                \App\Models\InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => "Discount ({$payment->coupon_code})",
                    'quantity' => 1,
                    'unit_price' => -$payment->discount_amount,
                    'amount' => -$payment->discount_amount,
                    'type' => 'discount'
                ]);
                $total -= $payment->discount_amount;
            }

            $invoice->update(['total_amount' => $total + $tax, 'tax_amount' => $tax]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to generate invoice: ' . $e->getMessage());
        }

        return redirect()->route('admin.payments.download-invoice', $invoice);
    }

    public function downloadInvoice(\App\Models\Invoice $invoice)
    {
        $invoice->load(['items', 'booking.user', 'user']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.payments.invoice-pdf', compact('invoice'));
        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function getPendingPayments()
    {
        $pendingPayments = Payment::with(['booking.user', 'activityBooking.user'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($pendingPayments);
    }

    public function dashboardStats()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        $stats = [
            'today_revenue' => Payment::whereDate('created_at', $today)
                ->where('status', 'completed')
                ->sum('amount'),
            'month_revenue' => Payment::whereDate('created_at', '>=', $thisMonth)
                ->where('status', 'completed')
                ->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'total_transactions' => Payment::where('status', 'completed')->count(),
        ];

        return response()->json($stats);
    }

    public function checkout(Payment $payment)
    {
        if ($payment->status === 'completed') {
            return redirect()->route('booking.rooms.index')->with('info', 'Payment already completed.');
        }

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $lineItems = [];

        if ($payment->booking) {
            $isDeposit = str_contains($payment->notes, 'Deposit');
            $typeLabel = $isDeposit ? "Deposit" : "Full Payment";
            $description = "$typeLabel for Room Booking: " . $payment->booking->room->roomType->name . " (" . $payment->booking->room->room_number . ")";

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd', // Adapt to your currency logic later
                    'product_data' => [
                        'name' => 'Room Booking (' . $typeLabel . ')',
                        'description' => $description,
                    ],
                    'unit_amount' => (int) ($payment->amount * 100), // Amount in cents
                ],
                'quantity' => 1,
            ];
        }

        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'payment_intent_data' => [
                'capture_method' => config('hotel.booking.payment_capture_method', 'automatic'),
            ],
            'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel'),
            'metadata' => [
                'payment_id' => $payment->id,
                'booking_id' => $payment->booking_id ?? null,
            ],
        ]);

        return redirect($checkout_session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('booking.rooms.index')->with('error', 'Invalid payment session.');
        }

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            $paymentId = $session->metadata->payment_id;

            $payment = Payment::findOrFail($paymentId);

            if ($payment->status !== 'completed') {
                $payment->update([
                    'status' => config('hotel.booking.payment_capture_method') === 'manual' ? 'authorized' : 'completed',
                    'transaction_id' => $session->payment_intent,
                    'payment_method' => 'online', // Ensure method is marked as online
                ]);

                // Auto-confirm booking if related
                if ($payment->booking) {
                    $payment->booking->update(['status' => 'confirmed']);
                    // Ideally send confirmation email here
                }
            }

            return view('payment.success', compact('payment'));

        } catch (\Exception $e) {
            return redirect()->route('booking.rooms.index')->with('error', 'Error verifying payment: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return view('payment.cancel');
    }
}
