<?php

namespace Yab\Quazar\Controllers\App;

use App\Http\Controllers\Controller;
use Yab\Quazar\Repositories\OrderRepository;
use Yab\Crypto\Services\Crypto;

class OrderController extends Controller
{
    public function __construct(OrderRepository $transactionRepo)
    {
        $this->orders = $transactionRepo;
    }

    public function allOrders()
    {
        $orders = $this->orders->getByCustomer(auth()->id())->orderBy('created_at', 'DESC')->paginate(config('quarx.pagination'));

        return view('quazar-frontend::orders.all')->with('orders', $orders);
    }

    public function getOrder($id)
    {
        $id = Crypto::decrypt($id);
        $order = $this->orders->getByCustomerAndId(auth()->id(), $id);

        return view('quazar-frontend::orders.order')->with('order', $order);
    }

    public function cancelOrder($id)
    {
        $id = Crypto::decrypt($id);

        if ($this->orders->cancelOrder($id)) {
            return back()->with('message', 'Order cancelled');
        }

        return back();
    }
}