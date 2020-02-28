<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use App\UserRegistry;
use Tests\Feature\UserRegistryTest;

$factory->define(UserRegistry::class, function (Faker $faker) {
    return UserRegistryTest::getNewUserData();
});
