<?php

use Phalcon\Validation\Message;
use Phalcon\Tag;
use Newsapp\Models\Validations\LoginValidation;
use Newsapp\Models\Validations\RegisterValidation;

/**
 * Class used to manage accounts
 */
class AccountController extends Newsapp\Controllers\BaseController
{
    /**
     * Displays the login view, if post and the credentials are right, lets the user login
     */
    public function loginAction()
    {
        if ($this->session->has('user')) {
            $this->view->disable();
            $this->response->redirect('/');
            return;
        }

        Tag::prependTitle('Login');
        

        if ($this->request->isPost()) {
            $validator = new LoginValidation();
            $errorMessages = $validator->validate($this->request->getPost());
            if (!count($errorMessages)) {
                $user = Users::query()
                    ->where('email = :email:')
                    ->bind(['email' => $this->request->getPost('email') ])
                    ->execute()
                    ->getFirst();
                if ($user) {
                    if ($this->security->checkHash($this->request->getPost('password'), $user->password)) {
                        $this->session->set(
                            'user',
                            [
                                'id' => $user->id,
                                'name' => $user->name,
                                'lastName' => $user->lastName,
                                'email' => $user->email
                            ]
                        );

                        $this->view->disable();
                        $this->response->redirect('/');
                        return;
                    } else {
                        $errorMessages->appendMessage(new Message('The passwords do not match.'));
                    }
                } else {
                    $errorMessages->appendMessage(new Message('This email address is not registred.'));
                }
            }
            $this->view->email = $this->request->getPost('email');
            $this->view->errorMessages = $errorMessages;
        }
    }

    /**
     * Logouts the current user if where is any
     */
    public function logoutAction()
    {
        $this->session->destroy();
        $this->view->disable();
        $this->response->redirect('/');
    }

    /**
     * Displays the register view, if post and the data fulfill the rules registers a new user
     */
    public function registerAction()
    {
        if ($this->session->has('user')) {
            $this->view->disable();
            $this->response->redirect('/');
            return;
        }
        Tag::prependTitle('Register');

        if ($this->request->isPost()) {
            $validator = new RegisterValidation();
            $errorMessages = $validator->validate($this->request->getPost());
            if (!count($errorMessages)) {
                $user = Users::query()
                    ->where('email = :email:')
                    ->bind(['email' => $this->request->getPost('email') ])
                    ->execute()
                    ->getFirst();
                if (!$user) {
                    $user = new Users();
                    $user->name = $this->request->getPost('name');
                    $user->lastName = $this->request->getPost('lastName');
                    $user->email = $this->request->getPost('email');
                    $user->password =  $this->security->hash($this->request->getPost('password'));
                    $user->save();

                    $this->session->set(
                        'user',
                        [
                            'id' => $user->id,
                            'name' => $user->name,
                            'lastName' => $user->lastName,
                            'email' => $user->email
                        ]
                    );

                    $this->view->disable();
                    $this->response->redirect('/');
                    return;
                } else {
                    $errorMessages->appendMessage(new Message('This email address is not available.'));
                }
            }
            $this->view->name = $this->request->getPost('name');
            $this->view->lastName = $this->request->getPost('lastName');
            $this->view->email = $this->request->getPost('email');
            $this->view->errorMessages = $errorMessages;
        }
    }
}
