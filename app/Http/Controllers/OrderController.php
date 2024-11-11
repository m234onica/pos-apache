<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::orderByRaw("FIELD(status, 'UNPAID', 'PROCESSING', 'COMPLETED', 'CANCELED')")->with('items')->get();
        foreach ($orders as $order) {
            $order->items->map(function ($item) {
                $remarkArr = json_decode($item->remark);
                if (isset($remarkArr->set)) {
                    $item->set = implode(', ', $remarkArr->set);
                } else {
                    $item->set = '無';
                }

                if (isset($remarkArr->no)) {
                    $item->no = implode(', ', $remarkArr->no);
                } else {
                    $item->no = '無';
                }
                return $item;
            });
        }


        return view('order.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);

        return view('order.show', compact('order'));
    }

    public function create()
    {

        $menus = Menu::where('status', true)->orderBy('id', 'asc')->get();
        return view('order.create', compact('menus'));
    }

    public function store(Request $request, $id = null)
    {
        if ($id) {
            $order = Order::where('id', $id)->update(['status' => $request->status]);
        } else {
            $order = Order::create([
                'order_no' => 'ORD-' . date('YmdHis'),
                'price' => 0,
                'status' => 'PROCESSING',
            ]);

            $totalPrice = 0;
            foreach ($request->input('carts') as $item) {
                $totalPrice += $item['price'] * $item['quantity'];

                $order->items()->create([
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total_price' => $item['price'] * $item['quantity'],
                    'remark' => $item['remark'] ?? '{}',
                ]);
            }

            $order->update(['price' => $totalPrice]);
        }

        $orders = Order::orderByRaw("FIELD(status, 'UNPAID', 'PROCESSING', 'COMPLETED', 'CANCELED')")->with('items')->get();

        return response()->json(['status' => 'success', 'data' => $orders]);
    }
}
