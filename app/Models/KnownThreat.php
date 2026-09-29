<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnownThreat extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'indicator',
        'type',
        'first_reported_report_id',
        'times_reported',
        'added_by',
    ];

    protected $casts = [
        'added_at' => 'datetime',
    ];

    public function firstReport(): BelongsTo
    {
        return $this->belongsTo(ThreatReport::class, 'first_reported_report_id');
    }

    public function addedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Get the attachment associated with this file hash indicator
     * Only applicable for file_hash type threats
     */
    public function attachment()
    {
        if ($this->type !== 'file_hash') {
            return null;
        }

        return Attachment::where('file_hash', $this->indicator)->first();
    }
}
