// ドロワーメニュー
document.addEventListener('DOMContentLoaded', function () {
  const drawerIcon = document.getElementById('js-drawer-icon');
  const drawerContent = document.getElementById('js-drawer-content');
  const body = document.body;

  if (!drawerIcon || !drawerContent) return;

  drawerIcon.addEventListener('click', function () {
    // クラスの切り替え
    drawerIcon.classList.toggle('is-open');
    drawerContent.classList.toggle('is-open');

    // bodyのスクロール制御
    if (drawerContent.classList.contains('is-open')) {
      body.style.overflow = 'hidden';
    } else {
      body.style.overflow = '';
    }
  });
});

//スムーススクロール
document.addEventListener('DOMContentLoaded', () => {
  const header = document.querySelector('.p-header');
  const drawer = document.getElementById('js-drawer-content');
  const drawerIcon = document.getElementById('js-drawer-icon');

  let isScrolling = false;

  if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
  }

  const easeInOutCubic = (t) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);

  const getHeaderHeight = () => (header ? header.offsetHeight : 0);

  function smoothScrollToY(targetY) {
    if (isScrolling) return;
    isScrolling = true;

    const startY = window.pageYOffset;
    const distance = targetY - startY;
    const duration = Math.min(Math.max(Math.abs(distance) * 0.8, 350), 1200);

    let startTime = null;

    function step(now) {
      if (startTime === null) startTime = now;

      const t = Math.min((now - startTime) / duration, 1);
      const eased = easeInOutCubic(t);

      window.scrollTo(0, startY + distance * eased);

      if (t < 1) {
        requestAnimationFrame(step);
      } else {
        isScrolling = false;
      }
    }

    requestAnimationFrame(step);
  }

  function scrollToHash(hash, { updateUrl = true } = {}) {
    if (!hash || hash === '#') return;

    const target = document.querySelector(hash);
    if (!target) return;

    requestAnimationFrame(() => {
      const offset = getHeaderHeight() + 20;
      const y = target.getBoundingClientRect().top + window.pageYOffset - offset;

      smoothScrollToY(y);

      if (updateUrl) {
        if (history.pushState) {
          history.pushState(null, '', hash);
        } else {
          location.hash = hash;
        }
      }
    });
  }

  document.addEventListener('click', (event) => {
    const a = event.target.closest('a');
    if (!a) return;

    const href = a.getAttribute('href');
    if (!href) return;

    let url;
    try {
      url = new URL(href, window.location.href);
    } catch {
      return;
    }

    const hash = url.hash;
    if (!hash || hash === '#') return;

    const isSameOrigin = url.origin === window.location.origin;
    const isSamePath = url.pathname === window.location.pathname;

    // 同一ページ内のアンカーだけJSで制御
    if (!(isSameOrigin && isSamePath)) return;

    const target = document.querySelector(hash);
    if (!target) return;

    event.preventDefault();

    const run = () => scrollToHash(hash, { updateUrl: true });
    const isDrawerOpen = drawer?.classList.contains('is-open');

    if (isDrawerOpen) {
      drawer.classList.remove('is-open');
      drawerIcon?.classList.remove('is-open');
      document.body.style.overflow = '';

      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          run();
        });
      });
    } else {
      run();
    }
  });

  // 別ページから /#top で来た時にも対応
  window.addEventListener('load', () => {
    if (!location.hash) return;

    setTimeout(() => {
      scrollToHash(location.hash, { updateUrl: false });
    }, 50);
  });
});

/* ===============================
  スクロール復元を完全停止
=============================== */
if ('scrollRestoration' in history) {
  history.scrollRestoration = 'manual';
}

/* ===============================
  判定
=============================== */
const hasHash = window.location.hash;
function isTopPage() {
  return !!document.querySelector('.p-loader') && !!document.querySelector('.p-mv');
}

/* ===============================
  Scroll Control
=============================== */
let scrollY = 0;
let isLocked = false;

function lockScroll() {
  const wrap = document.getElementById('js-site-wrap');
  if (!wrap || isLocked) return;

  scrollY = window.scrollY;

  wrap.classList.add('is-scroll-locked');
  wrap.style.top = `-${scrollY}px`;

  document.body.style.overflow = 'hidden';
  document.documentElement.style.overflow = 'hidden';
  document.body.style.touchAction = 'none';

  isLocked = true;
}

function unlockScroll() {
  const wrap = document.getElementById('js-site-wrap');
  if (!wrap) return;

  //ここで解除（タイミング統一）
  document.documentElement.classList.remove('is-force-loading');

  wrap.classList.remove('is-scroll-locked');
  wrap.style.top = '';

  document.body.style.overflow = '';
  document.documentElement.style.overflow = '';
  document.body.style.touchAction = '';

  if (location.hash) {
    const target = document.querySelector(location.hash);
    if (target) {
      const y = target.getBoundingClientRect().top + window.scrollY;
      window.scrollTo(0, y);
    }
  } else {
    window.scrollTo(0, 0);
  }

  isLocked = false;
}

/* ===============================
  初期処理
=============================== */
document.addEventListener('DOMContentLoaded', () => {
  if (isTopPage() && !hasHash) {
    window.scrollTo(0, 0);
    lockScroll();
  }
});

/* ===============================
  戻る・キャッシュ復帰対策
=============================== */
window.addEventListener('pageshow', (e) => {
  const wrap = document.getElementById('js-site-wrap');
  if (!wrap) return;

  wrap.classList.remove('is-scroll-locked');
  wrap.style.top = '';
  document.body.style.overflow = '';
  document.documentElement.style.overflow = '';
  document.body.style.touchAction = '';

  //#なしなら必ずトップ
  if (!location.hash) {
    window.scrollTo(0, 0);
  }

  isLocked = false;
});

/* ===============================
  ローディング
=============================== */
window.addEventListener('load', () => {
  const loader = document.querySelector('.p-loader');
  const mv = document.querySelector('.p-mv');

  // ===== トップ以外 =====
  if (!isTopPage()) {
    document.documentElement.classList.remove('is-force-loading');

    document.body.style.overflow = '';
    document.documentElement.style.overflow = '';
    document.body.style.touchAction = '';

    return;
  }

  // ===== #付き =====
  if (hasHash) {
    loader.style.display = 'none';

    mv.classList.add(
      'is-reveal-start',
      'is-copy-visible',
      'is-sliders-visible',
      'is-marquee-start'
    );

    initMvMarquee();
    unlockScroll();

    return;
  }

  // ===== 通常ローディング =====
  loader.classList.add('is-active');

  setTimeout(() => {
    loader.classList.add('is-bridge');

    setTimeout(() => {
      mv.classList.add('is-reveal-start');
      loader.classList.add('is-hide');

      setTimeout(() => {
        loader.style.display = 'none';

        //ここで初めて解除
        startMvAnimation();
      }, 800);
    }, 450);
  }, 2500);
});

/* ===============================
  MVアニメーション
=============================== */
function startMvAnimation() {
  const mv = document.querySelector('.p-mv');
  if (!mv) return;

  setTimeout(() => {
    mv.classList.add('is-copy-visible');
  }, 150);

  setTimeout(() => {
    mv.classList.add('is-sliders-visible');
    initMvMarquee();
  }, 900);

  setTimeout(() => {
    mv.classList.add('is-marquee-start');
  }, 1100);

  setTimeout(() => {
    unlockScroll();
  }, 1700);
}

/* ===============================
  marquee
=============================== */
function initMvMarquee() {
  const sliders = document.querySelectorAll('.js-mv-swiper');
  if (!sliders.length) return;

  sliders.forEach((root) => {
    const wrapper = root.querySelector('.swiper-wrapper');
    if (!wrapper) return;

    // クローン生成（未生成の場合のみ）
    if (!root.dataset.cloned) {
      const slides = Array.from(wrapper.children);
      slides.forEach((slide) => {
        wrapper.appendChild(slide.cloneNode(true));
      });
      root.dataset.cloned = 'true';
    }

    // 距離を計算
    const totalWidth = wrapper.scrollWidth;
    const oneSetWidth = totalWidth / 2;

    root.style.setProperty('--mv-distance', `${oneSetWidth}px`);

    // 速度設定
    const pxPerSec = 80;
    const duration = Math.max(8, oneSetWidth / pxPerSec);
    root.style.setProperty('--mv-duration', `${duration}s`);
  });
}

/* ===============================
  初期化（画像読み込み後）
=============================== */
window.addEventListener('load', () => {
  initMvMarquee();
});

/* ===============================
  resize（横幅変更時のみ再計算）
=============================== */
let resizeTimer;
let lastWindowWidth = window.innerWidth;

