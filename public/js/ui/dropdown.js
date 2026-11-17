(function () {
  document.querySelectorAll('[data-dropdown]').forEach(function (dropdown) {
    var trigger = dropdown.querySelector('[data-dropdown-toggle]');
    var panel = dropdown.querySelector('[data-dropdown-panel]');

    if (!trigger || !panel) {
      return;
    }

    function close() {
      panel.setAttribute('hidden', '');
      trigger.setAttribute('aria-expanded', 'false');
    }

    function open() {
      panel.removeAttribute('hidden');
      trigger.setAttribute('aria-expanded', 'true');
    }

    trigger.addEventListener('click', function () {
      if (trigger.getAttribute('aria-expanded') === 'true') {
        close();
      } else {
        open();
      }
    });

    document.addEventListener('click', function (event) {
      if (!dropdown.contains(event.target)) {
        close();
      }
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && trigger.getAttribute('aria-expanded') === 'true') {
        close();
        trigger.focus();
      }
    });

    dropdown.addEventListener('focusout', function (event) {
      if (!dropdown.contains(event.relatedTarget)) {
        close();
      }
    });
  });
})();
