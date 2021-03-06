<?php
/**
 * @var $errors array
 * @var $comment_data array
 */
?>

<?php // Report any errors in a bullet-point list ?>
<?php if ($errors) : ?>
    <div class="error box">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<h3>Add your comment</h3>

<form
        action="view_post.php?action=add-comment&amp;post_id=<?php echo $post_id ?>"
        method="post"
        class="comment-form user-form">
    <p>
        <label for="comment-name">
            Name:
        </label>
        <input
                type="text"
                id="comment-name"
                name="comment-name"
                value="<?php echo htmlEscape($comment_data['name']) ?>"/>
    </p>
    <p>
        <label for="comment-website">
            Website:
        </label>
        <input
                type="text"
                id="comment-website"
                name="comment-website"
                value="<?php echo htmlEscape($comment_data['website']) ?>"/>
    </p>
    <p>
        <label for="comment-text">
            Comment:
        </label>
        <textarea
                id="comment-text"
                name="comment-text"
                rows="8"
                cols="70"><?php echo htmlEscape($comment_data['text']) ?></textarea>
    </p>
    <input type="submit" value="Submit comment"/>
</form>