window.addEventListener('resize', () => {
  clearTimeout(resizeTimer);

  resizeTimer = setTimeout(() => {
    const currentWidth = window.innerWidth;

    // 高さ変化のみの場合は無視（スマホのアドレスバー対策）
    if (Math.abs(currentWidth - lastWindowWidth) < 2) return;

    lastWindowWidth = currentWidth;

    document.querySelectorAll('.js-mv-swiper').forEach((root) => {
      const wrapper = root.querySelector('.swiper-wrapper');
      if (!wrapper) return;

      // 既存の複製スライドを削除
      const slides = Array.from(wrapper.children);
      const half = slides.length / 2;
      slides.slice(half).forEach((slide) => slide.remove());

      // クローン状態をリセット
      delete root.dataset.cloned;
    });

    // 再初期化
    initMvMarquee();
  }, 200);
});

// プロフィールのSwiper
let profileSwiper;

// 2個前 / 2個先にクラス付与
function setFarClasses(swiper) {
  if (!swiper || !swiper.slides) return;

  swiper.slides.forEach((slide) => {
    slide.classList.remove('swiper-slide-prev2', 'swiper-slide-next2');
  });

  const activeIndex = swiper.activeIndex;
  const prev2 = swiper.slides[activeIndex - 2];
  const next2 = swiper.slides[activeIndex + 2];

  if (prev2) prev2.classList.add('swiper-slide-prev2');
  if (next2) next2.classList.add('swiper-slide-next2');
}

function initProfileSwiper() {
  const swiperEl = document.querySelector('.js-profile-swiper');
  if (!swiperEl) return;

  const root = swiperEl.closest('.p-profile') || document;
  const prevBtn = root.querySelector('.swiper-button-prev');
  const nextBtn = root.querySelector('.swiper-button-next');
  const paginationEl = root.querySelector('.js-profile-pagination');

  const realSlides = swiperEl.querySelectorAll('.swiper-slide:not(.swiper-slide-duplicate)').length;

  if (profileSwiper) return;

  profileSwiper = new Swiper(swiperEl, {
    loop: realSlides > 3,
    centeredSlides: true,
    grabCursor: true,
    speed: 1400,
    watchOverflow: true,
    observer: true,
    observeParents: true,

    // デフォルト（SP）
    slidesPerView: 1.15,
    spaceBetween: 16,

    // レスポンシブ設定
    breakpoints: {
      480: {
        slidesPerView: 1.25,
        spaceBetween: 14
      },
      768: {
        slidesPerView: 1.45,
        spaceBetween: 16
      },
      900: {
        slidesPerView: 2,
        spaceBetween: 24
      },
      1025: {
        slidesPerView: 2,
        spaceBetween: 32
      },
      1200: {
        slidesPerView: 3,
        spaceBetween: 32
      },
      1920: {
        slidesPerView: 3,
        spaceBetween: 8
      }
    },

    navigation: {
      prevEl: prevBtn,
      nextEl: nextBtn
    },

    on: {
      init(swiper) {
        setFarClasses(swiper);
      },
      slideChange(swiper) {
        setFarClasses(swiper);
      },
      resize(swiper) {
        setFarClasses(swiper);
      }
    }
  });

  requestAnimationFrame(() => setFarClasses(profileSwiper));
}

// 初期化
window.addEventListener('load', initProfileSwiper);

//出現アニメーション
document.addEventListener('DOMContentLoaded', () => {
  const targets = document.querySelectorAll(
    '.animation-fade, .animation-left, .animation-right, .animation-scale'
  );
  const observer = new IntersectionObserver(
    (entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-show');
          observer.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0,
      rootMargin: '0px 0px -40% 0px'
    }
  );
  targets.forEach((el) => observer.observe(el));
  // 初期表示フォールバック
  setTimeout(() => {
    targets.forEach((el) => {
      if (el.classList.contains('is-show')) return;

      const rect = el.getBoundingClientRect();
      if (rect.top >= 0 && rect.top < window.innerHeight) {
        el.classList.add('is-show');
        observer.unobserve(el);
      }
    });
  }, 100);
});

