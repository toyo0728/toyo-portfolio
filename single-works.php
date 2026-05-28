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

                preg_match('/href=["\']([^"\']+)["\']/', $crumb, $match);
                $url = $match[1] ?? '';

                // works-category のリンクなら強制変更
                if (strpos($url, '/works-category/') !== false) {

                    $slug = basename(untrailingslashit($url));
                    $url = home_url('/works/?filter=' . $slug);
                }

                echo '<div class="p-breadcrumb__item">
            <a class="p-breadcrumb__link" href="' . esc_url($url) . '">'
                    . esc_html(strip_tags($crumb)) .
                    '</a>
          </div>';
                echo ' ＞ ';
            }
        }
        ?>
    </div>
<?php endif; ?>

<section class="l-section--small p-single-works animation-fade">
    <div class="l-inner">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <?php
                // ACF取得
                $overview    = get_field('overview');
                $background  = get_field('background');
                $issues      = get_field('issues');
                $solution    = get_field('solution');
                $craft       = get_field('craft');
                $scope       = get_field('scope');
                $tools        = get_field('tools');
                $period      = get_field('period');
                $cost        = get_field('cost');
                $project_url = get_field('project_url');

                // カテゴリー取得
                $terms = get_the_terms(get_the_ID(), 'works_category');
                $category_name = '';
                if ($terms && !is_wp_error($terms)) {
                    $category_name = $terms[0]->name;
                }

                // モックアップ画像
                $mockup1 = get_field('mockup_1');
                $mockup2 = get_field('mockup_2');

                /**
                 * 改行区切りテキストをリスト表示
                 */
                function display_multiline_list($text)
                {
                    if (empty($text)) return;

                    $lines = preg_split('/\r\n|\r|\n/', $text);
                    $lines = array_filter(array_map('trim', $lines));

                    if (count($lines) > 1) {
                        echo '<ul class="p-single-works__points-list">';
                        foreach ($lines as $line) {
                            echo '<li>' . esc_html($line) . '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>' . esc_html($text) . '</p>';
                    }
                }
                ?>

                <!-- ビジュアルセクション -->
                <div class="p-single-works__visual">
                    <div class="p-single-works__mockups">
                        <?php if ($mockup1) : ?>
                            <div class="p-single-works__mockup --pc">
                                <img
                                    src="<?php echo esc_url($mockup1['url']); ?>"
                                    alt="<?php echo esc_attr($mockup1['alt']); ?>">
                            </div>
                        <?php endif; ?>

                        <?php if ($mockup2) : ?>
                            <div class="p-single-works__mockup --sp">
                                <img
                                    src="<?php echo esc_url($mockup2['url']); ?>"
                                    alt="<?php echo esc_attr($mockup2['alt']); ?>">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- プロジェクト詳細 -->
                <div class="p-single-works__details">
                    <div class="p-single-works__content-area">

                        <!-- ヘッダー -->
                        <div class="p-single-works__header">
                            <?php if ($category_name) : ?>
                                <div class="p-single-works__category">
                                    <p class="p-single-works__category-text"><?php echo esc_html($category_name); ?></p>
                                </div>
                            <?php endif; ?>

                            <h2 class="p-single-works__title"><?php the_title(); ?></h2>
                        </div>

                        <?php if ($overview) : ?>
                            <dl class="p-single-works__row">
                                <dt class="p-single-works__label">内容</dt>
                                <dd class="p-single-works__value">
                                    <?php echo nl2br(esc_html($overview)); ?>
                                </dd>
                            </dl>
                        <?php endif; ?>

                        <?php if ($background) : ?>
                            <dl class="p-single-works__row">
                                <dt class="p-single-works__label">制作背景</dt>
                                <dd class="p-single-works__value">
                                    <?php echo nl2br(esc_html($background)); ?>
                                </dd>
                            </dl>
                        <?php endif; ?>

                        <?php if ($issues) : ?>
                            <dl class="p-single-works__row">
                                <dt class="p-single-works__label">課題</dt>
                                <dd class="p-single-works__value">
                                    <?php display_multiline_list($issues); ?>
                                </dd>
                            </dl>
                        <?php endif; ?>

                        <?php if ($solution) : ?>
                            <dl class="p-single-works__row">
                                <dt class="p-single-works__label">解決</dt>
                                <dd class="p-single-works__value">
                                    <?php display_multiline_list($solution); ?>
                                </dd>
                            </dl>
                        <?php endif; ?>

                        <?php if ($craft) : ?>
                            <dl class="p-single-works__row">
                                <dt class="p-single-works__label">工夫</dt>
                                <dd class="p-single-works__value">
                                    <?php display_multiline_list($craft); ?>
                                </dd>
                            </dl>
                        <?php endif; ?>

                        <?php if ($scope) : ?>
                            <dl class="p-single-works__row">
                                <dt class="p-single-works__label">担当範囲</dt>
                                <dd class="p-single-works__value">
                                    <?php display_multiline_list($scope); ?>
                                </dd>
                            </dl>
                        <?php endif; ?>

                        <!-- 使用言語 -->
                        <?php if ($tools) : ?>
                            <dl class="p-single-works__row">
                                <dt class="p-single-works__label">使用言語</dt>
                                <dd class="p-single-works__value">
                                    <?php if (is_array($tools)) {
                                        echo esc_html(implode(' / ', $tools));
                                    } else {
                                        echo esc_html($tools);
                                    } ?> </dd>
                            </dl> <?php endif; ?>

                        <?php if ($period) : ?>
                            <dl class="p-single-works__row">
                                <dt class="p-single-works__label">制作期間</dt>
                                <dd class="p-single-works__value">
                                    <?php echo esc_html($period); ?>
                                </dd>
                            </dl>
                        <?php endif; ?>

                        <?php if ($cost) : ?>
                            <dl class="p-single-works__row">
                                <dt class="p-single-works__label">料金目安</dt>
                                <dd class="p-single-works__value">
                                    <?php echo esc_html($cost); ?>
                                </dd>
                            </dl>
                        <?php endif; ?>

                        <!-- URL -->
                        <?php
                        if ($project_url) : ?>
                            <?php
                            $lines = explode("\n", $project_url);
                            $url = trim($lines[0]); ?>
                            <dl class="p-single-works__row">
                                <dt class="p-single-works__label">URL</dt>
                                <dd class="p-single-works__value">
                                    <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer">
                                        <?php echo esc_html($url); ?> </a> <?php if (count($lines) > 1) : ?>
                                        <div class="p-single-works__auth">
                                            <?php echo nl2br(esc_html(implode("\n", array_slice($lines, 1)))); ?>
                                        </div>
                                    <?php endif; ?>
                                </dd>
                            </dl>
                        <?php endif; ?>
                    </div>

                    <!-- ナビゲーション -->
                    <div class="p-single-works__nav">
                        <a href="<?php echo esc_url(home_url('/works/')); ?>" class="c-btn c-btn--reverse">
                            <span class="c-btn__text">一覧に戻る</span>
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