<?php

use Phalcon\Mvc\Model;

class News extends Model
{
    public $id;
    public $title;
    public $content;
    public $userId;
    public $views;
    public $createdAt;
    public $updatedAt;
    public $isDeleted;

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
}
