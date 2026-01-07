@extends('layouts.app')

@section('title', 'Suppliers - COMS')

@section('content')
  <h2>Supplier Management</h2>
  <p>Manage supplier contacts, product categories, and order histories.</p>

  <table border="1" width="100%">
    <tr><th>Supplier Name</th><th>Contact</th><th>Email</th><th>Actions</th></tr>
    <tr><td>ABC Traders</td><td>+255 710 123 456</td><td>abc@suppliers.com</td><td>Edit | Delete</td></tr>
  </table>

  <button>Add Supplier</button>
@endsection
