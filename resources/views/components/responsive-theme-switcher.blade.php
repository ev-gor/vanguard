<div
    x-data="{
        theme: localStorage.theme || 'system',
        setTheme(newTheme) {
            this.theme = newTheme
            localStorage.theme = newTheme === 'system' ? '' : newTheme
            this.applyTheme()
        },
        applyTheme() {
            if (
                this.theme === 'dark' ||
                (this.theme === 'system' &&
                    window.matchMedia('(prefers-color-scheme: dark)').matches)
            ) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }
        },
    }"
    x-init="applyTheme()"
    @theme-changed.window="setTheme($event.detail)"
    class="border-t border-gray-700 px-4 py-3"
>
    <div class="mb-2 flex items-center justify-between">
        <span class="text-sm font-medium text-gray-300">
            {{ __('Theme') }}
        </span>
        <span
            x-text="
                theme === 'system'
                    ? '{{ __('System') }}'
                    : theme === 'dark'
                      ? '{{ __('Dark') }}'
                      : '{{ __('Light') }}'
            "
            class="text-sm text-gray-400"
        ></span>
    </div>
    <div class="flex space-x-2">
        <button
            @click="setTheme('light')"
            :class="{'bg-primary-600': theme === 'light', 'bg-gray-600': theme !== 'light'}"
            class="flex-1 rounded-md px-3 py-2 text-sm font-medium text-white transition-colors duration-200"
        >
            @svg('hugeicons-sun-02', 'mx-auto h-5 w-5')
            <span class="sr-only">{{ __('Light') }}</span>
        </button>
        <button
            @click="setTheme('dark')"
            :class="{'bg-primary-600': theme === 'dark', 'bg-gray-600': theme !== 'dark'}"
            class="flex-1 rounded-md px-3 py-2 text-sm font-medium text-white transition-colors duration-200"
        >
            @svg('hugeicons-moon-02', 'mx-auto h-5 w-5')
            <span class="sr-only">{{ __('Dark') }}</span>
        </button>
        <button
            @click="setTheme('system')"
            :class="{'bg-primary-600': theme === 'system', 'bg-gray-600': theme !== 'system'}"
            class="flex-1 rounded-md px-3 py-2 text-sm font-medium text-white transition-colors duration-200"
        >
            @svg('hugeicons-computer', 'mx-auto h-5 w-5')
            <span class="sr-only">{{ __('System') }}</span>
        </button>
    </div>
</div>
