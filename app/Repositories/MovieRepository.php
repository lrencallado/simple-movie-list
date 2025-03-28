<?php

namespace App\Repositories;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MovieRepository extends BaseRepository
{
    public function __construct(Movie $movie)
    {
        parent::__construct($movie);
    }

    /**
     * Get all movies with optional search and pagination.
     *
     * @param string|null $search
     * @param integer|null $perPage
     * @return LengthAwarePaginator|Collection
     */
    public function getAllMovies(string $search = null, ?int $perPage = null): LengthAwarePaginator|Collection
    {
        if ($search) {
            return $this->model->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('director', 'like', "%{$search}%")
                      ->orWhereHas('genres', function ($genreQuery) use ($search) {
                          $genreQuery->where('name', 'like', "%{$search}%");
                      });
            })->with(['genres'])->paginate($perPage);
        }
        return $this->getAll($perPage, ['genres']);
    }

    /**
     * Assign genres to a movie. Creates genres if they do not exist.
     *
     * @param Movie $movie
     * @param array $genreNames
     * @return void
     */
    public function assignGenres(Movie $movie, array $genreNames): void
    {
        $genreIds = collect($genreNames)->map(function ($name) {
            return Genre::firstOrCreate(['name' => $name])->id;
        })->toArray();

        $movie->genres()->sync($genreIds);
    }

    /**
     * Update a movie and assign genres.
     *
     * @param Movie $movie
     * @param array $data
     * @return Movie
     */
    public function updateMovie(Movie $movie, array $data): Movie
    {
        $this->update($movie, $data);
        $this->assignGenres($movie, $data['genres'] ?? []);
        return $movie;
    }

    /**
     * Create a new movie and assign genres.
     *
     * @param array $data
     * @return Movie
     */
    public function createMovie(array $data): Movie
    {
        $movie = $this->create($data);
        $this->assignGenres($movie, $data['genres'] ?? []);
        return $movie;
    }
}
