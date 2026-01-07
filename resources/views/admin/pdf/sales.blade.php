<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: DejaVu Sans; font-size: 10px; }
th { background:#1e7f43; color:white; }
table { width:100%; border-collapse: collapse; }
th, td { border:1px solid #ccc; padding:5px; }
</style>
</head>
<body>

<!-- Company Header -->
<div style="text-align:center; margin-bottom:15px; border-bottom:2px solid #1e7f43; padding-bottom:10px;">
    <h1 style="margin:0; font-size:20px; color:#1e7f43;">CHECLE GENERAL TRADERS CO.LTD</h1>
    <p style="margin:3px 0;">Tel: Call: +255 769 417 813 / +255 0782 089 229</p>
</div>

<h2>Sales Report</h2>
<p><strong>Period:</strong> {{ $from }} to {{ $to }}</p>

<p>
<strong>Total Sales:</strong> {{ number_format($total,2) }} |
<strong>Total Paid:</strong> {{ number_format($paidTotal,2) }}
</p>

<table>
<tr>
<th>#</th>
<th>Customer</th>
<th>Total</th>
<th>Paid</th>
<th>Balance</th>
<th>Date</th>
</tr>

@foreach($sales as $s)
<tr>
<td>{{ $loop->iteration }}</td>
<td>{{ $s->customer_name }}</td>
<td>{{ number_format($s->total_amount,2) }}</td>
<td>{{ number_format($s->amount_paid,2) }}</td>
<td>{{ number_format($s->balance,2) }}</td>
<td>{{ $s->created_at->format('Y-m-d') }}</td>
</tr>
@endforeach
</table>

</body>
</html>
