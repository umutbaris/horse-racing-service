<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateHorseRequest;
use App\Models\Horses;
use App\Repositories\HorseRepository;
use Illuminate\Http\Request;

class HorseController extends Controller
{
	protected $horseRepository;

	public function __construct(horseRepository $horseRepository)
	{
		$this->horseRepository = $horseRepository;
	}

	public function index()
	{
		$horse = $this->horseRepository->all();

		return $this->sendSuccess($horse);
	}

	public function show(int $id)
	{
		$horse = $this->horseRepository->find($id);

		return $this->sendSuccess($horse);
	}

	public function create(CreateHorseRequest $request)
	{
		$horse = $this->horseRepository->store($request->all());

		return $this->sendSuccess($horse, 201);
	}

	public function update(int $id, CreateHorseRequest $request)
	{
		$horse = $this->horseRepository->update($id, $request->all());

		$logProperties = [
			'redirection_id' => $id,
			'updated_data' => $request->all()
		];


		return $this->sendSuccess($horse);
	}

	public function destroy(int $id)
	{
		$customRedirection = $this->horseRepository->delete($id);

		$logProperties = [
			'redirection_id' => $id
		];


		return $this->sendSuccess($customRedirection, 204);
	}
}
