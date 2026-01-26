<div>
    {{-- Page header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('kpis.index') }}" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $kpi?->name ?? __('KPI') }}</h1>
                    @if($kpi?->description)
                        <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $kpi->description }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $this->getDataSourceColorClass() }}">
                    {{ $this->getDataSourceLabel() }}
                </span>
                <a href="{{ route('kpis.edit', $kpi) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    {{ __('Edit') }}
                </a>
            </div>
        </div>
    </div>

    @if($kpi)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Stats Cards --}}
            <div class="lg:col-span-1 space-y-4">
                {{-- Current Value Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Current Value') }}</h3>
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($stats['current_value'] ?? 0, $kpi->metric_type === 'ctr' || $kpi->metric_type === 'position' ? 2 : 0) }}
                        @if(in_array($kpi->metric_type, ['ctr', 'conversion_rate', 'bounce_rate']))
                            <span class="text-lg">%</span>
                        @endif
                    </p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $this->getMetricLabel() }}</p>
                </div>

                {{-- Comparison Value Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Comparison Value') }}</h3>
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($stats['comparison_value'] ?? 0, $kpi->metric_type === 'ctr' || $kpi->metric_type === 'position' ? 2 : 0) }}
                        @if(in_array($kpi->metric_type, ['ctr', 'conversion_rate', 'bounce_rate']))
                            <span class="text-lg">%</span>
                        @endif
                    </p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Previous Period') }}</p>
                </div>

                {{-- Change Percentage Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Change') }}</h3>
                        @if(($stats['change_percentage'] ?? 0) >= 0)
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                            </svg>
                        @endif
                    </div>
                    <p class="text-3xl font-bold {{ ($stats['change_percentage'] ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ ($stats['change_percentage'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($stats['change_percentage'] ?? 0, 1) }}%
                    </p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('vs Previous Period') }}</p>
                </div>

                {{-- Progress Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Progress') }}</h3>
                        @if($stats['target_achieved'] ?? false)
                            <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format(min($stats['progress'] ?? 0, 100), 1) }}%</p>
                    <div class="mt-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div
                            class="h-2 rounded-full transition-all duration-500 {{ ($stats['progress'] ?? 0) >= 100 ? 'bg-emerald-500' : 'bg-blue-500' }}"
                            style="width: {{ min($stats['progress'] ?? 0, 100) }}%"
                        ></div>
                    </div>
                    @if($stats['days_until_target'] !== null)
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            @if($stats['days_until_target'] > 0)
                                {{ __(':days days until target', ['days' => $stats['days_until_target']]) }}
                            @elseif($stats['days_until_target'] === 0)
                                {{ __('Target date is today') }}
                            @else
                                {{ __(':days days past target', ['days' => abs($stats['days_until_target'])]) }}
                            @endif
                        </p>
                    @endif
                </div>
            </div>

            {{-- Chart and Details --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Chart --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Performance Chart') }}</h3>
                    @if(!empty($chartData['labels']))
                        <div class="h-80">
                            <canvas id="kpiChart" wire:ignore></canvas>
                        </div>
                    @else
                        <div class="flex items-center justify-center h-64 text-gray-500 dark:text-gray-400">
                            <div class="text-center">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <p>{{ __('No data available for chart') }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- KPI Details --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('KPI Details') }}</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Code') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $kpi->code ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Category') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $kpi->category ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Data Source') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $this->getDataSourceLabel() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Source Type') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $kpi->source_type ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Metric Type') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $this->getMetricLabel() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Page/Query/Campaign') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white break-all">{{ $kpi->page_path ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Target Value') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ number_format($kpi->target_value ?? 0) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Goal Type') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $kpi->goal_type?->getLabel() ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('From Date') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $kpi->from_date?->format('Y-m-d') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Target Date') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $kpi->target_date?->format('Y-m-d') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Comparison Start') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $kpi->comparison_start_date?->format('Y-m-d') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Comparison End') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $kpi->comparison_end_date?->format('Y-m-d') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Status') }}</dt>
                            <dd class="mt-1">
                                @if($kpi->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                        {{ __('Active') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/50 dark:text-gray-300">
                                        {{ __('Inactive') }}
                                    </span>
                                @endif
                            </dd>
                        </div>
                        @if($kpi->formula)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Formula') }}</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white font-mono text-sm bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-lg">{{ $kpi->formula }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('KPI Not Found') }}</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('The requested KPI could not be found.') }}</p>
            <a href="{{ route('kpis.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">
                {{ __('Back to KPIs') }}
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:navigated', initChart);
    document.addEventListener('DOMContentLoaded', initChart);

    function initChart() {
        const chartData = @json($chartData);
        const canvas = document.getElementById('kpiChart');

        if (!canvas || !chartData.labels || chartData.labels.length === 0) {
            return;
        }

        if (canvas.chart) {
            canvas.chart.destroy();
        }

        const ctx = canvas.getContext('2d');
        const isDarkMode = document.documentElement.classList.contains('dark');

        canvas.chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: chartData.datasets.map(dataset => ({
                    ...dataset,
                    spanGaps: true,
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: isDarkMode ? '#e5e7eb' : '#374151',
                            usePointStyle: true,
                        }
                    },
                    tooltip: {
                        backgroundColor: isDarkMode ? '#374151' : '#ffffff',
                        titleColor: isDarkMode ? '#e5e7eb' : '#111827',
                        bodyColor: isDarkMode ? '#d1d5db' : '#4b5563',
                        borderColor: isDarkMode ? '#4b5563' : '#e5e7eb',
                        borderWidth: 1,
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: isDarkMode ? '#374151' : '#e5e7eb',
                        },
                        ticks: {
                            color: isDarkMode ? '#9ca3af' : '#6b7280',
                            maxTicksLimit: 10,
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: isDarkMode ? '#374151' : '#e5e7eb',
                        },
                        ticks: {
                            color: isDarkMode ? '#9ca3af' : '#6b7280',
                        }
                    }
                }
            }
        });
    }
</script>
@endpush
