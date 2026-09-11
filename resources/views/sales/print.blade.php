<!DOCTYPE html>
<html lang="az">

<head>
    <meta charset="UTF-8">
    <title>Qəbz #{{ $sale->sale_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 72mm;
            margin: 0;
            padding: 5mm;
            font-size: 12px;
        }

        @page {
            size: 78mm auto;
            /* Kağız eni 80mm, uzunluq məzmuna görə */
            margin: 0;
            /* Brauzerin avtomatik haşiyələrini ləğv edir */
        }

        @media print {

            html,
            body {
                width: 80mm;
                margin: 0;
                padding: 3mm;
            }

            header,
            footer {
                display: none !important;
            }
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 3px 0;
        }
    </style>
</head>

<body>
    <div class="text-center">
        <h2 style="margin:0;">MARKETİNİZİN ADI</h2>
        <p style="margin:5px 0;">Sürətli Satış Sistemi</p>
        <p>Tarix: {{ $sale->created_at->format('d.m.Y H:i:s') }}</p>
        <p>Qəbz No: {{ $sale->sale_number }}</p>
        <p>Kassir: {{ $sale->user?->name ?? 'Məlum deyil' }}</p>
    </div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr class="bold">
                <th>Məhsul</th>
                <th class="text-center">Say</th>
                <th class="text-right">Qiymət</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $item)
                <tr>
                    <td>{{ $item->product->name }}
                        @if ($item->product->brand)
                            <span style="font-size: 10px; color: #555;">({{ $item->product->brand->name }})</span>
                        @endif

                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">₼{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <div class="text-right">
        <p>Cəmi: ₼{{ number_format($sale->total + $sale->discount, 2) }}</p>
        @if ($sale->discount > 0)
            <p>Endirim: -₼{{ number_format($sale->discount, 2) }}</p>
        @endif
        <p class="bold">Yekun: ₼{{ number_format($sale->total, 2) }}</p>
        {{--         <p>Ödənilən: ₼{{ number_format($sale->paid_amount, 2) }}</p>
        <p>Qalıq: ₼{{ number_format($sale->due_amount, 2) }}</p>
        <p>Qaytarılan: ₼{{ number_format($sale->change_amount, 2) }}</p> --}}
    </div>

    <div class="divider"></div>
    <p class="text-center bold">TƏŞƏKKÜR EDİRİK!</p>

    <script>
        window.onload = function() {
            window.print();
            // Çap pəncərəsi bağlandıqdan sonra vərəqi avtomatik bağlasın
            window.onafterprint = function() {
                window.close();
            };
        }
    </script>
</body>

</html>
