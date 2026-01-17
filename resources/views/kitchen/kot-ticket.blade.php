<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KOT - {{ $foodOrder->kot_number }}</title>
    <style>
        body { 
            font-family: 'Courier New', monospace; 
            margin: 0; 
            padding: 20px; 
            background: white;
        }
        .kot-header { 
            text-align: center; 
            border-bottom: 2px dashed #000; 
            padding-bottom: 10px; 
            margin-bottom: 20px;
        }
        .kot-number { 
            font-size: 24px; 
            font-weight: bold; 
            margin-bottom: 5px;
        }
        .order-info { 
            margin-bottom: 20px;
        }
        .order-item { 
            border: 1px solid #000; 
            padding: 15px; 
            margin-bottom: 10px;
        }
        .item-name { 
            font-size: 18px; 
            font-weight: bold; 
            margin-bottom: 5px;
        }
        .item-details { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 5px;
        }
        .special-instructions { 
            background: #f0f0f0; 
            padding: 10px; 
            margin-top: 10px; 
            border-left: 3px solid #000;
        }
        .footer { 
            text-align: center; 
            margin-top: 30px; 
            border-top: 2px dashed #000; 
            padding-top: 10px;
        }
        .print-only {
            display: none;
        }
        @media print {
            .print-only {
                display: block;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="kot-header">
        <h1>KITCHEN ORDER TICKET</h1>
        <div class="kot-number">{{ $foodOrder->generateKotNumber() }}</div>
        <div>{{ now()->format('M j, Y H:i:s') }}</div>
    </div>

    <div class="order-info">
        <div><strong>Order Type:</strong> {{ $foodOrder->getOrderTypeLabel() }}</div>
        @if ($foodOrder->table_number)
            <div><strong>Table:</strong> {{ $foodOrder->table_number }}</div>
        @endif
        @if ($foodOrder->room_number)
            <div><strong>Room:</strong> {{ $foodOrder->room_number }}</div>
        @endif
        @if ($foodOrder->guest_name)
            <div><strong>Guest:</strong> {{ $foodOrder->guest_name }}</div>
        @endif
        <div><strong>Order Time:</strong> {{ $foodOrder->order_time->format('H:i') }}</div>
        <div><strong>Prep Time:</strong> {{ $foodOrder->food->preparation_time }} minutes</div>
        <div><strong>Status:</strong> <span class="print-only">{{ ucfirst($foodOrder->status) }}</span></div>
    </div>

    <div class="order-item">
        <div class="item-name">{{ $foodOrder->food->name }}</div>
        <div class="item-details">
            <span>Quantity: {{ $foodOrder->quantity }}</span>
            <span>Price: ${{ number_format($foodOrder->food->price, 2) }}</span>
        </div>
        <div class="item-details">
            <span>Category: {{ ucfirst($foodOrder->food->category) }}</span>
            <span>Total: ${{ number_format($foodOrder->total_price, 2) }}</span>
        </div>

        @if ($foodOrder->special_instructions)
            <div class="special-instructions">
                <strong>Special Instructions:</strong><br>
                {{ $foodOrder->special_instructions }}
            </div>
        @endif
    </div>

    @if ($foodOrder->booking)
        <div class="order-info">
            <div><strong>Booking ID:</strong> #{{ $foodOrder->booking->id }}</div>
            <div><strong>Room Number:</strong> {{ $foodOrder->booking->room->room_number }}</div>
            <div><strong>Guest:</strong> {{ $foodOrder->booking->user ? $foodOrder->booking->user->name : $foodOrder->booking->guest_name }}</div>
        </div>
    @endif

    <div class="footer">
        <div class="print-only">
            <div>================================</div>
            <div>KITCHEN COPY - DO NOT GIVE TO GUEST</div>
            <div>================================</div>
        </div>
        <div>Sapphire Hotel Kitchen</div>
        <div>Thank you!</div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
