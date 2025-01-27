<?php
namespace Modules\UserManagement\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\UserManagement\Database\Factories\ProfileFactory;

class Profile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];

    // protected static function newFactory(): ProfileFactory
    // {
    //     // return ProfileFactory::new();
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        // When creating or updating the profile, update the user's name
        static::created(function ($profile) {
            if ($profile->user) {
                $profile->user->name = "{$profile->first_name} {$profile->last_name}";
                $profile->user->save();
            }
        });

        // When creating or updating the profile, update the user's name
        static::updated(function ($profile) {
            if ($profile->user) {
                $profile->user->name = "{$profile->first_name} {$profile->last_name}";
                $profile->user->save();
            }
        });
    }
}
