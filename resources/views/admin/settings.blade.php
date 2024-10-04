@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Admin Settings</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="#">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Settings</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="col-lg-12">
                <div class="page-content my-account__edit">
                    <div class="my-account__edit-form">
                        <form action="{{ route('admin.settings.update') }}" method="POST" class="form-new-product form-style-1 needs-validation" novalidate="">
                            @csrf

                            <!-- First Name Field -->
                            <fieldset class="name">
                                <div class="body-title">First Name <span class="tf-color-1">*</span></div>
                                <input class="flex-grow" type="text" placeholder="First Name" name="firstname" value="{{ old('firstname', auth()->user()->firstname) }}" required>
                            </fieldset>

                            <!-- Middle Name Field -->
                            <fieldset class="name">
                                <div class="body-title">Middle Name</div>
                                <input class="flex-grow" type="text" placeholder="Middle Name" name="middlename" value="{{ old('middlename', auth()->user()->middlename) }}">
                            </fieldset>

                            <!-- Last Name Field -->
                            <fieldset class="name">
                                <div class="body-title">Last Name <span class="tf-color-1">*</span></div>
                                <input class="flex-grow" type="text" placeholder="Last Name" name="lastname" value="{{ old('lastname', auth()->user()->lastname) }}" required>
                            </fieldset>

                            <!-- Email Address Field -->
                            <fieldset class="name">
                                <div class="body-title">Email Address <span class="tf-color-1">*</span></div>
                                <input class="flex-grow" type="text" placeholder="Email Address" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                            </fieldset>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="my-3">
                                        <h5 class="text-uppercase mb-0">Password Change</h5>
                                    </div>
                                </div>

                                <!-- Old Password -->
                                <div class="col-md-12">
                                    <fieldset class="name">
                                        <div class="body-title pb-3">Old password <span class="tf-color-1">*</span></div>
                                        <input class="flex-grow" type="password" placeholder="Old password" name="old_password" id="old_password" required>
                                        @if ($errors->has('old_password'))
                                            <div class="text-danger">{{ $errors->first('old_password') }}</div>
                                        @endif
                                    </fieldset>
                                </div>

                                <!-- New Password -->
                                <div class="col-md-12">
                                    <fieldset class="name">
                                        <div class="body-title pb-3">New password <span class="tf-color-1">*</span></div>
                                        <input class="flex-grow" type="password" placeholder="New password" name="new_password" id="new_password" required>
                                    </fieldset>
                                </div>

                                <!-- Confirm New Password -->
                                <div class="col-md-12">
                                    <fieldset class="name">
                                        <div class="body-title pb-3">Confirm new password <span class="tf-color-1">*</span></div>
                                        <input class="flex-grow" type="password" placeholder="Confirm new password" name="new_password_confirmation" id="new_password_confirmation" required>
                                        <div class="invalid-feedback">Passwords did not match!</div>
                                    </fieldset>
                                </div>

                                <!-- Save Changes Button -->
                                <div class="col-md-12">
                                    <div class="my-3">
                                        <button type="submit" class="btn btn-primary tf-button w208">Save Changes</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const eyeIcon = document.getElementById(inputId + '_eye');
        if (input.type === 'password') {
            input.type = 'text';
            eyeIcon.classList.remove('icon-eye');
            eyeIcon.classList.add('icon-eye-off');
        } else {
            input.type = 'password';
            eyeIcon.classList.remove('icon-eye-off');
            eyeIcon.classList.add('icon-eye');
        }
    }
</script>

<script>
    @if (session('success'))
        Swal.fire({
            title: 'Success!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    @endif
</script>

@endsection
