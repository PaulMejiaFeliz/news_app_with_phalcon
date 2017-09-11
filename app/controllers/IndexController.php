<?php

use Phalcon\Validation\Message;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Phalcon\Tag;
use Newsapp\Models\Validations\PostValidation;

class IndexController extends Newsapp\Controllers\BaseController
{
    /**
     * Array of the columns which the news can be filtered
     *
     * @var array
     */
    private $filterFields = [
        'title' => 'Title',
        'views' => 'Views Count',
        'createdAt' => 'Created At',
        'updatedAt' => 'Updated At'
    ];

    /**
     * Array of the columns which the news can be ordered by
     *
     * @var array
     */
    private $oderByFields = [
        'title',
        'user',
        'views',
        'createdAt'
    ];

    /**
     * Displays the home page
     */
    public function indexAction()
    {
        Tag::prependTitle('Home');
        $this->showNews();

    }

    /**
     * Displays a view with a list of news of the current user
     */
    public function myPostsAction()
    {
        Tag::prependTitle('My News');
        if (! $this->confirmSession()) {
            return;
        }
        $this->showNews(['userId' => $this->session->get('user')['id']]);
    }

    /**
     * Displays a view with a list of filtered news
     *
     * @param array $filter
     * @return void
     */
    private function showNews(array $filter = [])
    {
        $order = 'createdAt DESC';
        
        if ($this->request->hasQuery('search') && $this->request->hasQuery('value')) {
            if (array_key_exists($this->request->getQuery('search'), $this->filterFields)) {
                $filter[$this->request->getQuery('search')] = $this->request->getQuery('value');
            }
            
        }
        
        if ($this->request->hasQuery('order')) {
            if (in_array($this->request->getQuery('order'), $this->oderByFields)) {
                $order = $this->request->getQuery('order');
                
                if ($this->request->hasQuery('reverseOrder')) {
                    if ($this->request->getQuery('reverseOrder') == 'true') {
                        $order .= ' DESC';
                    }
                }
            }
        }

        $currentPage = $this->request->getQuery('page', 'int');

        $criteria =  News::query()
            ->where('isDeleted = 0')
            ->orderBy($order);

        foreach ($filter as $key => $value) {
            $criteria->andWhere("{$key} LIKE :{$key}:")
                ->bind([$key => "%{$value}%"]);
        }
        $news = $criteria->execute();

        $paginator = new PaginatorModel(
            [
                'data'  => $news,
                'limit' => 10,
                'page'  => $currentPage,
            ]
        );
        $this->view->searchFields = $this->filterFields;
        $this->view->customTags = $this->customTags;
        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Saves a new post in the database
     */
    public function addPostAction()
    {
        if (! $this->confirmSession()) {
            return;
        }
        Tag::prependTitle('New Post');
        $this->view->title = '';
        $this->view->content = '';

        if ($this->request->isPost()) {
            $validator = new PostValidation();
            $errorMessages = $validator->validate($this->request->getPost());
            if (!count($errorMessages)) {
                $news = new News();
                $news->title = $this->request->getPost('title');
                $news->content = $this->request->getPost('content');
                $news->userId = $this->session->get('user')['id'];
                $news->createdAt = date('Y-m-d H:i:s');

                if ($news->save()) {
                    $this->response->redirect("index/postDetails?id={$news->id}");
                    return;
                }
                
                $errorMessages = $news->getMessages();
            }
            $this->view->title = $this->request->getPost('title');
            $this->view->content = $this->request->getPost('content');
            $this->view->errorMessages = $errorMessages;
        }
    }

    /**
     * Updates the given information of an existing post
     */
    public function editPostAction()
    {
        if (! $this->confirmSession()) {
            return;
        }
        Tag::prependTitle('Edit Post');

        if (!$this->request->hasQuery('id')) {
            $this->view->disable();
            $this->response->redirect('/index/notFound');
            return;
        }
        
        $id = $this->request->getQuery('id');

        $post = News::findFirst([
            'conditions' => 'id = ?0 AND isDeleted = 0 AND userId = ?1',
            'bind' => [
                $id,
                $this->session->get('user')['id']
            ]
        ]);

        if (!$post) {
            $this->view->disable();
            $this->response->redirect('/index/notFound');
            return;
        }

        $title = $post->title;
        $content = $post->content;
    
        if ($this->request->isPost()) {
            $validator = new PostValidation();
            $errorMessages = $validator->validate($this->request->getPost());

            $title = $this->request->getPost('title');
            $content = $this->request->getPost('content');

            if (!count($errorMessages)) {

                $post->title = $title;
                $post->content = $content;
                $post->updatedAt = date('Y-m-d H:i:s');


                if ($post->save()) {
                    $this->view->disable();
                    $this->response->redirect("index/postDetails?id={$post->id}");
                    return;
                }
                
                $errorMessages = $post->getMessages();
            }

            $this->view->errorMessages = $errorMessages;
        }
        
        $this->view->id = $id;
        $this->view->title = $title;
        $this->view->content = $content;
    }

    /**
     * Displays a view with the details of a news
     */
    public function postDetailsAction()
    {
        if (!$this->request->hasQuery('id')) {
            $this->view->disable();
            $this->response->redirect('/index/notFound');
            return;
        }

        $post = News::findFirst([
            'conditions' => 'id = ?0 AND isDeleted = 0',
            'bind' => [
                $this->request->getQuery('id')
            ]
        ]);

        if (!$post) {
            $this->view->disable();
            $this->response->redirect('/index/notFound');
            return;
        }

        $post->views++;
        $post->save();

        $currentPage = $this->request->getQuery('page', 'int');
        
        $paginator = new PaginatorModel(
            [
                'data'  => $post->comments,
                'limit' => 3,
                'page'  => $currentPage,
            ]
        );

        $this->view->title = $post->title;
        $this->view->post = $post;
        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Softly deletes a post
     */
    public function deletePostAction()
    {
        if (! $this->confirmSession()) {
            return;
        }

        if (!$this->request->hasPost('PostId')) {
            $this->view->disable();
            $this->response->redirect('/index/notFound');
            return;
        }

        $id = $this->request->getPost('PostId');

        $post = News::findFirst([
            'conditions' => 'id = ?0 AND isDeleted = 0 AND userId = ?1',
            'bind' => [
                $id,
                $this->session->get('user')['id']
            ]
        ]);
        
        if (!$post) {
            $this->view->disable();
            $this->response->redirect('/index/notFound');
            return;
        }

        $post->isDeleted = 1;
        $post->updatedAt = date('Y-m-d H:i:s');

        $post->save();

        $this->view->title = $post->title;
    }

    /**
     * Displays the 'Not Found' page
     */
    public function notFoundAction()
    {
        Tag::prependTitle('Not Found');
    }
}
