<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $connection = 'sqlsrv';
    protected $table = 'USERLOG_ID';
    public $timestamps = false;
    protected $primaryKey = 'ID';
    protected  $guarded = [];

    public function user_log()
    {
        return $this->belongsTo(UserLog::class, 'USERLOGNM', 'USLOGNM');
    }
}
