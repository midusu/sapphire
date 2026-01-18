<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use App\Models\ActivityBooking;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        // Use role slug instead of hardcoded ID
        $query = User::whereHas('role', function($q) {
            $q->where('slug', 'guest');
        });

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && !empty($request->status)) {
            if ($request->status === 'active') {
                $query->whereHas('bookings', function ($q) {
                    $q->whereIn('status', ['confirmed', 'checked_in']);
                });
            } elseif ($request->status === 'inactive') {
                // Debugging logic: ensure this block is hit
                $query->whereDoesntHave('bookings', function ($q) {
                    $q->whereIn('status', ['confirmed', 'checked_in']);
                });
            }
        }

        if ($request->has('loyalty_status') && !empty($request->loyalty_status)) {
            $status = $request->loyalty_status;
            $query->where(function($q) use ($status) {
                // Check for manual override first
                $q->where('loyalty_level_override', $status)
                  ->orWhere(function($subQ) use ($status) {
                      // Only apply auto-calculation logic if no override is set
                      $subQ->whereNull('loyalty_level_override');
                      
                      // Add count of bookings to filter
                      if ($status === 'platinum') {
                          $subQ->has('bookings', '>=', 20);
                      } elseif ($status === 'gold') {
                          $subQ->has('bookings', '>=', 10)->has('bookings', '<', 20);
                      } elseif ($status === 'silver') {
                          $subQ->has('bookings', '>=', 5)->has('bookings', '<', 10);
                      } else { // bronze
                          $subQ->has('bookings', '<', 5);
                      }
                  });
            });
        }

        $guests = $query->with([
                'bookings' => function ($query) {
                    $query->orderBy('check_in_date', 'desc')->limit(5);
                },
                'activityBookings' => function ($query) {
                    $query->orderBy('scheduled_time', 'desc')->limit(5);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        if (app()->runningUnitTests()) {
             // dump($guests->pluck('name')->toArray());
        }

        return view('admin.guests.index', compact('guests'));
    }

    public function create()
    {
        return view('admin.guests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'id_number' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
        ]);

        $guestData = $request->all();
        $guestData['role_id'] = 7; // Guest role
        $guestData['password'] = bcrypt('password'); // Default password, guest can change later
        $guestData['email_verified_at'] = now();

        $guest = User::create($guestData);

        // Send welcome notification/email
        $guest->notify(new \App\Notifications\WelcomeNotification($guest));

        return redirect()->route('admin.guests.index')
            ->with('success', 'Guest created successfully and welcome email sent!');
    }

    public function show(User $guest)
    {
        if ($guest->role_id != 7) {
            abort(404);
        }

        $guest->load([
            'bookings' => function ($query) {
                $query->orderBy('check_in_date', 'desc')->with(['room.roomType']);
            },
            'activityBookings' => function ($query) {
                $query->orderBy('scheduled_time', 'desc')->with(['activity']);
            }
        ]);

        // Calculate guest statistics
        $stats = [
            'total_bookings' => $guest->bookings->count(),
            'total_spent' => \App\Models\Payment::whereHas('booking', function ($query) use ($guest) {
                $query->where('user_id', $guest->id);
            })->orWhereHas('activityBooking', function ($query) use ($guest) {
                $query->where('user_id', $guest->id);
            })->where('status', 'completed')->sum('amount'),
            'total_stays' => $guest->bookings->where('status', 'checked_out')->count(),
            'favorite_room_type' => $guest->bookings->groupBy('room.roomType.name')->map->count()->sortDesc()->keys()->first(),
            'loyalty_status' => $this->calculateLoyaltyStatus($guest),
            'last_visit' => $guest->bookings->where('status', 'checked_out')->max('check_out_date'),
        ];

        return view('admin.guests.show', compact('guest', 'stats'));
    }

    public function edit(User $guest)
    {
        if ($guest->role_id != 7) {
            abort(404);
        }

        return view('admin.guests.edit', compact('guest'));
    }

    public function update(Request $request, User $guest)
    {
        if ($guest->role_id != 7) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($guest->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'id_number' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'loyalty_level_override' => 'nullable|in:bronze,silver,gold,platinum',
        ]);

        $guest->update($request->except(['password', 'role_id', 'email_verified_at']));

        return redirect()->route('admin.guests.show', $guest)
            ->with('success', 'Guest updated successfully!');
    }

    public function destroy(User $guest)
    {
        if ($guest->role_id != 7) {
            abort(404);
        }

        // Check if guest has active bookings
        $activeBookings = $guest->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->count();

        if ($activeBookings > 0) {
            return back()->with('error', 'Cannot delete guest with active bookings.');
        }

        $guest->delete();

        return redirect()->route('admin.guests.index')
            ->with('success', 'Guest deleted successfully!');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $guests = User::where('role_id', 7)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%")
                    ->orWhere('id_number', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'email', 'phone']);

        return response()->json($guests);
    }

    public function statistics()
    {
        $stats = [
            'total_guests' => User::where('role_id', 7)->count(),
            'new_this_month' => User::where('role_id', 7)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count(),
            'active_guests' => User::where('role_id', 7)
                ->whereHas('bookings', function ($query) {
                    $query->whereIn('status', ['confirmed', 'checked_in']);
                })->count(),
            'repeat_guests' => User::where('role_id', 7)
                ->whereHas('bookings', function ($query) {
                    $query->where('status', 'checked_out');
                }, '>', 1)
                ->count(),
            'by_phone' => User::where('role_id', 7)
                ->whereNotNull('phone')
                ->groupBy('phone')
                ->selectRaw('phone, COUNT(*) as count')
                ->orderByDesc('count')
                ->limit(10)
                ->get(),
            'loyalty_distribution' => [
                'bronze' => 0,
                'silver' => 0,
                'gold' => 0,
                'platinum' => 0,
            ],
        ];

        // Calculate loyalty distribution
        $guests = User::where('role_id', 7)->get();
        foreach ($guests as $guest) {
            $status = $this->calculateLoyaltyStatus($guest);
            $stats['loyalty_distribution'][$status]++;
        }

        return response()->json($stats);
    }

    public function loyalty(Request $request)
    {
        // Get all guests with their bookings
        $guests = User::where('role_id', 7)
            ->with([
                'bookings' => function ($query) {
                    $query->orderBy('check_in_date', 'desc')->limit(10);
                }
            ])
            ->get()
            ->map(function ($guest) {
                $guest->loyalty_status = $this->calculateLoyaltyStatus($guest);
                $guest->total_bookings = $guest->bookings->count();
                // Calculate total spent from payments
                $guest->total_spent = Payment::whereHas('booking', function ($query) use ($guest) {
                    $query->where('user_id', $guest->id);
                })->orWhereHas('activityBooking', function ($query) use ($guest) {
                    $query->where('user_id', $guest->id);
                })->where('status', 'completed')->sum('amount');
                return $guest;
            })
            ->sortByDesc('total_spent');

        // Apply filters
        if ($request->filled('loyalty_status')) {
            $guests = $guests->filter(function ($guest) use ($request) {
                return $guest->loyalty_status === $request->loyalty_status;
            });
        }

        if ($request->filled('min_bookings')) {
            $guests = $guests->filter(function ($guest) use ($request) {
                return $guest->total_bookings >= (int) $request->min_bookings;
            });
        }

        if ($request->filled('min_spent')) {
            $guests = $guests->filter(function ($guest) use ($request) {
                return $guest->total_spent >= (float) $request->min_spent;
            });
        }

        // Manual pagination for collection
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $items = $guests->forPage($currentPage, $perPage);
        $total = $guests->count();

        // Create paginator manually
        $guests = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.guests.loyalty', compact('guests'));
    }

    public function checkInToday()
    {
        $guests = User::where('role_id', 7)
            ->whereHas('bookings', function ($query) {
                $query->whereDate('check_in_date', Carbon::today())
                    ->whereIn('status', ['confirmed']);
            })
            ->with([
                'bookings' => function ($query) {
                    $query->whereDate('check_in_date', Carbon::today())
                        ->whereIn('status', ['confirmed'])
                        ->with(['room.roomType']);
                }
            ])
            ->get();

        return view('admin.guests.check-in-today', compact('guests'));
    }

    public function checkOutToday()
    {
        $guests = User::where('role_id', 7)
            ->whereHas('bookings', function ($query) {
                $query->whereDate('check_out_date', Carbon::today())
                    ->whereIn('status', ['confirmed', 'checked_in']);
            })
            ->with([
                'bookings' => function ($query) {
                    $query->whereDate('check_out_date', Carbon::today())
                        ->whereIn('status', ['confirmed', 'checked_in'])
                        ->with(['room.roomType']);
                }
            ])
            ->get();

        return view('admin.guests.check-out-today', compact('guests'));
    }

    public function vipGuests()
    {
        $guests = User::where('role_id', 7)
            ->with(['bookings.room.roomType'])
            ->get()
            ->filter(function ($guest) {
                // VIP criteria: more than 10 bookings or spent more than $5000
                $totalSpent = \App\Models\Payment::whereHas('booking', function ($query) use ($guest) {
                    $query->where('user_id', $guest->id);
                })->orWhereHas('activityBooking', function ($query) use ($guest) {
                    $query->where('user_id', $guest->id);
                })->where('status', 'completed')->sum('amount');
                return $guest->bookings->count() >= 10 || $totalSpent >= 5000;
            })
            ->map(function ($guest) {
                $guest->total_bookings = $guest->bookings->count();
                $guest->total_spent = \App\Models\Payment::whereHas('booking', function ($query) use ($guest) {
                    $query->where('user_id', $guest->id);
                })->orWhereHas('activityBooking', function ($query) use ($guest) {
                    $query->where('user_id', $guest->id);
                })->where('status', 'completed')->sum('amount');
                $guest->loyalty_status = $this->calculateLoyaltyStatus($guest);
                return $guest;
            })
            ->sortByDesc('total_spent');

        return view('admin.guests.vip', compact('guests'));
    }

    public function export(Request $request)
    {
        $guests = User::where('role_id', 7)
            ->with(['bookings'])
            ->get();

        // Export logic here - could return CSV, Excel, or PDF
        // For now, return a simple view
        return view('admin.guests.export', compact('guests'));
    }

    private function calculateLoyaltyStatus(User $guest)
    {
        return $guest->getLoyaltyStatus();
    }
}
