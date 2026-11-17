(function () {
  var media = window.matchMedia('(max-width: 720px)');

  document.querySelectorAll('details.pack-sidebar__section').forEach(function (details) {
    if (media.matches) {
      details.removeAttribute('open');
    } else {
      details.setAttribute('open', '');
    }
  });
})();
