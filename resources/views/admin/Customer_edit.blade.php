@extends('admin.layout.app')

@section('heading', 'Edit Customer')

@section('right_top_button')
<a href="{{ route('admin_customer') }}" class="btn btn-primary"><i class="fa fa-eye"></i> View All</a>
@endsection

@section('main_content')
<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin_customer_update',$Customer_data->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        {{-- <div class="row">
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <label class="form-label">Existing Photo</label>
                                    <div>
                                        <img src="{{ asset('uploads/'.$Customer_data->photo) }}" alt="" class="w_200">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Change Photo</label>
                                    <div>
                                        <input type="file" name="photo">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Name *</label>
                                    <input type="text" class="form-control" name="name" value="{{ $Customer_data->name }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">email *</label>
                                    <input type="text" class="form-control" name="designation" value="{{ $Customer_data->email }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">password *</label>
                                    <input type="text" name="password" class="form-control"  value="{{ $Customer_data->password }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">password *</label>
                                    <input type="text" name="password" class="form-control"  value="{{ $Customer_data->password }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">password *</label>
                                    <input type="text" name="password" class="form-control"  value="{{ $Customer_data->password }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label"></label>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </div> --}}
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Existing Photo</label>
                                <div>
                                    <img src="{{ asset('uploads/'.$Customer_data->photo) }}" alt="" class="w_200">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Change Photo</label>
                                    <div>
                                        <input type="file" name="photo">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label class="form-label">Name *</label>
                                            <input type="text" class="form-control" name="name" value="{{ $Customer_data->name }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label class="form-label">Email *</label>
                                            <input type="text" class="form-control" name="email" value="{{ $Customer_data->email }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label class="form-label">Phone</label>
                                            <input type="text" class="form-control" name="phone" value="{{ $Customer_data->phone }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label class="form-label">Country</label>
                                            <input type="text" class="form-control" name="country" value="{{ $Customer_data->country }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label class="form-label">Address</label>
                                            <input type="text" class="form-control" name="address" value="{{ $Customer_data->address }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label class="form-label">State</label>
                                            <input type="text" class="form-control" name="state" value="ss">
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label class="form-label">City</label>
                                            <input type="text" class="form-control" name="city" value="{{ $Customer_data->name }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label class="form-label">Zip</label>
                                            <input type="text" class="form-control" name="zip" value="{{ $Customer_data->name }}">
                                        </div>
                                    </div>
                                </div> --}}

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label class="form-label">Password</label>
                                            <input type="password" class="form-control" name="password">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label class="form-label">Retype Password</label>
                                            <input type="password" class="form-control" name="retype_password">
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                                
                                <div class="mb-4">
                                    <label class="form-label"></label>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection