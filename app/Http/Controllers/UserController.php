<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $middleNameInitial = !empty($user->middlename) ? strtoupper(substr($user->middlename, 0, 1)) . '.' : '';
        $fullName = trim("{$user->firstname} {$middleNameInitial} {$user->lastname}");

        return view('user.index', compact('fullName'));
    }

    public function orders()
    {
        $orders = Order::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->paginate(10);
        return view('user.orders', compact('orders'));
    }

    public function order_details($order_id)
    {
        $order = Order::where('user_id', Auth::user()->id)->where('id', $order_id)->first();
        if ($order) {
            $orderItems = OrderItem::where('order_id', $order->id)->orderBy('id')->paginate(12);
            $transaction = Transaction::where('order_id', $order->id)->first();

            $user = Auth::user();
            $fullName = trim("{$user->firstname} {$user->middlename} {$user->lastname}");

            return view('user.order-details', compact('order', 'orderItems', 'transaction', 'fullName'));
        } else {
            return redirect()->route('login');
        }
    }

public function order_cancel(Request $request)
{
    $order = Order::find($request->order_id);

    if ($order) {
        if ($order->status === 'canceled') {
            return back()->with('status', 'Order is already canceled.');
        }

        $order->status = 'canceled';
        $order->canceled_date = Carbon::now();
        $order->save();

        $transaction = Transaction::where('order_id', $order->id)->first();
        if ($transaction) {
            $transaction->status = 'canceled';
            $transaction->save();
        }

        return back()->with('status', 'Order has been canceled successfully!');
    }

    return back()->with('status', 'Order not found.');
}

            public function about()
    {
        return view('about.index');
    }

    public function settings()
{
    return view('user.settings');
}

public function settingsUpdate(Request $request)
{
    $request->validate([
        'firstname' => 'required|string|max:255',
        'middlename' => 'nullable|string|max:255',
        'lastname' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'old_password' => 'required|string',
        'new_password' => 'nullable|string|min:8|confirmed',
    ]);

    $user = auth()->user();

    if (!Hash::check($request->old_password, $user->password)) {
        return back()->withErrors(['old_password' => 'The provided password does not match your current password.']);
    }

    $user->firstname = $request->firstname;
    $user->middlename = $request->middlename;
    $user->lastname = $request->lastname;
    $user->email = $request->email;

    if ($request->new_password) {
        $user->password = Hash::make($request->new_password);
    }

    $user->save();

    return redirect()->back()->with('success', 'Settings updated successfully.');
}


    
}
