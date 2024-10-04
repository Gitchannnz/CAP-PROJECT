@extends('layouts.app')

@section('content')
    <style>
        .cart-total th,
        .cart-total td {
            color: green;
            font-weight: bold;
            font-size: 21px !important;
        }
    </style>

    <main class="pt-90">
        <div class="mb-4 pb-4"></div>
        <section class="shop-checkout container">
            <h2 class="page-title">Order Checkout</h2>
            <div class="checkout-steps">
                <a href="{{ route('cart.index') }}" class="checkout-steps__item active">
                    <span class="checkout-steps__item-number">01</span>
                    <span class="checkout-steps__item-title">
                        <span>Shopping Bag</span>
                        <em>Manage Your Items List</em>
                    </span>
                </a>
                <a href="javascript:void(0)" class="checkout-steps__item active">
                    <span class="checkout-steps__item-number">02</span>
                    <span class="checkout-steps__item-title">
                        <span>Checkout</span>
                        <em>Checkout Your Items List</em>
                    </span>
                </a>
                <a href="javascript:void(0)" class="checkout-steps__item">
                    <span class="checkout-steps__item-number">03</span>
                    <span class="checkout-steps__item-title">
                        <span>Confirmation</span>
                        <em>Review And Submit Your Order</em>
                    </span>
                </a>
            </div>
            <form name="checkout-form" action="{{ route('cart.place.an.order') }}" method="POST">
                @csrf
                <div class="checkout-form">
                    <div class="billing-info__wrapper">
                        <div class="row">
                            <div class="col-6">
                                <h4>DELIVERY DETAILS</h4>
                            </div>
                            <div class="col-6">
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-6">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" name="name" value="{{ $fullName }}">
                                    <label for="name">Full Name *</label>
                                    <span class="text-danger">
                                        @error('name')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating my-3">
                                    <input type="text" class="form-control" name="institutional_id"
                                        value="{{ $user->institutional_id }}">
                                    <label for="institutional_id">Institutional ID *</label>
                                    <span class="text-danger">
                                        @error('institutional_id')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="checkout__totals-wrapper">
                        <div class="sticky-content">
                            <div class="checkout__totals">
                                <h3>Your Order</h3>
                                <table class="checkout-cart-items">
                                    <thead>
                                        <tr>
                                            <th>PRODUCT</th>
                                            <th class="text-right">SUBTOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (Cart::instance('cart')->content() as $item)
                                            <tr>
                                                <td>
                                                    {{ $item->name }} x {{ $item->qty }}
                                                </td>
                                                <td class="text-right">
                                                    ₱{{ $item->subtotal }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <table class="checkout-totals">
                                    <tbody>
                                        <tr class="cart-total">
                                            <th>TOTAL</th>
                                            <td class="text-right">₱{{ Cart::instance('cart')->subtotal() }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="checkout__payment-methods">
                                <div class="policy-text">
                                    After placing your order online, please visit the IGP office to complete your
                                    transaction and pick up
                                    your items. Thank you!
                                </div>
                                <div class="policy-text mt-3">
                                    <div class="policy-text">
                                        Your personal data will be used to process your order, support your experience
                                        throughout this
                                        website, and for other purposes described in our
                                        <a href="terms.html" target="_blank">privacy policy</a>.
                                    </div>
                                </div>
                                <div style="text-align: center; margin-top: 20px;">
                                    <button type="submit" class="btn btn-primary">PLACE ORDER</button>
                                </div>
                            </div>
                        </div>
                    </div>
            </form>
        </section>
    </main>
@endsection
