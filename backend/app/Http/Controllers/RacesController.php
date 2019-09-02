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
			if ( 3 > $this->checkActiveRaceCount() ){
				$request['current_time'] = 0;
				$request['status'] = "ongoing";
				$request['best_time'] = $this->raceRepository->getBestTime();
				$race = $this->raceRepository->store($request->all());
	
				$freeHorses = $this->horseRepository->findby('status', 'free');
				$horses = $this->getHorsesRandomly($freeHorses);
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
			$races = $this->raceRepository->all();
			$activeRaces = [];
			foreach ($races as $race) {
				if ($race->status === "ongoing"){

					$race['horses'] = $this->raceRepository->find($race->id)->horses;
					array_push($activeRaces, $race);
				}
			}

			return $this->sendSuccess($activeRaces);
		}

		/**
		 * Showing active races on progress endpoint
		 *
		 * @return void
		 */
		public function progress()
		{
			$races = $this->actives()->getData()->data;
			foreach ($races as $race){
				$this->raceRepository->update($race->id,['current_time' => $race->current_time + 10]);
				$horse = $this->runToHorses($race->horses);
			}

			
			return $this->actives();
		}

		/**
		 * Calculating running meters for each progress and updating distance covered
		 *
		 * @param array horses
		 * @return void
		 */
		public function runToHorses($horses)
		{
			foreach($horses as $horse){
				$horse->distance_covered = $horse->speed * 10 + $horse->distance_covered;
				$this->horseRepository->update($horse->id,['distance_covered' => $horse->distance_covered]);
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
			array_multisort(array_column($horses, 'distance_covered'), SORT_DESC, $horses);

			foreach($horses as $key=>$horse){
				$this->horseRepository->update($horse->id,['position' => $key+1]);
			}
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
