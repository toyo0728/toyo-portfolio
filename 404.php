<?php
status_header(404);
?>

<?php get_header(); ?>

<div class="l-lower-message">
    <div class="l-inner">
        <div class="p-lower-message">
            <p class="p-lower-message__text --404">
                Not Found
            </p>
            <p class="p-lower-message__Subtext --404">
                申し訳ございません。<br>
                お探しページが見つかりません。
            </p>
        </div>
        <a href="<?php echo home_url(); ?>" class="c-btn l-lower-message__btn">TOPページに戻る</a>
    </div>
</div>

<?php get_footer(); ?>