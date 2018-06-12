<?php

use App\Models\Event;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(Venue::class, 20)->create();
        factory(Event::class, 20)->create();
    }
}
