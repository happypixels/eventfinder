<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Venue;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetSingleVenueTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function get_a_single_venue_by_slug()
    {
        $venue = factory(Venue::class)->create();

        $response = $this->getJson('/api/venue/' . $venue->slug)
            ->assertSuccessful()
            ->assertJsonFragment($venue->toArray());

        $this->assertEquals(1, count($response->getData()->venue));
    }

    /** @test */
    public function non_existant_venue_returns_404()
    {
        $this->get('api/venue/slug-that-doesnt-exist')->assertStatus(404);
    }
}
