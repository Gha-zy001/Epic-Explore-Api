<?php

namespace Tests\Feature;

use App\Models\Place;
use App\Models\Quest;
use App\Models\User;
use App\Models\State;
use App\Services\AiRecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class ExplorationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed some basic data
        $state = State::firstOrCreate(['name' => 'Cairo'], ['description' => 'Capital City']);
        Place::create([
            'name' => 'Pyramids', 
            'address' => 'Giza', 
            'description' => 'Great Pyramids of Giza',
            'state_id' => $state->id
        ]);
        Quest::create([
            'title' => 'First Trip',
            'description' => 'Visit your first place',
            'reward_xp' => 100,
            'requirement_type' => 'visits',
            'requirement_count' => 1
        ]);
    }

    public function test_user_can_check_in_to_place()
    {
        $user = User::factory()->create();
        $place = Place::first();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/user/place/check-in', [
            'place_id' => $place->id,
            'latitude' => 30.0,
            'longitude' => 31.0
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Check-in successful! +50 XP awarded.');

        $user->refresh();
        $this->assertEquals(50, $user->exp);
        $this->assertDatabaseHas('visits', [
            'user_id' => $user->id,
            'place_id' => $place->id
        ]);
        $this->assertDatabaseHas('reward_logs', [
            'user_id' => $user->id,
            'points' => 50,
            'reward_type' => 'xp'
        ]);
    }

    public function test_user_can_view_leaderboard()
    {
        $user1 = User::factory()->create(['name' => 'Top User', 'exp' => 1000]);
        $user2 = User::factory()->create(['name' => 'New User', 'exp' => 100]);

        Sanctum::actingAs($user2);

        $response = $this->getJson('/api/user/leaderboard');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['topUsers']])
            ->assertJsonPath('data.topUsers.0.name', 'Top User');
    }

    public function test_user_can_view_home_content()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user/home');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user_summary',
                    'recommendations',
                    'places',
                    'recent_activity'
                ]
            ]);
    }

    public function test_user_can_view_discover_feed()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user/discover');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'popular',
                    'hidden_gems',
                    'nearby'
                ]
            ]);
    }

    public function test_user_can_view_and_accept_quests()
    {
        $user = User::factory()->create();
        $quest = Quest::first();
        Sanctum::actingAs($user);

        // List quests
        $response = $this->getJson('/api/user/quests');
        $response->assertStatus(200)
            ->assertJsonPath('data.available_quests.0.title', $quest->title);

        // Accept quest
        $response = $this->postJson("/api/user/quests/accept/{$quest->id}");
        $response->assertStatus(200)
            ->assertJsonPath('data.message', 'Quest accepted!');

        $this->assertDatabaseHas('user_quests', [
            'user_id' => $user->id,
            'quest_id' => $quest->id,
            'status' => 'active'
        ]);
    }

    public function test_personalized_ai_recommendations()
    {
        $user = User::factory()->create();
        $place = Place::first();
        
        // Mock the AI service
        $this->mock(AiRecommendationService::class, function ($mock) use ($place) {
            $mock->shouldReceive('getRecommendations')->andReturn(collect([$place]));
        });

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user/recommended');

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Personalized AI recommendations');
    }
}
