<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PtoBalance extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'year',
        'allocated_days',
        'used_days',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
