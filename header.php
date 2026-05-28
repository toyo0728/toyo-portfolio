<!DOCTYPE html>
<html lang="ja" class="is-force-loading">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="WordPressを中心にWebサイトを制作するコーダーのポートフォリオ。デザインカンプをもとに、ユーザーに伝わるサイトを構築します。">

    <title><?php echo wp_get_document_title(); ?></title>

    <!-- favicon -->
    <link rel="icon" href="<?php echo get_theme_file_uri('/assets/img/favicon.ico'); ?>" />
    <link rel="apple-touch-icon" href="<?php echo get_theme_file_uri('/assets/img/favicon.ico'); ?>" />

    <!-- OGP -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo wp_get_document_title(); ?>">
    <meta property="og:description" content="<?php bloginfo('description'); ?>">
    <meta property="og:url" content="<?php echo home_url(); ?>">
    <meta property="og:site_name" content="<?php bloginfo('name'); ?>">
    <meta property="og:image" content="<?php echo get_theme_file_uri('/assets/img/ogp.webp'); ?>">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="<?php echo get_theme_file_uri('/assets/img/ogp.webp'); ?>">

    <script>
        (function() {
            if ('scrollRestoration' in history) {
                history.scrollRestoration = 'manual';
            }

            var hasHash = window.location.hash && window.location.hash !== '';

            if (!hasHash) {
                //描画前にトップへ
                window.scrollTo(0, 0);

                //強制ローディング状態
                document.documentElement.classList.add('is-force-loading');
            }
        })();
    </script>

    <?php wp_head(); ?>
</head>

<body id="top">
    <header class="l-header p-header">
        <div class="l-header__inner">
            <h1 class="p-header__logo">
                <a href="<?php echo home_url(); ?>">
                    <img src="<?= get_template_directory_uri(); ?>/assets/img/logo.webp" width="191" height="53" alt="タイトルロゴ" loading="lazy" />
                </a>
            </h1>
            <nav class="l-header__nav" aria-label="Main Navigation">
                <ul class="l-header__list">
                    <li class="p-header__item"><a href="<?php echo home_url('/#profile'); ?>">PROFILE</a></li>
                    <li class="p-header__item"><a href="<?php echo home_url('/works'); ?>">WORKS</a></li>
                    <li class="p-header__item"><a href="<?php echo home_url('/#service'); ?>">SERVICE</a></li>
                    <li class="p-header__item"><a href="<?php echo home_url('/#value'); ?>">VALUE</a></li>
                </ul>
            </nav>
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="c-btn c-btn--contact p-header__contact">CONTACT</a>

            <!-- ハンバーガーアイコン -->
            <button class="p-header__icon" id="js-drawer-icon" aria-label="Toggle navigation menu">
                <span class="p-header__icon-bar-wrap">
                    <span class="p-header__icon-bar"></span>
                    <span class="p-header__icon-bar"></span>
                    <span class="p-header__icon-bar"></span>
                </span>
            </button>
        </div>
    </header>

    <!-- ドロワーメニュー -->
    <div class="l-drawer p-drawer" id="js-drawer-content">
        <div class="l-drawer__inner">
            <nav class="l-drawer__nav" aria-label="Drawer Navigation">
                <ul class="l-drawer__list">
                    <li class="p-drawer__item"><a href="<?php echo home_url('/#profile'); ?>">PROFILE</a></li>
                    <li class="p-drawer__item"><a href="<?php echo home_url('/works'); ?>">WORKS</a></li>
                    <li class="p-drawer__item"><a href="<?php echo home_url('/#service'); ?>">SERVICE</a></li>
                    <li class="p-drawer__item"><a href="<?php echo home_url('/#value'); ?>">VALUE</a></li>
                    <li class="p-drawer__item">
                        <a href="<?php echo esc_url(home_url('/contact')); ?>" class="c-btn l-drawer__contact">CONTACT</a>
                    </li>
                    <li class="p-drawer__item">
                        <img src="<?= get_template_directory_uri(); ?>/assets/img/logo.webp" width="110" height="33" alt="タイトルロゴ" loading="lazy" />
                    </li>
                </ul>
            </nav>
        </div>
    </div>