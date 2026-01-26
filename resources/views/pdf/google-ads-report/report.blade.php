<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Google Ads | havi jelentés // {{ $data->team->name }}</title>
    <style>
        @page {
            margin: 20mm 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            line-height: 1.4;
            color: #333;
        }

        .page-break {
            page-break-before: always;
        }

        /* Header */
        .header {
            background: #1a73e8;
            color: white;
            padding: 15px 20px;
            margin-bottom: 20px;
            display: table;
            width: 100%;
        }

        .header-content {
            display: table-cell;
            vertical-align: middle;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 2px 0;
        }

        .header .subtitle {
            font-size: 9px;
            opacity: 0.9;
        }

        .header-date {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
        }

        .header-date .date-label {
            font-size: 7px;
            opacity: 0.8;
            text-transform: uppercase;
        }

        .header-date .date-value {
            font-size: 11px;
            font-weight: bold;
            white-space: nowrap;
        }

        /* Section titles */
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            margin: 20px 0 15px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #e0e0e0;
        }

        /* KPI Cards */
        .kpi-grid {
            width: 100%;
            margin-bottom: 25px;
        }

        .kpi-row {
            display: table;
            width: 100%;
            margin-bottom: 12px;
            table-layout: fixed;
        }

        .kpi-card {
            display: table-cell;
            width: 33.33%;
            padding: 12px 15px;
            background: #ffffff;
            border-left: 3px solid #1a73e8;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            vertical-align: top;
        }

        .kpi-card.highlight {
            border-left-color: #4caf50;
            background: rgba(76, 175, 80, 0.08);
        }

        .kpi-card.cost {
            border-left-color: #ff9800;
        }

        .kpi-spacer {
            display: table-cell;
            width: 10px;
        }

        .kpi-label {
            font-size: 8px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .kpi-value {
            font-size: 20px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 4px;
        }

        .kpi-change {
            font-size: 8px;
            padding: 2px 6px;
            border-radius: 3px;
            display: inline-block;
        }

        .kpi-change.positive {
            color: #0d9488;
            background: rgba(13, 148, 136, 0.1);
        }

        .kpi-change.negative {
            color: #dc2626;
            background: rgba(220, 38, 38, 0.1);
        }

        .kpi-change-label {
            font-size: 7px;
            color: #999;
            margin-top: 4px;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8px;
        }

        th {
            background: #f5f5f5;
            font-weight: bold;
            text-align: left;
            padding: 8px 6px;
            border-bottom: 2px solid #e0e0e0;
        }

        th.number {
            text-align: right;
        }

        td {
            padding: 6px;
            border-bottom: 1px solid #e8e8e8;
            vertical-align: middle;
        }

        td.number {
            text-align: right;
        }

        tr:nth-child(even) {
            background: #fafafa;
        }

        .total-row {
            font-weight: bold;
            background: #f0f0f0 !important;
        }

        .total-row td {
            border-top: 2px solid #333;
        }

        /* Bar visualization in tables */
        .bar-cell {
            text-align: right;
        }

        /* Charts */
        .chart-container {
            margin: 15px 0;
        }

        .bar-chart {
            display: table;
            width: 100%;
            height: 80px;
        }

        .bar-item {
            display: table-cell;
            vertical-align: bottom;
            text-align: center;
            padding: 0 2px;
        }

        .bar {
            background: #1a73e8;
            width: 100%;
            min-height: 1px;
        }

        .bar-label {
            font-size: 6px;
            color: #666;
            margin-top: 3px;
        }

        /* Conversions highlight */
        .highlight-cell {
            background: rgba(76, 175, 80, 0.2);
        }

        /* Small print */
        .footnote {
            font-size: 7px;
            color: #999;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    @include('pdf.google-ads-report.partials.header')

    <h2 class="section-title">{{ __('Főbb eredmény mutatók') }}</h2>
    @include('pdf.google-ads-report.partials.kpi-summary')

    <h2 class="section-title">{{ __('Kampányok múlt havi teljesítménye') }}</h2>
    @include('pdf.google-ads-report.partials.campaigns')

    <div class="page-break"></div>
    @include('pdf.google-ads-report.partials.header')

    <h2 class="section-title">{{ __('Kampányok és hirdetéscsoportok múlt havi teljesítménye') }}</h2>
    @include('pdf.google-ads-report.partials.ad-groups')

    <div class="page-break"></div>
    @include('pdf.google-ads-report.partials.header')

    <h2 class="section-title">{{ __('Múlt havi eredmények napi bontásban') }}</h2>
    @include('pdf.google-ads-report.partials.daily-breakdown')

    <div class="page-break"></div>
    @include('pdf.google-ads-report.partials.header')

    <h2 class="section-title">{{ __('Eredmények napszak szerinti bontásban') }}</h2>
    @include('pdf.google-ads-report.partials.hourly-breakdown')

    <div class="page-break"></div>
    @include('pdf.google-ads-report.partials.header')

    <h2 class="section-title">{{ __('Eszközök, nemek és korosztályok szerinti eredmények') }}</h2>
    @include('pdf.google-ads-report.partials.demographics')

    <div class="page-break"></div>
    @include('pdf.google-ads-report.partials.header')

    <h2 class="section-title">{{ __('Megyék szerinti eredmények') }}</h2>
    @include('pdf.google-ads-report.partials.geographic')

    <div class="page-break"></div>
    @include('pdf.google-ads-report.partials.header')

    @include('pdf.google-ads-report.partials.monthly-comparison')

</body>
</html>
