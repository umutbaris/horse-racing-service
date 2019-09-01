<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Races extends Model
{
	protected $guarded = [];
	
	/**
	 * The roles that belong to the user.
	 */
	public function horses()
	{
		return $this->belongsToMany(Horses::class, "horses_races", "race_id", "horse_id");
	}
}
