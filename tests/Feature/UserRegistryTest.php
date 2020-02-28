<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory as FakerFactory;
use Tests\TestCase;

use App\UserRegistry;
use App\Http\Controllers\UserRegistryController;

class UserRegistryTest extends TestCase
{
    use RefreshDatabase;

    const url = [
        'add-new-user' => '/api/add-new-user',
        'update-user' => '/api/update-user',
        'delete-user' => '/api/delete-user',
        'get-user' => '/api/get-user',
        'get-users' => '/api/get-users'
    ];

    /** @test **/
    public function a_new_user_can_be_added() {
        $userData = self::getNewUserData();
        $response = $this->post(self::url['add-new-user'], $userData);
        $responseData = json_decode($response->getContent());
        $newUser = UserRegistry::first();

        $this->assertCount(1, UserRegistry::all());
        $this->assertEquals(UserRegistryController::response['successful'], $responseData->result);
    }

    /** @test */
    public function a_user_can_be_updated() {
        $userData = self::getNewUserData();
        $userUpdateData = [
            'name' => "New {$userData['name']}",
            'email' => $userData['email']
        ];
        $response = $this->post(self::url['add-new-user'], $userData);
        $responseData = json_decode($response->getContent());
        $url = self::url['update-user'] . '/' . $responseData->data->user;
        $updateResponse = $this->patch($url, $userUpdateData);
        $updateResponseData = json_decode($updateResponse->getContent());

        $this->assertCount(1, UserRegistry::all());
        $this->assertEquals($userUpdateData['name'], UserRegistry::first()->name);
    }

    /** @test */
    public function a_user_can_be_deleted() {
        $userData = self::getNewUserData();
        $response = $this->post(self::url['add-new-user'], $userData);
        $responseData = json_decode($response->getContent());
        $url = self::url['delete-user'] . '/' . $responseData->data->user;
        $deleteResponse = $this->delete($url);

        $this->assertCount(0, UserRegistry::all());
    }

    /** @test */
    public function a_user_can_be_retrieved() {
        $userData = self::getNewUserData();
        $response = $this->post(self::url['add-new-user'], $userData);
        $responseData = json_decode($response->getContent());
        $url = self::url['get-user'] . '/' . $responseData->data->user;
        $getUserResponse = $this->get($url);
        $responseData = json_decode($getUserResponse->getContent());

        $this->assertEquals($userData['name'], $responseData->data->user->name);
    }

    /** @test */
    public function all_users_can_be_retrieved() {
        $userCount = 1;
        $userDataArray = self::getNewUserDataArray($userCount);
        $addUsersResponses = array_map(
            fn($userData) => $this->post(self::url['add-new-user'], $userData),
            $userDataArray
        );
        $getUsersResponse = $this->get(self::url['get-users']);
        $responseData = json_decode($getUsersResponse->getContent());

        $this->assertEquals($userCount, $responseData->data->users->total);
    }

    /** @test **/
    public function handle_invalid_user_data_format() {
        $userData = array_merge(
            self::getNewUserData(),
            [
                'name' => 'n1me',
                'surname' => '2surname',
                'email' => 'jou.ma.se.meow'
            ]
        );
        $response = $this->post(self::url['add-new-user'], $userData);
        $responseData = json_decode($response->getContent());
        $newUser = UserRegistry::first();

        $this->assertCount(0, UserRegistry::all());
    }

    /** @test **/
    public function handle_missing_user_data() {
        $userData = [];
        $response = $this->post(self::url['add-new-user'], $userData);
        $responseData = json_decode($response->getContent());
        $newUser = UserRegistry::first();

        $this->assertCount(0, UserRegistry::all());
        $this->assertEquals(UserRegistryController::response['failed'],  $responseData->result);
        $this->assertEquals(UserRegistryController::errorMessage['failedToAddNewUser'],  $responseData->message);
    }

    /** @test **/
    public function handle_unique_constraint_failed() {
        $newUser = self::getNewUserData();
        $newUsers = [
            $newUser,
            $newUser
        ];
        $responses = array_map(
            fn($userData) => $this->post(self::url['add-new-user'], $userData),
            $newUsers
        );
        $response = end($responses);
        $responseData = json_decode($response->getContent());

        $this->assertCount(1, UserRegistry::all());
        $this->assertEquals(UserRegistryController::response['failed'],  $responseData->result);
        $this->assertEquals(UserRegistryController::errorMessage['failedToAddNewUser'],  $responseData->message);
    }

    public static function getNewUserData() {
        $faker = FakerFactory::create();
        $newUser = [
            'name' => $faker->firstName(),
            'surname' => $faker->lastName(),
            'email' => $faker->email(),
            'position' => $faker->jobTitle()
        ];

        return $newUser;
    }

    public static function getNewUserDataArray($count = 2) {
        $newUsers = [];

        for ($currentCount = 1; $currentCount <= $count; ++ $currentCount) {
            array_push($newUsers, self::getNewUserData());
        }

        return $newUsers;
    }
}
