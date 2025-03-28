<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Movie;
use App\Models\Genre;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;

class MovieApiTest extends TestCase
{
    use RefreshDatabase; // Ensures a fresh database for each test

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(); // Create a test user
    }

    #[Test]
    public function authenticated_user_can_fetch_all_movies()
    {
        Sanctum::actingAs($this->user);

        $movies = Movie::factory(3)->create()->each(function ($movie) {
            $genres = Genre::factory(2)->create();
            $movie->genres()->attach($genres->pluck('id'));
        });

        $response = $this->getJson('/api/v1/movies');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         '*' => [
                             'id', 'title', 'director', 'release_date', 'created_at', 'updated_at',
                             'genres' => [
                                 '*' => ['id', 'name', 'created_at', 'updated_at']
                             ]
                         ]
                     ]
                 ]);
    }

    #[Test]
    public function unauthenticated_user_cannot_fetch_movies()
    {
        $response = $this->getJson('/api/v1/movies');

        $response->assertStatus(401) // Unauthorized
                 ->assertJson(['message' => 'Unauthenticated.']);
    }

    #[Test]
    public function authenticated_user_can_create_a_movie_with_genres()
    {
        Sanctum::actingAs($this->user);

        $genres = Genre::factory(2)->create();

        $data = [
            'title' => 'Inception',
            'director' => 'Christopher Nolan',
            'release_date' => '2010-07-16',
            'genres' => $genres->pluck('name')
        ];

        $response = $this->postJson('/api/v1/movies', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Movie created successfully.',
                 ]);

        $this->assertDatabaseHas('movies', ['title' => 'Inception']);

        // Check if the genres were attached correctly
        $movie = Movie::where('title', 'Inception')->first();
        $this->assertCount(2, $movie->genres);
    }

    #[Test]
    public function unauthenticated_user_cannot_create_a_movie()
    {
        $genres = Genre::factory(2)->create();

        $data = [
            'title' => 'Inception',
            'director' => 'Christopher Nolan',
            'release_date' => '2010-07-16',
            'genres' => $genres->pluck('name')->toArray()
        ];

        $response = $this->postJson('/api/v1/movies', $data);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Unauthenticated.']);
    }

    #[Test]
    public function authenticated_user_can_update_a_movie()
    {
        Sanctum::actingAs($this->user);

        $movie = Movie::factory()->create(['title' => 'Old Title']);
        $newGenres = Genre::factory(3)->create();

        $updatedData = [
            'title' => 'New Title',
            'director' => 'New Director',
            'release_date' => '2023-10-01',
            'genres' => $newGenres->pluck('name')->toArray()
        ];

        $response = $this->putJson("/api/v1/movies/{$movie->id}", $updatedData);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Movie updated successfully.',
                 ]);

        $this->assertDatabaseHas('movies', ['title' => 'New Title', 'director' => 'New Director', 'release_date' => '2023-10-01']);
        $this->assertCount(3, $movie->fresh()->genres); // Check if the genres were updated correctly
    }

    #[Test]
    public function unauthenticated_user_cannot_update_a_movie()
    {
        $movie = Movie::factory()->create();

        $response = $this->putJson("/api/v1/movies/{$movie->id}", ['title' => 'New Title']);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Unauthenticated.']);
    }

    #[Test]
    public function authenticated_user_can_delete_a_movie()
    {
        Sanctum::actingAs($this->user);

        $movie = Movie::factory()->create();

        $response = $this->deleteJson("/api/v1/movies/{$movie->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Movie deleted successfully'
                 ]);

        $this->assertDatabaseMissing('movies', ['id' => $movie->id]);
    }

    #[Test]
    public function unauthenticated_user_cannot_delete_a_movie()
    {
        $movie = Movie::factory()->create();

        $response = $this->deleteJson("/api/v1/movies/{$movie->id}");

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Unauthenticated.']);
    }
}
