<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChargingSession extends Model
{
    
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongTo(User::class);
    }

    public function connector()
    {
        return $this->belongTo(Connector::class);
    }

}
