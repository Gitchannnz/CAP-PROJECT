<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\Product;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function index()
    {
        $items = Cart::instance('cart')->content();
        return view('cart', compact('items'));
    }

    public function add_to_cart(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $quantity = $request->quantity;

        if ($product->quantity < $quantity) {
            return redirect()->back()->with('error', 'Not enough stock available.');
        }

        Cart::instance('cart')->add($request->id, $request->name, $request->quantity, $request->price)
            ->associate('App\Models\Product');
        return redirect()->back();
    }

    public function increase_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    public function decrease_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty - 1;
        if ($qty < 1)
            $qty = 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    public function remove_item($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    public function empty_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function checkout()
    {
        if (!Auth::check()) {
            return redirect()->route("login");
        }

        if (Cart::instance('cart')->count() <= 0) {
            return redirect()->route('cart.index')
                ->with('message', 'Your cart is empty. Please add items to your cart before proceeding to checkout.');
        }

        $user = Auth::user();
        // Concatenate first name, middle name initial, and last name
        $middleNameInitial = !empty($user->middlename) ? strtoupper(substr($user->middlename, 0, 1)) . '.' : '';
        $fullName = trim("{$user->firstname} {$middleNameInitial} {$user->lastname}");

        return view('checkout', compact('user', 'fullName'));
    }

    public function place_an_order(Request $request)
    {
        $user = Auth::user();

        if ($user->usertype === 'ADMIN' || $user->usertype === 'STAFF') {
            return redirect()->route('home')->with('error', 'Note: As an ADMIN/STAFF, you cannot place orders.');
        }

        $middleNameInitial = !empty($user->middlename) ? strtoupper(substr($user->middlename, 0, 1)) . '.' : '';
        $fullName = trim("{$user->firstname} {$middleNameInitial} {$user->lastname}");

        $this->setAmountForCheckout();

        $checkout = session()->get('checkout', []);
        $subtotal = isset($checkout['subtotal']) ? str_replace(',', '', $checkout['subtotal']) : 0;
        $total = isset($checkout['total']) ? str_replace(',', '', $checkout['total']) : 0;

        $order = new Order();
        $order->user_id = $user->id;
        $order->subtotal = (float) $subtotal;
        $order->total = (float) $total;
        $order->name = $fullName;
        $order->save();

        foreach (Cart::instance('cart')->content() as $item) {
            $orderitem = new OrderItem();
            $orderitem->product_id = $item->id;
            $orderitem->order_id = $order->id;
            $orderitem->price = $item->price;
            $orderitem->quantity = $item->qty;
            $orderitem->save();
        }

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->order_id = $order->id;
        $transaction->status = "pending";
        $transaction->save();

        $adminController = new AdminController();
        $adminController->createNewOrderNotification($order->id);

        Cart::instance('cart')->destroy();
        Session()->forget('checkout');
        Session::put('order_id', $order->id);

        return redirect()->route('cart.order.confirmation');
    }
    
    public function setAmountForCheckout()
    {
        if (Cart::instance('cart')->count() <= 0) {
            Session()->forget('checkout');
            return;
        }

        $subtotal = Cart::instance('cart')->subtotal();
        $total = $subtotal;

        Session()->put('checkout', [
            'subtotal' => $subtotal,
            'total' => $total
        ]);
    }

    public function order_confirmation()
    {
        if (Session::has('order_id')) {
            $order = Order::find(Session::get('order_id'));
            $orderItems = OrderItem::where('order_id', $order->id)->get();
            $user = User::find($order->user_id);
    
            return view('order-confirmation', compact('order', 'orderItems', 'user'));
        }
        return redirect()->route('cart.index');
    }
    
}