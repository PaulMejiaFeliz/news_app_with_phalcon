<?php

use Newsapp\Models\Validations\CommentValidation;
use Newsapp\Models\Validations\EditCommentValidation;

/**
 * Class with the comments CRUD
 */
class CommentsController extends Newsapp\Controllers\BaseController
{
    /**
     * Creates a new comment in a news
     */
    public function addCommentAction()
    {
        if (! $this->confirmSession()) {
            return;
        }

        $this->view->disable();
        if ($this->request->isPost()) {
            $validator = new CommentValidation();
            $errorMessages = $validator->validate($this->request->getPost());
            
            if (count($errorMessages)) {
                $this->response->redirect('/index/notFound');
                return;
            }

            $postId = $this->request->getPost('postId');

            $post = News::findFirst([
                'conditions' => 'id = ?0 AND isDeleted = 0',
                'bind' => [
                    $postId
                ]
            ]);
    
            if (!$post) {
                $this->response->redirect('/index/notFound');
                return;
            }

            $comment = new Comments();

            $comment->userId = $this->session->get('user')['id'];
            $comment->newsId = $postId;
            $comment->content = $this->request->getPost('content');
            $comment->createdAt = date('Y-m-d H:i:s');

            $comment->save();

            $this->response->redirect("/index/postDetails?id={$postId}");
        }
    }

    /**
     * Modifies an existing comment
     */
    public function editCommentAction()
    {
        if (! $this->confirmSession()) {
            return;
        }

        $this->view->disable();
        if ($this->request->isPost()) {
            $validator = new EditCommentValidation();
            $errorMessages = $validator->validate($this->request->getPost());
            
            if (count($errorMessages)) {
                $this->response->redirect('/index/notFound');
                return;
            }

            $comment = Comments::findFirst([
                'conditions' => 'id = ?0 AND isDeleted = 0 AND userId = ?1',
                'bind' => [
                    $this->request->getPost('commentId'),
                    $this->session->get('user')['id']
                ]
            ]);
    
            if (!$comment) {
                $this->response->redirect('/index/notFound');
                return;
            }

            $comment->content = $this->request->getPost('content');
            $comment->updatedAt = date('Y-m-d H:i:s');

            $comment->save();

            $this->response->redirect("/index/postDetails?id={$comment->newsId}");
        }
    }

    /**
     * Softly deletes a comment
     */
    public function deleteCommentAction()
    {
        if (! $this->confirmSession()) {
            return;
        }

        $this->view->disable();
        if ($this->request->hasPost('commentId')) {
            $comment = Comments::findFirst([
                'conditions' => 'id = ?0 AND isDeleted = 0 AND userId = ?1',
                'bind' => [
                    $this->request->getPost('commentId'),
                    $this->session->get('user')['id']
                ]
            ]);
    
            if (!$comment) {
                $this->response->redirect('/index/notFound');
                return;
            }

            $comment->isDeleted = 1;
            $comment->updatedAt = date('Y-m-d H:i:s');

            $comment->save();

            $this->response->redirect("/index/postDetails?id={$comment->newsId}");
        }
    }
}
