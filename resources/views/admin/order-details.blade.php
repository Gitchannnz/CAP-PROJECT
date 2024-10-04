@extends('layouts.admin')
@section('content')
    <style>
        .table th {
            white-space: nowrap;
            text-align: center;
            vertical-align: middle;
            background-color: #042444 !important;
            color: #fff !important;
        }

        .table td {
            text-align: center;
            vertical-align: middle;
        }

        .table>:not(:last-child)>:last-child>* {
            background-color: #042444 !important;
            color: #fff !important;
        }

        .wg-box {
            margin-bottom: 20px;
            padding: 20px;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);

            .status-message {
                margin-top: 10px;
                font-size: 1rem;
            }

            .flex-wrap {
                flex-wrap: wrap;
            }

            .mb-27 {
                margin-bottom: 27px;
            }
    </style>
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>ORDER DETAILS</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Order Details</div>
                    </li>
                </ul>
            </div>
            <div class="wg-box">
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <div class="wg-filter flex-grow">
                        <h5>Ordered Details</h5>
                    </div>
                    <a class="tf-button style-1 w208" href="{{ route('admin.orders') }}">Back</a>
                </div>
                <div class="table-responsive">
                    @if (Session::has('status'))
                        <p class="alert alert-success">{{ Session::get('status') }}</p>
                    @endif
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th>Order No.</th>
                            <td>{{ $order->order_number }}</td>
                            <th>Institutional ID</th>
                            <td>{{ $order->user->institutional_id ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Delivered Date</th>
                            <td>{{ $order->delivered_date ? $order->delivered_date : 'N/A' }}</td>
                            <th>Canceled Date</th>
                            <td>{{ $order->canceled_date ? $order->canceled_date : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Order Status</th>
                            <td>
                                @if ($order->status == 'delivered')
                                    <span class="badge bg-success">Order Delivered</span>
                                @elseif($order->status == 'canceled')
                                    <span class="badge bg-danger">Order Cancelled</span>
                                @else
                                    <span class="badge bg-warning">Pending Order</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="wg-box">
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <div class="wg-filter flex-grow">
                        <h5>Ordered Items</h5>
                    </div>
                    <a class="tf-button style-1 w208" href="{{ route('admin.print.order', $order->id) }}">Print</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-center">SKU</th>
                                <th class="text-center">Category</th>
                                <th class="text-center">Brand</th>
                                <th class="text-center">Options</th>
                                <th class="text-center">Return Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orderItems as $item)
                                <tr>
                                    <td class="pname">
                                        <div class="image">
                                            <img src="{{ asset('uploads/products/thumbnails') }}/{{ $item->product->image }}"
                                                alt="{{ $item->product->name }}" class="image">
                                        </div>
                                        <div class="name">
                                            <a href="{{ route('shop.product.details', ['product_slug' => $item->product->slug]) }}"
                                                target="_blank" class="body-title-2">{{ $item->product->name }}</a>
                                        </div>
                                    </td>
                                    <td class="text-center">₱{{ $item->price }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-center">{{ $item->product->SKU }}</td>
                                    <td class="text-center">{{ $item->product->category->name }}</td>
                                    <td class="text-center">{{ $item->product->brand->name }}</td>
                                    <td class="text-center">{{ $item->options }}</td>
                                    <td class="text-center">{{ $item->rstatus == 0 ? 'NO' : 'YES' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                    {{ $orderItems->links('pagination::bootstrap-5') }}
                </div>
            </div>
            <div class="wg-box mt-5">
                <h5>Transactions</h5>
                <table class="table table-bordered table-transaction">
                    <tbody>
                        <tr>
                            <th>Total</th>
                            <td>₱{{ $transaction->order->total }}</td>
                            <th>Status</th>
                            <td>
                                @if ($transaction->status == 'delivered')
                                    <span class="badge bg-success">Delivered</span>
                                @elseif($transaction->status == 'declined')
                                    <span class="badge bg-danger">Declined</span>
                                @elseif($transaction->status == 'refunded')
                                    <span class="badge bg-secondary">Refunded</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="wg-box mt-5">
                <h5>Update Order Status</h5>
                @if ($order->status == 'delivered')
                    <p class="status-message alert alert-info">This order has been delivered. No further actions are needed.
                    </p>
                @elseif($order->status == 'canceled')
                    <p class="status-message alert alert-info">This order has been canceled by the customer.</p>
                @else
                    <form action="{{ route('admin.order.status.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="order_id" value="{{ $order->id }}" />
                        <div class="row">
                            <div class="col-md-3">
                                <div class="select">
                                    <select id="order_status" name="order_status">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending
                                        </option>
                                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>
                                            Delivered
                                        </option>
                                        <option value="canceled" {{ $order->status == 'canceled' ? 'selected' : '' }}>
                                            Canceled
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary tf-button w208">Update</button>
                            </div>
                        </div>
                    </form>
                    <!-- <p class="status-message alert alert-info mt-2">You can update the status of this order.</p> -->
                @endif
            </div>
        </div>
    </div>
@endsection
