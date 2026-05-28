<?php get_header(); ?>

<!-- パンくず -->
<?php if (function_exists('bcn_display')) : ?>
    <div class="l-breadcrumb p-breadcrumb">
        <?php
        // bcn の HTML を文字列で取得
        $breadcrumbs = bcn_display(true);

        // ' > ' で分割
        $crumbs = explode(' &gt; ', $breadcrumbs);

        $total = count($crumbs);

        foreach ($crumbs as $index => $crumb) {

            // ===============================================
            // 1つ目（HOME）は固定で「ホーム」＋トップページリンク
            // ===============================================
            if ($index === 0) {
                echo '<div class="p-breadcrumb__item"><a class="p-breadcrumb__link p-breadcrumb__link--home" href="' . esc_url(home_url('/')) . '">Home</a></div>';

                // HOME の後に「＞」
                if ($total > 1) {
                    echo ' ＞ ';
                }

                continue;
            }

            // ===============================================
            // 最後の crumb（現在ページ） → span
            // ===============================================
            if ($index === $total - 1) {
                echo '<div class="p-breadcrumb__item"><span class="p-breadcrumb__text">' . esc_html(strip_tags($crumb)) . '</span></div>';
            }

            // ===============================================
            // 中間 crumb（カテゴリーなど）→ リンク
            // ===============================================
            else {
                // href 抽出
                preg_match('/href=["\']([^"\']+)["\']/', $crumb, $match);
                $url = $match[1] ?? '';

                echo '<div class="p-breadcrumb__item"><a class="p-breadcrumb__link" href="' . esc_url($url) . '">' . esc_html(strip_tags($crumb)) . '</a></div>';
                echo ' ＞ ';
            }
        }
        ?>
    </div>
<?php endif; ?>

<section class="l-section--small p-archive-works">
    <div class="l-inner">
        <h2 class="c-section-title animation-left">
            <span class="c-section-title__en">WORKS</span>
            <span class="c-section-title__row">
                <span class="c-section-title__jp">制作実績</span>
                <span class="c-section-title__line"></span>
                <span class="c-section-title__icon"></span>
            </span>
        </h2>

        <div class="p-works-filter">
            <button data-filter="all" class="is-active">
                全て
            </button>
            <?php
            $terms = get_terms(array(
                'taxonomy'   => 'works_category',
                'hide_empty' => false,
            ));
            foreach ($terms as $term) :
            ?>
                <button data-filter="<?php echo esc_attr($term->slug); ?>">
                    <?php echo esc_html($term->name); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="p-archive-works__items">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>

                    <?php
                    $terms = get_the_terms(get_the_ID(), 'works_category');
                    $term_slugs = [];

                    if ($terms && !is_wp_error($terms)) {
                        foreach ($terms as $term) {
                            $term_slugs[] = $term->slug;
                        }
                    }
                    ?>
                    <div class="p-archive-works-item animation-fade"
                        data-category="<?php echo esc_attr(implode(' ', $term_slugs)); ?>">

                        <a href="<?php the_permalink(); ?>" class="p-archive-works-item__link">
                            <div class="p-archive-works-item__img">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('large'); ?>
                                <?php endif; ?>
                            </div>
                        </a>
                        <!-- カテゴリー表示 -->
                        <div class="p-archive-works-item__categories">
                            <?php
                            if ($terms && !is_wp_error($terms)) :
                                foreach ($terms as $term) :
                            ?>
                                    <span class="p-archive-works-item__category">
                                        <?php echo esc_html($term->name); ?>
                                    </span>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </div>

                        <div class="p-archive-works-item__contents">
                            <h3 class="p-archive-works-item__title"><?php the_title(); ?></h3>
                            <div class="p-archive-works-item__excerpt"><?php the_excerpt(); ?></div>
                        </div>

                        <div class="p-works-item__btn">
                            <a href="<?php the_permalink(); ?>" class="c-btn c-btn--works">
                                詳細を見る
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
</section>

<section class="p-contact-cta animation-fade">
    <div class="l-inner">
        <a href="<?php echo esc_url(home_url('/contact')); ?>" class="p-contact-cta__link">
            <div class="p-contact-cta__wrap">
                <p class="p-contact-cta__title-en">CONTACT</p>
                <h3 class="p-contact-cta__title-ja">お問い合わせ</h3>
                <div class="p-contact-cta__icon"></div>
            </div>
        </a>
    </div>
</section>

<?php get_footer(); ?>