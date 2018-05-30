<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Event;
use Mockery;
use App\Agents\Agent;
use App\Jobs\DownloadEvents;
use App\Models\Venue;

class DownloadEventsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_stores_the_events_properly()
    {
        $events = factory(Event::class, 2)->make();
        $agent  = Mockery::mock(Agent::class);

        // The correct method chain is being run. Yes, it's an implementation detail but
        // it assures that any implementation of an agent will be correctly run.
        $this->assertRunsCorrectMethods($agent, $events);

        $this->assertEquals(0, Event::count());
        (new DownloadEvents($agent))->handle();

        // The events were properly stored.
        $this->assertEquals(2, Event::count());

        // Test that the various attributes are stored properly.
        $firstEvent = Event::first();
        $this->assertEquals(
            $events->first()->only('title', 'description', 'url', 'agent_event_id'),
            $firstEvent->only('title', 'description', 'url', 'agent_event_id')
        );
        $this->assertEquals($events->first()->event_starts_at->format('Y-m-d H:i:s'), $firstEvent->event_starts_at);
        $this->assertEquals($events->first()->sale_starts_at->format('Y-m-d H:i:s'), $firstEvent->sale_starts_at);

        $lastEvent = Event::skip(1)->first();
        $this->assertEquals(
            $events->last()->only('title', 'description', 'url', 'agent_event_id'),
            $lastEvent->only('title', 'description', 'url', 'agent_event_id')
        );
        $this->assertEquals($events->last()->event_starts_at->format('Y-m-d H:i:s'), $lastEvent->event_starts_at);
        $this->assertEquals($events->last()->sale_starts_at->format('Y-m-d H:i:s'), $lastEvent->sale_starts_at);
    }

    /** @test */
    public function it_uses_existing_venue_if_match_is_found()
    {
        $venue  = factory(Venue::class)->create();
        $events = factory(Event::class, 2)->make(['venue_id' => $venue->id]);
        $agent  = Mockery::mock(Agent::class);

        $this->assertEquals(1, Venue::count());

        // The correct method chain is being run. Yes, it's an implementation detail but
        // it assures that any implementation of an agent will be correctly run.
        $this->assertRunsCorrectMethods($agent, $events, $venue->toArray());

        (new DownloadEvents($agent))->handle();

        $this->assertEquals(1, Venue::count());
    }

    /** @test */
    public function it_creates_new_venue_if_no_match_is_found()
    {
        $venue     = factory(Venue::class)->create();
        $venueData = factory(Venue::class)->make(['title' => 'New venue']);
        $events    = factory(Event::class, 2)->make(['venue_id' => $venue->id]);
        $agent     = Mockery::mock(Agent::class);

        $this->assertEquals(1, Venue::count());

        // The correct method chain is being run. Yes, it's an implementation detail but
        // it assures that any implementation of an agent will be correctly run.
        $this->assertRunsCorrectMethods($agent, $events, $venueData->toArray());

        (new DownloadEvents($agent))->handle();

        $this->assertEquals(2, Venue::count());
    }

    /**
     * This assures any agent that implements the required functionality should work.
     */
    public function assertRunsCorrectMethods($agent, $events, $venue = null)
    {
        $venue = ($venue) ?: factory(Venue::class)->make()->toArray();

        $agent->shouldReceive('gatherEvents')->once()->andReturn($events->toArray());
        $agent->shouldReceive('mapEvent')->once()->andReturn($events->first()->toArray());
        $agent->shouldReceive('mapEvent')->once()->andReturn($events->last()->toArray());
        $agent->shouldReceive('mapPrices')->twice();
        $agent->shouldReceive('downloadAndMapImage')->twice();
        $agent->shouldReceive('mapVenue')->twice()->andReturn($venue);
        $agent->shouldReceive('fullyQualifiedClassName')->twice()->andReturn('App\Agents\Agent');
        $agent->shouldReceive('cleanup')->once();
    }
}
