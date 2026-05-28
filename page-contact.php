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

<section class="l-lower-top l-lower-top--contact">
  <div class="l-inner">
    <div class="l-lower-top__titleWrap">
      <p class="p-lower-top__Subtitle animation-fade">CONTACT</p>
      <h2 class="p-lower-top__title p-lower-top__titleWrap">
        <span class="p-lower-top__title-line"></span>
        <span class="p-lower-top__title-contact animation-fade">お問い合わせ</span>
        <span class="p-lower-top__title-line"></span>
      </h2>
    </div>
  </div>
</section>

<div class="l-lower l-lower-contact animation-fade">
  <section class="p-lower-contact">
    <?php
    if (have_posts()) {
      while (have_posts()) {
        the_post();
        the_content();
      }
    };
    ?>
  </section>
</div>

<!-- モーダル -->
<div
  class="p-modal"
  id="privacy-modal"
  aria-hidden="true">
  <!-- overlay（クリックで閉じる対象） -->
  <div class="p-modal__overlay js-modal-close"></div>

  <div
    class="p-modal__content"
    role="dialog"
    aria-modal="true"
    aria-labelledby="privacy-modal-title"
    tabindex="-1">
    <!-- ×ボタン -->
    <button
      type="button"
      class="p-modal__close js-modal-close"
      aria-label="閉じる">
      <svg
        class="p-modal__close-icon"
        viewBox="0 0 12 12"
        aria-hidden="true">
        <path d="M1 1L11 11" />
        <path d="M11 1L1 11" />
      </svg>
    </button>

    <!-- タイトル -->
    <h2 id="privacy-modal-title" class="p-modal__title">
      プライバシーポリシー
    </h2>

    <!-- 本文（WP固定ページ） -->
    <div class="p-modal__body">
      <?php
      $privacy = get_page_by_path('privacy-policy');
      if ($privacy) {
        echo apply_filters('the_content', $privacy->post_content);
      }
      ?>
    </div>

    <!-- フッター -->
    <div class="p-modal__footer">
      <button
        type="button"
        class="p-modal__close-btn js-modal-close c-btn">
        閉じる
      </button>
    </div>
  </div>
</div>

<?php get_footer(); ?>