<?php

namespace Newsapp\Models\Validations;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

/**
 * Validates an edit comment form
 */
class EditCommentValidation extends Validation
{
    public function initialize()
    {
        $this->add(
            [
                'commentId',
                'content'
            ],
            new PresenceOf(
                [
                    'message' => [
                        'commentId' => 'Comment no found.',
                        'content' => 'The content is required.'
                    ]
                ]
            )
        );
    }
}