// モーダル
document.addEventListener('DOMContentLoaded', () => {
  const openBtns = document.querySelectorAll('.js-modal-open');

  // すべて閉じる（必要なら）
  const closeAll = () => {
    document.querySelectorAll('.p-modal.is-active').forEach((m) => {
      m.classList.remove('is-active');
      m.setAttribute('aria-hidden', 'true');
    });
    document.body.style.overflow = '';
  };

  // 指定IDのモーダルを開く
  const openModal = (modalId) => {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    // 1つだけ表示にしたいなら先に全部閉じる
    closeAll();

    modal.classList.add('is-active');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  };

  // モーダルを閉じる
  const closeModal = (modal) => {
    if (!modal) return;

    modal.classList.remove('is-active');
    modal.setAttribute('aria-hidden', 'true');

    // 他に開いてるモーダルがなければスクロール復帰
    if (!document.querySelector('.p-modal.is-active')) {
      document.body.style.overflow = '';
    }
  };

  /* =========================
     開くボタン（どこからでも）
  ========================= */
  openBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      const targetId = btn.dataset.modalTarget;
      if (!targetId) return;
      openModal(targetId);
    });
  });

  /* =========================
     閉じる（overlay / × / footer）
     ※各モーダル内だけを監視する
  ========================= */
  document.querySelectorAll('.p-modal').forEach((modal) => {
    const overlay = modal.querySelector('.p-modal__overlay');
    const closeBtns = modal.querySelectorAll('.p-modal__close, .p-modal__close-btn');

    overlay?.addEventListener('click', () => closeModal(modal));
    closeBtns.forEach((btn) => btn.addEventListener('click', () => closeModal(modal)));
  });

  /* =========================
     ESCで閉じる（開いてるものだけ）
  ========================= */
  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    const activeModal = document.querySelector('.p-modal.is-active');
    closeModal(activeModal);
  });
});

// タイトルアニメーション
const targets = document.querySelectorAll(
  '.c-section-title, .p-works-item, .p-lower-top__titleWrap'
);

const observer = new IntersectionObserver(
  (entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-active');
        observer.unobserve(entry.target); // ← 1回だけ発火
      }
    });
  },
  {
    threshold: 0.2
  }
);
targets.forEach((target) => observer.observe(target));

// Worksフィルター + FLIPアニメーション + URL対応
document.addEventListener('DOMContentLoaded', () => {
  const buttons = document.querySelectorAll('.p-works-filter button');
  const items = document.querySelectorAll('.p-archive-works-item');

  // FLIPフィルター関数
  function filterWorks(filter) {
    // ① First
    const firstPositions = new Map();
    items.forEach((item) => {
      firstPositions.set(item, item.getBoundingClientRect());
    });

    // ② 表示切替
    items.forEach((item) => {
      const categories = item.dataset.category ? item.dataset.category.split(' ') : [];

      const shouldShow = filter === 'all' || categories.includes(filter);

      if (shouldShow) {
        item.classList.remove('is-hidden');
      } else {
        item.classList.add('is-hidden');
      }
    });

    // ③ FLIPアニメーション
    items.forEach((item) => {
      if (item.classList.contains('is-hidden')) return;

      const first = firstPositions.get(item);
      const last = item.getBoundingClientRect();

      const deltaX = first.left - last.left;
      const deltaY = first.top - last.top;

      if (deltaX || deltaY) {
        item.style.transform = `translate(${deltaX}px, ${deltaY}px) scale(.98)`;
        item.style.transition = 'none';

        requestAnimationFrame(() => {
          item.style.transition = 'transform .7s cubic-bezier(.22,1,.36,1)';
          item.style.transform = '';
        });
      }
    });
  }

  // ボタンクリックイベント
  buttons.forEach((button) => {
    button.addEventListener('click', () => {
      buttons.forEach((b) => b.classList.remove('is-active'));
      button.classList.add('is-active');

      filterWorks(button.dataset.filter);

      // URLパラメータを書き換える（履歴に残すだけ）
      const filter = button.dataset.filter;
      const url = new URL(window.location);
      if (filter === 'all') {
        url.searchParams.delete('filter');
      } else {
        url.searchParams.set('filter', filter);
      }
      window.history.replaceState({}, '', url);
    });
  });

  // URLパラメータから初回フィルター
  const urlParams = new URLSearchParams(window.location.search);
  const filterParam = urlParams.get('filter');

  if (filterParam) {
    const targetButton = document.querySelector(
      `.p-works-filter button[data-filter="${filterParam}"]`
    );
    if (targetButton) targetButton.click();
  } else {
    // 初期は全て表示
    filterWorks('all');
  }
});

// serviceアニメーション
gsap.registerPlugin(ScrollTrigger);

ScrollTrigger.config({
  ignoreMobileResize: true
});

