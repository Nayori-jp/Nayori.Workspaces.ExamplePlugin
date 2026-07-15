<?php

namespace Plugins\ExamplePlugin\Models;

use App\Models\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExampleRecord extends Model
{
    protected $table = 'example_plugin_records';

    protected $fillable = [
        'business_id',
        'name',
        'slug',
        'status',
        'summary',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
