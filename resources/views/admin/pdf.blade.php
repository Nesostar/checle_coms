<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->id }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        /* =========================
           BRAND COLORS
        ========================== */
        :root {
            --green: #1e7f43;
            --light-green: #eaf6ef;
            --dark: #222;
        }

        /* =========================
           HEADER
        ========================== */
        .header {
            border-bottom: 3px solid var(--green);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 19px;
            font-weight: bold;
            color: var(--green);
        }

        .company-contact {
            font-size: 11px;
            color: #555;
        }

        .invoice-title {
            text-align: right;
            font-size: 22px;
            font-weight: bold;
            color: var(--green);
        }

        /* =========================
           INFO BOXES
        ========================== */
        .info-table {
            width: 100%;
            margin-top: 15px;
        }

        .info-box {
            background: var(--light-green);
            border: 1px solid var(--green);
            padding: 10px;
            border-radius: 4px;
        }

        .info-box strong {
            color: var(--green);
        }

        /* =========================
           MAIN TABLE
        ========================== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        thead th {
            background: var(--green);
            color: #fff;
            padding: 8px;
            font-size: 12px;
            text-transform: uppercase;
        }

        tbody td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        tfoot th {
            background: var(--light-green);
            border: 1px solid var(--green);
            padding: 10px;
            font-size: 13px;
        }

        /* =========================
           TEXT HELPERS
        ========================== */
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* =========================
           FOOTER
        ========================== */
        .footer {
            margin-top: 40px;
            padding-top: 10px;
            border-top: 2px dashed var(--green);
            font-size: 11px;
        }

        .signature {
            margin-top: 30px;
        }
    </style>
</head>
<body>

<!-- =========================
     HEADER
========================== -->
<div class="header">
    <table width="100%">
        <tr>
            <td width="60%">
                <div class="company-name">CHECLE GENERAL TRADERS CO. LTD</div>
                <div class="company-contact">
                    Call: +255 769 417 813 / +255 0782 089 229
                </div>
            </td>
            <td width="40%" class="invoice-title">
                INVOICE
            </td>
        </tr>
    </table>
</div>

<!-- =========================
     INFO SECTION
========================== -->
<table class="info-table" cellspacing="10">
    <tr>
        <td width="50%" class="info-box">
            <strong>Invoice To</strong><br><br>
            <strong>Customer:</strong> {{ $invoice->customer->name ?? 'N/A' }}<br>
            <strong>Invoice #:</strong> INV-{{ str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}<br>
            <strong>Invoice Date:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}<br>
            <strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}
        </td>

        <td width="50%" class="info-box text-right">
            <strong>Status:</strong> {{ $invoice->status }}<br><br>
            <strong>Total Amount:</strong><br>
            <span style="font-size:18px; font-weight:bold; color:var(--green);">
                Tsh {{ number_format($invoice->total, 2) }}
            </span><br><br>
            <strong>Issued By:</strong><br>
            {{ auth()->user()->name ?? 'System Admin' }}
        </td>
    </tr>
</table>

<!-- =========================
     ITEMS TABLE
========================== -->
<table>
    <thead>
        <tr>
            <th width="5%">#</th>
            <th>Description</th>
            <th width="10%" class="text-center">Qty</th>
            <th width="15%" class="text-right">Price</th>
            <th width="15%" class="text-right">Total</th>
        </tr>
    </thead>

    <tbody>
        @php $grandTotal = 0; @endphp

        @forelse($invoice->items as $index => $invoiceItem)
            @php
                $quantity = $invoiceItem->quantity ?? 0;
                $price = $invoiceItem->price ?? 0;
                $itemName = optional($invoiceItem->item)->name ?? '— Item Deleted —';
                $total = $quantity * $price;
                $grandTotal += $total;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $itemName }}</td>
                <td class="text-center">{{ number_format($quantity) }}</td>
                <td class="text-right">{{ number_format($price, 2) }}</td>
                <td class="text-right">{{ number_format($total, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">No items found</td>
            </tr>
        @endforelse
    </tbody>

    <tfoot>
        <tr>
            <th colspan="4" class="text-right">Grand Total</th>
            <th class="text-right">
                Tsh {{ number_format($grandTotal, 2) }}
            </th>
        </tr>
    </tfoot>
</table>

<!-- =========================
     FOOTER
========================== -->
<div class="footer">
    <div class="signature">
        <strong>Authorized Signature:</strong> ____________________________
    </div>

    <br>

    <strong>Thank you for your business!</strong><br>
    <span style="color:#555;">This invoice was generated electronically and is valid without a stamp.</span>
</div>

</body>
</html>