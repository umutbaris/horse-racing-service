<?php

namespace App\Http\Controllers;
use App\Http\Requests\CreateRaceRequest;
use App\Repositories\RaceRepository;
use App\Modeks\Races;
use Illuminate\Http\Request;

class RacesController extends Controller
{
	protected $raceRepository;
	
		public function __construct(raceRepository $raceRepository)
		{
			$this->raceRepository = $raceRepository;
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
}
