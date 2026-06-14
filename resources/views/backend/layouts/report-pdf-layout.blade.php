<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@yield('pdf-title', 'Report')</title>
    @include('backend.layouts.partials.report-pdf-styles')
    @stack('pdf-styles')
</head>
<body>
<div class="rpdf-page">
    @include('backend.layouts.partials.report-pdf-header')

    @hasSection('pdf-actions')
        <div class="rpdf-actions no-print">@yield('pdf-actions')</div>
    @endif

    <div class="rpdf-title-block">
        <h2 class="rpdf-title">@yield('pdf-title', 'Report')</h2>
        @hasSection('pdf-subtitle')
            <p class="rpdf-subtitle">@yield('pdf-subtitle')</p>
        @endif
        @hasSection('pdf-period')
            <div class="rpdf-period">@yield('pdf-period')</div>
        @endif
    </div>

    @hasSection('pdf-summary')
        @yield('pdf-summary')
    @endif

    <div class="rpdf-body">
        @yield('content')
    </div>

    <div class="rpdf-footer">
        Generated on {{ now()->timezone('Asia/Dhaka')->format('d M Y, h:i A') }} (Asia/Dhaka)
    </div>
</div>
@stack('pdf-scripts')
</body>
</html>
