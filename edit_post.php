<?php
require_once 'lib/common.php';
require_once 'lib/edit_post.php';
require_once 'lib/view_post.php';

session_start();

// Don't let non-auth users see this screen
if (!isLoggedIn()) {
    redirectAndExit('index.php');
}

// Empty defaults
$title = $body = '';

// Init database and get handle
$pdo = getPDO();

$post_id = null;
if (isset($_GET['post_id'])) {
    $post = getPostRow($pdo, $_GET['post_id']);
    if ($post) {
        $post_id = $_GET['post_id'];
        $title = $post['title'];
        $body = $post['body'];
    }
}

// Handle the post operation here
$errors = array();
if ($_POST) {
    // Validate these first
    $title = $_POST['post-title'];
    if (!$title) {
        $errors[] = 'The post must have a title';
    }
    $body = $_POST['post-body'];
    if (!$body) {
        $errors[] = 'The post must have a body';
    }

    if (!$errors) {
        //        $pdo = getPDO();
        // Decide i fwe are editing or adding
        if ($post_id) {
            editPost($pdo, $title, $body, $post_id);
        } else {
            $user_id = getAuthUserId($pdo);
            $post_id = addPost($pdo, $title, $body, $user_id);

            if ($post_id === false) {
                $errors[] = 'Post operation failed';
            }
        }
    }

    if (!$errors) {
        redirectAndExit('edit_post.php?post_id=' . $post_id);
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>A blog application | New post</title>
    <meta charset="utf-8">
    <?php require 'templates/head.php' ?>
</head>

<body>
    <?php require 'templates/top_menu.php' ?>

    <?php if (isset($_GET['post_id'])) : ?>
        <h1>Edit post</h1>
    <?php else : ?>
        <h1>New post</h1>
    <?php endif; ?>

    <?php if ($errors) : ?>
        <div class="error box">
            <ul>
                <?php foreach ($errors as $error) : ?>
                    <li><?php echo $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" class="post-form user-form">
        <div>
            <label for="post-title">Title:</label>
            <input id="post-title" name="post-title" type="text" value="<?php echo htmlEscape($title) ?>" />
        </div>
        <div>
            <label for="post-body">Body:</label>
            <br />
            <br />
            <div id="editor"><?= $body ?></div>
            <textarea id="post-body" name="post-body" rows="12" cols="70" hidden></textarea>
            <script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
            <script type="text/javascript" src="./lib/wangEditor.min.js"></script>
            <script type="text/javascript">
                var E = window.wangEditor
                var editor = new E('#editor')
                editor.customConfig.onchange = function(html) {
                    $("#post-body").val(html);
                };
                editor.create()
                $("#post-body").val(editor.txt.html())
            </script>
        </div>
        <div>
            <input type="submit" value="Save post" />
            <a href="index.php">Cancel</a>
        </div>
    </form>
</body>

</html>