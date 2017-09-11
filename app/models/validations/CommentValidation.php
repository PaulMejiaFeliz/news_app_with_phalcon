<?php

namespace Newsapp\Models\Validations;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

class CommentValidation extends Validation
{
    public function initialize()
    {
        $this->add(
            [
                'postId',
                'content'
            ],
            new PresenceOf(
                [
                    'message' => [
                        'postId' => 'No post found.',
                        'content' => 'The content is required.'
                    ]
                ]
            )
        );
    }
}
