<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;

class KitchenPage extends Component
{
    public $search = '';
    public $filterOrderType = 'all';

    public function markAsDone($orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => 'Selesai']);
        session()->flash('message', 'Order #'.$orderId.' telah selesai');
    }

    public function render()
    {
        $orders = Order::with('items.variant.product')
            ->where('status', 'Diproses')
            ->when($this->search, function($query) {
                $query->where(function($subQuery) {
                    $subQuery->where('customer_name', 'like', '%'.$this->search.'%')
                             ->orWhere('id', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterOrderType !== 'all', function($query) {
                $query->where('order_type', $this->filterOrderType);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return view('livewire.kitchen-page', [
            'orders' => $orders
        ]);
    }
}