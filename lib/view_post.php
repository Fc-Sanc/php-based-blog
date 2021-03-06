<?php

/**
 * Called to handle the comment form, redirects upon success
 *
 * @param PDO $pdo
 * @param $post_id
 * @param array $comment_data
 * @return array
 * @throws Exception
 */
function handleAddComment(PDO $pdo, $post_id, array $comment_data)
{
    $errors = addCommentToPost($pdo, $post_id, $comment_data);

    // If there are no errors, redirect back to self and redisplay
    if (!$errors) {
        redirectAndExit('view_post.php?post_id=' . $post_id);
    }

    return $errors;
}

/**
 * @param PDO $pdo
 * @param integer $post_id
 * @param array $delete_response
 */
function handleDeleteComment(PDO $pdo, $post_id, array $delete_response)
{
    if (isLoggedIn()) {
        $keys = array_keys($delete_response);
        $delete_comment_id = $keys[0];
        if ($delete_comment_id) {
            deleteComment($pdo, $post_id, $delete_comment_id);
        }

        redirectAndExit('view_post.php?post_id=' . $post_id);
    }
}

/**
 * Delete the specified comment on the specified post
 *
 * @param PDO $pdo
 * @param $post_id
 * @param $comment_id
 * @return bool
 * @throws Exception
 */
function deleteComment(PDO $pdo, $post_id, $comment_id)
{
    // The comment id on its own would suffice, but post_id is a nice extra safety check
    $sql = "
        DELETE FROM
            comment
            WHERE 
                post_id = :post_id,
                AND id = :comment_id
    ";
    $stmt = $pdo->prepare($sql);
    if ($stmt === false) {
        throw new Exception('There was a problem preparing this query');
    }

    $result = $stmt->execute(
        array(
            'post_id' => $post_id,
            'comment_id' => $comment_id,
        )
    );

    return $result !== false;
}

/**
 * Retrieves a single post
 *
 * @param PDO $pdo
 * @param integer $post_id
 * @return mixed
 * @throws Exception
 */
function getPostRow(PDO $pdo, $post_id)
{
    $stmt = $pdo->prepare(
        'SELECT title, created_at, body,
            (SELECT COUNT(*) FROM comment WHERE comment.post_id = post.id) comment_count 
        FROM post WHERE id = :id');

    if ($stmt === false) {
        throw new Exception('There was a problem preparing this query');
    }

    $result = $stmt->execute(array('id' => $post_id,));
    if ($result === false) {
        throw new Exception('There was a problem running this query');
    }

    // Let's get a row
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row;
}

/**
 * Writes a comment to a particular post
 *
 * @param PDO $pdo
 * @param integer $post_id
 * @param array $comment_data
 * @return array
 * @throws Exception
 */
function addCommentToPost(PDO $pdo, $post_id, array $comment_data)
{
    $errors = array();

    // Do some validation
    if (empty($comment_data['name'])) {
        $errors['name'] = 'A name is required';
    }
    if (empty($comment_data['text'])) {
        $errors['text'] = 'A comment is required';
    }

    // If we are error free, try writing the comment
    if (!$errors) {
        $sql = "
            INSERT INTO
            comment(name, website, text, created_at, post_id)
            VALUES (:name, :website, :text, :created_at, :post_id)
        ";
        $stmt = $pdo->prepare($sql);
        if ($stmt === false) {
            throw new Exception('Cannot prepare statement to insert comment');
        }

        $result = $stmt->execute(
            array_merge($comment_data,
                array(
                    'created_at' => getSqlDateForNow(),
                    'post_id' => $post_id,))
        );

        if ($result === false) {
            // @todo This renders a database-level message to the user, fix this
            $error_info = $stmt->errorInfo();
            if ($error_info) {
                $errors[] = $error_info[2];
            }
        }
    }
    return $errors;
}