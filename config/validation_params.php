<?php
return [
    'account' => [
        'emailUpdateApply' => [
            'sub'   => 'required|',
            'email' => 'required|',
        ],
        'emailUpdateComplete' => [
            'sub'                => 'required|',
            'email_verify_token' => 'required|',
        ],
    ],
    'register' => [
        'apply' => [
            'email'           => 'required|',
            'occupation_code' => '',
        ],
        'find' => [
            'register_token' => 'required|',
        ],
        'complete' => [
            'register_token'   => 'required|',
            'password'         => 'required|',
            'given_name'       => 'required|',
            'family_name'      => 'required|',
            'given_name_kana'  => '',
            'family_name_kana' => '',
            'gender'           => 'required|',
            'birthdate'        => '',
            'country'          => 'required|',
            'region'           => 'required|',
            'occupation_code'  => 'required|',
            'graduation_year'  => 'required|',
            'is_login_auto'    => '',
        ],
    ],
    'login' => [
        'password' => [
            'authentication_email'    => 'required|',
            'authentication_password' => 'required|',
            'is_login_auto'           => '',
        ],
    ],
    'user' => [
        'find' => [
            'sub' => 'required|',
        ],
    ],
    'test' => [
        'dbDuplicateEntry' => [
            'email' => '',
        ],
        'databaseQueryException' => [
            'email' => '',
        ],
        'unexpectedError' => [
            'email' => '',
        ],
    ],
];
