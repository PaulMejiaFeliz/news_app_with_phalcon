<?php

namespace Newsapp\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Tag;
use Newsapp\Exceptions\AccessDeniedException;

class BaseController extends Controller
{
    protected $customTags;
    
    public function initialize()
    {
        $this->customTags = $this->di->get('customTags');
        Tag::setTitle(' - News App');
    }

    /**
     * Checks if there's a user logged
     *
     * @return void
     * @throws Newsapp\Exceptions\AccessDeniedException if there isn't a logged user
     */
    protected function confirmSession()
    {
        if (! $this->session->has('user')) {
            throw new AccessDeniedException();
        }
    }

    /**
     * Redirects to the given URL
     *
     * @param string $url
     * @return void
     */
    protected function redirect(string $url)
    {
        $this->view->disable();
        $this->response->redirect($url);
    }
}
