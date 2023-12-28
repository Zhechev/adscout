<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Player;
use App\Models\User;
use App\Models\Team;

class PlayerApiTest extends TestCase
{
    public function test_it_returns_all_players()
    {
        $user = User::factory()->create();
        $player = Player::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/players');

        $response->assertOk()
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => ['id', 'name', 'position', 'age', 'nationality', 'goals_season', 'team', 'created_at', 'updated_at']
                     ],
                     'total',
                     'current_page'
                 ]);
    }

    public function test_it_returns_details_of_a_player()
    {
        $user = User::factory()->create();

        Team::factory()->create();

        $player = Player::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/players/{$player->id}");

        $response->assertOk()
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $player->id,
                         'name' => $player->name,
                         'position' => $player->position,
                         'age' => $player->age,
                         'nationality' => $player->nationality,
                         'goals_season' => $player->goals_season,
                         'team' => $player->team->name,
                         'created_at' => $player->created_at->format('d-m-Y H:i:s'),
                         'updated_at' => $player->updated_at->format('d-m-Y H:i:s'),
                     ]
                 ]);
    }


    public function test_it_creates_a_new_player()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        $playerData = [
            'name' => 'New Player',
            'position' => 'Forward',
            'age' => 25,
            'nationality' => 'Spanish',
            'goals_season' => 10,
            'team_id' => $team->id,
        ];

        $response = $this->actingAs($user)->postJson('/api/players', $playerData);

        $response->assertCreated();
        $this->assertDatabaseHas('players', ['name' => 'New Player']);
    }

    public function test_it_updates_a_player()
    {
        $user = User::factory()->create();
        $player = Player::factory()->create();
        $updatedData = ['name' => 'Updated Name', 'age' => 26];

        $response = $this->actingAs($user)->putJson("/api/players/{$player->id}", $updatedData);

        $response->assertOk();
        $this->assertDatabaseHas('players', ['id' => $player->id, 'name' => 'Updated Name']);
    }

    public function test_it_deletes_a_player()
    {
        $user = User::factory()->create();
        $player = Player::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/players/{$player->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('players', ['id' => $player->id]);
    }

    public function test_it_fails_to_create_a_player_with_invalid_data()
    {
        $user = User::factory()->create();

        $invalidData = ['name' => ''];

        $response = $this->actingAs($user)->postJson('/api/players', $invalidData);

        $response->assertStatus(422);
    }

    public function test_it_returns_not_found_for_non_existing_player()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/players/999');

        $response->assertNotFound();
    }

    public function test_it_returns_paginated_players()
    {
        $user = User::factory()->create();
        Player::factory(10)->create();

        $response = $this->actingAs($user)->getJson('/api/players?page=2');

        $response->assertOk()
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => ['id', 'name', 'position', 'age', 'nationality', 'goals_season', 'team', 'created_at', 'updated_at']
                     ],
                     'total',
                     'current_page'
                 ]);
    }


    public function test_it_filters_players_by_position()
    {
        $user = User::factory()->create();
        $position = 'Forward';
        Player::factory()->count(5)->create(['position' => $position]);

        $response = $this->actingAs($user)->getJson('/api/players?position=' . $position);

        $response->assertOk();

        $responseData = $response->json('data');
        foreach ($responseData as $playerData) {
            $this->assertEquals($position, $playerData['position']);
        }
    }

    public function test_it_fails_to_update_a_player_with_invalid_data()
    {
        $user = User::factory()->create();
        $player = Player::factory()->create();
        $invalidData = ['age' => 'invalid'];

        $response = $this->actingAs($user)->putJson("/api/players/{$player->id}", $invalidData);

        $response->assertStatus(422);
    }

    public function test_it_returns_player_with_team_info()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['name' => 'Test Team']);
        $player = Player::factory()->create(['team_id' => $team->id]);

        $response = $this->actingAs($user)->getJson("/api/players/{$player->id}");

        $response->assertOk()
                ->assertJson([
                    'data' => [
                        'id' => $player->id,
                        'name' => $player->name,
                        'position' => $player->position,
                        'age' => $player->age,
                        'nationality' => $player->nationality,
                        'goals_season' => $player->goals_season,
                        'team' => $player->team->name,
                        'created_at' => $player->created_at->format('d-m-Y H:i:s'),
                        'updated_at' => $player->updated_at->format('d-m-Y H:i:s'),
                    ]
                ]);
    }

    public function test_it_filters_players_by_name()
    {
        $user = User::factory()->create();
        $playerName = 'Common Name';
        Player::factory()->count(3)->create(['name' => $playerName]);

        $response = $this->actingAs($user)->getJson("/api/players?name=$playerName");

        $response->assertOk();

        $responseData = $response->json('data');
        foreach ($responseData as $playerData) {
            $this->assertEquals($playerName, $playerData['name']);
        }
    }


    public function test_it_filters_players_by_team()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        Player::factory()->create(['team_id' => $team->id]);

        $response = $this->actingAs($user)->getJson("/api/players?team_id={$team->id}");

        $response->assertOk()
                ->assertJsonFragment([
                    'team' => $team->name
                ]);
    }

    public function test_it_filters_players_by_position_and_team()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $position = 'Forward';
        Player::factory()->create(['team_id' => $team->id, 'position' => $position]);

        $response = $this->actingAs($user)->getJson("/api/players?position=$position&team_id={$team->id}");

        $response->assertOk()
                ->assertJsonFragment([
                    'position' => $position,
                    'team' => $team->name
                ]);
    }

    public function test_it_fails_to_update_non_existing_player()
    {
        $user = User::factory()->create();
        $updatedData = ['name' => 'Updated Name', 'age' => 30];

        $response = $this->actingAs($user)->putJson("/api/players/999", $updatedData);

        $response->assertNotFound();
    }

    public function test_it_fails_to_delete_non_existing_player()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/players/999");

        $response->assertNotFound();
    }

    public function test_it_returns_empty_list_when_no_players_exist()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/players");

        $response->assertOk()
                ->assertJson([
                    'success' => true,
                    'data' => []
                ]);
    }

    public function test_it_validates_data_when_creating_a_player()
    {
        $user = User::factory()->create();
        $invalidData = [
            'name' => '',
            'position' => 'Invalid Position',
            'age' => -1,
            'nationality' => '',
            'goals_season' => 'ten',
            'team_id' => 999,
        ];

        $response = $this->actingAs($user)->postJson('/api/players', $invalidData);

        $response->assertStatus(422);
    }

    public function test_it_returns_detailed_errors()
    {
        $user = User::factory()->create();
        $invalidData = ['name' => ''];

        $response = $this->actingAs($user)->postJson('/api/players', $invalidData);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'errors' => [
                        'name'
                    ]
                ]);
    }

    public function test_it_returns_correct_data_after_creating_a_player()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $playerData = [
            'name' => 'New Player',
            'position' => 'Forward',
            'age' => 25,
            'nationality' => 'Spanish',
            'goals_season' => 10,
            'team_id' => $team->id,
        ];

        $response = $this->actingAs($user)->postJson('/api/players', $playerData);

        $response->assertCreated()
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'New Player',
                         'position' => 'Forward',
                         'age' => 25,
                         'nationality' => 'Spanish',
                         'goals_season' => 10,
                         'team' => $team->name,
                     ],
                     'message' => 'Player successfully created'
                 ]);

        $this->assertDatabaseHas('players', [
            'name' => 'New Player',
            'position' => 'Forward',
            'age' => 25,
            'nationality' => 'Spanish',
            'goals_season' => 10,
            'team_id' => $team->id
        ]);
    }

}
