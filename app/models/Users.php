<?php

use Phalcon\Mvc\Model;

class Users extends Model
{
    public $id;
    protected $name;
    protected $lastName;
    public $email;
    public $password;

    public function setName(string $name)
    {
        $this->name = trim(htmlspecialchars($name));
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setLastName(string $lastName)
    {
        $this->lastName = trim(htmlspecialchars($lastName));
    }

    public function getLastName() : string
    {
        return $this->lastName;
    }

    public function initialize()
    {
        $this->setSource('users');

        $this->hasMany('id', 'News', 'userId');
        $this->hasMany('id', 'Comments', 'userId');
    }
}
