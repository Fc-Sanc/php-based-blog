<?php

function addPost(PDO $pdo, $title, $body, $user_id)
{
    // Prepare the insert query
    $sql = "
        INSERT INTO
            post(title, body, user_id, created_at) 
            VALUES(:title, :body, :user_id, :created_at) 
    ";
    $stmt = $pdo->prepare($sql);
    if ($stmt === false) {
        throw new Exception('Could not prepare post insert query');
    }

    // Now run the query, with these parameters
    $result = $stmt->execute(
        array(
            'title' => $title,
            'body' => $body,
            'user_id' => $user_id,
            'created_at' => getSqlDateForNow(),
        )
    );
    if ($result === false) {
        throw new Exception('Could not run post insert query');
    }

    return $pdo->lastInsertId();
}

function editPost(PDO $pdo, $title, $body, $post_id)
{
    // Prepare the insert query
    $sql = "
        UPDATE post 
        SET 
            title = :title,
            body = :body
        WHERE
            id = :post_id
    ";
    $stmt = $pdo->prepare($sql);
    if ($stmt === false) {
        throw new Exception('Could not prepare post update query');
    }

    //Now run the query, with these parameters
    $result = $stmt->execute(
        array(
            'title' => $title,
            'body' => $body,
            'post_id' => $post_id,
        )
    );
    if ($result === false) {
        throw new Exception('Could not run post update query');
    }

    return true;
}
