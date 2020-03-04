<?php

/**
 * Tries to delete the specified post
 *
 * @param PDO $pdo
 * @param integer $post_id
 * @return bool
 * @throws Exception
 */
function deletePost(PDO $pdo, $post_id)
{
    $sql = "
        DELETE FROM
            post
        WHERE
            id = :id
    ";
    $stmt = $pdo->prepare($sql);
    if ($stmt === false) {
        throw new Exception("There was a problem preparing this query");
    }

    $result = $stmt->execute(
        array(
            'id' => $post_id,
        )
    );

    return $result !== false;
}
