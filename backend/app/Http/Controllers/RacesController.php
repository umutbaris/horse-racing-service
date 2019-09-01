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
					array_push($activeRaces, $race);
				}
			}

			return $this->sendSuccess($activeRaces);
		}
	
		public function show(int $id)
		{
			$race = $this->raceRepository->find($id);
	
			return $this->sendRaceInfo($race);
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

		/**
		 * Generate numbers randomly between 1 to max horse number
		 *
		 * @param int $max
		 * @return array
		 */
		public function getHorsesRandomly($max): array
		{
			$random = range(1, $max);
			shuffle($random);
			$randomHorses = array_slice($random ,0, 8);

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

		public function progress()
		{
			$races = $this->actives()->getData()->data;
			foreach ($races as $race){
				$this->raceRepository->update($race->id,['current_time' => $race->current_time + 10]);
			}

			return $this->actives();
		}

}
