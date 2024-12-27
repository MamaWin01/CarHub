<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table   = 'verification_code';

    public $timestamps = false;
}
