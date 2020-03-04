<?php
require_once 'lib/common.php';
require_once 'lib/list_posts.php';

session_start();

// Don't let non-auth users see this screen
if (!isLoggedIn()) {
    redirectAndExit('index.php');
}

if ($_POST) {
    $delete_response = $_POST['delete-post'];
    if ($delete_response) {
        $keys = array_keys($delete_response);
        $delete_post_id = $keys[0];
        if ($delete_post_id) {
            deletePost(getPDO(), $delete_post_id);
            redirectAndExit('list_posts.php');
        }
    }
}

// Connect to the database, run a query
$pdo = getPDO();
$posts = getAllPosts($pdo);

?>
<!DOCTYPE html>
<html lang="zh">
    <head>
        <title>A blog application | Blog posts</title>
        <?php require 'templates/head.php' ?>
    </head>
    <body>
        <?php require 'templates/top_menu.php' ?>

        <h1>Post list</h1>

        <p>You have <?php echo count($posts) ?> post(s).</p>

        <form method="post">
            <table id="post-list">
                <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td>
                            <?php echo htmlEscape($post['title']) ?>
                        </td>
                        <td>
                            <?php echo convertSqlDate($post['created_at']) ?>
                        </td>
                        <td>
                            <a href="edit_post.php?post_id=<?php echo post['id'] ?>">Edit</a>
                        </td>
                        <td>
                            <input
                                    type="submit"
                                    name="delete-post[<?php echo $post['id'] ?>]"
                                    value="Delete"
                            />
                        </td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>
        </form>
        <a href="index.php">Cancel</a>
    </body>
</html>
