<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horses extends Model
{
	protected $guarded = [];

	public function races()
	{
		return $this->belongsToMany(Races::class);
	}
	
}
