<?php

use Faker\Generator as Faker;

$factory->define(App\Question::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'a' => $faker->word,
        'b' => $faker->word,
        'c' => $faker->word,
        'd' => $faker->word,
        'correct' => 0,
    ];
});
