@if (Auth::user()->backupTasks->isNotEmpty())
    @section('title', __('Overview'))
@else
    @section('title', __('Steps to Get Started'))
@endif
<x-app-layout>
    @if (Auth::user()->backupTasks->isNotEmpty())
        <x-slot name="header">
            {{ __('Overview') }}
        </x-slot>
        <div>
            <div class="mb-6">
                <div x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 1000)"
                     class="flex flex-col sm:flex-row items-center bg-white dark:bg-gray-800/50 rounded-lg shadow-sm p-4">
                    <div class="relative h-16 w-16">
                        <div
                            x-show="!loaded"
                            class="absolute inset-0 bg-gray-200 dark:bg-gray-700 rounded-full animate-pulse"
                        ></div>
                        <img
                            x-show="loaded"
                            x-transition:enter="transition-opacity duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            class="h-16 w-16 rounded-full border-2 border-primary-200 dark:border-primary-700"
                            src="{{ Auth::user()->gravatar('200') }}"
                        />
                    </div>
                    <div class="ml-4 mt-4 sm:mt-0 text-center sm:text-left">
                        <h3 x-show="!loaded" class="h-8 w-48 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></h3>
                        <h3 x-show="loaded" x-transition:enter="transition-opacity duration-300"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            class="font-semibold text-2xl text-gray-900 dark:text-gray-100">
                            {{ \App\Facades\Greeting::auto(Auth::user()->timezone) }}, {{ Auth::user()->first_name }}!
                        </h3>
                        <p x-show="!loaded"
                           class="h-4 w-64 mt-2 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></p>
                        <p x-show="loaded" x-transition:enter="transition-opacity duration-300"
                           x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                           class="text-gray-600 dark:text-gray-400 mt-1">
                            {{ trans_choice(':count backup task has|:count backup tasks have', Auth::user()->backupTasklogCountToday(), ['count' => Auth::user()->backupTasklogCountToday()]) }} {{ __('been run today.') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-chart-card
                    title="{{ __('Monthly Backup Task Activity') }}"
                    description="{{ __('Overview of backup tasks performed each month') }}."
                    icon="hugeicons-clock-01"
                >
                    <div
                        x-data="{ loaded: false }"
                        x-init="setTimeout(() => loaded = true, 1500)"
                    >
                        <div x-show="!loaded" class="h-64 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                        <div x-show="loaded" x-transition:enter="transition-opacity duration-300"
                             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="h-64">
                            <canvas id="totalBackupsPerMonth"></canvas>
                        </div>
                    </div>
                </x-chart-card>
                <x-chart-card
                    title="{{ __('Backup Tasks Categorized by Type') }}"
                    description="{{ __('Distribution of backup tasks across different types') }}."
                    icon="hugeicons-file-02"
                >
                    <div
                        x-data="{ loaded: false }"
                        x-init="setTimeout(() => loaded = true, 1500)"
                    >
                        <div x-show="!loaded" class="h-64 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                        <div x-show="loaded" x-transition:enter="transition-opacity duration-300"
                             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="h-64">
                            <canvas id="backupTasksByType"></canvas>
                        </div>
                    </div>
                </x-chart-card>
            </div>
            <div class="mt-6">
                @livewire('dashboard.upcoming-backup-tasks')
            </div>
        </div>
        <script>
            document.addEventListener('livewire:navigated', function () {
                function createCharts() {
                    const isDarkMode = document.documentElement.classList.contains('dark');
                    const textColor = isDarkMode ? 'rgb(229, 231, 235)' : 'rgb(17, 24, 39)'; // dark:text-gray-200 : text-gray-900
                    const backgroundColor = isDarkMode ? 'rgba(229, 231, 235, 0.24)' : 'rgba(17, 24, 39, 0.24)';

                    const label = '{!! __('Backup Tasks') !!}';
                    const ctx = document.getElementById('totalBackupsPerMonth').getContext('2d');
                    const totalBackupsPerMonth = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: {!! $months !!},
                            datasets: [{
                                label: label,
                                data: {!! $counts !!},
                                borderColor: textColor,
                                backgroundColor: backgroundColor,
                                tension: 0.2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {color: textColor}
                                },
                                y: {
                                    ticks: {color: textColor}
                                }
                            }
                        },
                    });

                    const type = '{!! __('Type') !!}';
                    const ctx2 = document.getElementById('backupTasksByType').getContext('2d');
                    const translations = {
                        'Files': '{!! __('Files') !!}',
                        'Database': '{!! __('Database') !!}'
                    };
                    const labels = {!! json_encode(array_keys($backupTasksCountByType), JSON_THROW_ON_ERROR) !!}
                        .map(label => translations[label] || label)
                        .map(label => label.charAt(0).toUpperCase() + label.slice(1));
                    const backupTasksByType = new Chart(ctx2, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: type,
                                data: {!! json_encode(array_values($backupTasksCountByType), JSON_THROW_ON_ERROR) !!},
                                backgroundColor: isDarkMode
                                    ? ['rgb(55, 65, 81)', 'rgb(75, 85, 99)']  // dark:bg-gray-700, dark:bg-gray-600
                                    : ['rgb(237,254,255)', 'rgb(250,245,255)'],
                                borderColor: isDarkMode
                                    ? ['rgb(107, 114, 128)', 'rgb(156, 163, 175)']  // dark:border-gray-500, dark:border-gray-400
                                    : ['rgb(189,220,223)', 'rgb(192,180,204)'],
                                borderWidth: 0.8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {color: textColor}
                                },
                                y: {
                                    ticks: {color: textColor}
                                }
                            }
                        },
                    });

                    return {totalBackupsPerMonth, backupTasksByType};
                }

                let charts = createCharts();

                window.addEventListener('themeChanged', function (event) {
                    charts.totalBackupsPerMonth.destroy();
                    charts.backupTasksByType.destroy();
                    charts = createCharts();
                });
            });
        </script>
    @else
        <x-slot name="outsideContainer">
            @include('partials.steps-to-get-started.view')
        </x-slot>
    @endif
</x-app-layout>
