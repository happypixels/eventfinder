<?php

use Illuminate\Database\Seeder;
use App\Models\Agent;
use App\Models\Venue;
use App\Models\Event;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(Agent::class, 20)->create();
        factory(Venue::class, 20)->create();
        factory(Event::class, 20)->create();
    }
}
