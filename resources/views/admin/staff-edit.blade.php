@extends('layouts.admin')

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="wg-box">
                <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                    <h3>Edit Staff Account</h3>
                    <a class="tf-button style-1 w208" href="{{ route('admin.staff') }}">Back</a>
                </div>
                <div>
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @elseif(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <form method="POST" action="{{ route('admin.staff.update', $user->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="firstname">First Name</label>
                                <input type="text" name="firstname" value="{{ old('firstname', $user->firstname) }}"
                                    class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="lastname">Last Name</label>
                                <input type="text" name="lastname" value="{{ old('lastname', $user->lastname) }}"
                                    class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password (Leave blank to keep current)</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Update Account</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
