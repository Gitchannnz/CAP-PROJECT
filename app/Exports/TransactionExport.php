<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransactionExport implements FromCollection, WithHeadings
{   
    
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        return Transaction::with(['order.user', 'order.orderItems']) // Use orderItems here
            ->when($this->search, function ($query) {
                return $query->where('order_number', 'LIKE', "%{$this->search}%");
            })->get()->map(function ($transaction) {
                // Access the total via the getTotalAttribute
                $total = optional($transaction->order)->total ?? 0; // Use the total attribute
                
                return [
                    'order_no' => optional($transaction->order)->order_number ?? 'N/A',
                    'name' => optional($transaction->order->user)->firstname . ' ' . optional($transaction->order->user)->lastname ?? 'N/A',
                    'institutional_id' => optional($transaction->order->user)->institutional_id ?? 'N/A',
                    'total' => $total,
                    'status' => $transaction->status,
                    'order_date' => $transaction->created_at->format('Y-m-d H:i:s'),
                    'total_items' => $transaction->order->orderItems->count(),
                    'delivered_on' => $transaction->delivered_on ? $transaction->delivered_on->format('Y-m-d H:i:s') : 'N/A',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Order No.',
            'Name',
            'Institutional ID',
            'Total',
            'Status',
            'Order Date',
            'Total Items',
            'Delivered On',
        ];
    }
}