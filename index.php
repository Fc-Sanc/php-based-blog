<?php
require_once 'lib/common.php';

session_start();

// Connect to the database, run a query, handle errors
$pdo = getPDO();
$posts = getAllPosts($pdo);

$notFound = isset($_GET['not-found']);

?>

<!DOCTYPE html>
<html lang="zh">

    <head>
        <title>A blog application</title>
        <?php require 'templates/head.php' ?>
    </head>

    <body>
        <?php require 'templates/title.php' ?>

        <?php if ($notFound) : ?>
            <div class="error box">
                Error: cannot find the requested blog post.
            </div>
        <?php endif ?>

        <div class="post-list">
            <?php foreach ($posts as $post) : ?>
                <div class="post-synopsis">
                    <h2>
                        <?php echo htmlEscape($post['title']) ?>
                    </h2>
                    <div class="meta">
                        <?php echo convertSqlDate($post['created_at']) ?>
                        (<?php echo countCommentsForPost($pdo, $post['id']) ?> comments)
                    </div>
                    <p>
                        <?php echo substr(htmlEscape($post['body']), 0, strpos(htmlEscape($post['body']), "\n")) . '...' ?>
                    </p>
                    <div class="post-controls">
                        <a href="view_post.php?post_id=<?php echo $post['id'] ?>">Read more...</a>
                        <?php if (isLoggedIn()): ?>
                            |
                            <a href="edit_post.php?post_id=<?php echo $post['id'] ?>">Edit</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </body>

</html>
