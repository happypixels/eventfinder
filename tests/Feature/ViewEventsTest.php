<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewEventsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_event_can_be_visited()
    {
        $event = factory(Event::class)->create();

        $response = $this->get('events/' . $event->slug);

        $this->assertTrue($response->data('event')->is($event));
    }
}
