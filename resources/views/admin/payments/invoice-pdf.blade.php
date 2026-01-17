<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
        .header { margin-bottom: 20px; }
        .logo { font-size: 28px; font-weight: bold; color: #2563eb; letter-spacing: 1px; }
        .company-info { font-size: 12px; color: #666; margin-top: 5px; }
        
        .invoice-header { display: table; width: 100%; border-bottom: 1px solid #ddd; padding-bottom: 20px; margin-bottom: 20px; }
        .invoice-title-col { display: table-cell; vertical-align: top; text-align: right; }
        .invoice-logo-col { display: table-cell; vertical-align: top; }
        
        .invoice-title { font-size: 36px; font-weight: bold; color: #ddd; text-transform: uppercase; }
        .invoice-details { margin-top: 10px; font-size: 14px; }
        
        .info-section { display: table; width: 100%; margin-bottom: 30px; }
        .info-col { display: table-cell; vertical-align: top; width: 33%; }
        .info-label { font-size: 11px; text-transform: uppercase; color: #888; margin-bottom: 5px; font-weight: bold; }
        .info-value { font-size: 14px; font-weight: 500; }
        
        table.items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table.items-table th { background: #f8fafc; border-bottom: 2px solid #e2e8f0; padding: 12px; text-align: left; font-size: 12px; text-transform: uppercase; color: #475569; }
        table.items-table td { border-bottom: 1px solid #f1f5f9; padding: 12px; font-size: 14px; }
        table.items-table tr:last-child td { border-bottom: none; }
        
        .totals-section { float: right; width: 350px; }
        .totals-row { display: table; width: 100%; margin-bottom: 8px; }
        .totals-label { display: table-cell; text-align: right; padding-right: 20px; color: #64748b; font-size: 14px; }
        .totals-value { display: table-cell; text-align: right; width: 100px; font-weight: bold; font-size: 14px; }
        .grand-total { border-top: 2px solid #e2e8f0; padding-top: 10px; margin-top: 10px; font-size: 18px; color: #2563eb; }
        
        .status-badge { display: inline-block; padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .status-paid { background: #dcfce7; color: #166534; }
        .status-unpaid { background: #fee2e2; color: #991b1b; }
        
        .footer { border-top: 1px solid #eee; margin-top: 50px; padding-top: 20px; text-align: center; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="invoice-header">
            <div class="invoice-logo-col">
                <div class="logo">Sapphire</div>
                <div class="company-info">
                    123 Luxury Avenue, Paradise City<br>
                    support@sapphirehotel.com | +1 (555) 123-4567
                </div>
            </div>
            <div class="invoice-title-col">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-details">
                    <b>#{{ $invoice->invoice_number }}</b><br>
                    Date: {{ $invoice->issue_date->format('M d, Y') }}<br>
                    <div style="margin-top: 5px;">
                        <span class="status-badge {{ $invoice->status == 'paid' ? 'status-paid' : 'status-unpaid' }}">
                            {{ $invoice->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-section">
            <div class="info-col">
                <div class="info-label">Billed To</div>
                <div class="info-value">
                    {{ $invoice->user ? $invoice->user->name : ($invoice->booking ? $invoice->booking->guest_name : 'Guest') }}<br>
                    {{ $invoice->user ? $invoice->user->email : ($invoice->booking ? $invoice->booking->guest_email : '-') }}<br>
                    {{ $invoice->user ? $invoice->user->phone : ($invoice->booking ? $invoice->booking->guest_phone : '-') }}
                </div>
            </div>
            @if($invoice->booking)
            <div class="info-col">
                <div class="info-label">Stay Details</div>
                <div class="info-value">
                    Check-in: {{ $invoice->booking->check_in_date->format('M d, Y') }}<br>
                    Check-out: {{ $invoice->booking->check_out_date->format('M d, Y') }}<br>
                    Duration: {{ $invoice->booking->check_in_date->diffInDays($invoice->booking->check_out_date) }} Nights
                </div>
            </div>
            <div class="info-col">
                <div class="info-label">Room Info</div>
                <div class="info-value">
                    Room {{ $invoice->booking->room->room_number }}<br>
                    {{ $invoice->booking->room->roomType->name }}<br>
                    Guests: {{ $invoice->booking->adults }} Adults, {{ $invoice->booking->children }} Kids
                </div>
            </div>
            @endif
        </div>

        @php
            $roomItems = $invoice->items->where('type', 'room');
            $activityItems = $invoice->items->where('type', 'activity');
            $foodItems = $invoice->items->where('type', 'food');
            $otherItems = $invoice->items->whereNotIn('type', ['room', 'activity', 'food']);
        @endphp

        <!-- Room Charges -->
        @if($roomItems->count() > 0)
        <div style="margin-bottom: 15px;">
            <div style="font-weight: bold; color: #2563eb; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px;">ROOM CHARGES</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th width="50%">Description</th>
                        <th width="15%" style="text-align: center;">Nights/Qty</th>
                        <th width="15%" style="text-align: right;">Rate</th>
                        <th width="20%" style="text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roomItems as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">${{ number_format(abs($item->unit_price), 2) }}</td>
                        <td style="text-align: right;">${{ number_format(abs($item->amount), 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Activity Charges -->
        @if($activityItems->count() > 0)
        <div style="margin-bottom: 15px;">
            <div style="font-weight: bold; color: #2563eb; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px;">ACTIVITIES & FACILITIES</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th width="50%">Activity</th>
                        <th width="15%" style="text-align: center;">Guests</th>
                        <th width="15%" style="text-align: right;">Price</th>
                        <th width="20%" style="text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activityItems as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">${{ number_format(abs($item->unit_price), 2) }}</td>
                        <td style="text-align: right;">${{ number_format(abs($item->amount), 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Food & Dining -->
        @if($foodItems->count() > 0)
        <div style="margin-bottom: 15px;">
            <div style="font-weight: bold; color: #2563eb; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px;">FOOD & DINING</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th width="50%">Item</th>
                        <th width="15%" style="text-align: center;">Qty</th>
                        <th width="15%" style="text-align: right;">Unit Price</th>
                        <th width="20%" style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($foodItems as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">${{ number_format(abs($item->unit_price), 2) }}</td>
                        <td style="text-align: right;">${{ number_format(abs($item->amount), 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Other Items (Tax, Discount only) -->
        <!-- Logic moved to totals section for Tax/Discount, usually. 
             If there are other custom items, show them here. -->
        @if($otherItems->whereNotIn('type', ['tax', 'discount'])->count() > 0)
        <div style="margin-bottom: 15px;">
            <div style="font-weight: bold; color: #2563eb; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px;">OTHER SERIVCES</div>
            <table class="items-table">
                <tbody>
                    @foreach($otherItems->whereNotIn('type', ['tax', 'discount']) as $item)
                    <tr>
                        <td width="50%">{{ $item->description }}</td>
                        <td width="15%" style="text-align: center;">{{ $item->quantity }}</td>
                        <td width="15%" style="text-align: right;">${{ number_format(abs($item->unit_price), 2) }}</td>
                        <td width="20%" style="text-align: right;">${{ number_format(abs($item->amount), 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="totals-section">
            @php
                $subtotal = $invoice->total_amount - $invoice->tax_amount + $invoice->items->where('type', 'discount')->sum(fn($i) => abs($i->amount)); 
                // Note: Discount is negative in DB, so to get subtotal BEFORE discount/tax, we add absolute discount and subtract tax.
                // Actually simplified: Sum of positive items (Rooms, Food, Activity)
                $subtotal = $invoice->items->where('amount', '>', 0)->where('type', '!=', 'tax')->sum('amount');
                $discount = abs($invoice->items->where('type', 'discount')->sum('amount'));
            @endphp
            
            <div class="totals-row">
                <div class="totals-label">Subtotal</div>
                <div class="totals-value">${{ number_format($subtotal, 2) }}</div>
            </div>
            @if($discount > 0)
            <div class="totals-row">
                <div class="totals-label">Discount</div>
                <div class="totals-value" style="color: #16a34a;">-${{ number_format($discount, 2) }}</div>
            </div>
            @endif
            <div class="totals-row">
                <div class="totals-label">Tax (10%)</div>
                <div class="totals-value">${{ number_format($invoice->tax_amount, 2) }}</div>
            </div>
            <div class="totals-row grand-total">
                <div class="totals-label" style="color: #2563eb;">Total</div>
                <div class="totals-value">${{ number_format($invoice->total_amount, 2) }}</div>
            </div>
            
            <div style="border-top: 1px solid #eee; margin-top: 15px; padding-top: 10px;">
                <div class="totals-row">
                    <div class="totals-label">Amount Paid</div>
                    <div class="totals-value">${{ number_format($invoice->booking->payments->sum('amount'), 2) }}</div>
                </div>
                <div class="totals-row" style="font-weight: bold; color: {{ ($invoice->total_amount - $invoice->booking->payments->sum('amount')) > 0 ? '#dc2626' : '#166534' }};">
                    <div class="totals-label">Balance Due</div>
                    <div class="totals-value">${{ number_format($invoice->total_amount - $invoice->booking->payments->sum('amount'), 2) }}</div>
                </div>
            </div>
        </div>
        
        <div style="clear: both;"></div>

        <div class="footer">
            <p>Thank you for staying at Sapphire Hotel. We hope to see you again soon!</p>
            <p style="margin-top: 5px;">This invoice was generated electronically and is valid without a signature.</p>
        </div>
    </div>
</body>
</html>
