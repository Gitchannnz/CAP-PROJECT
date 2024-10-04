@extends('layouts.app')
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
            background-color: #f2f7fb !important;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

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

        .pt-90 {
            padding-top: 90px !important;
        }

        .pr-6px {
            padding-right: 6px;
            text-transform: uppercase;
        }

        .my-account .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 40px;
            border-bottom: 1px solid;
            padding-bottom: 13px;
        }

        .my-account .wg-box {
            display: -webkit-box;
            display: -moz-box;
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
            padding: 24px;
            flex-direction: column;
            gap: 24px;
            border-radius: 12px;
            background: var(--White);
            box-shadow: 0px 4px 24px 2px rgba(20, 25, 38, 0.05);
        }

        .bg-success {
            background-color: #40c710 !important;
        }

        .bg-danger {
            background-color: #f44032 !important;
        }

        .bg-warning {
            background-color: #f5d700 !important;
            color: #000;
        }

        .table-transaction>tbody>tr:nth-of-type(odd) {
            --bs-table-accent-bg: #fff !important;

        }

        .table-transaction th,
        .table-transaction td {
            padding: 0.625rem 1.5rem .25rem !important;
            color: #000 !important;
        }

        .table> :not(caption)>tr>th {
            padding: 0.625rem 1.5rem .25rem !important;
            background-color: #042444 !important;
        }

        .table-bordered>:not(caption)>*>* {
            border-width: inherit;
            line-height: 32px;
            font-size: 14px;
            border: 1px solid #042444;
            vertical-align: middle;
        }

        .table-striped .image {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            flex-shrink: 0;
            border-radius: 10px;
            overflow: hidden;
        }

        .table-striped td:nth-child(1) {
            min-width: 250px;
            padding-bottom: 7px;
        }

        .pname {
            display: flex;
            gap: 13px;
        }

        .table-bordered> :not(caption)>tr>th,
        .table-bordered> :not(caption)>tr>td {
            border-width: 1px 1px !important;
            border-color: #042444 !important;
        }
    </style>
    <main class="pt-90" style="padding-top: 0px;">
        <div class="mb-4 pb-4"></div>
        <section class="my-account container">
            <h2 class="page-title">Order Details</h2>
            <div class="row">
                <div class="col-lg-2">
                    @include('user.account-nav')
                </div>

                <div class="col-lg-10">
                    <div class="wg-box">
                        <div class="flex items-center justify-between gap10 flex-wrap">
                            <div class="row">
                                <div class="col-6">
                                    <h5>Ordered Details</h5>
                                </div>
                                <div class="col-6 text-right">
                                    <a class="btn btn-sm btn-danger" href="{{ route('user.orders') }}">Back</a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            @if (Session::has('status'))
                                <p class="alert alert-success">{{ Session::get('status') }}</p>
                            @endif
                            <table class="table table-bordered">
                                <tr>
                                    <th>Order No.</th>
                                    <td>{{ $order->order_number }}</td>
                                    <th>Institutional ID</th>
                                    <td>{{ $order->user->institutional_id ?? 'N/A' }}</td>
                                    <th>Order Status</th>
                                    <td colspan="5">
                                        @if ($transaction->order->status == 'delivered')
                                            <span class="badge bg-success">Delivered</span>
                                        @elseif($transaction->order->status == 'canceled')
                                            <span class="badge bg-danger">Canceled</span>
                                        @else
                                            <span class="badge bg-warning">Ordered</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Order Date</th>
                                    <td>{{ $order->created_at }}</td>
                                    <th>Delivered Date</th>
                                    <td>{{ $order->delivered_date ? $order->delivered_date : 'N/A' }}</td>
                                    <th>Canceled Date</th>
                                    <td>{{ $order->canceled_date ? $order->canceled_date : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="wg-box">
                        <div class="flex items-center justify-between gap10 flex-wrap">
                            <div class="wg-filter flex-grow">
                                <h5>Ordered Items</h5>
                            </div>
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
                                        <th class="text-center">Action</th>
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
                                            <!-- <td class="text-center">
                                                                            <div class="list-icon-function view-icon">
                                                                                <div class="item eye">
                                                                                    <i class="icon-eye"></i>
                                                                                </div>
                                                                            </div>
                                                                        </td> -->
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
                        <table class="table table-striped table-bordered table-transaction">
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
                                        @elseif($transaction->status == 'canceled')
                                            <span class="badge bg-dark">Canceled</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        @if ($order->status == 'delivered')
                            <p class="alert alert-info text-right mt-3">This order has been delivered.</p>
                        @elseif($order->status == 'canceled')
                            <p class="alert alert-info text-right mt-3">This order has been canceled.</p>
                        @else
                            <div class="text-right mt-3">
                                <form action="{{ route('user.order.cancel') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="order_id" value="{{ $order->id }}" />
                                    <button type="button" class="btn btn-danger cancel-order">Cancel Order</button>
                                </form>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </section>
    </main>
@endsection
@push('scripts')
    <script>
        $(function() {
            var orderStatus = "{{ $order->status }}";
            if (orderStatus === 'delivered') {
                $('.cancel-order').prop('disabled', true);
            } else {
                $('.cancel-order').on('click', function(e) {
                    e.preventDefault();
                    var form = $(this).closest('form');
                    swal({
                        title: "Are you sure you want to cancel this order?",
                        text: "This action cannot be undone.",
                        type: "warning",
                        buttons: ["No", "Yes"],
                        confirmButtonColor: '#880808'
                    }).then(function(result) {
                        if (result) {
                            form.submit();
                        }
                    });
                });
            }
        });
    </script>
@endpush
