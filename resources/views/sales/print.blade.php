<!DOCTYPE html>
<html lang="az">

<head>
    <meta charset="UTF-8">
    <title>{{ __('resource.receipt') }} #{{ $sale->sale_number }}</title>
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

        .text-left {
            text-align: left;
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
        <h2 style="margin:0;">{{ __('resource.companyName') }}</h2>
        <p class="text-left">{{ __('resource.created') }}: {{ $sale->created_at->format('d.m.Y H:i:s') }}</p>

        <p class="text-left">{{ __('resource.receipt') }} №: {{ $sale->sale_number }}</p>

        <p class="text-left">{{ __('resource.cashier') }}: {{ $sale->user?->name ?? __('resource.not') }}</p>
    </div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr class="bold">
                <th class="text-left">{{ __('resource.product') }}</th>
                <th class="text-center">{{ __('resource.quantity') }} </th>
                <th class="text-right"> {{ __('resource.price') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $item)
                <tr>
                    <td>{{ Str::limit($item->product->name, 30, '...') }}
                        @if ($item->product->brand)
                            <span style="font-size: 10px; color: #555;">({{ $item->product->brand->name }})</span>
                        @endif

                    </td>
                    <td class="text-center">{{ rtrim(rtrim(number_format($item->quantity, 2, '.', ''), '0'), '.') }}
                    </td>
                    <td class="text-right">{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <div class="text-right">
        <p>{{ __('resource.total') }}:
            {{ number_format($sale->total + $sale->discount, 2) }}</p>
        @if ($sale->discount > 0)
            <p>{{ __('resource.discount') }}: -{{ number_format($sale->discount, 2) }}</p>
        @endif
        <p class="bold">{{ __('resource.result') }}: {{ number_format($sale->total, 2) }}</p>
    </div>

    <div class="divider"></div>
    <p class="text-center bold"> {{ __('resource.thank') }}</p>

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
