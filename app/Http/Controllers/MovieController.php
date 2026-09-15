<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function fetchTmdb(Request $request)
    {
        $request->validate([
            'tmdb_id' => 'required|string'
        ]);

        $tmdbId = $request->tmdb_id;
        $apiKey = env('TMDB_API_KEY');

        if (!$apiKey) {
            return response()->json(['message' => 'TMDB API key is not configured.'], 500);
        }

        $response = \Illuminate\Support\Facades\Http::get("https://api.themoviedb.org/3/movie/{$tmdbId}", [
            'api_key' => $apiKey,
            'language' => 'vi-VN' // or 'en-US'
        ]);

        if ($response->failed()) {
            return response()->json(['message' => 'Failed to fetch data from TMDB', 'error' => $response->json()], $response->status());
        }

        $data = $response->json();

        // Optional: you can also fetch credits to get director
        $creditsResponse = \Illuminate\Support\Facades\Http::get("https://api.themoviedb.org/3/movie/{$tmdbId}/credits", [
            'api_key' => $apiKey
        ]);

        $director = 'Unknown';
        if ($creditsResponse->successful()) {
            $crew = $creditsResponse->json()['crew'] ?? [];
            foreach ($crew as $member) {
                if ($member['job'] === 'Director') {
                    $director = $member['name'];
                    break;
                }
            }
        }

        $mappedData = [
            'tmdb_id' => $tmdbId,
            'title' => $data['title'] ?? $data['original_title'],
            'director' => $director,
            'poster_url' => isset($data['poster_path']) ? "https://image.tmdb.org/t/p/w500" . $data['poster_path'] : null,
            'teaser_url' => null, // Typically TMDB videos endpoint is needed for trailers
            'duration_minutes' => $data['runtime'] ?? 0,
            'status' => 'coming_soon',
            'raw_data' => $data // optional for review
        ];

        return response()->json([
            'message' => 'TMDB data fetched successfully.',
            'data' => $mappedData
        ]);
    }

    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Movie $movie)
    {
        //
    }

    public function update(Request $request, Movie $movie)
    {
        //
    }

    public function destroy(Movie $movie)
    {
        //
    }
}
