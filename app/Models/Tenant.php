<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function ptoBalances()
    {
        return $this->hasMany(PtoBalance::class);
    }

    public function ptoRequests()
    {
        return $this->hasMany(PtoRequest::class);
    }
}
