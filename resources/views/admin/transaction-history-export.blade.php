<table>
    <thead>
        <tr>
            <th>Order No.</th>
            <th>Name</th>
            <th>Institutional ID</th>
            <th>Total</th>
            <th>Status</th>
            <th>Order Date</th>
            <th>Total Items</th>
            <th>Delivered On</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
            <tr>
                <td>{{ $order->id ?? 'N/A' }}</td>
                <td>{{ $order->user->name ?? 'N/A' }}</td>
                <td>{{ $order->user->institutional_id ?? 'N/A' }}</td>
                <td>{{ number_format($order->total, 2) }}</td>
                <td>{{ ucfirst($order->status) }}</td>
                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d H:i:s') }}</td>
                <td>{{ count($order->items) }}</td>
                <td>{{ \Carbon\Carbon::parse($order->delivered_on)->format('Y-m-d') ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
