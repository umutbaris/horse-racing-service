<?php

namespace App\Http\Services;
use App\Http\Controllers\RacesController;
use App\Repositories\RaceRepository;
use App\Repositories\HorseRepository;

class RaceService
{
	public function __construct(raceRepository $raceRepository, HorseRepository $horseRepository)
	{
		$this->raceRepository = $raceRepository;
		$this->horseRepository = $horseRepository;
	}

	/**
	 * Generate numbers randomly between 1 to max horse number
	 *
	 * @param int $max
	 * @return array
	 */
	public function getHorsesRandomly($freeHorses): array
	{
		$freeHorsesIds = $this->horseRepository->onlyFields($freeHorses, "id");
		$random = range(1, count($freeHorsesIds));
		shuffle($random);
		$randomHorses = array_slice($random ,0, 8);
		foreach ($randomHorses as $randomHorse){
			$this->horseRepository->update($randomHorse,['status' => 'run']);
		}

		return $randomHorses;
	}

	/**
	 * Calculating running meters for each progress and updating distance covered
	 *
	 * @param array horses
	 * @return void
	 */
	public function runToHorses($race)
	{
		$horses = $race->horses;
		foreach($horses as $horse){
			if($horse->status === 'run'){
				$horse->distance_covered = $this->getDistanceCovered($horse, $race->current_time + 10);
				$this->horseRepository->update($horse->id,['distance_covered' => $horse->distance_covered]);
			}
			$this->checkIsHorseFinishedRace($horse, $race);
		}

		$this->determineHorsePosition($horses);
	}

	/**
	 * Calculating position according to distance covered and updating position
	 *
	 * @param [type] $horses
	 * @return void
	 */
	public function determineHorsePosition($horses)
	{
		array_multisort(array_column($horses, 'distance_covered'), SORT_ASC, $horses);
		$count = 0;
		foreach($horses as $key=>$horse){
			if($horse->status === 'run'){
				$this->horseRepository->update($horse->id,['position' => count($horses) - $count]);
				$count++;
			}

		}
	}

	public function getDistanceCovered($horse, $currentTime)
	{
		$slowedMeter = $horse->endurance * 100;
		$fastTime = $slowedMeter / $horse->speed;
		if($currentTime > $fastTime){
			$slowTime = $currentTime - $fastTime;
			$fullSpeedDistance = $fastTime * $horse->speed;
			$slowedPercentage = $horse->strength * 8 / 100;
			$horse->speed = $horse->speed - (5 - 5 * $slowedPercentage);
			$slowSpeedDistance = $slowTime * $horse->speed;
			$this->horseRepository->update($horse->id,['slow_speed' => $horse->speed]);

			return $fullSpeedDistance + $slowSpeedDistance;

		} else {
			return $horse->distance_covered = $horse->speed * $currentTime;
		}
	}

	public function checkIsHorseFinishedRace($horse, $race)
	{
		if ($race->current_time >= 150 && $horse->distance_covered >= 1500 && $horse->status === 'run') {
			$this->horseRepository->update($horse->id,['distance_covered' => 1500]);
			$this->horseRepository->update($horse->id,['status' => 'Completed Race']);
			$this->raceRepository->update($race->id,['completed_horse_count' => $race->completed_horse_count + 1]);

			$extraDistance = 1500 - $horse->distance_covered;
			$extraTime = $extraDistance / $horse->slow_speed;

			if ($horse->position === 1){
				$this->raceRepository->update($race->id,['best_time' => $race->current_time - $extraTime]);
			}
			if(count($this->horseRepository->findBy('status', 'Completed Race')) === 8){
				$this->raceRepository->update($race->id,['status' => 'Finished']);
			}
		}
	}
}
