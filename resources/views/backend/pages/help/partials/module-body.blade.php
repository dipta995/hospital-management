@php
    $module = $module ?? [];
    $sections = $module['sections'] ?? [];
@endphp

@foreach($sections as $section)
    <section class="doc-section" id="{{ $section['id'] ?? '' }}">
        <h2>{{ $section['title'] ?? '' }}</h2>

        @if(!empty($section['intro']))
            <p>{{ $section['intro'] }}</p>
        @endif

        @if(!empty($section['flow']))
            <div class="doc-flow">
                @foreach($section['flow'] as $i => $step)
                    @if($i > 0)<span class="doc-flow-arrow"><i class="fas fa-chevron-right"></i></span>@endif
                    <span class="doc-flow-item">{{ $step }}</span>
                @endforeach
            </div>
        @endif

        @if(!empty($section['steps']))
            <ol class="doc-steps">
                @foreach($section['steps'] as $step)
                    <li class="doc-step"><div class="doc-step-text">{{ $step }}</div></li>
                @endforeach
            </ol>
        @endif

        @if(!empty($section['fields']))
            <table class="doc-field-table">
                <thead>
                    <tr>
                        <th>{{ __('documentation.labels.field') }}</th>
                        <th>{{ __('documentation.labels.description') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($section['fields'] as $field)
                        <tr>
                            <td><strong>{{ $field['name'] }}</strong></td>
                            <td>{{ $field['desc'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if(!empty($section['tips']))
            @foreach($section['tips'] as $tip)
                <div class="doc-callout doc-callout-tip">
                    <i class="fas fa-lightbulb"></i>
                    <div>{{ $tip }}</div>
                </div>
            @endforeach
        @endif

        @if(!empty($section['warnings']))
            @foreach($section['warnings'] as $warn)
                <div class="doc-callout doc-callout-warn">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>{{ $warn }}</div>
                </div>
            @endforeach
        @endif
    </section>
@endforeach

@if(!empty($module['faqs']))
    <section class="doc-section" id="faq">
        <h2>{{ __('documentation.labels.faq') }}</h2>
        @foreach($module['faqs'] as $faq)
            <div class="doc-faq-item">
                <div class="doc-faq-q" onclick="this.parentElement.classList.toggle('open')">
                    {{ $faq['q'] }}
                    <i class="fas fa-chevron-down small"></i>
                </div>
                <div class="doc-faq-a">{{ $faq['a'] }}</div>
            </div>
        @endforeach
    </section>
@endif
