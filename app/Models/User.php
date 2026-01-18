<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'date_of_birth',
        'address',
        'emergency_contact',
        'emergency_phone',
        'id_number',
        'nationality',
        'gender',
        'notes',
        'loyalty_level_override'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function activityBookings()
    {
        return $this->hasMany(ActivityBooking::class);
    }

    public function foodOrders()
    {
        return $this->hasMany(FoodOrder::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }


    public function hasRole($roleSlug)
    {
        return $this->role && $this->role->slug === $roleSlug;
    }

    public function isAdmin()
    {
        return $this->role && $this->role->slug === 'admin';
    }

    public function isManager()
    {
        return $this->role && $this->role->slug === 'manager';
    }

    public function isReceptionist()
    {
        return $this->role && $this->role->slug === 'receptionist';
    }

    public function isAccountant()
    {
        return $this->role && $this->role->slug === 'accountant';
    }

    public function isHousekeeping()
    {
        return $this->role && $this->role->slug === 'housekeeping';
    }

    public function isActivityStaff()
    {
        return $this->role && $this->role->slug === 'activity-staff';
    }

    public function isGuest()
    {
        return $this->role && $this->role->slug === 'guest';
    }

    public function getPaymentsAttribute()
    {
        return Payment::whereHas('booking', function ($query) {
            $query->where('user_id', $this->id);
        })->orWhereHas('activityBooking', function ($query) {
            $query->where('user_id', $this->id);
        })->orderBy('created_at', 'desc')->get();
    }

    public function getLoyaltyStatus()
    {
        if ($this->loyalty_level_override) {
            return $this->loyalty_level_override;
        }

        $totalBookings = $this->bookings->count();
        // Sum completed payments
        $totalSpent = Payment::whereHas('booking', function ($query) {
            $query->where('user_id', $this->id);
        })->orWhereHas('activityBooking', function ($query) {
            $query->where('user_id', $this->id);
        })->where('status', 'completed')->sum('amount');

        if ($totalBookings >= 20 || $totalSpent >= 10000) {
            return 'platinum';
        } elseif ($totalBookings >= 10 || $totalSpent >= 5000) {
            return 'gold';
        } elseif ($totalBookings >= 5 || $totalSpent >= 2000) {
            return 'silver';
        } else {
            return 'bronze';
        }
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }
}
