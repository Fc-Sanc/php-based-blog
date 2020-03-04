<?php

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
    $stmt = $pdo->prepare('SELECT title, created_at, body FROM post WHERE id = :id');

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