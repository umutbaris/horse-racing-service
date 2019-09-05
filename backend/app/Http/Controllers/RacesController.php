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
		$this->raceService = $raceService;
	}

	public function index()
	{
		$races = $this->raceRepository->all();

		return $this->sendSuccess($races);
	}

	public function show(int $id)
	{
		$race = $this->raceRepository->find($id);

		return $this->sendRaceInfo($race);
	}

	/**
	 * Checking active race count and determine horses
	 *
	 * @param CreateraceRequest $request
	 * @return message
	 */
	public function create(CreateraceRequest $request)
	{
		$this->raceService = new RaceService($this->raceRepository, $this->horseRepository);
		if ( 3 > $this->checkActiveRaceCount() ){
			$request['current_time'] = 0;
			$request['completed_horse_count'] = 0;
			$request['status'] = "ongoing";
			$request['best_time'] = $this->raceRepository->getBestTime();
			$race = $this->raceRepository->store($request->all());

			$freeHorses = $this->horseRepository->findby('status', 'free');
			$this->raceService = new RaceService($this->raceRepository, $this->horseRepository);
			$horses = $this->raceService->getHorsesRandomly($freeHorses);
			
			$racingHorses = $this->horseRepository->find($horses);
			$race->horses()->attach($racingHorses);

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
	 * Creating race informations
	 *
	 * @param array $data
	 * @param integer $statusCode
	 * @return json
	 */
	public function sendRaceInfo($race = [], $statusCode = 200)
	{
		return response()->json([
			'success' => true,
			'data' => $race,
			'best_ever_time' => $this->raceRepository->getBestTime(),
			'horses' => $race->horses
		], $statusCode);
	}

	/**
	 * Showing active races
	 *
	 * @return void
	 */
	public function actives()
	{
		$races = $this->raceRepository->findBy('status', 'ongoing', ['horses']);
		return $this->sendSuccess($races);
	}

	/**
	 * Showing active races on progress endpoint
	 *
	 * @return void
	 */
	public function progress()
	{
		$this->raceService = new RaceService($this->raceRepository, $this->horseRepository);
		$races = $this->actives()->getData()->data;
		foreach ($races as $race){
			$this->raceRepository->update($race->id,['current_time' => $race->current_time + 10]);
			$this->raceService->runToHorses($race);
		}
		return $this->actives();
	}

	/**
	 * Checking active race count
	 *
	 * @return integer
	 */
	public function checkActiveRaceCount(): int
	{
		return count($races = $this->actives()->getData()->data);
	}

}