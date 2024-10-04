<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Sale;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;


class OrderController extends Controller
{
    protected $notificationController;

    public function __construct(AdminController $notificationController)
    {
        $this->notificationController = $notificationController;
    }
    public function orders()
    {
        if (!Gate::any(['is-admin', 'is-staff'])) {
            abort(403, 'This action is unauthorized.');
        }

        $orders = Order::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'DESC')
            ->paginate(12);

        return view("admin.orders", compact('orders'));
    }

    public function order_details($order_id)
    {
        if (!Gate::any(['is-admin', 'is-staff'])) {
            abort(403, 'This action is unauthorized.');
        }

        $order = Order::with('user')->find($order_id);

        if (!$order) {
            return redirect()->route('admin.orders')->with('error', 'Order not found.');
        }

        $orderItems = OrderItem::where('order_id', $order_id)->orderBy('id')->paginate(12);
        $transaction = Transaction::where('order_id', $order_id)->first();

        return view("admin.order-details", compact('order', 'orderItems', 'transaction'));
    }

    public function update_order_status(Request $request)
    {
        if (!Gate::any(['is-admin', 'is-staff'])) {
            abort(403, 'This action is unauthorized.');
        }

        $order = Order::find($request->order_id);
        if (!$order) {
            return back()->with('error', 'Order not found.');
        }

        if ($request->order_status == 'delivered') {
            $order->status = 'delivered';
            $order->delivered_date = Carbon::now();

            Transaction::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'user_id' => auth()->id(),
                    'status' => 'delivered',
                ]
            );

            foreach ($order->orderItems as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->quantity -= $item->quantity;
                    $product->save();

                    $priceToUse = $product->sale_price ?? $product->regular_price;
                    $totalAmount = $item->quantity * $priceToUse;

                    $sale = Sale::where('product_id', $product->id)
                        ->where('order_id', $order->id)
                        ->first();

                    if ($sale) {
                        $sale->quantity_sold += $item->quantity;
                        $sale->total_amount += $totalAmount;
                        $sale->save();
                    } else {
                        Sale::create([
                            'product_id' => $product->id,
                            'order_id' => $order->id,
                            'quantity_sold' => $item->quantity,
                            'total_amount' => $totalAmount,
                        ]);
                    }

                }
            }
        } elseif ($request->order_status == 'canceled') {
            $order->status = 'canceled';
            $order->canceled_date = Carbon::now();
        } else {
            $order->status = $request->order_status;
        }

        $order->save();
        $products = Product::all();
        foreach ($products as $product) {
            if ($product->quantity <= $product->critical_level) {
                $adminController = new AdminController();
                $adminController->createLowStockNotification($product->id);
            }
        }

        return back()->with('status', 'Order status updated successfully!');
    }

    public function print_order($id)
    {
        if (!Gate::any(['is-admin', 'is-staff'])) {
            abort(403, 'This action is unauthorized.');
        }

        $order = Order::find($id);
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        $user = $order->user;

        $middleNameInitial = !empty($user->middlename) ? strtoupper(substr($user->middlename, 0, 1)) . '.' : '';
        $fullName = strtoupper(trim("{$user->firstname} {$middleNameInitial} {$user->lastname}"));

        $order->fullName = $fullName;

        $orderItems = $order->orderItems;

        $pdf = PDF::loadView('admin.print-order', compact('order', 'orderItems'));

        return $pdf->stream('receipt.pdf');
    }

    public function transactions_history(Request $request)
    {
        if (!Gate::any(['is-admin', 'is-staff'])) {
            abort(403, 'This action is unauthorized.');
        }

        $search = $request->input('search');

        $transactions = Transaction::with('order.user')
            ->orderBy('created_at', 'desc')
            ->when($search, function ($query, $search) {
                return $query->whereHas('order', function ($q) use ($search) {
                    $q->where('order_number', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($q2) use ($search) {
                            $q2->where('firstname', 'like', '%' . $search . '%')
                                ->orWhere('lastname', 'like', '%' . $search . '%')
                                ->orWhere('institutional_id', 'like', '%' . $search . '%');
                        });
                });
            })
            ->paginate(12);

        return view('admin.transactions-history', compact('transactions'));
    }

}
