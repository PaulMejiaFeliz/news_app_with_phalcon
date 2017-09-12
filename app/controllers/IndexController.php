<?php

use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Phalcon\Tag;

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
        $this->confirmSession();
        Tag::prependTitle('My News');
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

        $conditions = 'isDeleted = 0';
        $i = 0;
        foreach ($filter as $key => $value) {
            $conditions .= " AND {$key} LIKE ?" . $i++;
            $filter[$key] = "%{$value}%";
        }

        $news =  News::find(
            [
                'conditions' => $conditions,
                'bind' => array_values($filter),
                'order' => $order
            ]
        );

        $paginator = new PaginatorModel(
            [
                'data'  => $news,
                'limit' => 10,
                'page'  => $currentPage,
            ]
        );
        $this->view->searchFields = $this->filterFields;
        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Saves a new post in the database
     */
    public function addPostAction()
    {
        $this->confirmSession();
        Tag::prependTitle('New Post');
        $this->view->title = '';
        $this->view->content = '';

        if ($this->request->isPost()) {
            $news = new News();
            $news->assign($this->request->getPost());
            $news->userId = $this->session->get('user')['id'];
            $news->createdAt = date('Y-m-d H:i:s');

            if ($news->save()) {
                $this->redirect("index/postDetails?id={$news->id}");
                return;
            }
            
            $this->view->title = $news->title;
            $this->view->content = $news->content;
            $this->view->errorMessages = $news->getMessages();
        }
    }

    /**
     * Updates the given information of an existing post
     */
    public function editPostAction()
    {
        $this->confirmSession();
        Tag::prependTitle('Edit Post');

        if (!$this->request->hasQuery('id')) {
            $this->redirect('/index/notFound');
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
            $this->redirect('/index/notFound');
            return;
        }
    
        if ($this->request->isPost()) {
            $post->assign($this->request->getPost());
            $post->updatedAt = date('Y-m-d H:i:s');

            if ($post->save()) {
                $this->redirect("index/postDetails?id={$post->id}");
                return;
            }
                
            $this->view->errorMessages = $post->getMessages();
        }
        
        $this->view->id = $id;
        $this->view->title = $post->title;
        $this->view->content = $post->content;
    }

    /**
     * Displays a view with the details of a news
     */
    public function postDetailsAction()
    {
        if (!$this->request->hasQuery('id')) {
            $this->redirect('/index/notFound');
            return;
        }

        $post = News::findFirst([
            'conditions' => 'id = ?0 AND isDeleted = 0',
            'bind' => [
                $this->request->getQuery('id')
            ]
        ]);

        if (!$post) {
            $this->redirect('/index/notFound');
            return;
        }

        $post->views++;
        $post->save();

        $currentPage = $this->request->getQuery('page', 'int');
        
        $paginator = new PaginatorModel(
            [
                'data'  => $post->getComments(
                    [
                        'isDeleted = 0'
                    ]
                ),
                'limit' => 3,
                'page'  => $currentPage,
            ]
        );

        Tag::prependTitle($post->title);
        $this->view->post = $post;
        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Softly deletes a post
     */
    public function deletePostAction()
    {
        $this->confirmSession();

        if (!$this->request->hasPost('PostId')) {
            $this->redirect('/index/notFound');
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
            $this->redirect('/index/notFound');
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
