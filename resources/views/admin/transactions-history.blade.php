@extends('layouts.admin')

@section('content')
    <style>
        .page-title {
            border-bottom: 1px solid;
        }

        .col-lg-10 {
            padding: 2rem;
        }

        .table th {
            white-space: nowrap;
            text-align: center;
            vertical-align: middle;
        }

        .table td {
            text-align: center;
            vertical-align: middle;
        }

        .table> :not(caption)>tr>th {
            padding: 0.625rem 1.5rem .625rem !important;
            background-color: #042444 !important;
        }

        .table>tr>td {
            padding: 0.625rem 1.5rem .625rem !important;
        }

        .table-bordered> :not(caption)>tr>th,
        .table-bordered> :not(caption)>tr>td {
            border-width: 1px 1px;
            border-color: #042444;
        }

        .table> :not(:last-child)>tr:last-child>th,
        .table> :not(:last-child)>tr:last-child>td {
            border-bottom-color: #042444 !important;
            border-right-color: #042444 !important;
            color: #fff !important;
        }

        .table> :not(caption)>tr>td {
            padding: .8rem 1rem !important;
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

        .form-search fieldset {
            display: inline-block;
            margin-right: 1rem;
        }

        .form-search .button-submit {
            display: inline-block;
            margin-left: 1rem;
        }

        .flex {
            display: flex;
        }

        .items-center {
            align-items: center;
        }

        .justify-between {
            justify-content: space-between;
        }

        .gap-10 {
            gap: 10px;
        }

        .flex-wrap {
            flex-wrap: wrap;
        }

        .d-flex {
            display: flex;
        }

        .flex-grow {
            flex-grow: 1;
        }

        .button-submit i {
            font-size: 16px;
        }

        .wg-filter {
            display: flex;
            align-items: center;
        }

        .shop-acs__select {
            border: 1px solid #ddd;
            padding: 2rem 5rem;
            border-radius: 5rem;
            font-size: 14px;
            font-weight: bold;
            box-sizing: border-box;
        }
    </style>

    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>TRANSACTIONS HISTORY</h3>
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
                        <div class="text-tiny">Transactions History</div>
                    </li>
                </ul>
            </div>

            <div class="wg-box">
                <div class="flex items-center justify-between gap-10 flex-wrap">
                    <form id="search-form" class="form-search d-flex flex-grow" method="GET"
                        action="{{ route('admin.transactions.history') }}">
                        <div class="wg-filter d-flex flex-grow">
                            <fieldset class="name">
                                <input type="text" placeholder="Search by Order No., User Name, or Institutional ID"
                                    name="search" value="{{ request('search') }}">
                            </fieldset>
                        </div>
                        <div class="wg-filter d-flex flex-wrap">
                            <button type="submit"><i class="icon-search"></i></button>
                            <button type="button" on click="exportTransactions()"
                                class="text-black bg-gray-800 px-5 py-2 rounded-lg hover:ring-4 focus:ring-4 ml-2">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m-3 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1" />
                                        </svg>
                                    </div>
                                    <div>Export Excel</div>
                                </div>
                            </button>

                        </div>
                        <div class="wg-filter d-flex">
                            <fieldset class="status">
                                <select name="status" class="shop-acs__select form-select w-auto border-0 py-0"
                                    aria-label="Filter by Status" onchange="this.form.submit()">
                                    <option value="">All</option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>
                                        Delivered</option>
                                    <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>
                                        Canceled</option>
                                </select>
                            </fieldset>
                        </div>
                    </form>
                </div>
                <div class="wg-table table-all-user">
                    @if ($transactions->isEmpty())
                        <p class="text-center">No Transaction Found</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width:70px">Order No.</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Institutional ID</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Order Date</th>
                                        <th class="text-center">Total Items</th>
                                        <th class="text-center">Delivered On</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        <tr>
                                            <td class="text-center">{{ $transaction->order->order_number }}</td>
                                            <td class="text-center">{{ $transaction->order->user->firstname }}
                                                {{ $transaction->order->user->lastname }}</td>
                                            <td class="text-center">
                                                {{ $transaction->order->user->institutional_id ?? 'N/A' }}
                                            </td>
                                            <td class="text-center">₱{{ $transaction->order->total }}</td>
                                            <td class="text-center">
                                                @if ($transaction->status == 'delivered')
                                                    <span class="badge bg-success">Delivered</span>
                                                @elseif($transaction->status == 'canceled')
                                                    <span class="badge bg-danger">Canceled</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $transaction->created_at }}</td>
                                            <td class="text-center">{{ $transaction->order->orderItems->count() }}</td>
                                            <td class="text-center">{{ $transaction->order->delivered_date }}</td>
                                            <td class="text-center">
                                                <a
                                                    href="{{ route('admin.order.details', ['order_id' => $transaction->order->id]) }}">
                                                    <div class="list-icon-function view-icon">
                                                        <div class="item eye">
                                                            <i class="icon-eye"></i>
                                                        </div>
                                                    </div>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                    {{ $transactions->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    function exportTransactions() {

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('admin.transaction.export') }}';

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);

        const searchInput = document.createElement('input');
        searchInput.type = 'hidden';
        searchInput.name = 'search';
        searchInput.value = '{{ request('search') }}';
        form.appendChild(searchInput);

        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = '{{ request('status') }}';
        form.appendChild(statusInput);

        document.body.appendChild(form);
        form.submit();
    }
</script>
