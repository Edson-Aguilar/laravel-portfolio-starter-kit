const applyThemeMode = () => {
    const mode = localStorage.getItem('theme-mode') || 'light';
    const shouldUseDark = mode === 'dark';

    document.documentElement.classList.remove('dark');
    document.documentElement.classList.toggle('dark', shouldUseDark);
    document.documentElement.dataset.theme = shouldUseDark ? 'dark' : 'light';
};

const applySidebarState = () => {
    document.documentElement.classList.toggle(
        'sidebar-collapsed',
        localStorage.getItem('admin-sidebar-collapsed') === 'true',
    );
};

applyThemeMode();
applySidebarState();

window.toggleThemeMode = () => {
    const isDark = document.documentElement.classList.contains('dark');
    localStorage.setItem('theme-mode', isDark ? 'light' : 'dark');
    document.documentElement.classList.remove('sidebar-open');
    applyThemeMode();
};

window.toggleAdminSidebar = () => {
    document.documentElement.classList.toggle('sidebar-open');
};

window.toggleDesktopSidebar = () => {
    const nextState = ! document.documentElement.classList.contains('sidebar-collapsed');

    localStorage.setItem('admin-sidebar-collapsed', nextState ? 'true' : 'false');
    document.documentElement.classList.toggle('sidebar-collapsed', nextState);
    document.documentElement.classList.remove('sidebar-open');
};
