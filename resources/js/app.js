import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('themeApp', () => ({
    themeMode: 'system',
    darkMode: false,

    initTheme() {
        const meta = document.querySelector('meta[name="theme-preference"]');
        const stored = localStorage.getItem('theme');
        this.themeMode = stored || (meta ? meta.content : 'system');
        this.applyTheme();

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (this.themeMode === 'system') this.applyTheme();
        });

        this.$watch('themeMode', (val) => {
            localStorage.setItem('theme', val);
            this.applyTheme();
            this.syncTheme(val);
        });
    },

    applyTheme() {
        if (this.themeMode === 'system') {
            this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
        } else {
            this.darkMode = this.themeMode === 'dark';
        }
        document.documentElement.classList.toggle('dark', this.darkMode);
    },

    setTheme(mode) {
        this.themeMode = mode;
    },

    syncTheme(mode) {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) {
            fetch('/theme', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': meta.content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ theme: mode }),
            }).catch(() => {});
        }
    },
}));

Alpine.start();
