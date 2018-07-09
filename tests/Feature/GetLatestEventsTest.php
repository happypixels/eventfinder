<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetLatestEventsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function get_latest_events()
    {
        $olderEvents = factory(Event::class, 15)->create(['created_at' => now()->subDays(15)]);
        $latestEvents = factory(Event::class, 15)->create(['created_at' => now()->subDays(5)]);

        $response = $this->getJson('/api/events/latest')
            ->assertSuccessful()
            ->assertJsonFragment([
                'slug' => $latestEvents[0]->slug,
                'slug' => $latestEvents[9]->slug,
            ])
            ->assertJsonMissing([
                'slug' => $latestEvents[10]->slug,
                'slug' => $olderEvents[0]->slug,
            ]);

        $this->assertEquals(10, count($response->getData()->events));
    }
}