const mm = gsap.matchMedia();

// ===============================
// PC（901px以上）
// ===============================
mm.add('(min-width: 901px)', () => {
  const contents = document.querySelector('.p-service__contents');
  const skillWrap = document.querySelector('.p-service__skillWrap');
  const cards = gsap.utils.toArray('.p-service-card');

  if (!contents || !skillWrap || !cards.length) return;

  // 左カラム固定
  const pinTrigger = ScrollTrigger.create({
    trigger: contents,
    start: 'top 140px',
    end: () => '+=' + Math.max(0, contents.offsetHeight - skillWrap.offsetHeight),
    pin: skillWrap,
    pinSpacing: true,
    anticipatePin: 1,
    invalidateOnRefresh: true
  });

  // 初期状態
  gsap.set(cards, {
    opacity: 0,
    y: 56
  });

  const animations = [];

  cards.forEach((card, i) => {
    // フェードアップ
    animations.push(
      gsap.to(card, {
        opacity: 1,
        y: 0,
        duration: 0.9,
        ease: 'power3.out',
        scrollTrigger: {
          trigger: card,
          start: 'top 82%',
          toggleActions: 'play none none reverse'
        }
      })
    );

    // 重なり演出
    if (i !== cards.length - 1) {
      animations.push(
        gsap.to(card, {
          scale: 0.96,
          ease: 'none',
          scrollTrigger: {
            trigger: cards[i + 1],
            start: 'top 78%',
            end: 'top 36%',
            scrub: true
          }
        })
      );
    }
  });

  // ブレークポイント変更時のクリーンアップ
  return () => {
    pinTrigger.kill();
    animations.forEach((anim) => anim.kill());
    gsap.set(skillWrap, { clearProps: 'all' });
    gsap.set(cards, { clearProps: 'all' });
  };
});

// ===============================
// タブレット・スマホ（900px以下）
// ===============================
mm.add('(max-width: 900px)', () => {
  const cards = gsap.utils.toArray('.p-service-card');
  const cardsWrap = document.querySelector('.p-service-cards');

  if (!cards.length || !cardsWrap) return;

  gsap.set(cards, {
    y: 0,
    opacity: 1,
    scale: 1
  });

  cards.forEach((card, i) => {
    gsap.set(card, { y: i === 0 ? 0 : 80 });
  });

  const animations = [];

  cards.forEach((card, i) => {
    const nextCard = cards[i + 1];
    if (!nextCard) return;

    // 次のカードが重なる
    animations.push(
      gsap.to(nextCard, {
        y: 0,
        ease: 'none',
        scrollTrigger: {
          trigger: nextCard,
          start: 'top bottom',
          end: 'top top+=96',
          scrub: true,
          invalidateOnRefresh: true
        }
      })
    );

    // 前カードを縮小
    animations.push(
      gsap.to(card, {
        scale: 0.96,
        ease: 'none',
        scrollTrigger: {
          trigger: nextCard,
          start: 'top center',
          end: 'top top+=96',
          scrub: true,
          invalidateOnRefresh: true
        }
      })
    );
  });

  // クリーンアップ
  return () => {
    animations.forEach((anim) => anim.kill());
    gsap.set(cards, { clearProps: 'all' });
  };
});

// ===============================
// ScrollTrigger 再計算制御
// ===============================
const refreshScrollTrigger = () => {
  clearTimeout(resizeTimer);

  resizeTimer = setTimeout(() => {
    ScrollTrigger.refresh();
    // Chrome回転バグ対策
    setTimeout(() => ScrollTrigger.refresh(), 300);
  }, 250);
};

window.addEventListener('load', refreshScrollTrigger);
window.addEventListener('resize', refreshScrollTrigger);
window.addEventListener('orientationchange', refreshScrollTrigger);

