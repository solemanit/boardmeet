<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'date_of_birth', 'gender',
        'blood_group', 'address', 'photo',
        'employee_id', 'designation', 'department', 'organization', 'joining_date',
        'degree', 'institution', 'passing_year',
        'status', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date'  => 'date',
    ];

    // ── Relationships ─────────────────────────────────────────────────────
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ── Accessors ─────────────────────────────────────────────────────────
    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo && Storage::disk('public')->exists($this->photo)) {
            return Storage::url($this->photo);
        }
        return asset('assets/img/avatar/default-avatar.png');
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    // ── Scopes ────────────────────────────────────────────────────────────
    public function scopeActive($q)   { return $q->where('status', 'active'); }
    public function scopeInactive($q) { return $q->where('status', 'inactive'); }
}
