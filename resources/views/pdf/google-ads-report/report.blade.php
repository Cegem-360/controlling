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
            margin: -20mm -15mm 20px -15mm;
            display: table;
            width: calc(100% + 30mm);
        }

        .header-content {
            display: table-cell;
            vertical-align: middle;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }

        .header h1 span {
            font-weight: normal;
        }

        .header-date {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
            font-size: 10px;
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
            margin-bottom: 20px;
        }

        .kpi-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .kpi-card {
            display: table-cell;
            width: 33%;
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            vertical-align: top;
        }

        .kpi-card + .kpi-card {
            margin-left: 10px;
        }

        .kpi-label {
            font-size: 8px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .kpi-value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 3px;
        }

        .kpi-change {
            font-size: 8px;
        }

        .kpi-change.positive {
            color: #0d9488;
        }

        .kpi-change.negative {
            color: #dc2626;
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
            position: relative;
        }

        .bar-bg {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            background: rgba(26, 115, 232, 0.15);
        }

        .bar-value {
            position: relative;
            z-index: 1;
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
