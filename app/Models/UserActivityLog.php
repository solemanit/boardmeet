<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivityLog extends Model
{
  use HasFactory;

  protected $table = 'user_activity_logs';

  protected $fillable = [
    'user_id',
    'action',
    'ip_address',
    'user_agent',
    'details',
  ];

  protected $casts = [
    'details' => 'json',
    'created_at' => 'datetime',
  ];

  /**
   * Get the user associated with this activity log
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}
