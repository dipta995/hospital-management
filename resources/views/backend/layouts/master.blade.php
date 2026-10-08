<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <!-- Title Meta -->
    <meta charset="utf-8" />
    <title>@yield('title', 'Diagnosis')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully responsive premium admin dashboard template" />
    <meta name="author" content="Techzaa" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    @include('backend.layouts.partials.style')
    @include('backend.layouts.partials.subscription-styles')
    @stack('styles')
</head>

<body>

@php
    $subscriptionMeta = $subscriptionMeta ?? null;
@endphp

<!-- START Wrapper -->
<div class="wrapper">

    <!-- ========== Topbar Start ========== -->
    @include('backend.layouts.partials.navbar')
    <!-- ========== Topbar End ========== -->

    <!-- ========== App Menu Start ========== -->
    @include('backend.layouts.partials.sidebar')
    <!-- ========== App Menu End ========== -->

    <!-- ==================================================== -->
    <!-- Start right Content here -->
    <!-- ==================================================== -->
    <div class="page-content">
        @include('backend.layouts.partials.subscription-banner')

        <!-- Start Container Fluid -->
        @yield('admin-content')
        <!-- End Container Fluid -->

        <!-- ========== Footer Start ========== -->
       @include('backend.layouts.partials.footer')
        <!-- ========== Footer End ========== -->

    </div>
    <!-- ==================================================== -->
    <!-- End Page Content -->
    <!-- ==================================================== -->

</div>
@include('backend.layouts.partials.script')
<script>
    (function () {
        var expiredUrl = @json(route('admin.subscriptions.index'));
        var redirected = false;
        var goExpired = function (url) {
            if (redirected) return;
            redirected = true;
            window.location.href = url || expiredUrl;
        };

        if (window.fetch) {
            var nativeFetch = window.fetch.bind(window);
            window.fetch = function () {
                return nativeFetch.apply(null, arguments).then(function (response) {
                    if (response.status === 402) {
                        response.clone().json().then(function (data) { goExpired(data && data.redirect); }, function () { goExpired(); });
                    }
                    return response;
                });
            };
        }

        if (window.jQuery) {
            jQuery(document).ajaxError(function (event, xhr) {
                if (xhr && xhr.status === 402) {
                    goExpired(xhr.responseJSON && xhr.responseJSON.redirect);
                }
            });
        }
    })();
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
@include('backend.layouts.partials.auth-feedback')
@if($subscriptionMeta && !empty($subscriptionMeta['show_popup']) && empty($subscriptionMeta['expired']))
    @include('backend.layouts.partials.subscription-reminder-popup')
@endif
@stack('scripts')
@include('backend.layouts.partials.ai-chat-widget')

</body>

</html>
