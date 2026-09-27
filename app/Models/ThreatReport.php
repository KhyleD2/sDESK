<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThreatReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'description',
        'severity',
        'verdict',
        'escalation_note',
        'status',
        'scan_result',
        'archived_by',
        'archive_reason',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ThreatCategory::class, 'category_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'report_id');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(Action::class, 'report_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'report_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'report_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ReportComment::class, 'report_id');
    }
}
