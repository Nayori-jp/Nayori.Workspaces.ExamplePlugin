<?php

namespace Plugins\ExamplePlugin\Models;

use Illuminate\Database\Eloquent\Model;

class ExampleRecord extends Model
{
    protected $table = 'example_plugin_records';

    protected $fillable = [
        'name',
        'slug',
        'status',
        'summary',
    ];
}
