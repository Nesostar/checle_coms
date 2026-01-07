<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans; font-size: 11px; }
        h2 { color: #1e7f43; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #ccc; padding:6px; }
        th { background:#1e7f43; color:white; }
    </style>
</head>
<body>

<!-- Company Header -->
<div style="text-align:center; margin-bottom:15px; border-bottom:2px solid #1e7f43; padding-bottom:10px;">
<h1 style="margin:0; font-size:20px; color:#1e7f43;">CHECLE GENERAL TRADERS CO.LTD</h1>
<p style="margin:3px 0;">Tel: Call: +255 769 417 813 / +255 0782 089 229</p>
</div>

<h2>Cash Book Report</h2>
<p><strong>Period:</strong> {{ $from }} to {{ $to }}</p>

<p>
<strong>Money IN:</strong> {{ number_format($moneyIn,2) }} |
<strong>Money OUT:</strong> {{ number_format($moneyOut,2) }} |
<strong>Balance:</strong> {{ number_format($balance,2) }}
</p>

<h4>Money In</h4>
<table>
<tr><th>Source</th><th>Amount</th><th>Date</th></tr>
@foreach($salesList as $s)
<tr><td>Sale {{ $s->id }}</td><td>{{ number_format($s->amount_paid,2) }}</td><td>{{ $s->created_at->format('Y-m-d') }}</td></tr>
@endforeach
@foreach($incomeList as $i)
<tr><td>{{ $i->title ?? ($i->category->name ?? 'Income') }}</td><td>{{ number_format($i->amount,2) }}</td><td>{{ $i->date }}</td></tr>
@endforeach
</table>

<h4>Money Out</h4>
<table>
<tr><th>Source</th><th>Amount</th><th>Date</th></tr>
@foreach($expenseList as $e)
<tr><td>{{ $e->title }}</td><td>{{ number_format($e->amount,2) }}</td><td>{{ $e->date }}</td></tr>
@endforeach
@foreach($purchaseList as $p)
<tr><td>Purchase {{ $p->id }}</td><td>{{ number_format($p->total_amount ?? 0,2) }}</td><td>{{ $p->created_at->format('Y-m-d') }}</td></tr>
@endforeach
</table>

</body>
</html>
