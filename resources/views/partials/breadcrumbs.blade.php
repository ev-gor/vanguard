@php
    $defaultClasses = 'text-gray-500 transition-all duration-200 hover:text-black dark:text-gray-400 dark:hover:text-white';
    $activeClasses = 'font-medium text-gray-700 dark:text-gray-300';
    $separatorClasses = 'mx-2 text-gray-400 dark:text-gray-500';
@endphp

<nav aria-label="Breadcrumb" class="bg-white py-4 dark:bg-gray-900">
    <div class="mx-auto flex max-w-6xl justify-center px-4 sm:px-6 lg:px-8">
        <ol class="flex flex-wrap items-center justify-center text-sm sm:text-base">
            @foreach ($breadcrumbs as $breadcrumb)
                <li class="flex items-center">
                    @if (! is_null($breadcrumb->url) && ! $loop->last)
                        <a href="{{ $breadcrumb->url }}" wire:navigate class="{{ $defaultClasses }} group">
                            @if ($loop->first)
                                <span class="sr-only">Home</span>
                                @svg('hugeicons-home-05', 'h-5 w-5')
                            @else
                                <span class="group-hover:underline">
                                    {{ $breadcrumb->title }}
                                </span>
                            @endif
                        </a>
                    @else
                        <span class="{{ $activeClasses }}" aria-current="page">
                            {{ $breadcrumb->title }}
                        </span>
                    @endif

                    @if (! $loop->last)
                        <svg
                            class="{{ $separatorClasses }} h-5 w-5"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            width="24"
                            height="24"
                            fill="none"
                        >
                            <path
                                d="M9.00005 6C9.00005 6 15 10.4189 15 12C15 13.5812 9 18 9 18"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>
</nav>
