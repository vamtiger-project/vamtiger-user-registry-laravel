<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public const response = [
        'successful' => 'successful',
        'failed' => 'failed',
        'error' => 'error'
    ];

    public const errorMessage = [
        'failedToAddNewUser' => 'failed to add new user',
        'unhandledError' => 'unhandled error'
    ];

    protected const errorDescription = [
        'UNIQUE constraint failed' => 'field must be unique',
        'format is invalid' => 'field cannot contain numbers',
        'field is required' => 'field cannot be empty'
    ];

    protected const prefix = [
        'fieldName' => 'user_registries'
    ];

    protected const regex = [
        'digit' => '/\d/',
        'noDigits' => '/^\D+$/',
        'nonDigitField' => '/^(?:surname|name)$/i'
    ];

    protected const fieldValidation = [
        'noNumbers' => 'regex:' . self::regex['noDigits']
    ];

    protected const fieldRegex = [
        'uniqueContraintFailed' => "/^
            (?<error>
                unique [\w\s]+
            )
            :\s
            (?<field>
                .+
            )
        /ix",
        'fieldIsRequired' => "/^
            the\s
            (?<field>
                [\S]+
            )
            \s
            (?<error>
                [\w\s]+
            )
        /ix"
    ];

    protected function getErrorResponseData($error) {
        $errors = self::getErrors($error);
        $errorData = self::getFieldErrorData($errors);
        $errorMessage = count($errorData) ? self::errorMessage['failedToAddNewUser'] : false;
        $errorResponseData = false;

        if ($errorMessage) {
            $errorResponseData = self::getResponseData(
                self::response['failed'],
                $errorMessage,
                $errorData
            );
        }

        return $errorResponseData;
    }

    protected static function getResponseData($result, $message = false, $currentData = false) {
        $data = [
            'result' => $result
        ];

        if ($message) {
            $data['message'] = $message;
        }

        if ($currentData) {
            $data['data'] = $currentData;
        }

        return $data;
    }

    private static function getErrors($error) {
        $errors = [];

        if ($error instanceof QueryException) {
            forEach($error->errorInfo as $currentError) {
                array_push($errors, $currentError);
            }
        } else if ($error instanceof ValidationException) {
            forEach($error->errors() as $currentErrors) {
                forEach($currentErrors as $currentError) {
                    array_push($errors, $currentError);
                }
            }
        }

        return $errors;
    }

    private static function getFieldErrorData($errors) {
        $prefix = self::prefix['fieldName'];
        $fieldErrorData = [];
        $match;
        $fieldName;
        $fieldError;

        forEach(self::fieldRegex as $regex) {
            forEach($errors as $error) {
                preg_match($regex, $error, $match);

                if ($match) {
                    $fieldName = strpos($match['field'], $prefix) !== false
                        ? $match['field']
                        : "{$prefix}.{$match['field']}";

                    $fieldError = [
                        'error' => $match['error']
                    ];

                    if ((isset(self::errorDescription[$match['error']])
                    && preg_match(self::regex['nonDigitField'], $match['field']))
                    || isset(self::errorDescription[$match['error']])) {
                        $fieldError['errorDescription'] = self::errorDescription[$match['error']];
                    }

                    $fieldErrorData[$fieldName] = isset($fieldErrorData[$fieldName])
                        ? array_merge($fieldErrorData[$fieldName], arry($fieldError))
                        : array($fieldError);
                }
            };
        }

        return $fieldErrorData;
    }
}
