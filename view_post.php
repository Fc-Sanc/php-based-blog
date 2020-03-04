<?php
require_once 'lib/common.php';
require_once 'lib/view_post.php';

session_start();

// Get the post ID
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
} else {
    // So we always have a post ID var defined
    $post_id = 0;
}

// Connect to the database, run a query, handle errors
$pdo = getPDO();
$row = getPostRow($pdo, $post_id);

// If the post does not exist, let's deal with that here
if (!$row) {
    redirectAndExit('index.php?not-found=1');
}

$errors = null;
if ($_POST) {
    $comment_data = array(
        'name' => $_POST['comment-name'],
        'website' => $_POST['comment-website'],
        'text' => $_POST['comment-text'],
    );
    $errors = addCommentToPost($pdo, $post_id, $comment_data);

    // If there are no errors, redirect back to self redisplay
    if (!$errors) {
        redirectAndExit('view_post.php?post_id=' . $post_id);
    }
} else {
    $comment_data = array(
        'name' => '',
        'website' => '',
        'text' => '',
    );
}

?>
<!DOCTYPE html>
<html>

    <head>
        <title>
            A blog application |
            <?php echo htmlEscape($row['title']) ?>
        </title>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    </head>

    <body>
        <?php require 'templates/title.php' ?>
        <h2>
            <?php echo htmlEscape($row['title']) ?>
        </h2>
        <div>
            <?php echo convertSqlDate($row['created_at']) ?>
        </div>
        <?php // This is already escaped, so doesn't need further escaping
        echo convertNewlinesToParagraphs($row['body']) ?>

        <h3><?php echo countCommentsForPost($post_id) ?> comment(s)</h3>

        <?php foreach (getCommentsForPost($post_id) as $comment) : ?>
            <?php // For now,  we'll use a horizontal rule-off to split it up a bit
            ?>
            <hr/>
            <div class="comment">
                <div class="comment-meta">
                    Comment from
                    <?php echo htmlEscape($comment['name']) ?>
                    on
                    <?php echo convertSqlDate($comment['created_at']) ?>
                </div>
                <div class="comment-body">
                    <?php // This is already escaped ?>
                    <?php echo convertNewlinesToParagraphs($comment['text']) ?>
                </div>
            </div>
        <?php endforeach ?>

        <?php require 'templates/comment_form.php' ?>
    </body>

</html>