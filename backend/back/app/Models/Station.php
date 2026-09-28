<?php

namespace App\Models;

use App\Models\Connector;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    
    protected $guarded = ['id'];

    public function connectors()
    {
        return $this->hasMany(Conenctor::class);
    }

    public function chargingSessions()
    {
        return $this->hasManyThrough(chargingSessions::class, Connector::class);
    }

}
