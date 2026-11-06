(function () {
  const root = document.documentElement;
  const storageKey = 'streamrip-theme';
  const media = window.matchMedia('(prefers-color-scheme: light)');

  function readStoredTheme() {
    try {
      const value = window.localStorage.getItem(storageKey);
      return value === 'light' || value === 'dark' ? value : null;
    } catch (error) {
      return null;
    }
  }

  function storeTheme(theme) {
    try {
      window.localStorage.setItem(storageKey, theme);
    } catch (error) {
      return;
    }
  }

  function systemTheme() {
    return media.matches ? 'light' : 'dark';
  }

  function applyTheme(theme) {
    const isLight = theme === 'light';

    root.setAttribute('data-theme', theme);

    document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
      button.setAttribute('aria-label', isLight ? 'Switch to dark theme' : 'Switch to light theme');

      const icon = button.querySelector('.icon');

      if (icon) {
        icon.textContent = isLight ? 'dark_mode' : 'light_mode';
      }
    });
  }

  function currentTheme() {
    return root.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
  }

  function toggleTheme() {
    const next = currentTheme() === 'light' ? 'dark' : 'light';

    storeTheme(next);
    applyTheme(next);
  }

  applyTheme(readStoredTheme() || systemTheme());

  document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
    button.addEventListener('click', toggleTheme);
  });

  function handleSystemChange(event) {
    if (!readStoredTheme()) {
      applyTheme(event.matches ? 'light' : 'dark');
    }
  }

  if (typeof media.addEventListener === 'function') {
    media.addEventListener('change', handleSystemChange);
  } else if (typeof media.addListener === 'function') {
    media.addListener(handleSystemChange);
  }
})();
