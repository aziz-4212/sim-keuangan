<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;
    protected $connection = 'sqlsrv';
    protected $table = 'USERLOG';
    public $timestamps = false;
    protected  $guarded = [];
}
