<?php
/* =========================================================
   テーマセットアップ
========================================================= */
function my_theme_setup()
{
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script'
    ));
}
add_action('after_setup_theme', 'my_theme_setup');


/* =========================================================
   Google Fonts 最適化（Preconnect）
========================================================= */
function add_google_fonts_preconnect()
{
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
}
add_action('wp_head', 'add_google_fonts_preconnect', 1);


/* =========================================================
   CSS・JavaScript 読み込み
========================================================= */
function my_scripts()
{
    // Google Fonts
    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Noto+Sans+JP:wght@400;500;600;700&display=swap',
        array(),
        null
    );

    // Swiper CSS
    wp_enqueue_style(
        'swiper-style',
        get_template_directory_uri() . '/assets/css/lib/swiper-bundle.min.css',
        array(),
        filemtime(get_theme_file_path('/assets/css/lib/swiper-bundle.min.css'))
    );

    // Main CSS
    wp_enqueue_style(
        'main-style',
        get_template_directory_uri() . '/assets/css/style.css',
        array('swiper-style'),
        filemtime(get_theme_file_path('/assets/css/style.css'))
    );

    // Swiper JS
    wp_enqueue_script(
        'swiper-script',
        get_template_directory_uri() . '/assets/js/lib/swiper-bundle.min.js',
        array(),
        filemtime(get_theme_file_path('/assets/js/lib/swiper-bundle.min.js')),
        true
    );

    // GSAP
    wp_enqueue_script(
        'gsap',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js',
        array(),
        '3.12.5',
        true
    );

    // ScrollTrigger
    wp_enqueue_script(
        'gsap-scrolltrigger',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js',
        array('gsap'),
        '3.12.5',
        true
    );

    // Main JS
    wp_enqueue_script(
        'main-js',
        get_template_directory_uri() . '/assets/js/script.js',
        array('swiper-script', 'gsap', 'gsap-scrolltrigger'),
        filemtime(get_theme_file_path('/assets/js/script.js')),
        true
    );
}
add_action('wp_enqueue_scripts', 'my_scripts');


/* =========================================================
   JavaScript を defer 読み込み
========================================================= */
function add_defer_to_scripts($tag, $handle)
{
    if (is_admin()) return $tag;

    $defer_scripts = array(
        'swiper-script',
        'gsap',
        'gsap-scrolltrigger',
        'main-js'
    );

    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src=', ' defer src=', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'add_defer_to_scripts', 10, 2);


/* =========================================================
   絵文字スクリプト削除（高速化）
========================================================= */
function disable_emojis()
{
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
}
add_action('init', 'disable_emojis');


/* =========================================================
   Contact Form 7 自動整形を無効化
========================================================= */
add_filter('wpcf7_autop_or_not', '__return_false');


/* =========================================================
   LCP画像の最適化（遅延読み込み除外）
========================================================= */
function optimize_lcp_image($attr)
{
    if (is_front_page()) {
        $attr['loading'] = 'eager';
        $attr['fetchpriority'] = 'high';
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'optimize_lcp_image');


/* =========================================================
   Works カスタム投稿タイプ
========================================================= */
function create_post_type_works()
{
    $labels = array(
        'name'               => '制作実績',
        'singular_name'      => 'Work',
        'menu_name'          => '制作実績',
        'add_new'            => '新規追加',
        'add_new_item'       => '実績を追加',
        'edit_item'          => '実績を編集',
        'new_item'           => '新しい実績',
        'view_item'          => '実績を見る',
        'search_items'       => '実績を検索',
        'not_found'          => '実績が見つかりません',
        'not_found_in_trash' => 'ゴミ箱に実績はありません',
    );

    $args = array(
        'labels'        => $labels,
        'public'        => true,
        'has_archive'   => true,
        'rewrite'       => array(
            'slug' => 'works',
            'with_front' => false,
        ),
        'supports'      => array(
            'title',
            'editor',
            'thumbnail',
            'excerpt',
            'page-attributes'
        ),
        'show_in_rest'  => true,
    );

    register_post_type('works', $args);
}
add_action('init', 'create_post_type_works');


/* =========================================================
   Works タクソノミー
========================================================= */
function create_works_taxonomy()
{
    register_taxonomy(
        'works_category',
        'works',
        array(
            'label'        => '制作カテゴリ',
            'public'       => true,
            'hierarchical' => true,
            'rewrite'      => array(
                'slug' => 'works-category',
                'with_front' => false
            ),
            'show_in_rest' => true,
        )
    );
}
add_action('init', 'create_works_taxonomy');


/* =========================================================
   管理画面に「順序」列を追加
========================================================= */
function add_works_order_column($columns)
{
    $new_columns = array();

    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;

        if ($key === 'title') {
            $new_columns['menu_order'] = '順序';
        }
    }

    return $new_columns;
}
add_filter('manage_works_posts_columns', 'add_works_order_column');


function show_works_order_column($column, $post_id)
{
    if ($column === 'menu_order') {
        echo get_post_field('menu_order', $post_id);
    }
}
add_action('manage_works_posts_custom_column', 'show_works_order_column', 10, 2);
