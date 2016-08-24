<?php

namespace LaraKiss;

use Illuminate\Database\Eloquent\Model;

class Show extends Model
{
    public function episodes()
    {
    	return $this->hasMany(Episode::class);
    }
}
