<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }} - Sapphire Hotel</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .logo {
            display: flex;
            align-items: center;
        }
        
        .logo h1 {
            color: #3b82f6;
            margin: 0;
            font-size: 28px;
        }
        
        .logo p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }
        
        .invoice-info {
            text-align: right;
        }
        
        .invoice-info h2 {
            margin: 0;
            color: #1f2937;
            font-size: 24px;
        }
        
        .invoice-info p {
            margin: 5px 0;
            color: #6b7280;
        }
        
        .billing-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 30px;
        }
        
        .section-title {
            font-weight: 600;
            color: #374151;
            margin-bottom: 10px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .billing-info p {
            margin: 5px 0;
            color: #4b5563;
        }
        
        .payment-details {
            background: #f9fafb;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
        }
        
        .payment-details h3 {
            margin: 0 0 15px 0;
            color: #1f2937;
        }
        
        .payment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .payment-item {
            display: flex;
            justify-content: space-between;
        }
        
        .payment-item span:first-child {
            color: #6b7280;
        }
        
        .payment-item span:last-child {
            font-weight: 600;
            color: #1f2937;
        }
        
        .service-details {
            margin-bottom: 30px;
        }
        
        .service-details h3 {
            margin: 0 0 15px 0;
            color: #1f2937;
        }
        
        .service-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .service-table th {
            background: #f3f4f6;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .service-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            color: #4b5563;
        }
        
        .service-table .amount {
            text-align: right;
            font-weight: 600;
        }
        
        .total-section {
            background: #f9fafb;
            padding: 20px;
            border-radius: 6px;
            text-align: right;
        }
        
        .total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }
        
        .total-row span {
            width: 200px;
            text-align: right;
            padding-right: 20px;
        }
        
        .total-row.grand-total span:first-child {
            font-weight: 600;
            color: #1f2937;
        }
        
        .total-row.grand-total span:last-child {
            font-weight: 700;
            font-size: 18px;
            color: #059669;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-pending {
            background: #fed7aa;
            color: #92400e;
        }
        
        .status-refunded {
            background: #fee2e2;
            color: #991b1b;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .invoice-container {
                box-shadow: none;
                border-radius: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <div>
                    <h1>Sapphire Hotel</h1>
                    <p>Hotel Management System</p>
                </div>
            </div>
            <div class="invoice-info">
                <h2>INVOICE</h2>
                <p>#{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</p>
                <p>Date: {{ $payment->created_at->format('M d, Y') }}</p>
                <p>Time: {{ $payment->created_at->format('H:i') }}</p>
            </div>
        </div>

        <!-- Billing Information -->
        <div class="billing-section">
            <div>
                <div class="section-title">Bill To</div>
                <div class="billing-info">
                    <p><strong>{{ $payment->booking?->user->name ?? $payment->activityBooking?->user->name }}</strong></p>
                    <p>{{ $payment->booking?->user->email ?? $payment->activityBooking?->user->email }}</p>
                    @if($payment->booking?->user->phone || $payment->activityBooking?->user->phone)
                        <p>{{ $payment->booking?->user->phone ?? $payment->activityBooking?->user->phone }}</p>
                    @endif
                    @if($payment->booking?->user->address || $payment->activityBooking?->user->address)
                        <p>{{ $payment->booking?->user->address ?? $payment->activityBooking?->user->address }}</p>
                    @endif
                </div>
            </div>
            
            <div>
                <div class="section-title">Payment Status</div>
                <div class="billing-info">
                    <p>
                        <span class="status-badge status-{{ $payment->status }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </p>
                    <p><strong>Method:</strong> {{ ucfirst($payment->payment_method) }}</p>
                    @if($payment->transaction_id)
                        <p><strong>Transaction ID:</strong> {{ $payment->transaction_id }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="payment-details">
            <h3>Payment Information</h3>
            <div class="payment-grid">
                <div class="payment-item">
                    <span>Payment Date:</span>
                    <span>{{ $payment->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="payment-item">
                    <span>Payment Method:</span>
                    <span>{{ ucfirst($payment->payment_method) }}</span>
                </div>
                @if($payment->transaction_id)
                    <div class="payment-item">
                        <span>Transaction ID:</span>
                        <span>{{ $payment->transaction_id }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Service Details -->
        <div class="service-details">
            <h3>Service Details</h3>
            @if($payment->booking_id)
                <table class="service-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Details</th>
                            <th class="amount">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Room Booking</td>
                            <td>
                                Room {{ $payment->booking->room->room_number }} ({{ $payment->booking->room->roomType->name }})<br>
                                {{ $payment->booking->check_in_date->format('M d, Y') }} - {{ $payment->booking->check_out_date->format('M d, Y') }}<br>
                                {{ $payment->booking->check_in_date->diffInDays($payment->booking->check_out_date) }} nights
                            </td>
                            <td class="amount">${{ number_format($payment->booking->total_amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            @else
                <table class="service-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Details</th>
                            <th class="amount">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Activity Booking</td>
                            <td>
                                {{ $payment->activityBooking->activity->name }}<br>
                                {{ $payment->activityBooking->scheduled_time->format('M d, Y H:i') }}<br>
                                {{ $payment->activityBooking->participants }} participants
                            </td>
                            <td class="amount">${{ number_format($payment->activityBooking->total_price, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Total -->
        <div class="total-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>${{ number_format($payment->amount, 2) }}</span>
            </div>
            <div class="total-row">
                <span>Tax (0%):</span>
                <span>$0.00</span>
            </div>
            <div class="total-row grand-total">
                <span>Total Amount:</span>
                <span>${{ number_format($payment->amount, 2) }}</span>
            </div>
        </div>

        <!-- Notes -->
        @if($payment->notes)
            <div style="margin-top: 30px;">
                <div class="section-title">Notes</div>
                <p style="color: #4b5563; margin: 10px 0;">{{ $payment->notes }}</p>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for choosing Sapphire Hotel!</p>
            <p>This is a computer-generated invoice and does not require a signature.</p>
            <p>Generated on {{ now()->format('M d, Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
