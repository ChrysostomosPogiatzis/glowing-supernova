<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'surname', 'mobile_number', 'email', 'company_id', 'outlet_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
