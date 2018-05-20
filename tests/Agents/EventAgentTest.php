<?php

namespace Tests\Agents;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Event;

class EventAgentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_agent_is_available_on_the_event()
    {
        $event = factory(Event::class)->create(['agent_class' => 'App\Agents\BaseAgent']);

        $this->assertNotNull($event->agent);
        $this->assertEquals('BaseAgent', $event->agent->name);
        $this->assertEquals('', $event->agent->website);
        $this->assertEquals('baseagent', $event->agent->identifier);
        $this->assertEquals('', $event->agent->trackback());
    }
}
