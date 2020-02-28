<?php

use Illuminate\Database\Seeder;
use App\UserRegistry;

class UserRegistrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(UserRegistry::class, 100)->create();
    }
}
