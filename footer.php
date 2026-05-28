<section class="l-footer">
    <div class="p-footer">
        <a href="<?php echo home_url('/#top'); ?>" class="p-footer__logo">
            <img src="<?= get_template_directory_uri(); ?>/assets/img/footer-logo.webp" width="191" height="53" alt="フッターロゴ">
        </a>
        <ul class="l-footer__list">
            <li class="p-footer__item"><a href="<?php echo home_url('/#profile'); ?>">PROFILE</a></li>
            <li class="p-footer__item"><a href="<?php echo home_url('/works'); ?>">WORKS</a></li>
            <li class="p-footer__item"><a href="<?php echo home_url('/#service'); ?>">SERVICE</a></li>
            <li class="p-footer__item"><a href="<?php echo home_url('/#value'); ?>">VALUE</a></li>
        </ul>
        <div class="p-footer__nameLogo">
            <img src="<?= get_template_directory_uri(); ?>/assets/img/footer-logo-pc.webp" alt="名前ロゴ" loading="lazy">
        </div>
        <div class="p-footer__name">
            <p>YUYATOYOSHIMA</p>
        </div>
        <div class="p-footer__copyright">
            <small>© toyo's portfolio</small>
        </div>

    </div>
</section>

<?php wp_footer(); ?>

</body>

</html>