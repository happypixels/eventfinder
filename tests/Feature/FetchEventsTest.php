<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FetchEventsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_event_can_be_fetched_using_the_api()
    {
        $event = factory(Event::class)->create();

        $this->getJson('/api/events/' . $event->slug)->assertJsonFragment(['id' => $event->id]);
    }
}
