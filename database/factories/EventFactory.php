<?php

use App\Models\Venue;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\Event::class, function (Faker $faker) {
    return [
        'venue_id'              => function () {
            return factory(Venue::class)->create()->id;
        },
        'agent_class'                        => 'App\Agents\Agent',
        'agent_event_id'                     => uniqid(),
        'title'                              => $faker->sentence,
        'description'                        => $faker->paragraph,
        'url'                                => $faker->url,
        'image'                              => null,
        'image_url'                          => null,
        'min_price'                          => $faker->randomFloat(3, 10, 1000),
        'max_price'                          => $faker->randomFloat(3, 10, 1000),
        'is_cancelled'                       => 0,
        'is_sold_out'                        => 0,
        'event_starts_at'                    => $faker->dateTime,
        'sale_starts_at'                     => $faker->dateTime,
        'sale_ends_at'                       => $faker->dateTime,
    ];
});
