<?php

use Phalcon\Mvc\Model;

class Comments extends Model
{
    public $id;
    public $userId;
    public $newsId;
    public $content;
    public $createdAt;
    public $updatedAt;
    public $isDeleted;

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
}
