<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table      = 'vehicle';
    protected $primaryKey = 'id';

    public $timestamps = false;

}
