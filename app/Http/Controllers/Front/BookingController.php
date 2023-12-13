<?php

namespace App\Http\Controllers\Front;

use Stripe;
use App\Models\Room;
use App\Models\Order;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Payment;
use App\Models\Customer;
use App\Mail\Websitemail;
use App\Models\BookedRoom;
use App\Models\OrderDetail;
use PayPal\Api\Transaction;
use Illuminate\Http\Request;
use PayPal\Api\PaymentExecution;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{

    public function reservation_submit(Request $request)
    {
        $request->validate([
            'room_id' => 'required',
            'checkin_checkout' => 'required',
            'adult' => 'required'
        ]);

        $dates = explode(' - ', $request->checkin_checkout);
        $checkin_date = $dates[0];
        $checkout_date = $dates[1];

        $d1 = explode('/', $checkin_date);
        $d2 = explode('/', $checkout_date);
        $d1_new = $d1[2] . '-' . $d1[1] . '-' . $d1[0];
        $d2_new = $d2[2] . '-' . $d2[1] . '-' . $d2[0];
        $t1 = strtotime($d1_new);
        $t2 = strtotime($d2_new);

        $cnt = 1;
        while (1) {
            if ($t1 >= $t2) {
                break;
            }
            $single_date = date('d/m/Y', $t1);
            $total_already_booked_rooms = BookedRoom::where('booking_date', $single_date)->where('room_id', $request->room_id)->count();

            $arr = Room::where('id', $request->room_id)->first();
            $total_allowed_rooms = $arr->total_rooms;

            if ($total_already_booked_rooms == $total_allowed_rooms) {
                $cnt = 0;
                break;
            }
            $t1 = strtotime('+1 day', $t1);
        }

        if ($cnt == 0) {
            return redirect()->back()->with('error', 'Maximum number of this room is already booked');
        }

        session()->push('reservation_room_id', $request->room_id);
        session()->push('reservation_checkin_date', $checkin_date);
        session()->push('reservation_checkout_date', $checkout_date);
        session()->push('reservation_adult', $request->adult);
        session()->push('reservation_children', $request->children);

        return redirect()->back()->with('success', 'Room is added to the reservation successfully.');
    }

    public function reservation_view()
    {
        return view('front.reservation');
    }

    public function reservation_delete($id)
    {
        $arr_reservation_room_id = array();
        $i = 0;
        foreach (session()->get('reservation_room_id') as $value) {
            $arr_reservation_room_id[$i] = $value;
            $i++;
        }

        $arr_reservation_checkin_date = array();
        $i = 0;
        foreach (session()->get('reservation_checkin_date') as $value) {
            $arr_reservation_checkin_date[$i] = $value;
            $i++;
        }

        $arr_reservation_checkout_date = array();
        $i = 0;
        foreach (session()->get('reservation_checkout_date') as $value) {
            $arr_reservation_checkout_date[$i] = $value;
            $i++;
        }

        $arr_reservation_adult = array();
        $i = 0;
        foreach (session()->get('reservation_adult') as $value) {
            $arr_reservation_adult[$i] = $value;
            $i++;
        }

        $arr_reservation_children = array();
        $i = 0;
        foreach (session()->get('reservation_children') as $value) {
            $arr_reservation_children[$i] = $value;
            $i++;
        }

        session()->forget('reservation_room_id');
        session()->forget('reservation_checkin_date');
        session()->forget('reservation_checkout_date');
        session()->forget('reservation_adult');
        session()->forget('reservation_children');

        for ($i = 0; $i < count($arr_reservation_room_id); $i++) {
            if ($arr_reservation_room_id[$i] == $id) {
                continue;
            } else {
                session()->push('reservation_room_id', $arr_reservation_room_id[$i]);
                session()->push('reservation_checkin_date', $arr_reservation_checkin_date[$i]);
                session()->push('reservation_checkout_date', $arr_reservation_checkout_date[$i]);
                session()->push('reservation_adult', $arr_reservation_adult[$i]);
                session()->push('reservation_children', $arr_reservation_children[$i]);
            }
        }

        return redirect()->back()->with('success', 'reservation item is deleted.');
    }


    public function checkout()
    {
        if (!Auth::guard('customer')->check()) {
            return redirect()->back()->with('error', 'You must have to login in order to checkout');
        }

        if (!session()->has('reservation_room_id')) {
            return redirect()->back()->with('error', 'There is no item in the reservation');
        }

        return view('front.checkout');
    }

    public function payment(Request $request)
    {
        if (!Auth::guard('customer')->check()) {
            return redirect()->back()->with('error', 'You must have to login in order to checkout');
        }

        if (!session()->has('reservation_room_id')) {
            return redirect()->back()->with('error', 'There is no item in the reservation');
        }

        $request->validate([
            'billing_name' => 'required',
            'billing_email' => 'required|email',
            'billing_phone' => 'required',
            'billing_country' => 'required',
            'billing_address' => 'required',
            'billing_state' => 'required',
            'billing_city' => 'required',
            'billing_zip' => 'required'
        ]);

        session()->put('billing_name', $request->billing_name);
        session()->put('billing_email', $request->billing_email);
        session()->put('billing_phone', $request->billing_phone);
        session()->put('billing_country', $request->billing_country);
        session()->put('billing_address', $request->billing_address);
        session()->put('billing_state', $request->billing_state);
        session()->put('billing_city', $request->billing_city);
        session()->put('billing_zip', $request->billing_zip);

        return view('front.payment');
    }

     

    public function stripe(Request $request, $final_price)
    {
        $stripe_secret_key = 'sk_test_51LT28GF67T3XLjgL8ICWowviN9gL7cXzOr1hPOEVX94aizsO58jdO1CtIBpo583748yVPzAV46pivFolrjqZddSx00PSAfpyff';
        $cents = $final_price * 100;
        Stripe\Stripe::setApiKey($stripe_secret_key);
        $response = Stripe\Charge::create([
            "amount" => $cents,
            "currency" => "usd",
            "source" => $request->stripeToken,
            "description" => env('APP_NAME')
        ]);

        $responseJson = $response->jsonSerialize();
        $transaction_id = $responseJson['balance_transaction'];
        $last_4 = $responseJson['payment_method_details']['card']['last4'];

        $order_no = time();

        $statement = DB::select("SHOW TABLE STATUS LIKE 'orders'");
        $ai_id = $statement[0]->Auto_increment;

        $obj = new Order();
        $obj->customer_id = Auth::guard('customer')->user()->id;
        $obj->order_no = $order_no;
        $obj->transaction_id = $transaction_id;
        $obj->payment_method = 'Stripe';
        $obj->card_last_digit = $last_4;
        $obj->paid_amount = $final_price;
        $obj->booking_date = date('d/m/Y');
        $obj->status = 'Completed';
        $obj->save();

        $arr_reservation_room_id = array();
        $i = 0;
        foreach (session()->get('reservation_room_id') as $value) {
            $arr_reservation_room_id[$i] = $value;
            $i++;
        }

        $arr_reservation_checkin_date = array();
        $i = 0;
        foreach (session()->get('reservation_checkin_date') as $value) {
            $arr_reservation_checkin_date[$i] = $value;
            $i++;
        }

        $arr_reservation_checkout_date = array();
        $i = 0;
        foreach (session()->get('reservation_checkout_date') as $value) {
            $arr_reservation_checkout_date[$i] = $value;
            $i++;
        }

        $arr_reservation_adult = array();
        $i = 0;
        foreach (session()->get('reservation_adult') as $value) {
            $arr_reservation_adult[$i] = $value;
            $i++;
        }

        $arr_reservation_children = array();
        $i = 0;
        foreach (session()->get('reservation_children') as $value) {
            $arr_reservation_children[$i] = $value;
            $i++;
        }

        for ($i = 0; $i < count($arr_reservation_room_id); $i++) {
            $r_info = Room::where('id', $arr_reservation_room_id[$i])->first();
            $d1 = explode('/', $arr_reservation_checkin_date[$i]);
            $d2 = explode('/', $arr_reservation_checkout_date[$i]);
            $d1_new = $d1[2] . '-' . $d1[1] . '-' . $d1[0];
            $d2_new = $d2[2] . '-' . $d2[1] . '-' . $d2[0];
            $t1 = strtotime($d1_new);
            $t2 = strtotime($d2_new);
            $diff = ($t2 - $t1) / 60 / 60 / 24;
            $sub = $r_info->price * $diff;

            $obj = new OrderDetail();
            $obj->order_id = $ai_id;
            $obj->room_id = $arr_reservation_room_id[$i];
            $obj->order_no = $order_no;
            $obj->checkin_date = $arr_reservation_checkin_date[$i];
            $obj->checkout_date = $arr_reservation_checkout_date[$i];
            $obj->adult = $arr_reservation_adult[$i];
            $obj->children = $arr_reservation_children[$i];
            $obj->subtotal = $sub;
            $obj->save();

            while (1) {
                if ($t1 >= $t2) {
                    break;
                }

                $obj = new BookedRoom();
                $obj->booking_date = date('d/m/Y', $t1);
                $obj->order_no = $order_no;
                $obj->room_id = $arr_reservation_room_id[$i];
                $obj->save();

                $t1 = strtotime('+1 day', $t1);
            }
        }

        $subject = 'New Order';
        $message = 'You have made an order for hotel booking. The booking information is given below: <br>';
        $message .= '<br>Order No: ' . $order_no;
        $message .= '<br>Transaction Id: ' . $transaction_id;
        $message .= '<br>Payment Method: Stripe';
        $message .= '<br>Paid Amount: ' . $final_price;
        $message .= '<br>Booking Date: ' . date('d/m/Y') . '<br>';

        for ($i = 0; $i < count($arr_reservation_room_id); $i++) {

            $r_info = Room::where('id', $arr_reservation_room_id[$i])->first();

            $message .= '<br>Room Name: ' . $r_info->name;
            $message .= '<br>Price Per Night: $' . $r_info->price;
            $message .= '<br>Checkin Date: ' . $arr_reservation_checkin_date[$i];
            $message .= '<br>Checkout Date: ' . $arr_reservation_checkout_date[$i];
            $message .= '<br>Adult: ' . $arr_reservation_adult[$i];
            $message .= '<br>Children: ' . $arr_reservation_children[$i] . '<br>';
        }

        session()->forget('reservation_room_id');
        session()->forget('reservation_checkin_date');
        session()->forget('reservation_checkout_date');
        session()->forget('reservation_adult');
        session()->forget('reservation_children');

        return redirect()->route('home')->with('success', 'Payment is successful');

    }
    public function processPayment(Request $request)
    {
        
        $request->validate([
            'payment_method' => 'required', 
        ]);
        session()->forget('reservation_room_id');
        session()->forget('reservation_checkin_date');
        session()->forget('reservation_checkout_date');
        session()->forget('reservation_adult');
        session()->forget('reservation_children');
        return redirect()->route('home')->with('success', 'Payment is successful');
    }



    
}


