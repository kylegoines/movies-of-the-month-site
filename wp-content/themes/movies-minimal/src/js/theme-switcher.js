export class ThemeSwitcher {
  static themeClasses = ['theme-default', 'theme-inverse', 'theme-deep-pop'];

  static storageKey = 'movies-theme';

  static defaultTheme = 'theme-default';

  constructor(select) {
    this.select = select instanceof HTMLSelectElement ? select : null;
    this.root = document.documentElement;
  }

  apply(themeName) {
    const safeTheme = ThemeSwitcher.themeClasses.includes(themeName)
      ? themeName
      : ThemeSwitcher.defaultTheme;

    this.root.classList.remove(...ThemeSwitcher.themeClasses);
    this.root.classList.add(safeTheme);

    return safeTheme;
  }

  init() {
    const savedTheme =
      window.localStorage.getItem(ThemeSwitcher.storageKey) ||
      ThemeSwitcher.defaultTheme;
    const activeTheme = this.apply(savedTheme);

    if (!this.select) {
      return;
    }

    this.select.value = activeTheme;
    this.select.addEventListener('change', (event) => {
      const nextTheme = this.apply(event.target.value);
      window.localStorage.setItem(ThemeSwitcher.storageKey, nextTheme);
    });
  }
}
