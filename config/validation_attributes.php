<?php
return [
    'email'          => 'email|max:128',
    'email_verified' => 'boolean',
    'password'       => 'password',
    'sub'            => 'integer|between:1,4294967294',
];
