<?php

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\StringLength;

class News extends Model
{
    public $id;
    protected $title;
    protected $content;
    public $userId;
    public $views;
    public $createdAt;
    public $updatedAt;
    public $isDeleted;

    public function setTitle(string $title)
    {
        $this->title = trim(htmlspecialchars($title));
    }

    public function getTitle() : string
    {
        return $this->title;
    }

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
        $this->setSource('news');
        
        $this->belongsTo('userId', 'Users', 'id');
        $this->hasMany('id', 'Comments', 'newsId');
    }

    public function columnMap()
    {
        return [
            'id' => 'id',
            'title' => 'title',
            'content' => 'content',
            'user_id' => 'userId',
            'views' => 'views',
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

        $validator->add(
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
        return $this->validate($validator);
    }
}
