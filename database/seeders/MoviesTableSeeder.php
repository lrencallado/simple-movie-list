<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Movie;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MoviesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define genres
        $genres = [
            'Action', 'Sci-Fi', 'Adventure', 'Drama', 'Thriller', 'Romance', 'War', 'Heist'
        ];

        // Insert genres into database
        $genreIds = [];
        foreach ($genres as $genre) {
            $genreIds[$genre] = Genre::firstOrCreate(['name' => $genre])->id;
        }

        // Movies with release dates
        $movies = [
            ['title' => 'Inception', 'director' => 'Christopher Nolan', 'release_date' => '2010-07-16', 'genres' => ['Sci-Fi', 'Thriller']],
            ['title' => 'Titanic', 'director' => 'James Cameron', 'release_date' => '1997-12-19', 'genres' => ['Drama', 'Romance']],
            ['title' => 'Interstellar', 'director' => 'Christopher Nolan', 'release_date' => '2014-11-07', 'genres' => ['Sci-Fi', 'Drama']],
            ['title' => 'Passengers', 'director' => 'Morten Tyldum', 'release_date' => '2016-12-21', 'genres' => ['Sci-Fi', 'Romance']],
            ['title' => '13 Hours: The Secret Soldiers of Benghazi', 'director' => 'Michael Bay', 'release_date' => '2016-01-15', 'genres' => ['Action', 'War']],
            ['title' => "Ocean's Eleven", 'director' => 'Steven Soderbergh', 'release_date' => '2001-12-07', 'genres' => ['Heist', 'Thriller']],

            // Marvel Movies
            ['title' => 'Captain America: The First Avenger', 'director' => 'Joe Johnston', 'release_date' => '2011-07-22', 'genres' => ['Action', 'Adventure']],
            ['title' => 'Captain America: The Winter Soldier', 'director' => 'Anthony Russo, Joe Russo', 'release_date' => '2014-04-04', 'genres' => ['Action', 'Thriller']],
            ['title' => 'Captain America: Civil War', 'director' => 'Anthony Russo, Joe Russo', 'release_date' => '2016-05-06', 'genres' => ['Action', 'Adventure']],
            ['title' => 'The Avengers', 'director' => 'Joss Whedon', 'release_date' => '2012-05-04', 'genres' => ['Action', 'Adventure']],
            ['title' => 'Avengers: Age of Ultron', 'director' => 'Joss Whedon', 'release_date' => '2015-05-01', 'genres' => ['Action', 'Sci-Fi']],
            ['title' => 'Avengers: Infinity War', 'director' => 'Anthony Russo, Joe Russo', 'release_date' => '2018-04-27', 'genres' => ['Action', 'Sci-Fi']],
            ['title' => 'Avengers: Endgame', 'director' => 'Anthony Russo, Joe Russo', 'release_date' => '2019-04-26', 'genres' => ['Action', 'Sci-Fi']],

            // Spider-Man Movies (Marvel & Sony)
            ['title' => 'Spider-Man', 'director' => 'Sam Raimi', 'release_date' => '2002-05-03', 'genres' => ['Action', 'Adventure']],
            ['title' => 'Spider-Man 2', 'director' => 'Sam Raimi', 'release_date' => '2004-06-30', 'genres' => ['Action', 'Adventure']],
            ['title' => 'Spider-Man 3', 'director' => 'Sam Raimi', 'release_date' => '2007-05-04', 'genres' => ['Action', 'Adventure']],
            ['title' => 'The Amazing Spider-Man', 'director' => 'Marc Webb', 'release_date' => '2012-07-03', 'genres' => ['Action', 'Adventure']],
            ['title' => 'The Amazing Spider-Man 2', 'director' => 'Marc Webb', 'release_date' => '2014-05-02', 'genres' => ['Action', 'Adventure']],
            ['title' => 'Spider-Man: Homecoming', 'director' => 'Jon Watts', 'release_date' => '2017-07-07', 'genres' => ['Action', 'Adventure']],
            ['title' => 'Spider-Man: Far From Home', 'director' => 'Jon Watts', 'release_date' => '2019-07-02', 'genres' => ['Action', 'Adventure']],
            ['title' => 'Spider-Man: No Way Home', 'director' => 'Jon Watts', 'release_date' => '2021-12-17', 'genres' => ['Action', 'Adventure']],
        ];

        // Insert movies into database
        foreach ($movies as $movieData) {
            $movie = Movie::create([
                'title' => $movieData['title'],
                'director' => $movieData['director'],
                'release_date' => Carbon::parse($movieData['release_date']),
            ]);

            // Attach genres to movie
            $genreIdsToAttach = array_map(fn($g) => $genreIds[$g] ?? null, $movieData['genres']);
            $movie->genres()->attach(array_filter($genreIdsToAttach));
        }
    }
}
