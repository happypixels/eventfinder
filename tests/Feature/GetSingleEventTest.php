<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetSingleEventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_event_can_be_fetched_using_the_api()
    {
        $event = factory(Event::class)->create();

        $this->getJson('/api/events/' . $event->slug)->assertStatus(200)
            ->assertJsonFragment([
                'id' => $event->id,
                'slug' => $event->slug,
                'url' => $event->url
            ]);
    }

    /** @test */
    public function not_found_status_code_when_event_does_not_exist()
    {
        $this->getJson('/api/events/slug-that-doesnt-exist')->assertStatus(404);
    }
}
