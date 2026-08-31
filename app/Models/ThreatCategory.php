<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThreatCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function threatReports(): HasMany
    {
        return $this->hasMany(ThreatReport::class, 'category_id');
    }
}
