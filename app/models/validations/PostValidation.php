<?php

namespace Newsapp\Models\Validations;

use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\StringLength;

class PostValidation extends Validation
{
    public function initialize()
    {
        $this->add(
            [
                'title',
                'content'
            ],
            new PresenceOf(
                [
                    'message' => [
                        'title' => 'The title is required.',
                        'content' => 'The content is required.'
                    ]
                    
                ]
            )
        );

        $this->add(
            [
                'title'
            ],
            new StringLength(
                [
                    'max' => 100,
                    'min' => 5,
                    'messageMaximum' => 'The title is too long, 100 characters maximun.',
                    'messageMinimum' => 'The title is too short, 5 characters minimun.'
                ]
            )
        );
    }
}
