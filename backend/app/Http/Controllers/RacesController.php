<?php

namespace App\Http\Controllers;
use App\Http\Requests\CreateRaceRequest;
use App\Repositories\RaceRepository;
use App\Repositories\HorseRepository;
use App\Models\Races;
use App\Models\Horses;
use Illuminate\Http\Request;

use App\Http\Services\RaceService;

class RacesController extends Controller
{
	protected $raceRepository;
	protected $raceService;
	
	public function __construct(RaceRepository $raceRepository, HorseRepository $horseRepository, RaceService $raceService)
	{
		$this->raceRepository = $raceRepository;
		$this->horseRepository = $horseRepository;
		$this->raceService = new RaceService($this->raceRepository, $this->horseRepository);
	}

	public function index()
	{
		$races = $this->raceRepository->all('horses');
		return $this->sendSuccess($races);
	}

	public function show(int $id)
	{
		$race = $this->raceRepository->find($id, 'horses');
		return $this->sendSuccess($race);
	}

	/**
	 * Checking active race count and determine horses
	 *
	 * @param CreateraceRequest $request
	 * @return message
	 */
	public function create(CreateraceRequest $request)
	{
		if (3 > count($this->actives()->getData()->data)){
			$request['current_time'] = 0;
			$request['status'] = "ongoing";
			$request['finished_time'] = '0';
			if(!empty([$request['race_meter']])){
				$request['race_meter'] = 1500;
			}
			$request['best_time'] = $this->raceRepository->getBestTime();
			$race = $this->raceRepository->store($request->all());

			$horses = $this->raceService->getHorsesRandomly();
			$race->horses()->attach($horses);

			return $this->sendSuccess($race, 201);
		} else {
			return $this->sendError("There are already 3 races. You can't create new race. You should wait to finish any active race", 500);
		}
	}

	public function update(int $id, CreateraceRequest $request)
	{
		$race = $this->raceRepository->update($id, $request->all());
		return $this->sendSuccess($race);
	}

	public function destroy(int $id)
	{
		$race = $this->raceRepository->delete($id);
		return $this->sendSuccess($race, 204);
	}

	/**
	 * Showing active races
	 *
	 * @return void
	 */
	public function actives()
	{
		$races = $this->raceRepository->findBy('status', 'ongoing', ['horses']);
		foreach($races as $race){
			$race['lastFiveResult'] = $this->raceService->getLastFiveResults();
		}

		return $this->sendSuccess($races);
	}

	/**
	 * Tirgger progress process and return uppdated data
	 *
	 * @return void
	 */
	public function progress()
	{
		$races = $this->actives()->getData()->data;
		$this->raceService->getLastFiveResults();
		foreach ($races as $race){
			$this->raceRepository->update($race->id,['current_time' => $race->current_time + 10]);
			$this->raceService->runToHorses($race);
		}

		return $this->actives();
	}
}