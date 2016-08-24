<?php

namespace LaraKiss;

use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    public function show()
    {
    	return $this->belongsTo(Show::class);
    }
}
