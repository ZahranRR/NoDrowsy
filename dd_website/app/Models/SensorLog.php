<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['hr', 'spo2', 'created_at'];
}
