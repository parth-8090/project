// Theme Toggle JavaScript - resilient + no-flash
// - Works even if localStorage is blocked/disabled
// - Applies html[data-theme] early, before body renders

function safeThemeStorage() {
    try {
        const testKey = '__theme_test__';
        window.localStorage.setItem(testKey, '1');
        window.localStorage.removeItem(testKey);
        return window.localStorage;
    } catch (e) {
        return null;
    }
}

const _themeStorage = safeThemeStorage();

function getSavedTheme() {
    const t = _themeStorage ? _themeStorage.getItem('theme') : null;
    return (t === 'dark' || t === 'light') ? t : null;
}

function saveTheme(theme) {
    if (_themeStorage) {
        try { _themeStorage.setItem('theme', theme); } catch (e) { /* ignore */ }
    }
}

function detectPreferredTheme() {
    try {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) return 'dark';
    } catch (e) { }
    return 'light';
}

function getInitialTheme() {
    const saved = getSavedTheme();
    if (saved) return saved;
    return detectPreferredTheme();
}

// Apply theme ASAP to reduce flash
(function () {
    const theme = getInitialTheme();
    // Apply to <html> as early as possible to avoid FOUC
    document.documentElement.dataset.theme = theme;
    document.documentElement.setAttribute('data-bs-theme', theme);
    document.documentElement.style.colorScheme = theme === 'dark' ? 'dark' : 'light';
    // Mirror class on <html> so CSS that targets html.dark-theme also works
    if (theme === 'dark') document.documentElement.classList.add('dark-theme');
    else document.documentElement.classList.remove('dark-theme');
    // Persist initial choice when no saved theme existed so other tabs match
    if (!getSavedTheme()) saveTheme(theme);
})();

function getThemeToggles() {
    // Support multiple toggles (student navbar + business navbar)
    const toggles = Array.from(document.querySelectorAll('#themeToggle, .theme-toggle'));
    // De-dupe just in case
    return Array.from(new Set(toggles));
}

const DEBUG = false;

function applyTheme(theme) {
    const body = document.body;
    const isDark = theme === 'dark';
    if (DEBUG) console.log('Theme.js: applyTheme called with:', theme);

    if (isDark) {
        body.classList.add('dark-theme');
        document.documentElement.classList.add('dark-theme');
        document.documentElement.dataset.theme = 'dark';
        document.documentElement.setAttribute('data-bs-theme', 'dark');
        document.documentElement.style.colorScheme = 'dark';
        if (DEBUG) console.log('Theme.js: Switched to DARK theme');
    } else {
        body.classList.remove('dark-theme');
        document.documentElement.classList.remove('dark-theme');
        document.documentElement.dataset.theme = 'light';
        document.documentElement.setAttribute('data-bs-theme', 'light');
        document.documentElement.style.colorScheme = 'light';
        if (DEBUG) console.log('Theme.js: Switched to LIGHT theme');
    }

    // Update all toggle icons
    const toggles = getThemeToggles();
    if (DEBUG) console.log('Theme.js: Found', toggles.length, 'toggle buttons');
    toggles.forEach((toggle) => {
        const icon = toggle.querySelector('i');
        if (icon) {
            if (isDark) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        }
        toggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
    });
}

function toggleTheme() {
    const current = document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light';
    const next = current === 'dark' ? 'light' : 'dark';
    saveTheme(next);
    applyTheme(next);
    return next;
}

// expose helpers for other scripts
window.toggleTheme = toggleTheme;
window.applyTheme = applyTheme;

// Full theme toggle functionality
document.addEventListener('DOMContentLoaded', function () {
    const body = document.body;
    console.log('Theme.js: DOMContentLoaded event fired');
    
    // Load saved theme from localStorage
    const savedTheme = getSavedTheme();
    console.log('Theme.js: Loaded saved theme:', savedTheme);
    
    // Apply saved theme once DOM is ready
    applyTheme(savedTheme);

    // Theme toggle: event delegation (works even if navbar re-renders/collapses)
    document.addEventListener('click', function (e) {
        const toggle = e.target && e.target.closest ? e.target.closest('#themeToggle, .theme-toggle') : null;
        if (!toggle) return;

        e.preventDefault();
        console.log('Theme.js: Theme toggle clicked!');

        const isDark = body.classList.contains('dark-theme');
        const nextTheme = isDark ? 'light' : 'dark';
        console.log('Theme.js: Current theme is', isDark ? 'dark' : 'light', '-> switching to', nextTheme);
        saveTheme(nextTheme);
        applyTheme(nextTheme);
    });
});
