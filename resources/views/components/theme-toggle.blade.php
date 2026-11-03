<div class="relative inline-block text-left" id="theme-toggle-dropdown-container">
    <button type="button" onclick="toggleThemeMenu()" id="theme-toggle-btn"
            class="p-2 rounded-xl border border-[var(--color-cinema-border)] hover:bg-[var(--color-cinema-card)] text-[var(--color-cinema-text-muted)] hover:text-white transition-all flex items-center justify-center cursor-pointer"
            title="Đổi giao diện Sáng / Tối / Hệ thống">
        {{-- Icon Dark --}}
        <svg id="theme-icon-dark" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
        {{-- Icon Light --}}
        <svg id="theme-icon-light" class="w-4 h-4 hidden text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9h-1m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        {{-- Icon System --}}
        <svg id="theme-icon-system" class="w-4 h-4 hidden text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
    </button>

    {{-- Menu options --}}
    <div id="theme-menu" class="hidden absolute right-0 mt-2 w-36 glass rounded-xl border border-[var(--color-cinema-border)] shadow-2xl py-1 z-50 text-xs font-medium">
        <button type="button" onclick="setAppTheme('light')" class="w-full text-left px-3.5 py-2 hover:bg-[var(--color-cinema-card)] flex items-center gap-2.5 transition-colors cursor-pointer text-[var(--color-cinema-text)]">
            <span class="text-amber-500 text-sm">☀️</span>
            <span>Giao diện Sáng</span>
        </button>
        <button type="button" onclick="setAppTheme('dark')" class="w-full text-left px-3.5 py-2 hover:bg-[var(--color-cinema-card)] flex items-center gap-2.5 transition-colors cursor-pointer text-[var(--color-cinema-text)]">
            <span class="text-indigo-400 text-sm">🌙</span>
            <span>Giao diện Tối</span>
        </button>
        <button type="button" onclick="setAppTheme('system')" class="w-full text-left px-3.5 py-2 hover:bg-[var(--color-cinema-card)] flex items-center gap-2.5 transition-colors cursor-pointer text-[var(--color-cinema-text)] border-t border-[var(--color-cinema-border)]">
            <span class="text-blue-400 text-sm">💻</span>
            <span>Theo hệ thống</span>
        </button>
    </div>
</div>

<script>
    (function() {
        // Init theme immediately to prevent flashing
        function applyInitialTheme() {
            const stored = localStorage.getItem('cinema_theme') || 'system';
            applyThemeClass(stored);
        }

        window.applyThemeClass = function(theme) {
            const root = document.documentElement;
            const darkIcon = document.getElementById('theme-icon-dark');
            const lightIcon = document.getElementById('theme-icon-light');
            const systemIcon = document.getElementById('theme-icon-system');

            let isDark = true;
            if (theme === 'light') {
                isDark = false;
            } else if (theme === 'dark') {
                isDark = true;
            } else {
                // system
                isDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            }

            if (isDark) {
                root.classList.remove('light');
                root.classList.add('dark');
            } else {
                root.classList.remove('dark');
                root.classList.add('light');
            }

            if (darkIcon && lightIcon && systemIcon) {
                darkIcon.classList.add('hidden');
                lightIcon.classList.add('hidden');
                systemIcon.classList.add('hidden');

                if (theme === 'light') lightIcon.classList.remove('hidden');
                else if (theme === 'dark') darkIcon.classList.remove('hidden');
                else systemIcon.classList.remove('hidden');
            }
        };

        window.toggleThemeMenu = function() {
            const menu = document.getElementById('theme-menu');
            if (menu) menu.classList.toggle('hidden');
        };

        window.setAppTheme = function(theme) {
            localStorage.setItem('cinema_theme', theme);
            applyThemeClass(theme);
            const menu = document.getElementById('theme-menu');
            if (menu) menu.classList.add('hidden');
        };

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const container = document.getElementById('theme-toggle-dropdown-container');
            const menu = document.getElementById('theme-menu');
            if (container && menu && !container.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        // Listen for system changes if system mode selected
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                const stored = localStorage.getItem('cinema_theme') || 'system';
                if (stored === 'system') {
                    applyThemeClass('system');
                }
            });
        }

        applyInitialTheme();
    })();
</script>

