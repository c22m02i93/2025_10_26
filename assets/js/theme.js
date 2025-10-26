/*--------------------------------------------------------------
Theme JS
--------------------------------------------------------------*/

jQuery(function ($) {
  // Close offcanvas on click a, keep .dropdown-menu open (see https://github.com/bootscore/bootscore/discussions/347)
  $('.offcanvas a:not(.dropdown-toggle, .remove_from_cart_button)').on('click', function () {
    $('.offcanvas').offcanvas('hide');
  });

  // Searchform focus
  $('#collapse-search').on('shown.bs.collapse', function () {
    $('.top-nav-search input:first-of-type').trigger('focus');
  });

  // Close collapse if click outside searchform
  $(document).on('click', function (event) {
    if ($(event.target).closest('#collapse-search').length === 0) {
      $('#collapse-search').collapse('hide');
    }
  });

  // Scroll to top Button
  $(window).on('scroll', function () {
    var scroll = $(window).scrollTop();

    if (scroll >= 500) {
      $('.top-button').addClass('visible');
    } else {
      $('.top-button').removeClass('visible');
    }
  });

  // div height, add class to your content
  $('.height-50').css('height', 0.5 * $(window).height());
  $('.height-75').css('height', 0.75 * $(window).height());
  $('.height-85').css('height', 0.85 * $(window).height());
  $('.height-100').css('height', 1.0 * $(window).height());

  const heroSliderElement = document.querySelector('.hram-hero-slider__swiper');

  if (heroSliderElement && typeof Swiper !== 'undefined') {
    const sliderLoop = heroSliderElement.dataset.sliderLoop === 'true';
    const autoplayDelay = parseInt(heroSliderElement.dataset.sliderAutoplay || '0', 10);

    const sliderOptions = {
      loop: sliderLoop,
      speed: 1400,
      parallax: true,
      watchSlidesProgress: true,
      effect: 'fade',
      fadeEffect: {
        crossFade: true
      },
      allowTouchMove: sliderLoop,
      grabCursor: sliderLoop,
      autoplay: autoplayDelay > 0 ? {
        delay: autoplayDelay,
        disableOnInteraction: false,
        pauseOnMouseEnter: true
      } : false
    };

    const heroSlider = new Swiper(heroSliderElement, sliderOptions);

    if (!sliderLoop) {
      heroSlider.allowTouchMove = false;
    }

    const sliderSection = heroSliderElement.closest('.hram-hero-slider');

    if (sliderSection) {
      const parallaxNodes = sliderSection.querySelectorAll('[data-parallax-scroll]');

      if (parallaxNodes.length) {
        let rafId = null;

        const updateParallax = () => {
          const rect = sliderSection.getBoundingClientRect();
          const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
          const progress = Math.min(Math.max((viewportHeight - rect.top) / (viewportHeight + rect.height), 0), 1);

          parallaxNodes.forEach((node) => {
            const intensity = parseFloat(node.getAttribute('data-parallax-scroll')) || 18;
            node.style.transform = `translate3d(0, ${-progress * intensity}px, 0)`;
          });
        };

        const requestParallax = () => {
          if (rafId !== null) {
            return;
          }

          rafId = window.requestAnimationFrame(() => {
            rafId = null;
            updateParallax();
          });
        };

        updateParallax();

        window.addEventListener('scroll', requestParallax, { passive: true });
        window.addEventListener('resize', requestParallax);
      }
    }
  }

  const logoTitleElement = document.querySelector('.logo-title');

  if (logoTitleElement && typeof gsap !== 'undefined' && typeof TextPlugin !== 'undefined') {
    gsap.registerPlugin(TextPlugin);

    const finalText = logoTitleElement.textContent.trim();

    if (finalText.length) {
      gsap.set(logoTitleElement, { opacity: 0 });
      logoTitleElement.textContent = '';

      const timeline = gsap.timeline({ delay: 0.4 });

      timeline.to(logoTitleElement, {
        opacity: 1,
        duration: 0.6,
        ease: 'power1.out'
      }, 0);

      timeline.to(logoTitleElement, {
        text: finalText,
        duration: 2.8,
        ease: 'power1.out'
      }, 0);
    }
  }

}); // jQuery End
