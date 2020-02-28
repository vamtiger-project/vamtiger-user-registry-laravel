<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

use App\UserRegistry;

class UserRegistryController extends Controller
{
    const responseMessages = [
        'addedNewUser' => 'added new user',
        'updatedUser' => 'updated user',
        'deletedUser' => 'deleted user',
        'retrievedUsers' => 'retrieved user(s)',
        'retrievedUsersFailed' => 'retrieved user(s) failed',
        'noUserData' => 'no user data'
    ];

    public function addNewUser() {
        try {
            $userData = $this->getNewUserData();
            $user = UserRegistry::create($userData);
            $responseData = self::getResponseData(
                self::response['successful'],
                self::responseMessages['addedNewUser'],
                $user->getSummaryData()
            );
        } catch(QueryException | ValidationException $error) {
            $responseData = self::getErrorResponseData($error);

            if (!$responseData) {
                throw $error;
            }
        }

        return $responseData;
    }

    public function updateUser($id) {
        $userData = $this->getUpdatedUserData();

        if (count($userData)) {
            $update = UserRegistry::where('id', $id)->update($userData);

            $responseData = self::getResponseData(
                self::response['successful'],
                self::responseMessages['updatedUser'],
                [
                    'user' => $id,
                    'update' => $update
                ]
            );
        } else {
            throw new Exception(self::responseMessages['noUserData']);
        }

        return $responseData;
    }

    public function deleteUser($id) {
        $delete = UserRegistry::where('id', $id)->delete();
        $responseData = self::getResponseData(
            self::response['successful'],
            self::responseMessages['deletedUser'],
            [
                'user' => $id,
                'delete' => $delete
            ]
        );

        return $responseData;
    }

    public function getUser($id) {
        $user = UserRegistry::find($id);
        $result = $user ? self::response['successful']
            : self::response['failed'];
        $message = $user ? self::responseMessages['retrievedUsers']
            : self::responseMessages['retrievedUsersFailed'];
        $responseData = self::getResponseData(
            $result,
            $message,
            [
                'user' => $user ? $user : (int)$id
            ]
        );

        return $responseData;
    }

    public function getUsers() {
        $users = UserRegistry::paginate(10);
        $responseData = self::getResponseData(
            self::response['successful'],
            self::responseMessages['retrievedUsers'],
            [
                'users' => $users
            ]
        );

        return $responseData;
    }

    private function getNewUserData() {
        $noNumbers = self::fieldValidation['noNumbers'];
        $data = request()->validate([
            'name' => "required|$noNumbers",
            'surname' => "required|$noNumbers",
            'email' => 'required|email',
            'position' => 'required|string'
        ]);

        return $data;
    }

    private function getUpdatedUserData() {
        $noNumbers = self::fieldValidation['noNumbers'];
        $data = request()->validate([
            'name' => $noNumbers,
            'surname' => $noNumbers,
            'email' => "email",
            'position' => 'string'
        ]);

        return $data;
    }
}
