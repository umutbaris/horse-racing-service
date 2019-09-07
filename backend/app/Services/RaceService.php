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

	/**
	 * Calculating distance covered with high speed and slow speed
	 *
	 * @param array $horse
	 * @param int $currentTime
	 * @return int
	 */
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
		if ($race->current_time >=  $race->race_meter / 10 && $horse->distance_covered >= $race->race_meter && $horse->status === 'run') {
			$this->horseRepository->update($horse->id,['distance_covered' => $race->race_meter]);
			$this->horseRepository->update($horse->id,['status' => 'Completed Race']);

			$extraDistance =  $race->race_meter - $horse->distance_covered;
			$extraTime = $extraDistance / $horse->slow_speed;

			if ($horse->position === 1){
				$this->raceRepository->update($race->id,['best_time' => $race->current_time - $extraTime]);
			}
		}

		$this->checkIsRaceFinished($race);
	}

	public function checkIsRaceFinished($race)
	{
		$statuses = array_column($race->horses, 'status');
		$finishedHorsecount = 0;
		foreach($statuses as $status){
			if($status === 'Completed Race'){
				$finishedHorsecount++;
			}
		}

		if($finishedHorsecount === 8){
			$this->raceRepository->update($race->id,['status' => 'Finished']);
		}
	}


	public function getLastFiveResult()
	{
		$finishedRaces = $this->raceRepository->findByLastNelements("status", "Finished", 5, ['horses']);
		$topHorses = [];
		foreach($finishedRaces as $finishedRace){
			$sortedHorses = $finishedRace->horses->sortBy('position');
			$topThreeHorses = $sortedHorses->slice(0, 3);
			$lastRaces = [
				'race' => $finishedRace->id,
				'topThreeHorses' => $topThreeHorses
			];
			array_push($topHorses, $lastRaces);
		}

		return $topHorses;
	}
}
