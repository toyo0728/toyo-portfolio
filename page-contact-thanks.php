<?php
/*
Template Name: Contact Thanks
*/
?>

<?php get_header(); ?>

<div class="l-lower-message">
    <div class="l-inner">
        <div class="p-lower-message">
            <p class="p-lower-message__text">
                送信完了しました。<br>
                お問い合わせいただき、<br class="hidden-pc">誠にありがとうございます。
            </p>
            <p class="p-lower-message__Subtext">
                24時間以内に内容を確認しご連絡いたします。
            </p>
        </div>
        <a href="<?php echo home_url(); ?>" class="c-btn l-lower-message__btn">TOPページに戻る</a>
    </div>
</div>

<?php get_footer(); ?>