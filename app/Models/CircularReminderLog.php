<?php
// app/Models/CircularReminderLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircularReminderLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['circular_id', 'sent_by', 'recipient_count', 'sent_at'];

    protected $casts = ['sent_at' => 'datetime'];

    public function circular()
    {
        return $this->belongsTo(Circular::class);
    }

    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
