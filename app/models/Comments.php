<?php

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;

class Comments extends Model
{
    public $id;
    public $userId;
    public $newsId;
    protected $content;
    public $createdAt;
    public $updatedAt;
    public $isDeleted;

    //Getters and setters
    
    public function setContent(string $content)
    {
        $this->content = trim(htmlspecialchars($content));
    }

    public function getContent() : string
    {
        return $this->content;
    }

    public function initialize()
    {
        $this->setSource('news_comments');
        
        $this->belongsTo('userId', 'Users', 'id');
        $this->belongsTo('newsId', 'News', 'id');
        
    }

    public function columnMap()
    {
        return [
            'id' => 'id',
            'user_id' => 'userId',
            'news_id' => 'newsId',
            'content' => 'content',
            'created_at' => 'createdAt',
            'updated_at' => 'updatedAt',
            'is_deleted' => 'isDeleted'
        ];
    }

    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            [
                'content'
            ],
            new PresenceOf(
                [
                    'message' => 'The content is required.'
                ]
            )
        );

        $validator->add(
            [
                'content'
            ],
            new StringLength(
                [
                    'min' => 5,
                    'messageMinimum' => 'The title is too short, 5 characters minimun.'
                ]
            )
        );
        return $this->validate($validator);
    }
}
