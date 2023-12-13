<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class AdminCustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::get();
        return view('admin.customer', compact('customers'));
    }

    public function change_status($id)
    {
        $customer_data = Customer::where('id',$id)->first();
        if($customer_data->status == 1) {
            $customer_data->status = 0;
        } else {
            $customer_data->status = 1;
        }
        $customer_data->update();
        return redirect()->back()->with('success', 'Status is changed successfully.');
    }

    public function add()
    {
        return view('admin.customer_add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'photo' => 'required|image',
            'name' => 'required',
            'email' => 'required|email|unique:customers',
            'password' => 'required',
            'retype_password' => 'required|same:password',
        ]);

        $ext = $request->file('photo')->extension();
        $final_name = time().'.'.$ext;
        $request->file('photo')->move(public_path('uploads/'),$final_name);
       
        
        $obj = new Customer();
        $obj->photo = $final_name;
        $obj->name = $request->name;
        $obj->email = $request->email;
        $obj->password = $request->password;
        $obj->phone = $request->phone;
        $obj->country = $request->country;
        $obj->address = $request->address;
        $obj->status = 1;
        $obj->save();


        // $obj->name = $request->name;
        // $obj->email = $request->email;
        // $obj->phone = $request->phone;
        // $obj->country = $request->country;
        // $obj->address = $request->address;
        // $obj->state = $request->state;
        // $obj->city = $request->city;
        // $obj->zip = $request->zip;
        

        return redirect()->route('admin_customer')->with('success', 'Customer is added successfully.');
        
    }


    public function edit($id)
    {
        $Customer_data = Customer::where('id',$id)->first();
        return view('admin.customer_edit', compact('Customer_data'));
    }

    public function update(Request $request,$id) 
    {        
        $obj = Customer::where('id',$id)->first();
        if($request->hasFile('photo')) {
            $request->validate([
                'photo' => 'image|mimes:jpg,jpeg,png,gif'
            ]);
            unlink(public_path('uploads/'.$obj->photo));
            $ext = $request->file('photo')->extension();
            $final_name = time().'.'.$ext;
            $request->file('photo')->move(public_path('uploads/'),$final_name);
            $obj->photo = $final_name;
        }

        $obj->name = $request->name;
        $obj->email = $request->email;
        $obj->password = $request->password;
        // $obj->retype_password = $request->retype_password;
        $obj->phone = $request->phone;
        $obj->country = $request->country;
        $obj->address = $request->address;
        $obj->status = 1;

        $obj->update();

        return redirect()->route('admin_customer')->with('success', 'Customer is updated successfully.');
    }

    public function delete($id)
    {
        $single_data = Customer::where('id',$id)->first();
        unlink(public_path('uploads/'.$single_data->photo));
        $single_data->delete();

        return redirect()->back()->with('success', 'Customer is deleted successfully.');
    }
}
