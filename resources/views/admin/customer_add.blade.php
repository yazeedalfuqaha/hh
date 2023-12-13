@extends('admin.layout.app')

@section('heading', 'Add Customer')

@section('right_top_button')
<a href="{{ route('admin_customer') }}" class="btn btn-primary"><i class="fa fa-eye"></i> View All</a>
@endsection

@section('main_content')
<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin_customerl_store') }}" method="post" enctype="multipart/form-data" >
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <label class="form-label">Photo :</label>
                                    <div>
                                        <input type="file" name="photo" value="{{ old('name') }}">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Name :</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Email :</label>
                                    <input type="text" class="form-control" name="email" value="{{ old('email') }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Password :</label>
                                    <input type="password" class="form-control" name="password" value="{{ old('password') }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Confirm Password :</label>
                                    <input type="password" class="form-control" name="retype_password" value="{{ old('retype_password') }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Phone :</label>
                                    <input type="tel" class="form-control" name="phone" value="{{ old('phone') }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Country :</label>
                                    <input type="text" class="form-control" name="country" value="{{ old('country') }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Address :</label>
                                    <input type="text" class="form-control" name="address" value="{{ old('address') }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label"></label>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    {{-- <form action="{{ route('admin_customer') }}" method="post">
                        @csrf
                        <div class="login-form">
                            <div class="mb-3">
                                <label for="" class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="name">
                                @if($errors->has('name'))
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Email Address</label>
                                <input type="text" class="form-control" name="email">
                                @if($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password">
                                @if($errors->has('password'))
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" name="retype_password">
                                @if($errors->has('retype_password'))
                                    <span class="text-danger">{{ $errors->first('retype_password') }}</span>
                                @endif
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary bg-website">Submit</button>
                            </div>
                            <div class="mb-3">
                                <a href="{{ route('admin_customer') }}" class="primary-color">Existing User? Login Now</a>
                            </div>
                        </div>
                    </form> --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection