<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $order->invoice_number }}</title>
    <style>
        /* Desain khusus kertas Thermal / Struk */
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #fff;
            margin: 0 auto;
            padding: 10px;
            width: 58mm; /* Ubah ke 80mm jika printer Anda lebih besar */
        }
        h2, h3, h4, p { margin: 0; padding: 0; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .divider { border-bottom: 1px dashed #000; margin: 8px 0; }
        .flex { display: flex; justify-content: space-between; }

        @media print {
            body { width: 100%; margin: 0; padding: 0; }
        }
    </style>
</head>
<body onload="window.print(); setTimeout(() => window.close(), 1000);">

    <div class="text-center font-bold" style="font-size: 16px; margin-bottom: 5px;">RESTO POS</div>
    <div class="text-center">Jl. Contoh Restoran No. 123</div>
    <div class="text-center">Telp: 0812-3456-7890</div>

    <div class="divider"></div>

    <div class="flex">
        <span>No: {{ $order->invoice_number }}</span>
        <span>{{ $order->created_at->format('d/m/y H:i') }}</span>
    </div>
    <div class="flex">
        <span>Tipe: {{ $order->order_type == 'takeaway' ? 'Takeaway' : 'Dine-In' }}</span>
        <span>{{ $order->order_type == 'takeaway' ? 'A/n: ' . ($order->customer_name ?? '-') : 'Meja: ' . ($order->table->table_number ?? '-') }}</span>
    </div>
    <div class="flex">
        <span>Kasir: {{ $order->cashier->name ?? 'Admin' }}</span>
        <span>Via: {{ $order->payment_method }}</span>
    </div>

    <div class="divider"></div>

    <table style="width: 100%; border-collapse: collapse;">
        @foreach($order->items as $item)
        <tr>
            <td colspan="2" style="font-weight:bold; padding-top: 5px;">{{ $item->menu->name }}</td>
        </tr>

        @if($item->selectedOptions && $item->selectedOptions->count() > 0)
        <tr>
            <td colspan="2" style="font-size: 11px; padding-left: 5px; color: #333;">
                + {{ implode(', ', $item->selectedOptions->pluck('option_name')->toArray()) }}
            </td>
        </tr>
        @endif

        @if($item->notes)
        <tr>
            <td colspan="2" style="font-size: 11px; padding-left: 5px; font-style: italic;">
                Ket: {{ $item->notes }}
            </td>
        </tr>
        @endif

        <tr>
            <td style="padding-bottom: 5px;">{{ $item->quantity }} x {{ number_format(($item->total_price / $item->quantity), 0, ',', '.') }}</td>
            <td style="text-align: right; padding-bottom: 5px;">{{ number_format($item->total_price, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <div class="flex">
        <span>Subtotal</span>
        <span>{{ number_format($order->subtotal, 0, ',', '.') }}</span>
    </div>
    <div class="flex">
        <span>Pajak (10%)</span>
        <span>{{ number_format($order->tax_amount, 0, ',', '.') }}</span>
    </div>

    <div class="divider"></div>

    <div class="flex font-bold" style="font-size: 14px;">
        <span>TOTAL</span>
        <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
    </div>

    <div class="divider"></div>

    <div class="text-center" style="margin-top: 15px;">
        Terima Kasih Atas Kunjungan Anda!<br>
        <small>Silakan datang kembali</small>
    </div>

</body>
</html>
