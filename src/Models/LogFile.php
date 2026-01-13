<?php

namespace Munch\FilamentLogviewer\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Placeholder model for Filament resource
 * This model is not backed by a database table
 */
class LogFile extends Model
{
    protected $guarded = [];

    public $incrementing = false;

    protected $keyType = 'string';

    public function getTable()
    {
        return '';
    }
}
