<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- added asset method on style,body,js and imgs --}}
    <title>{{ config('app.name', 'e-IGP') }}</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="author" content="surfside media" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/animate.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/animation.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('font/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('icon/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="apple-touch-icon-precomposed" href="{{ asset('images/favicon.ico') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/sweetalert.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/custom.css') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>


    @stack('styles') {{-- to render css --}}

</head>
<style>
    .product-item.gap14.mb-10 {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 15px;
        transition: all 0.3s ease;
        padding-right: 5px;
    }

    .product-item .image-no-bg {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        gap: 10px;
        flex-shrink: 0;
        padding: 5px;
        border-radius: 10px;
        background: #EFF4F8;
    }


    #box-content-search li {
        list-style: none;
    }

    #box-content-search .product-item {
        margin-bottom: 10px;
    }
</style>

<body class="body">
    <div id="wrapper">
        <div id="page" class="">
            <div class="layout-wrap">

                <!-- <div id="preload" class="preload-container">
    <div class="preloading">
        <span></span>
    </div>
</div> -->

                <div class="section-menu-left">
                    <div class="box-logo">
                        <a href="{{ route('admin.index') }}" id="site-logo-inner">
                            <img class="" id="logo_header_1" alt=""
                                src="{{ asset('images/logo/logo.png') }}"
                                data-light="{{ asset('images/logo/logo.png') }}"
                                data-dark="{{ asset('images/logo/logo.png') }}">
                        </a>
                        <div class="button-show-hide">
                            <i class="icon-menu-left"></i>
                        </div>
                    </div>
                    <div class="center">
                        <div class="center-item">
                            <div class="center-heading">Home</div>
                            <ul class="menu-list">
                                <li class="menu-item">
                                    <a href="{{ route('admin.index') }}" class="">
                                        <div class="icon"><i class="icon-grid"></i></div>
                                        <div class="text">Dashboard</div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="center-item">
                            <ul class="menu-list">
                                <li class="menu-item has-children">
                                    <a href="javascript:void(0);" class="menu-item-button">
                                        <div class="icon"><i class="icon-shopping-cart"></i></div>
                                        <div class="text">Manage Products</div>
                                    </a>
                                    <ul class="sub-menu">
                                        @can('is-admin')
                                            <li class="sub-menu-item">
                                                <a href="{{ route('admin.product.add') }}" class="">
                                                    <div class="text">Add Product</div>
                                                </a>
                                            </li>
                                        @endcan

                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.products') }}" class="">
                                                <div class="text">All Products</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                @can('is-admin')
                                    <li class="menu-item has-children">
                                        <a href="javascript:void(0);" class="menu-item-button">
                                            <div class="icon"><i class="icon-layers"></i></div>
                                            <div class="text">Manage Brands</div>
                                        </a>
                                        <ul class="sub-menu">
                                            <li class="sub-menu-item">
                                                <a href="{{ route('admin.brand.add') }}" class="">
                                                    <div class="text">New Brand</div>
                                                </a>
                                            </li>
                                            <li class="sub-menu-item">
                                                <a href="{{ route('admin.brands') }}" class="">
                                                    <div class="text">Brands</div>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="menu-item has-children">
                                        <a href="javascript:void(0);" class="menu-item-button">
                                            <div class="icon"><i class="icon-layers"></i></div>
                                            <div class="text">Manage Categories</div>
                                        </a>
                                        <ul class="sub-menu">
                                            <li class="sub-menu-item">
                                                <a href="{{ route('admin.category.add') }}" class="">
                                                    <div class="text">New Category</div>
                                                </a>
                                            </li>
                                            <li class="sub-menu-item">
                                                <a href="{{ route('admin.categories') }}" class="">
                                                    <div class="text">Categories</div>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endcan
                                <li class="menu-item has-children">
                                    <a href="javascript:void(0);" class="menu-item-button">
                                        <div class="icon"><i class="icon-file-plus"></i></div>
                                        <div class="text">Manage Orders</div>
                                    </a>
                                    <ul class="sub-menu">
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.orders') }}" class="">
                                                <div class="text">Orders</div>
                                            </a>
                                        </li>
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.transactions.history') }}" class="">
                                                <div class="text">Transactions History</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                @can('is-admin')
                                    <li class="menu-item">
                                        <a href="{{ route('admin.slides') }}" class="">
                                            <div class="icon"><i class="icon-image"></i></div>
                                            <div class="text">Manage Sliders</div>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="{{ route('admin.staff') }}" class="">
                                            <div class="icon"><i class="icon-user"></i></div>
                                            <div class="text">Manage Staff</div>
                                        </a>
                                    </li>
                                <li class="menu-item">
                                    <a href="{{ route('admin.settings') }}" class="">
                                        <div class="icon"><i class="icon-settings"></i></div>
                                        <div class="text">Settings</div>
                                    </a>
                                </li>
                                 @endcan
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="section-content-right">

                    <div class="header-dashboard">
                        <div class="wrap">
                            <div class="header-left">
                                <a href="index-2.html">
                                    <img class="" id="logo_header_mobile" alt=""
                                        src="{{ asset('images/logo/logo.png') }}"
                                        data-light="{{ asset('images/logo/logo.png') }}"
                                        data-dark="{{ asset('images/logo/logo.png') }}" data-width="154px"
                                        data-height="52px" data-retina="{{ asset('images/logo/logo.png') }}">
                                </a>
                                <div class="button-show-hide">
                                    <i class="icon-menu-left"></i>
                                </div>

                                <form class="form-search flex-grow">
                                    <fieldset class="name">
                                        <input type="text" placeholder="Search here..." class="show-search"
                                            name="name" id="search-input" tabindex="2" value=""
                                            aria-required="true" required="" autocomplete="off">
                                    </fieldset>
                                    <div class="button-submit">
                                        <button class="" type="submit"><i class="icon-search"></i></button>
                                    </div>
                                    <div class="box-content-search">
                                        <ul id="box-content-search"></ul>
                                    </div>
                                </form>
                            </div>
                            <div class="header-grid">
                                <div class="popup-wrap message type-header">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="header-item">
                                                <span class="text-tiny">
                                                    {{ \App\Models\Notification::where('is_read', false)->orderBy('created_at', 'desc')->count() }}
                                                </span>
                                                <i class="icon-bell"></i>
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end has-content"
                                            aria-labelledby="dropdownMenuButton2">
                                            <li>
                                                <h6>Notifications</h6>
                                            </li>
                                            @php
                                                $unreadNotifications = \App\Models\Notification::where('is_read', false)
                                                    ->orderBy('created_at', 'desc')
                                                    ->get();
                                            @endphp
                                            @foreach ($unreadNotifications as $notification)
                                                <li>
                                                    @if ($notification->type === 'product')
                                                        <form
                                                            action="{{ route('notification.read', ['id' => $notification->id]) }}"
                                                            method="POST">
                                                            @csrf
                                                            <a href="#"
                                                                onclick="this.closest('form').submit(); return false;">
                                                                <div class="message-item">
                                                                    <div class="image">
                                                                        <i class="icon-noti-3"
                                                                            style="color: orange;"></i>
                                                                    </div>
                                                                    <div>
                                                                        <div class="body-title-2">
                                                                            {{ $notification->message }}</div>
                                                                        <div class="text-tiny">
                                                                            {{ $notification->created_at->diffForHumans() }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </form>
                                                    @elseif ($notification->type === 'order')
                                                        <form
                                                            action="{{ route('notification.read', ['id' => $notification->id]) }}"
                                                            method="POST">
                                                            @csrf
                                                            <a href="#"
                                                                onclick="this.closest('form').submit(); return false;">
                                                                <div class="message-item">
                                                                    <div class="image">
                                                                        <i class="icon-noti-4"
                                                                            style="color: green;"></i>
                                                                    </div>
                                                                    <div>
                                                                        <div class="body-title-2">
                                                                            {{ $notification->message }}</div>
                                                                        <div class="text-tiny">
                                                                            {{ $notification->created_at->diffForHumans() }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </form>
                                                    @endif
                                                </li>
                                            @endforeach
                                            @if ($unreadNotifications->count() === 0)
                                                <li>No notifications available.</li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                <div class="popup-wrap user type-header">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton3" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="header-user wg-user">
                                                <span class="image">
                                                    <img src="{{ asset('uploads/avatars/' . ($user->avatar ?? 'default-avatar.png')) }}"
                                                        alt="">
                                                </span>
                                                <span class="flex flex-column">
                                                    <span
                                                        class="body-title mb-2">{{ Auth::user()->firstname . ' ' . (Auth::user()->middlename ? substr(Auth::user()->middlename, 0, 1) . '.' : '') . ' ' . Auth::user()->lastname }}</span>
                                                    <span class="text-tiny">{{ Auth::user()->usertype }}</span>
                                                </span>
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end has-content"
                                            aria-labelledby="dropdownMenuButton3">
                                            <li>
                                                <a href="#" class="user-item" data-bs-toggle="modal"
                                                    data-bs-target="#changeProfilePicModal">
                                                    <div class="icon">
                                                        <i class="icon-camera"></i>
                                                    </div>
                                                    <div class="body-title-2">Change Profile Picture</div>
                                                </a>
                                            </li>
                                            {{-- <li>
                                                <a href="#" class="user-item">
                                                    <div class="icon">
                                                        <i class="icon-user"></i>
                                                    </div>
                                                    <div class="body-title-2">Account</div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="user-item">
                                                    <div class="icon">
                                                        <i class="icon-mail"></i>
                                                    </div>
                                                    <div class="body-title-2">Inbox</div>
                                                    <div class="number">27</div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="user-item">
                                                    <div class="icon">
                                                        <i class="icon-file-text"></i>
                                                    </div>
                                                    <div class="body-title-2">Taskboard</div>
                                                </a>
                                            </li> --}}
                                            <li>
                                                <form method="POST" action="{{ route('logout') }}"
                                                    id="logout-form">
                                                    @csrf
                                                    <a href="#" class="user-item"
                                                        onclick="confirmLogout(event)">
                                                        <div class="icon">
                                                            <i class="icon-log-out"></i>
                                                        </div>
                                                        <div class="body-title-2">Log out</div>
                                                    </a>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Change Profile Picture Modal -->
                    <div class="modal fade" id="changeProfilePicModal" tabindex="-1"
                        aria-labelledby="changeProfilePicModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="changeProfilePicModalLabel">Change
                                        Profile Picture</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form action="{{ route('profile.updatePicture') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="profile_picture" class="form-label">Select New
                                                Profile Picture</label>
                                            <input type="file" class="form-control" id="profile_picture"
                                                name="profile_picture" accept="image/*" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save
                                            changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="main-content">
                        @yield('content')

                        <div class="bottom-page">
                            <div class="body-text">Copyright (c) NBSC 2024. All rights reserved.</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery.min.') }}js"></script>
    <script src="{{ asset('js/bootstrap.min.') }}js"></script>
    <script src="{{ asset('js/bootstrap-select.min.') }}js"></script>
    <script src="{{ asset('js/sweetalert.min.') }}js"></script>
    <script src="{{ asset('js/apexcharts/apexcharts.') }}js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmLogout(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Confirm Logout',
                text: "Are you sure you want to log out?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, log out',
                cancelButtonText: 'No, cancel',
                customClass: {
                    popup: 'custom-popup',
                    title: 'custom-title',
                    content: 'custom-content',
                    confirmButton: 'custom-confirm',
                    cancelButton: 'custom-cancel'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>
    <script>
        $(function() {
            $(".form-search").on("submit", function(e) {
                // e.preventDefault();
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

                            $.each(data.orders, function(index, item) {
                                var url =
                                    "{{ route('admin.order.details', ['order_id' => 'order_id']) }}";
                                var link = url.replace('order_id', item.id);

                                $("#box-content-search").append(`
                                    <li>
                                        <ul>
                                            <li class="order-item gap14 mb-10">
                                                <div class="flex items-center justify-between gap20 flex-grow">
                                                    <div class="name">
                                                        <a href="${link}" class="body-text">Order #${item.order_number} - ${item.name}</a>
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
    <script>
        function markAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                })
                .then(response => {
                    if (response.ok) {
                        window.location.reload();
                    }
                });
        }
    </script>
    <script src="{{ asset('js/main.') }}js"></script>

    @stack('scripts') {{-- to render javascript --}}
</body>

</html>