// valueアニメーション
document.addEventListener('DOMContentLoaded', function () {
  gsap.registerPlugin(ScrollTrigger);

  ScrollTrigger.config({
    ignoreMobileResize: true
  });

  let resizeTimer;

  // ===============================
  // ScrollTrigger 再計算制御
  // ===============================
  const refreshScrollTrigger = () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
      ScrollTrigger.refresh();
      // Chromeの回転対策
      setTimeout(() => ScrollTrigger.refresh(), 300);
    }, 250);
  };

  window.addEventListener('load', refreshScrollTrigger);
  window.addEventListener('resize', refreshScrollTrigger);
  window.addEventListener('orientationchange', refreshScrollTrigger);

  // ===============================
  // matchMedia
  // ===============================
  ScrollTrigger.matchMedia({
    /* ===============================
       PC（1025px以上）
    =============================== */
    '(min-width: 1025px)': function () {
      const wrapper = document.querySelector('.p-value-wrapper');
      const left = document.querySelector('.p-value-left');
      const right = document.querySelector('.p-value-right');

      const icons = gsap.utils.toArray('.p-value-right .p-value-icon--pc');
      const cards = gsap.utils.toArray('.p-value-card');

      if (!wrapper || !left || !right || !icons.length || !cards.length) return;

      let current = 0;
      let isAnimating = false;

      // ===== 初期状態 =====
      gsap.set(icons, {
        autoAlpha: 0,
        scale: 0.95,
        zIndex: 0
      });

      gsap.set(icons[0], {
        autoAlpha: 1,
        scale: 1,
        zIndex: 1
      });

      icons.forEach((icon, i) => {
        icon.classList.toggle('is-active', i === 0);
      });

      // ===== アイコン切り替え =====
      function changeIcon(index) {
        if (index === current || isAnimating) return;

        isAnimating = true;
        gsap.killTweensOf(icons);

        icons.forEach((icon) => icon.classList.remove('is-active'));
        gsap.set(icons, { zIndex: 0 });
        gsap.set(icons[index], { zIndex: 1 });

        const tl = gsap.timeline({
          defaults: { overwrite: 'auto' },
          onComplete: () => {
            icons[index].classList.add('is-active');
            current = index;
            isAnimating = false;
          }
        });

        tl.to(
          icons[current],
          {
            autoAlpha: 0,
            scale: 0.95,
            duration: 0.2
          },
          0
        ).to(
          icons[index],
          {
            autoAlpha: 1,
            scale: 1,
            duration: 0.25
          },
          0
        );
      }

      // ===== 右カラム固定 =====
      const pinTrigger = ScrollTrigger.create({
        trigger: wrapper,
        start: 'top top',
        end: () => '+=' + Math.max(0, left.offsetHeight - right.offsetHeight),
        pin: right,
        invalidateOnRefresh: true,
        anticipatePin: 1
      });

      // ===== カードごとの切り替え =====
      const cardTriggers = cards.map((card, i) =>
        ScrollTrigger.create({
          trigger: card,
          start: 'center center',
          onEnter: () => changeIcon(i),
          onEnterBack: () => changeIcon(i)
        })
      );

      // ===== クリーンアップ =====
      return () => {
        pinTrigger.kill();
        cardTriggers.forEach((trigger) => trigger.kill());
        gsap.killTweensOf(icons);
        gsap.set(icons, { clearProps: 'all' });
        icons.forEach((icon) => icon.classList.remove('is-active'));
        current = 0;
        isAnimating = false;
      };
    },

    /* ===============================
       タブレット・スマホ（1024px以下）
    =============================== */
    '(max-width: 1024px)': function () {
      const cards = gsap.utils.toArray('.p-value-card');

      if (!cards.length) return;

      const animations = cards.map((card) => {
        const elements = card.querySelectorAll(
          '.p-value__tag, .p-value-icon--sp, .p-value__title, .p-value__text'
        );

        return gsap.from(elements, {
          y: 20,
          opacity: 0,
          duration: 0.6,
          stagger: 0.1,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: card,
            start: 'top 85%',
            once: true
          }
        });
      });

      // クリーンアップ
      return () => {
        animations.forEach((anim) => anim.kill());
      };
    }
  });
});

// Contact Form 7 の送信後のリダイレクト
document.addEventListener(
  'wpcf7mailsent',
  function (event) {
    window.location.href = '/contact-thanks/';
  },
  false
);

/* ===============================
  Marquee 初期化（フォント読込後）
  PROFILE & VALUE 共通
=============================== */
window.addEventListener('load', async () => {
  try {
    // Webフォントの読み込み完了を待つ
    if (document.fonts && document.fonts.ready) {
      await document.fonts.ready;
    }
  } catch (e) {
    console.warn('Font loading check failed:', e);
  }

  // レイアウト確定後にアニメーション開始
  requestAnimationFrame(() => {
    document
      .querySelectorAll(
        '.p-profile__logoMotion-track, .p-value-marquee__track'
      )
      .forEach((track) => {
        track.classList.add('is-ready');
      });
  });
});