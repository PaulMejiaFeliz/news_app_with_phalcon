<?php

namespace Newsapp\Models\Validations;

use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\StringLength;

class RegisterValidation extends Validation
{
    public function initialize()
    {
        $this->add(
            [
                'name',
                'lastName',
                'email',
                'password'
            ],
            new PresenceOf(
                [
                    'message' => [
                        'name' => 'The name is required.',
                        'lastname' => 'The lastname is required.',
                        'email' => 'The e-mail is required.',
                        'pasword' => 'The password is required.'
                    ]
                    
                ]
            )
        );

        $this->add(
            'email',
            new Email(
                [
                    'message' => 'The e-mail is not valid.',
                ]
            )
        );

        $this->add(
            'password',
            new Confirmation(
                [
                    'message' => 'Password does not match confirmation.',
                    'with'    => 'confirmPassword',
                ]
            )
        );

        $this->add(
            [
                'name',
                'lastName',
                'email',
                'password'
            ],
            new StringLength(
                [
                    'max' => [
                        'name' => 100,
                        'lastName' => 100,
                        'email' => 30,
                        'password' => 100
                    ],
                    'min' => [
                        'password' => 5
                    ],
                    'messageMaximum' => [
                        'name' => 'The name is too long, 100 characters maximun.',
                        'lastName' => 'The lastname is too long, 100 characters maximun.',
                        'email' => 'The e-mail is too long, 30 characters maximun.',
                        'password' => 'The password is too long, 100 characters maximun.'
                    ],
                    'messageMinimum' => [
                        'password' => 'The password is too short, 5 characters minimun.'
                    ]
                ]
            )
        );
    }
}
