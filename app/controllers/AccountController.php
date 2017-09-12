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
                $user = Users::findFirst(
                    [
                        'conditions' => 'email = ?0',
                        'bind' => [
                            $this->request->getPost('email')
                        ]
                    ]
                );

                if ($user) {
                    if ($this->security->checkHash($this->request->getPost('password'), $user->password)) {
                        $this->loginUser($user);
                        $this->redirect('/');
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
     * Logouts the current user if there is any
     */
    public function logoutAction()
    {
        $this->session->destroy();
        $this->redirect('/');
    }

    /**
     * Displays the register view, if post and the data fulfill the rules registers a new user
     */
    public function registerAction()
    {
        if ($this->session->has('user')) {
            $this->redirect('/');
            return;
        }
        Tag::prependTitle('Register');

        if ($this->request->isPost()) {
            $user = new Users();
            $user->assign($this->request->getPost());
            
            $validator = new RegisterValidation();
            $errorMessages = $validator->validate($this->request->getPost());
            if (!count($errorMessages)) {
                $ConfirmUser = Users::findFirst(
                    [
                        'conditions' => 'email = ?0',
                        'bind' => [
                            $this->request->getPost('email')
                        ]
                    ]
                );

                if (!$ConfirmUser) {
                    if ($user->save()) {
                        $this->loginUser($user);
                        $this->redirect('/');
                    }
                } else {
                    $errorMessages->appendMessage(new Message('This email address is not available.'));
                }
            }
            
            $this->view->name = $user->name;
            $this->view->lastName = $user->lastName;
            $this->view->email = $user->email;
            $this->view->errorMessages = $errorMessages;
        }
    }

    /**
     * Saves a user in the session
     *
     * @param Users $user
     * @return void
     */
    private function loginUser(Users $user)
    {
        $this->session->set(
            'user',
            [
                'id' => $user->id,
                'name' => $user->name,
                'lastName' => $user->lastName,
                'email' => $user->email
            ]
        );
    }
}
