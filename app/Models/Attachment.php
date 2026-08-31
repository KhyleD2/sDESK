<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'report_id',
        'original_filename',
        'stored_filename',
        'file_hash',
        'file_type',
        'storage_path',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(ThreatReport::class, 'report_id');
    }
}
