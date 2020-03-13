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
    $sqls = array(
        // Delete comments first, to remove the foreign key objection
        "DELETE FROM
            comment
        WHERE
            post_id = :id",
        // Now we can delete the post
        "DELETE FROM
            post
        WHERE
            id = :id",
    );

    $result = false;

    foreach ($sqls as $sql) {
        $stmt = $pdo->prepare($sql);
        if ($stmt === false) {
            throw new Exception('There was a problem preparing this query');
        }

        $result = $stmt->execute(
            array('id' => $post_id,)
        );

        //Don't continue if something went wrong
        if ($result === false) {
            break;
        }
    }

    return $result !== false;
}
