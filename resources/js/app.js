import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('theme', {
    mode: localStorage.getItem('theme') || 'system',
    isDark: false,

    init() {
        this._apply();
        this._watchOS();
    },

    set(mode) {
        this.mode = mode;
        localStorage.setItem('theme', mode);
        this._apply();
        this._syncToServer(mode);
    },

    cycle() {
        const modes = ['light', 'dark', 'system'];
        const next = modes[(modes.indexOf(this.mode) + 1) % modes.length];
        this.set(next);
    },

    _apply() {
        if (this.mode === 'system') {
            this.isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        } else {
            this.isDark = this.mode === 'dark';
        }
        document.documentElement.classList.toggle('dark', this.isDark);
    },

    _watchOS() {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (this.mode === 'system') {
                this._apply();
            }
        });
    },

    _syncToServer(mode) {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            window.axios.post('/theme', { theme: mode }).catch(() => {});
        }
    },
});

Alpine.start();
