(function () {
  var toggle = document.querySelector('[data-nav-toggle]');
  var nav = document.querySelector('[data-nav]');

  if (!toggle || !nav) {
    return;
  }

  var navId = nav.getAttribute('id') || 'main-nav';
  var media = window.matchMedia('(min-width: 45rem)');

  toggle.setAttribute('aria-controls', navId);
  toggle.setAttribute('aria-expanded', 'false');

  function open() {
    nav.classList.remove('main-nav--hidden');
    toggle.setAttribute('aria-expanded', 'true');
    toggle.classList.add('nav-toggle--open');
  }

  function close() {
    nav.classList.add('main-nav--hidden');
    toggle.setAttribute('aria-expanded', 'false');
    toggle.classList.remove('nav-toggle--open');
  }

  toggle.addEventListener('click', function () {
    if (toggle.getAttribute('aria-expanded') === 'true') {
      close();
    } else {
      open();
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
      close();
      toggle.focus();
    }
  });

  media.addEventListener('change', function (event) {
    if (event.matches) {
      nav.classList.remove('main-nav--hidden');
      toggle.setAttribute('aria-expanded', 'false');
      toggle.classList.remove('nav-toggle--open');
    }
  });

  if (!media.matches) {
    close();
  }
})();
