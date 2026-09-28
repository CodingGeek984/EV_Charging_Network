<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Connector extends Model
{
    
    protected $guarded = ['id'];

    public function station()
    {
        return $this->belongTo(Station::class);
    }

    public function chargingSessions()
    {
        return $this->hasMany(Chargingsession::class);
    }
    
}
