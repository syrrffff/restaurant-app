<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print QR Code - {{ $table->table_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f3f4f6;
            margin: 0;
        }
        .card {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
            border: 2px solid #000;
        }
        h1 { margin-top: 0; font-size: 2rem; }
        .qr-wrapper { margin: 20px 0; }
        p { color: #555; font-size: 1.2rem; margin-bottom: 5px; }
        .btn-print {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #000;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
        /* Sembunyikan tombol saat dicetak ke kertas */
        @media print {
            .btn-print { display: none; }
            body { background-color: #fff; }
            .card { box-shadow: none; border: none; }
        }
    </style>
</head>
<body>

    <div class="card">
        <h1>{{ $table->table_number }}</h1>
        <p>Scan di sini untuk memesan</p>
        
        <div class="qr-wrapper">
            <!-- Menampilkan SVG QR Code -->
            {!! $qrCode !!}
        </div>
        
        <small style="color: #888;">{{ $url }}</small>
    </div>

    <button class="btn-print" onclick="window.print()">Print QR Code</button>

</body>
</html>
