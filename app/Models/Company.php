<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'address', 'phone'];

    public function outlets()
    {
        return $this->hasMany(Outlet::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
