@extends('layouts.admin')
@section('content')
    <style>
        /* sweetalert style */
        .custom-popup {
            font-size: 12px;
        }

        .custom-title {
            font-size: 18px;
        }

        .custom-content {
            font-size: 14px;
        }

        .custom-confirm {
            font-size: 12px;
        }

        .custom-cancel {
            font-size: 12px;
        }

        .table {
            width: 100%;
            table-layout: auto;
        }

        .table td {
            vertical-align: middle;
            padding: 8px;
        }

        .table th {
            text-align: center;
            padding: 8px;
        }

        .list-icon-function {
            display: flex;
            gap: 15px;
        }

        .list-icon-function .item {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            font-size: 16px;
            border-radius: 4px;
            padding: 4px;
        }

        .list-icon-function .item i {
            margin: 0;
        }
    </style>
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>ALL PRODUCTS</h3>
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
                        <div class="text-tiny">All Products</div>
                    </li>
                </ul>
            </div>
            <div class="wg-box">
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <div class="wg-filter flex-grow">
                        <form class="form-search">
                            <fieldset class="name">
                                <input id="search-input" type="text" placeholder="Search here..." name="name"
                                    tabindex="2" required>
                            </fieldset>
                            <div class="button-submit">
                                <button type="submit"><i class="icon-search"></i></button>
                            </div>
                        </form>
                        <ul id="box-content-search">
                        </ul>
                    </div>
                    <a class="tf-button style-1 w208" href="{{ route('admin.product.add') }}"><i class="icon-plus"></i>Add
                        new</a>
                </div>
                <div class="table-responsive">
                    @if (Session::has('status'))
                        <p class="alert alert-success">{{ Session::get('status') }}</p>
                    @endif
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Sale Price</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Featured</th>
                                <th>Stock</th>
                                <th>Quantity</th>
                                <th>Critical Level</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td class="pname">
                                        <div class="image">
                                            <img src="{{ asset('uploads/products/thumbnails/' . $product->image) }}"
                                                alt="{{ $product->name }}" class="image">
                                        </div>
                                        <div class="name">
                                            <a href="#" class="body-title-2">{{ $product->name }}</a>
                                            <div class="text-tiny mt-3">{{ $product->slug }}</div>
                                        </div>
                                    </td>
                                    <td>₱{{ number_format($product->regular_price, 2) }}</td>
                                    <td>{{ $product->sale_price !== null ? '₱' . number_format($product->sale_price, 2) : 'N/A' }}
                                    </td>
                                    <td>{{ $product->SKU }}</td>
                                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                                    <td>{{ $product->brand->name ?? 'N/A' }}</td>
                                    <td>{{ $product->featured == 0 ? 'No' : 'Yes' }}</td>
                                    <td>{{ $product->stock_status }}</td>
                                    <td>{{ $product->quantity }}</td>
                                    <td>{{ $product->critical_level }}</td>
                                    <td class="text-center">
                                        <div class="list-icon-function">
                                            {{-- <a href="#" target="_blank" data-toggle="tooltip" data-placement="bottom"
                                                title="View">
                                                <div class="item eye">
                                                    <i class="icon-eye"></i>
                                                </div>
                                            </a> --}}
                                            <a href="{{ route('admin.product.edit', ['id' => $product->id]) }}"
                                                data-toggle="tooltip" data-placement="bottom" title="Edit">
                                                <div class="item edit">
                                                    <i class="icon-edit-3"></i>
                                                </div>
                                            </a>
                                            <a href="#" data-toggle="tooltip" data-placement="bottom" title="Stock In"
                                                onclick="stockIn({{ $product->id }})">
                                                <div class="item stock" style="color: #FFA500;">
                                                    <i class="icon-box"></i>
                                                </div>
                                            </a>
                                            <a href="#" data-toggle="tooltip" data-placement="bottom"
                                                title="Undo Stock In" onclick="undoStockIn({{ $product->id }})">
                                                <div class="item undo-stock" style="color: #FF0000;">
                                                    <i class="icon-rotate-ccw"></i>
                                                </div>
                                            </a>
                                            @can('is-admin')
                                                <a href="#" data-toggle="tooltip" data-placement="right"
                                                    title="{{ $product->is_active ? 'Disable?' : 'Enable?' }}"
                                                    onclick="toggleStatus({{ $product->id }})">
                                                    <div class="item status-toggle"
                                                        style="display: inline-block; padding: 5px; border-radius: 4px; color: {{ $product->is_active ? '#00ff00' : '#ff0000' }};">
                                                        <i
                                                            class="icon-{{ $product->is_active ? 'check-circle' : 'slash' }}"></i>
                                                    </div>
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">

                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    <script>
        function stockIn(productId) {
            Swal.fire({
                title: 'Stock In',
                input: 'number',
                inputAttributes: {
                    min: 1,
                    step: 1,
                    placeholder: 'Enter quantity'
                },
                showCancelButton: true,
                confirmButtonText: 'Submit',
                showLoaderOnConfirm: true,
                customClass: {
                    popup: 'custom-popup',
                    title: 'custom-title',
                    content: 'custom-content',
                    confirmButton: 'custom-confirm',
                    cancelButton: 'custom-cancel'
                },
                preConfirm: (quantity) => {
                    if (!quantity || quantity <= 0) {
                        Swal.showValidationMessage('Please enter a valid quantity.');
                        return false;
                    } else {
                        return Swal.fire({
                            title: 'Confirm Stock In',
                            text: `Are you sure you want to add ${quantity} units to the stock?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, proceed',
                            cancelButtonText: 'No, cancel',
                            customClass: {
                                popup: 'custom-popup',
                                title: 'custom-title',
                                content: 'custom-content',
                                confirmButton: 'custom-confirm',
                                cancelButton: 'custom-cancel'
                            }
                        }).then((confirmResult) => {
                            if (confirmResult.isConfirmed) {
                                return fetch(`/admin/product/stockin/${productId}`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        quantity: quantity
                                    })
                                }).then(response => {
                                    if (!response.ok) {
                                        throw new Error(
                                            'Failed to update stock. Please try again.');
                                    }
                                    return response.json();
                                }).catch(error => {
                                    Swal.showValidationMessage(
                                        `Request failed: ${error.message}`);
                                });
                            }
                            return null;
                        });
                    }
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.value) {
                    Swal.fire({
                        title: 'Stock Updated',
                        text: 'Stock has been successfully updated.',
                        icon: 'success',
                        customClass: {
                            popup: 'custom-popup',
                            title: 'custom-title',
                            content: 'custom-content',
                            confirmButton: 'custom-confirm'
                        }
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }
    </script>

    <script>
        function undoStockIn(productId) {
            Swal.fire({
                title: 'Undo Stock In',
                text: "Are you sure you want to undo the last stock-in operation?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, undo it!',
                showLoaderOnConfirm: true,
                customClass: {
                    popup: 'custom-popup',
                    title: 'custom-title',
                    content: 'custom-content',
                    confirmButton: 'custom-confirm',
                    cancelButton: 'custom-cancel'
                },
                preConfirm: () => {
                    return fetch(`/admin/product/undo-stockin/${productId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to undo stock-in. Please try again.');
                        }
                        return response.json();
                    }).catch(error => {
                        Swal.showValidationMessage(`Error: ${error.message}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value && result.value.success) {
                        Swal.fire({
                            title: 'Restocking Undone',
                            text: 'The stock has been successfully reverted.',
                            icon: 'success',
                            customClass: {
                                popup: 'custom-popup',
                                title: 'custom-title',
                                content: 'custom-content',
                                confirmButton: 'custom-confirm'
                            }
                        }).then(() => {
                            location.reload();
                        });
                    } else if (result.value && !result.value.success) {
                        Swal.fire({
                            title: 'Undo Failed',
                            text: result.value.message,
                            icon: 'error',
                            customClass: {
                                popup: 'custom-popup',
                                title: 'custom-title',
                                content: 'custom-content',
                                confirmButton: 'custom-confirm'
                            }
                        });
                    }
                }
            });
        }
    </script>

    <script>
        function toggleStatus(productId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Change the product status.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, change it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/product/toggle-status/${productId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(response => {
                        if (response.ok) {
                            Swal.fire('Success!', 'Product status has been updated.', 'success').then(
                                () => {
                                    location.reload();
                                });
                        } else {
                            Swal.fire('Error!', 'There was a problem updating the status.', 'error');
                        }
                    });
                }
            });
        }
    </script>
    <script>
        $(function() {
            $(".form-search").on("submit", function(e) {
                e.preventDefault();
            });

            $("#search-input").on("keyup", function() {
                var searchQuery = $(this).val();
                if (searchQuery.length > 2) {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('admin.search') }}",
                        data: {
                            query: searchQuery
                        },
                        dataType: 'json',
                        success: function(data) {
                            $("#box-content-search").html('');
                            $.each(data.products, function(index, item) {
                                var url =
                                    "{{ route('admin.product.edit', ['id' => 'product_id']) }}";
                                var link = url.replace('product_id', item.id);

                                $("#box-content-search").append(`
                            <li>
                                <ul>
                                    <li class="product-item gap14 mb-10">
                                        <div class="image-no-bg">
                                            <img src="{{ asset('uploads/products/thumbnails') }}/${item.image}" alt="${item.name}">
                                        </div>
                                        <div class="flex items-center justify-between gap20 flex-grow">
                                            <div class="name">
                                                <a href="${link}" class="body-text">${item.name}</a>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="mb-10">
                                        <div class="divider"></div>
                                    </li>
                                </ul>
                            </li>
                        `);
                            });
                        }
                    });
                }
            });
        });
    </script>
@endpush
