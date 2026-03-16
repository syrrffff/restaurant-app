<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Pesanan</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0 0 5px 0; font-size: 18px; }
        .header p { margin: 0; color: #666; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background-color: #f4f4f4; font-weight: bold; font-size: 12px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { padding: 3px 6px; border-radius: 4px; font-size: 10px; font-weight: bold; }
        .badge-lunas { background-color: #d1fae5; color: #065f46; }
        .badge-belum { background-color: #fef3c7; color: #92400e; }
        .item-list { margin: 0; padding-left: 15px; font-size: 10px; }
        .variant-text { color: #666; font-style: italic; }
        /* Style untuk Footer Total Penghasilan */
        tfoot td { background-color: #e5e7eb; font-size: 13px; border-top: 2px solid #333; }
        .total-highlight { font-size: 16px; font-weight: bold; color: #166534; }
    </style>
</head>
<body>

    <div class="header">
        <h2>LAPORAN RIWAYAT PESANAN</h2>

        @php
            $startStr = !empty($startDate) ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'Awal';
            $endStr = !empty($endDate) ? \Carbon\Carbon::parse($endDate)->format('d M Y') : 'Sekarang';
            $rentangWaktu = (!empty($startDate) || !empty($endDate)) ? "$startStr s/d $endStr" : 'Semua Waktu';
        @endphp

        <p>Rentang Waktu: <strong>{{ $rentangWaktu }}</strong></p>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d M Y, H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="10%">Tanggal</th>
                <th width="12%">Invoice / Kasir</th>
                <th width="12%">Tipe & Pelanggan</th>
                <th width="35%">Detail Pesanan (Menu & Varian)</th>
                <th width="13%" class="text-right">Total Nominal</th>
                <th width="8%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <strong>{{ $order->invoice_number }}</strong><br>
                        <span style="font-size: 10px; color:#555;">Oleh: {{ $order->cashier->name ?? 'Sistem' }}</span>
                    </td>
                    <td>
                        {{ $order->order_type == 'takeaway' ? 'Takeaway' : 'Dine-In' }}<br>
                        <strong>{{ $order->order_type == 'takeaway' ? ($order->customer_name ?? '-') : 'Meja ' . ($order->table->table_number ?? '-') }}</strong>
                    </td>
                    <td>
                        <ul class="item-list">
                            @foreach($order->items as $item)
                                <li>
                                    {{ $item->quantity }}x {{ $item->menu->name ?? 'Menu Dihapus' }}
                                    @if($item->selectedOptions->count() > 0)
                                        <br><span class="variant-text">+ {{ implode(', ', $item->selectedOptions->pluck('option_name')->toArray()) }}</span>
                                    @endif
                                    @if($item->notes)
                                        <br><span class="variant-text">📝 {{ $item->notes }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="text-right">
                        Sub: {{ number_format($order->subtotal, 0, ',', '.') }}<br>
                        Tax: {{ number_format($order->tax_amount, 0, ',', '.') }}<br>
                        <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $order->payment_status == 'paid' ? 'badge-lunas' : 'badge-belum' }}">
                            {{ strtoupper($order->payment_status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4">Tidak ada data pesanan pada rentang waktu ini.</td>
                </tr>
            @endforelse
        </tbody>

        @if($orders->count() > 0)
        <tfoot>
            <tr>
                <td colspan="4" class="text-right" style="font-weight: bold; padding-top: 10px; padding-bottom: 10px;">
                    TOTAL PENDAPATAN (DARI PESANAN LUNAS):
                </td>
                <td colspan="2" class="text-left total-highlight" style="padding-top: 10px; padding-bottom: 10px;">
                    Rp {{ number_format($totalPenghasilan, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
        @endif

    </table>

</body>
</html>
