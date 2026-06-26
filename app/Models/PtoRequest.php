<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PtoRequest extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'start_date',
        'end_date',
        'status',
        'reason',
        'reviewer_id',
        'reviewed_at',
        'reviewer_notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
