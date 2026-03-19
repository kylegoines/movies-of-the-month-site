import './styles.css';

const themeClasses = [
  'theme-default',
  'theme-inverse',
  'theme-deep-pop',
];

const storageKey = 'movies-minimal-theme';
const defaultTheme = 'theme-default';

function applyTheme(themeName) {
  const root = document.documentElement;
  const safeTheme = themeClasses.includes(themeName) ? themeName : defaultTheme;

  root.classList.remove(...themeClasses);
  root.classList.add(safeTheme);

  return safeTheme;
}

document.addEventListener('DOMContentLoaded', () => {
  const select = document.getElementById('theme-debug-select');
  const savedTheme = window.localStorage.getItem(storageKey) || defaultTheme;
  const activeTheme = applyTheme(savedTheme);

  if (!(select instanceof HTMLSelectElement)) {
    return;
  }

  select.value = activeTheme;
  select.addEventListener('change', (event) => {
    const nextTheme = applyTheme(event.target.value);
    window.localStorage.setItem(storageKey, nextTheme);
  });
});
