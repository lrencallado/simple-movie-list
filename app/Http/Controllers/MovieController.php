<?php

namespace App\Http\Controllers;

use App\Http\Requests\Movie\CreateRequest;
use App\Http\Requests\Movie\UpdateRequest;
use App\Http\Resources\MovieResource;
use App\Models\Movie;
use App\Repositories\MovieRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MovieController extends Controller
{
    public function __construct(protected MovieRepository $movieRepository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->has('per_page') ? (int) $request->query('per_page') : 10;
        $movies = $this->movieRepository->getAllMovies($request->search, $perPage);

        return Inertia::render('movie/Index', [
            'movies' => MovieResource::collection($movies),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request)
    {
        $this->movieRepository->createMovie($request->validated());
        return redirect()->back()->with('success', 'Movie added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Movie $movie)
    {
        $this->movieRepository->updateMovie($movie, $request->validated());

        return redirect()->back()->with('success', 'Movie updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie)
    {
        $this->movieRepository->delete($movie);
        return redirect()->back()->with('success', 'Movie deleted successfully.');
    }
}
