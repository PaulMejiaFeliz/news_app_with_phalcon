<?php

namespace Newsapp\Models\Validations;

use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;

class LoginValidation extends Validation
{
    public function initialize()
    {
        $this->add(
            'email',
            new PresenceOf(
                [
                    'message' => 'The e-mail is required',
                ]
            )
        );

        $this->add(
            'email',
            new Email(
                [
                    'message' => 'The e-mail is not valid',
                ]
            )
        );

        $this->add(
            'password',
            new PresenceOf(
                [
                    'message' => 'The password is required',
                ]
            )
        );
    }
}
