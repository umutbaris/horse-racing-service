<?php

namespace App\Http\Controllers;
use App\Http\Requests\CreateRaceRequest;
use App\Repositories\RaceRepository;
use App\Repositories\HorseRepository;
use App\Models\Races;
use App\Models\Horses;
use Illuminate\Http\Request;

class RacesController extends Controller
{
	protected $raceRepository;
	
		public function __construct(raceRepository $raceRepository, HorseRepository $horseRepository)
		{
			$this->raceRepository = $raceRepository;
			$this->horseRepository = $horseRepository;
		}
	
		public function index()
		{
			$race = $this->raceRepository->all();
	
			return $this->sendSuccess($race);
		}
	
		public function show(int $id)
		{
			$race = $this->raceRepository->find($id);
	
			return $this->sendSuccess($race);
		}
	
		public function create(CreateraceRequest $request)
		{
			$race = $this->raceRepository->store($request->all());
			
			$maxHorseNumber = $this->horseRepository->getLastId();
			$horses = $this->getHorsesRandomly($maxHorseNumber);
			$racingHorses = $this->horseRepository->find($horses);
			$race->horses()->attach($racingHorses);

			return $this->sendSuccess($race, 201);
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

		public function getHorsesRandomly($max)
		{
			$random = range(1, $max);
			shuffle($random);
			$randomHorses = array_slice($random ,0, 8);

			return $randomHorses;
		}
}
