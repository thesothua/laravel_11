<?php

namespace Modules\UserManagement\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\UserManagement\Database\Factories\AddressFactory;

class Address extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];

    // protected static function newFactory(): AddressFactory
    // {
    //     // return AddressFactory::new();
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
