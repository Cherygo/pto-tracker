<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PtoBalance extends Model
{
    use BelongsToTenant;

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
