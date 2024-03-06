<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;
    protected $connection = 'sqlsrv1';
    protected $table = 'VENDOR';
    public $timestamps = false;
    protected  $guarded = [];
}
