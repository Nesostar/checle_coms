<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: DejaVu Sans; font-size: 11px; }
th { background:#1e7f43; color:white; }
table { width:100%; border-collapse: collapse; }
th, td { border:1px solid #ccc; padding:6px; }
</style>
</head>
<body>

<!-- Company Header -->
<div style="text-align:center; margin-bottom:15px; border-bottom:2px solid #1e7f43; padding-bottom:10px;">
    <h1 style="margin:0; font-size:20px; color:#1e7f43;">CHECLE GENERAL TRADERS CO.LTD</h1>
    <p style="margin:3px 0;">Tel: Call: +255 769 417 813 / +255 0782 089 229</p>
</div>

<h2>Income Report</h2>
<p><strong>Period:</strong> {{ $from }} to {{ $to }}</p>
<p><strong>Total Income:</strong> {{ number_format($totalIncome,2) }}</p>

<table>
<tr>
<th>#</th>
<th>Title</th>
<th>Category</th>
<th>Amount</th>
<th>Date</th>
</tr>

@foreach($incomes as $inc)
<tr>
<td>{{ $loop->iteration }}</td>
<td>{{ $inc->title }}</td>
<td>{{ $inc->category->name ?? 'N/A' }}</td>
<td>{{ number_format($inc->amount,2) }}</td>
<td>{{ $inc->date }}</td>
</tr>
@endforeach
</table>

</body>
</html>
