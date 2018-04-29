<?php

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

$factory->define(App\Models\Agent::class, function (Faker $faker) {
    return [
        'title'              => $faker->company,
        'website'            => $faker->url,
        'track_url'          => 'default-track-url',
        'logotype'           => null,
        'is_trusted'         => mt_rand(0, 1),
        'is_enabled'         => 1,
    ];
});
