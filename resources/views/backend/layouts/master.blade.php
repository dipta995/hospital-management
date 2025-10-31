<!DOCTYPE html>
<html lang="en">

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
    @stack('styles')
</head>

<body>

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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
@stack('scripts')

</body>

</html>
