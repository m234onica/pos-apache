@extends('layout/container')

@section('content')
<div class="order-list">
    @csrf
    <button
        id="addOrder"
        class="add-btn"
        onclick="window.location.href='/order/create'">+ 新增訂單</button>

    @if (!$orders->count())
    <div class="empty">
        <p>今日尚未有任何訂單!</p>
    </div>
    @endif

    @foreach ($orders as $order)
    @if ($order->status == 'UNPAID')
    <!-- Total and Checkout -->
    <div class="order-card checkout">
        <div class="order-header">
            <span>{{ $order->order_no }}</span>
            <span>{{ $order->created_at->format('H:i:s') }}</span>
        </div>
        <!-- Order items -->
        @foreach($order->items as $item)
        <div class="order-item">
            <div>
                <h4>{{$item->name}} x {{ $item->quantity}}</h4>
                <p>{{ $item->rice }}</p>
                <p>{{ $item->rice_advanced }}</p>
                <p>{{ $item->basic }}</p>
                <p>{{ $item->club }}</p>
                <p>{{ $item->advanced }}</p>
                <p>{{ $item->spicy }}</p>
                <p>{{ $item->size }}</p>
                <p>{{ $item->heat }}</p>
            </div>
            <span>${{ $item->total_price }}</span>
        </div>
        @endforeach

        <div class="total">
            <span class="total-label">總計:</span>
            <span style="color:red">{{ $order->price }}</span>
        </div>
        <button class="checkout-button" onclick="updateOrderStatus({{ $order->id }}, 'PROCESSING')">結帳</button>
    </div>

    @elseif ($order->status == 'PROCESSING')
    <!-- Order Card (Processing) -->
    <div class="order-card processing">
        <div class="order-header">
            <span>{{ $order->order_no }}</span>
            <span>{{ $order->created_at->format('H:i:s') }}</span>
        </div>

        <!-- Order items -->
        @foreach ($order->items as $item)
        <div class="order-item">
            <div>
                <h4>{{$item->name}} x {{ $item->quantity}}</h4>
                <p>{{ $item->rice }}</p>
                <p>{{ $item->rice_advanced }}</p>
                <p>{{ $item->basic }}</p>
                <p>{{ $item->club }}</p>
                <p>{{ $item->advanced }}</p>
                <p>{{ $item->spicy }}</p>
                <p>{{ $item->size }}</p>
                <p>{{ $item->heat }}</p>
            </div>
            <span>${{ $item->total_price }}</span>
        </div>
        @endforeach

        <div class="total">
            <span class="total-label">總計:</span>
            <span style="color:red">{{ $order->price }}</span>
        </div>
        <button class="processing-button" onclick="updateOrderStatus({{ $order->id }}, 'COMPLETED')">處理中</button>
    </div>

    @elseif ($order->status == 'COMPLETED')
    <!-- Order Card (Finished) with Collapse -->
    <a class="order-card finished" data-toggle="collapse" data-target="#completedOrder{{ $order->id }}" aria-expanded="false" aria-controls="completedOrder{{ $order->id }}">
        <div class="order-header">
            <span class="material-symbols-outlined icon" style="font-size:24px;">
                check_circle
            </span>
            <span style="text-align: center;">{{ $order->order_no }}</span>
            <span style="text-align: center;">{{ $order->created_at->format('H:i:s') }}</span>
            <!-- Collapse Button -->
            <span class="view" style="width: 10%; font-size: 24px;">查看訂單詳情</span>
        </div>

        <div id="completedOrder{{ $order->id }}" class="collapse">
            <!-- Order items -->
            @foreach($order->items as $item)
            <div class="order-item">
                <div>
                    <h4>{{$item->name}} x {{ $item->quantity}}</h4>
                    <p>{{ $item->rice }}</p>
                    <p>{{ $item->rice_advanced }}</p>
                    <p>{{ $item->basic }}</p>
                    <p>{{ $item->club }}</p>
                    <p>{{ $item->advanced }}</p>
                    <p>{{ $item->spicy }}</p>
                    <p>{{ $item->size }}</p>
                    <p>{{ $item->heat }}</p>
                </div>
                <span>${{ $item->total_price }}</span>
            </div>
            @endforeach

            <div class="total">
                <span class="total-label">總計:</span>
                <span style="color:red">${{ $order->price }}</span>
            </div>
        </div>
    </a>

    @elseif($order->status == 'CANCELED')
    <!-- Order Card (Canceled) with Collapse -->
    <a class="order-card cancel" data-toggle="collapse" data-target="#canceledOrder{{ $order->id }}" aria-expanded="false" aria-controls="canceledOrder{{ $order->id }}">
        <div class="order-header">
            <div>
                <span class="material-symbols-outlined icon">
                    cancel
                </span>
                <span>{{ $order->order_no }}</span>
            </div>
            <span>{{ $order->created_at->format('H:i:s') }}</span>
            <span class="view">查看訂單詳情</span>
        </div>

        <div id="canceledOrder{{ $order->id }}" class="collapse">
            <!-- Order items -->
            @foreach($order->items as $item)
            <div class="order-item">
                <div>
                    <h4>{{$item->name}} x {{ $item->quantity}}</h4>
                    <p>{{ $item->rice }}</p>
                    <p>{{ $item->rice_advanced }}</p>
                    <p>{{ $item->basic }}</p>
                    <p>{{ $item->club }}</p>
                    <p>{{ $item->advanced }}</p>
                    <p>{{ $item->spicy }}</p>
                    <p>{{ $item->size }}</p>
                    <p>{{ $item->heat }}</p>
                </div>
                <span>${{ $item->total_price }}</span>
            </div>
            @endforeach

            <div class="total">
                <span class="total-label">總計:</span>
                <span style="color:red">${{ $order->price }}</span>
            </div>
        </div>
    </a>
    @endif

    @endforeach
</div>

{{-- 引入 modal --}}
@include('layout.modal')

<script type="text/javascript">
    function updateOrderStatus(id, status) {
        // 這裡應該是一個 AJAX 請求，用於更新訂單狀態
        $.ajax({
            url: '/order/' + id,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(response) {
                // 更新成功後重新加載頁面
                location.reload();
            },
            error: function(error) {
                console.error(error);
            }
        });
    }
</script>
@stop
