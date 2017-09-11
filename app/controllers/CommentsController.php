<?php namespace newsapp\controllers;

use newsapp\core\App;
use newsapp\core\Controller;

/**
 * Class with the comments CRUD
 */
class CommentsController extends Controller
{
    /**
     * Creates a new comment in a news
     */
    public function addCommentAction()
    {
        $this->startSession();
        
        extract($_POST);
        if (isset($_SESSION['logged'])) {
            $user = $_SESSION['user'];

            if (strlen(trim($newId)) == 0) {
                $this->view('notFound', [ 'message' => 'Post not found' ]);
                return;
            } else {
                $post = App::get('qBuilder')->selectById('news', $newId);
                if ($post['is_deleted']) {
                    $this->view('notFound', [ 'message' => 'Post not found' ]);
                    return;
                }
            }
            if (strlen(trim($content)) > 0) {

                App::get('qBuilder')->insert(
                    'news_comments',
                    [
                        'user' => $user['id'],
                        'new' => $newId,
                        'content' => trim($content),
                        'created_at' => date('Y-m-d H:i:s')
                    ]
                );
            }
            header("Location: /postDetails?id={$newId}");
            return;
        }
        header('Location: /login');
    }

    /**
     * Modifies an existing comment
     */
    public function editCommentAction()
    {
        $this->startSession();
        
        if (!isset($_SESSION['logged'])) {
            header('Location: /login');
            return;
        }
        
        if (isset($_POST['commentId'])) {
            $qBuilder = App::get('qBuilder');
            extract($_POST);
            $comment = $qBuilder->selectById('news_comments', $commentId);
            
            if (! $comment['is_deleted']
                && strlen(trim($content)) > 0
                && $comment['user'] == $_SESSION['user']['id']
            ) {
                $qBuilder->update(
                    'news_comments',
                    $comment['id'],
                    [
                        'content' => $content,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                );
            }
            header("Location: /postDetails?id={$comment['new']}");
            return;
        }
        header('Location: /');
    }

    /**
     * Softly deletes a comment
     */
    public function deleteCommentAction()
    {
        $this->startSession();
        if (!$_SESSION['logged']) {
            header('Location: /login');
            return;
        }
        if (isset($_POST['commentId'])) {
            $qBuilder = App::get('qBuilder');

            $comment = $qBuilder->selectById('news_comments', $_POST['commentId']);
            
            if (! $comment['is_deleted']
                && $comment['user'] === $_SESSION['user']['id']
            ) {
                $qBuilder->update(
                    'news_comments',
                    $comment['id'],
                    [
                        'is_deleted' => 1,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                );
            }
            header("Location: /postDetails?id={$comment['new']}");
            return;
        }
        header('Location: /');
    }
}
