<?php

use Phalcon\Mvc\Model;

class Users extends Model
{
    public $id;
    public $name;
    public $lastName;
    public $email;
    public $password;

    public function initialize()
    {
        $this->setSource('users');

        $this->hasMany('id', 'News', 'userId');
        $this->hasMany('id', 'Comments', 'userId');
    }
}
