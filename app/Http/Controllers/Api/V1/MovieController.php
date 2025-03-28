<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Movie\CreateRequest;
use App\Http\Requests\Movie\UpdateRequest;
use App\Repositories\MovieRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function __construct(protected MovieRepository $movieRepository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            return ApiResponse::success($this->movieRepository->getAllMovies());
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to fetch movies.', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $movie = $this->movieRepository->findById($id);
            if (!$movie) {
                return response()->json(['message' => 'Movie not found'], 404);
            }
            return ApiResponse::success($movie);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to fetch movie.', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request): JsonResponse
    {
        try {
            $movie = $this->movieRepository->createMovie($request->validated());
            return ApiResponse::success($movie, 'Movie created successfully.', 201);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to create movie.', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $id): JsonResponse
    {
        try {
            $movie = $this->movieRepository->findById($id);
            if (!$movie) {
                return response()->json(['message' => 'Movie not found'], 404);
            }
            $movie = $this->movieRepository->updateMovie($movie, $request->validated());
            return ApiResponse::success($movie, 'Movie updated successfully.');
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to update movie.', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $movie = $this->movieRepository->findById($id);
            if (!$movie) {
                return ApiResponse::error('Movie not found', 404);
            }
            $this->movieRepository->delete($movie);
            return ApiResponse::success(null, 'Movie deleted successfully');
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to delete movie.', 500);
        }
    }
}
