@extends('backend.layouts.master')
@section('title')
    Home
@endsection
@push('styles')
    <style>
        .card-custom {
            margin: 20px 0;
        }

        .card-header-custom {
            background-color: #343a40;
            color: white;
        }

        .list-group-item-custom {
            background-color: #f8f9fa;
        }
    </style>
@endpush
@section('admin-content')
    @php
        $userGuard = Auth::guard('admin')->user();
    @endphp
    <div class="main-panel">
        @include('backend.layouts.partials.message')
        <div class="content-wrapper">
            @if ($userGuard->can('dashboards.view'))
            <div class="row">
                <h4>Collection Summery</h4>
                <div class="col-md-6 col-lg-6 col-xl-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="icon-wrap">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div class="flex-right-height">
                                    <p class="font-weight-bold mb-1">Today's Collection</p>
                                    <h2>{{ $todaysTotal->sum('total_amount') }}</h2>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="icon-wrap">
                                    <i class="fas fa-calendar-week"></i>
                                </div>
                                <div class="flex-right-height">
                                    <p class="font-weight-bold mb-1">Last Week's Collection</p>
                                    <h2>{{ $lastWeekTotal->sum('total_amount')-$lastWeekTotal->sum('discount_amount') }}</h2>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="icon-wrap">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="flex-right-height">
                                    <p class="font-weight-bold mb-1">Last Month's Collection</p>
                                    <h2>{{ $lastMonthTotal->sum('total_amount')-$lastMonthTotal->sum('discount_amount') }}</h2>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="icon-wrap">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="flex-right-height">
                                    <p class="font-weight-bold mb-1">Last Year's Collection</p>
                                    <h2>{{ $lastYearTotal->sum('total_amount')-$lastYearTotal->sum('discount_amount') }}</h2>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <h4>Credit Summery</h4>
                <div class="col-md-6 col-lg-6 col-xl-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="icon-wrap">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div class="flex-right-height">
                                    <p class="font-weight-bold mb-1">Today's Credit</p>
                                    <h2>{{ $todaysCost }}</h2>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="icon-wrap">
                                    <i class="fas fa-calendar-week"></i>
                                </div>
                                <div class="flex-right-height">
                                    <p class="font-weight-bold mb-1">Last Week's Credit</p>
                                    <h2>{{$lastWeekCost}}</h2>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="icon-wrap">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="flex-right-height">
                                    <p class="font-weight-bold mb-1">Last Month's Credit</p>
                                    <h2>{{$lastMonthCost}}</h2>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="icon-wrap">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="flex-right-height">
                                    <p class="font-weight-bold mb-1">Last Year's Credit</p>
                                    <h2>{{$lastYearCost}}</h2>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            @else
                <h2 class="text-center">Welcome To Home Page</h2>
            @endif
        </div>
        <!-- content-wrapper ends -->

        <!-- partial -->
    </div>
@endsection

@push('scripts')
@endpush
