(function () {
  'use strict';

  const initScheduleSlider = (wrapper) => {
    if (typeof Swiper === 'undefined') {
      return null;
    }

    const swiperContainer = wrapper.querySelector('.service-schedule__swiper');
    if (!swiperContainer) {
      return null;
    }

    const prevButton = wrapper.querySelector('.service-schedule__nav-button--prev');
    const nextButton = wrapper.querySelector('.service-schedule__nav-button--next');

    const swiper = new Swiper(swiperContainer, {
      slidesPerView: 3,
      spaceBetween: 28,
      speed: 600,
      grabCursor: true,
      watchOverflow: true,
      keyboard: {
        enabled: true,
      },
      navigation: {
        prevEl: prevButton,
        nextEl: nextButton,
      },
      breakpoints: {
        0: {
          slidesPerView: 1,
          spaceBetween: 18,
        },
        600: {
          slidesPerView: 1.3,
          spaceBetween: 20,
        },
        768: {
          slidesPerView: 2,
          spaceBetween: 24,
        },
        1024: {
          slidesPerView: 3,
          spaceBetween: 28,
        },
      },
      on: {
        init(swiperInstance) {
          swiperInstance.slides.forEach((slide, index) => {
            slide.style.setProperty('--slide-index', index);
          });
        },
        slideChangeTransitionStart(swiperInstance) {
          swiperInstance.slides.forEach((slide) => {
            slide.classList.add('is-transitioning');
          });
        },
        slideChangeTransitionEnd(swiperInstance) {
          swiperInstance.slides.forEach((slide) => {
            slide.classList.remove('is-transitioning');
          });
        },
      },
    });

    return swiper;
  };

  const init = () => {
    const wrappers = document.querySelectorAll('[data-service-schedule]');
    if (!wrappers.length) {
      return;
    }

    wrappers.forEach((wrapper) => {
      initScheduleSlider(wrapper);
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
  } else {
    init();
  }
})();
