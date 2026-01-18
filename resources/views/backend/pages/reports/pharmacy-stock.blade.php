@extends('backend.layouts.master')
@section('title')
    {{ $pageHeader['title'] }}
@endsection

@section('admin-content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">{{ $pageHeader['title'] }}</h4>

                            <p class="card-description">
                                @include('backend.layouts.partials.message')
                            </p>

                            <div class="table-responsive">
                                <table class="table table-striped mt-3">
                                    <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Generic Name</th>
                                        <th>Category</th>
                                        <th>Brand</th>
                                        <th>Unit</th>
                                        <th>Total Purchased</th>
                                        <th>Total Sold</th>
                                        <th>Current Stock</th>
                                        <th>Alert Qty</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($datas as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->generic_name }}</td>
                                            <td>{{ optional($product->category)->name }}</td>
                                            <td>{{ optional($product->brand)->name }}</td>
                                            <td>{{ optional($product->quantityType)->name }}</td>
                                            <td>{{ number_format($product->total_purchased, 2) }}</td>
                                            <td>{{ number_format($product->total_sold, 2) }}</td>
                                            <td>{{ number_format($product->current_stock, 2) }}</td>
                                            <td>{{ $product->alert_qty }}</td>
                                            <td>
                                                @php
                                                    $current = $product->current_stock;
                                                    $alert = (float) $product->alert_qty;
                                                @endphp
                                                @if ($alert && $current <= $alert)
                                                    <span class="badge bg-danger">Low</span>
                                                @elseif ($current <= 0)
                                                    <span class="badge bg-secondary">Out</span>
                                                @else
                                                    <span class="badge bg-success">OK</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">No pharmacy products found.</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
