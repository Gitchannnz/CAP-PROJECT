@extends('layouts.app')
@section('content')
    <style>
        .page-title {
            border-bottom: 1px solid;
        }
    </style>
    <main class="pt-90">
        <div class="mb-4 pb-4"></div>
        <section class="my-account container">
            <h2 class="page-title">My Account</h2>
            <div class="row">
                <div class="col-lg-3">
                    @include('user.account-nav')
                </div>
                <div class="col-lg-9">
                    <div class="page-content my-account__dashboard">
                        <p>Hello! <strong>{{ auth()->user()->firstname }} {{ auth()->user()->lastname }},</strong></p>
                        <p>From your account dashboard you can view your <a class="unerline-link"
                                href="{{route('user.orders')}}">recent orders</a>, <a class="unerline-link"
                                href="{{route('wishlist.index')}}">and view your wishlist.</a></p>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
