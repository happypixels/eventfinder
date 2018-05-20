<?php

namespace Tests\Agents;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Event;
use App\Agents\Eventim;
use Illuminate\Support\Facades\Storage;

class EventimTestIntegration extends TestCase
{
    use RefreshDatabase;

    protected $events;

    /** @test */
    public function the_agent_is_correctly_configured()
    {
        $event = factory(Event::class)->create(['agent_class' => 'App\Agents\Eventim']);

        $this->assertNotNull($event->agent);
        $this->assertEquals('Eventim', $event->agent->name);
        $this->assertEquals('eventim', $event->agent->identifier);
        $this->assertEquals(config('agents.eventim.website'), $event->agent->website);
        $this->assertEquals(config('agents.eventim.trackback'), $event->agent->trackback());
    }

    /** @test */
    public function it_gathers_events()
    {
        $agent  = new Eventim;

        // This makes the gathered events available in the rest of the test class without making additional
        // calls to the API.
        $events = &$this->sharedEvents();
        $events = $agent->gatherEvents();

        $this->assertTrue(is_array($events));
        $this->assertTrue(count($events) > 0);
        $this->assertNotNull($events[0]->eventid);
    }

    /** @test */
    public function it_maps_events()
    {
        $events      = $this->events();
        $agent       = new Eventim;
        $mappedEvent = $agent->mapEvent($events[0]);

        $this->assertEquals(
            ['agent_class', 'agent_event_id', 'title', 'description', 'url', 'event_starts_at', 'is_cancelled', 'is_sold_out', 'sale_starts_at', 'sale_ends_at'],
            array_keys($mappedEvent)
        );
        $this->assertEquals($agent->fullyQualifiedClassName(), $mappedEvent['agent_class']);
        $this->assertEquals($events[0]->eventid, $mappedEvent['agent_event_id']);
        $this->assertEquals($events[0]->eventname, $mappedEvent['title']);
        $this->assertEquals($events[0]->estext, $mappedEvent['description']);
        $this->assertEquals($events[0]->eventlink, $mappedEvent['url']);
    }

    /** @test */
    public function it_maps_venues()
    {
        $events      = $this->events();
        $mappedVenue = (new Eventim)->mapVenue($events[0]);

        $this->assertEquals(
            ['title', 'city', 'address', 'zipcode', 'latitude', 'longitude'],
            array_keys($mappedVenue)
        );

        $this->assertEquals($events[0]->eventvenue, $mappedVenue['title']);
        $this->assertEquals($events[0]->eventstreet, $mappedVenue['address']);
        $this->assertEquals($events[0]->eventzip, $mappedVenue['zipcode']);
        $this->assertEquals($events[0]->eventplace, $mappedVenue['city']);
    }

    /** @test */
    public function it_maps_prices_or_returns_zero()
    {
        $events         = $this->events();
        $amountOfPrices = count($events[0]->pricekategory);

        if ($amountOfPrices > 1) {
            $tmpPrices = [];
            for ($i = 0; $i < $amountOfPrices; $i++) {
                $tmpPrices[] = intval($events[0]->pricekategory[$i]->price);
            }

            $expectedPrices['min_price'] = min($tmpPrices);
            $expectedPrices['max_price'] = max($tmpPrices);
        } else {
            $expectedPrices['min_price'] = $events[0]->pricekategory->price;
            $expectedPrices['max_price'] = $events[0]->pricekategory->price;
        }

        $mappedPrices = (new Eventim)->mapPrices($events[0]);
        $this->assertEquals($expectedPrices, $mappedPrices);
    }

    /** @test */
    public function it_downloads_and_maps_images()
    {
        $events = $this->events();

        $event = null;
        $i     = 0;

        while (!$event) {
            if ($events[$i]->eventlink) {
                $event = $events[$i];
            }

            $i++;
        }

        if (file_exists(storage_path('app/events/eventim/' . $event->eventid))) {
            Storage::deleteDirectory('events/eventim/' . $event->eventid);
        }

        $this->assertFalse(file_exists(storage_path('app/events/eventim/' . $event->eventid)));

        $response  = (new Eventim)->downloadAndMapImage($event);
        $extension = pathinfo($event->imageUrl)['extension'];
        $filename  = str_slug($event->eventname) . '.' . $extension;

        $this->assertTrue(file_exists(storage_path('app/events/eventim/' . $event->eventid . '/' . $filename)));
        $this->assertEquals($event->imageUrl, $response['image_url']);
        $this->assertEquals($filename, $response['image']);
    }

    /** @test */
    public function it_returns_the_correct_config()
    {
        $agent = new Eventim;
        $this->assertEquals(config('agents.eventim.username'), $agent->config('username'));
        $this->assertEquals(config('agents.eventim.password'), $agent->config('password'));
        $this->assertEquals(config('agents.eventim.website'), $agent->config('website'));
    }

    /** @test */
    public function it_cleans_up()
    {
        $this->assertTrue(file_exists(storage_path('app/temp/eventim')));
        (new Eventim)->cleanup();
        $this->assertFalse(file_exists(storage_path('app/temp/eventim')));
    }

    /**
     * This is simply a helper method for using the shared events and throwing an exception if that's not
     * possible.
     */
    private function events()
    {
        $events = &$this->sharedEvents();

        if ($events === null) {
            throw new \Exception('Please run the whole test class to make sure events are gathered.');
        }

        return $events;
    }

    /**
     * This method makes the events list available globally in this test class.
     */
    protected function &sharedEvents()
    {
        static $events = null;

        return $events;
    }
}
