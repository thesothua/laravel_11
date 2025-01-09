<?php

namespace Modules\UserManagement\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
}
