<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\UserManagement\Models\Address;
use Modules\UserManagement\Models\Profile;
use Modules\UserManagement\Notifications\ResetPasswordNotification;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $guard_name = 'api';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'password'          => 'hashed',
        ];
    }

    // protected static $logName = 'user_activity';

    // // Optional: Configure the custom description for the activity log
    // public function getDescriptionForEvent(string $eventName): string
    // {
    //     return "User {$eventName}";
    // }

    // Implement the required getActivitylogOptions method
    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults();

    // }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    // Scope for customer users
    public function scopeCustomer(Builder $query)
    {
        $query->whereHas('roles', function ($q) {
            $q->where('name', 'Customer');
        });
    }
    // Scope to exclude users with the "customer" role
    public function scopeWithoutCustomer(Builder $query)
    {
        $query->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'Customer');
        });
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'subject_id');
    }
}
