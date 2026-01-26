@php
    $changes = $data->getKpiChanges();
    $kpi = $data->kpiSummary;
@endphp

<div class="kpi-grid">
    <div class="kpi-row">
        <div class="kpi-card">
            <div class="kpi-label">{{ __('Megjelenítések') }}:</div>
            <div class="kpi-value">{{ number_format($kpi['impressions'], 0, ',', ' ') }}</div>
            <div class="kpi-change {{ $changes['impressions'] >= 0 ? 'positive' : 'negative' }}">
                {{ $changes['impressions'] >= 0 ? '+' : '' }}{{ number_format($changes['impressions'], 1, ',', ' ') }}% ehhez képest: előző hónap
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">{{ __('Kattintások') }}</div>
            <div class="kpi-value">{{ number_format($kpi['clicks'], 0, ',', ' ') }}</div>
            <div class="kpi-change {{ $changes['clicks'] >= 0 ? 'positive' : 'negative' }}">
                {{ $changes['clicks'] >= 0 ? '+' : '' }}{{ number_format($changes['clicks'], 1, ',', ' ') }}% ehhez képest: előző hónap
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">{{ __('Átl. CPC') }}</div>
            <div class="kpi-value">{{ number_format($kpi['avg_cpc'], 2, ',', ' ') }} Ft</div>
            <div class="kpi-change {{ $changes['avg_cpc'] >= 0 ? 'negative' : 'positive' }}">
                {{ $changes['avg_cpc'] >= 0 ? '+' : '' }}{{ number_format($changes['avg_cpc'], 1, ',', ' ') }}% ehhez képest: előző hónap
            </div>
        </div>
    </div>
    <div class="kpi-row">
        <div class="kpi-card">
            <div class="kpi-label">{{ __('Költség') }}</div>
            <div class="kpi-value">{{ number_format($kpi['cost'], 2, ',', ' ') }} Ft</div>
            <div class="kpi-change {{ $changes['cost'] >= 0 ? 'negative' : 'positive' }}">
                {{ $changes['cost'] >= 0 ? '+' : '' }}{{ number_format($changes['cost'], 1, ',', ' ') }}% ehhez képest: előző hónap
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">{{ __('Konverziók') }}</div>
            <div class="kpi-value">{{ number_format($kpi['conversions'], 0, ',', ' ') }}</div>
            <div class="kpi-change {{ $changes['conversions'] >= 0 ? 'positive' : 'negative' }}">
                {{ $changes['conversions'] >= 0 ? '+' : '' }}{{ number_format($changes['conversions'], 1, ',', ' ') }}% ehhez képest: előző hónap
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">{{ __('Költség/konv.') }}</div>
            <div class="kpi-value">{{ number_format($kpi['cost_per_conversion'], 2, ',', ' ') }} Ft</div>
            <div class="kpi-change {{ $changes['cost_per_conversion'] >= 0 ? 'negative' : 'positive' }}">
                {{ $changes['cost_per_conversion'] >= 0 ? '+' : '' }}{{ number_format($changes['cost_per_conversion'], 1, ',', ' ') }}% ehhez képest: előző hónap
            </div>
        </div>
    </div>
</div>
