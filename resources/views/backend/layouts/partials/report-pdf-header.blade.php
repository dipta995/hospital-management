@php
    $logoFile = \App\Models\Setting::get('logo');
    $useDomPdfLogo = ($pdfEngine ?? '') === 'dompdf' || request()->query('export') === 'pdf';
    $logoSrc = $useDomPdfLogo
        ? public_path('images/' . $logoFile)
        : asset('images/' . $logoFile);
@endphp
<table class="rpdf-header">
    <tr>
        <td class="rpdf-logo-cell">
            @if($logoFile)
                <img src="{{ $logoSrc }}" alt="Logo" class="rpdf-logo">
            @endif
        </td>
        <td>
            <div class="rpdf-company-name">{{ \App\Models\Setting::get('company_name') }}</div>
            <div class="rpdf-company-meta">
                {!! \App\Models\Setting::get('address') !!}<br>
                Mobile: {{ \App\Models\Setting::get('phone_one') }}@if(\App\Models\Setting::get('phone_two')), {{ \App\Models\Setting::get('phone_two') }}@endif<br>
                Email: {{ \App\Models\Setting::get('email') }}
            </div>
        </td>
    </tr>
</table>
