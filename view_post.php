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
$comment_count = $row['comment_count'];

// If the post does not exist, let's deal with that here
if (!$row) {
    redirectAndExit('index.php?not-found=1');
}

$errors = null;
if ($_POST) {
    switch ($_GET['action']) {
        case 'add-comment':
            $comment_data = array(
                'name' => $_POST['comment-name'],
                'website' => $_POST['comment-website'],
                'text' => $_POST['comment-text'],
            );
            $errors = handleAddComment($pdo, $post_id, $comment_data);

            break;
        case 'delete-comment':
            // Don't do anything if the user is not authorised
            $delete_response = $_POST['delete-comment'];
            handleDeleteComment($pdo, $post_id, $delete_response);
            break;
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
        <?php require 'templates/head.php' ?>
    </head>

    <body>
        <?php require 'templates/title.php' ?>

        <div class="post">
            <h2>
                <?php echo htmlEscape($row['title']) ?>
            </h2>
            <div class="date">
                <?php echo convertSqlDate($row['created_at']) ?>
            </div>
            <?php // This is already escaped, so doesn't need further escaping
            // echo convertNewlinesToParagraphs($row['body'])
            echo $row['body'] ?>
        </div>

        <?php require 'templates/list_comments.php' ?>

        <?php // We use $comment_data in this HTML fragment
        ?>
        <?php require 'templates/comment_form.php' ?>
    </body>

</html>