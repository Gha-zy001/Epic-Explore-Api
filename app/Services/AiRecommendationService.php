<?php

namespace App\Services;

use App\Models\User;
use App\Models\Place;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiRecommendationService
{
    protected string $apiKey;
    protected string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', '');
    }

    /**
     * Get recommendations from AI based on user background.
     */
    public function getRecommendations(User $user)
    {
        if (empty($this->apiKey)) {
            Log::warning("Gemini API key not found. Falling back to default recommendations.");
            return null;
        }

        // 1. Collect user preference context
        $favorites = $user->favorites()->with('favoritable')->limit(5)->get();
        $recentVisits = $user->rewardLogs()->where('reward_type', 'xp')->limit(5)->get();
        
        $context = "User likes " . $favorites->pluck('favoritable.name')->implode(', ') . ". ";
        $context .= "Recent activities: " . $recentVisits->pluck('description')->implode(', ');

        // 2. Collect candidate places
        $places = Place::inRandomOrder()->limit(20)->get(['id', 'name', 'description']);
        $candidates = $places->map(fn($p) => "ID: {$p->id}, Name: {$p->name}")->implode("; ");

        // 3. Construct prompt
        $prompt = "Based on the user's preference context: '{$context}', please pick the top 5 places from this list: '{$candidates}' that would be most interesting to them. Return ONLY the IDs of the chosen places as a comma-separated list of numbers.";

        try {
            $response = Http::post("{$this->apiUrl}?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text');
                // Extract IDs from text
                preg_match_all('/\d+/', $text, $matches);
                $ids = $matches[0] ?? [];
                
                return Place::whereIn('id', $ids)->get();
            }

            Log::error("Gemini API error: " . $response->body());
        } catch (\Exception $e) {
            Log::error("AI Recommendation Exception: " . $e->getMessage());
        }

        return null;
    }
}
