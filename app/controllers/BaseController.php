<?php

namespace Newsapp\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Tag;

class BaseController extends Controller
{
    protected $customTags;
    
    public function initialize()
    {
        $this->customTags = $this->di->get('customTags');
        Tag::setTitle(' - News App');
    }

    protected function confirmSession() : bool
    {
        if (! $this->session->has('user')) {
            $this->view->disable();
            $this->response->redirect('account/login');
            return false;
        }
        return true;
    }
}
