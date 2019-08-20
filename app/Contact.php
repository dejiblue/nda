<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contacts';

    protected $fillable = ['first_name','last_name','province','mobile','email','opt_in','ip_address','user_agent'];

    protected $dates = ['created_at','updated_at'];
}
