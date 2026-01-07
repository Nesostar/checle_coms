<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Cashier Dashboard - COMS')</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
 @stack('scripts')
<style>
  body { 
    margin: 0; 
    font-family: Arial, sans-serif; 
    background: #f8f8f8; 
    overflow: hidden; /* Prevent page scroll */
  }

  .sidebar { 
    width: 220px; 
    background: #0a5f4e; 
    color: #fff; 
    position: fixed; 
    top: 0; 
    left: 0; 
    height: 100vh; 
    overflow: auto; /* Remove vertical scroll */
    transition: width 0.3s ease; 
  }

  
.main { 
    margin-left: 220px; 
    padding: 15px; 
    transition: margin-left 0.3s ease; 
    height: 100vh;
    overflow-y: auto;
}

#content-area {
    background: #fff;
    padding: 20px;
    max-height: calc(100vh - 120px);
    overflow-y: auto;
    border: 1px solid #ccc;
    border-radius: 5px;
}

  .sidebar.collapsed { width: 70px; }
  .sidebar h3, .sidebar strong, .sidebar a span { transition: opacity 0.3s ease; }
  .sidebar.collapsed h3, .sidebar.collapsed strong, .sidebar.collapsed a span { opacity: 0; }

  .sidebar h3 { text-align: center; font-size: 16px; margin-bottom: 10px; }
  .sidebar strong { display: block; padding: 5px 15px; font-size: 11px; text-transform: uppercase; color: #ccc; }
  .sidebar a { display: flex; align-items: center; color: #fff; padding: 8px 15px; text-decoration: none; font-size: 13px; white-space: nowrap; }
  .sidebar a:hover, .sidebar a.active { background: #074033; }
  .sidebar a i { margin-right: 10px; width: 18px; text-align: center; }

  .submenu { display: none; background: #0e7a63; }
  .submenu a { padding-left: 35px; font-size: 12px; }
  .submenu a:hover, .submenu a.active { background: #09614c; }

  .menu-toggle { cursor: pointer; display: flex; align-items: center; justify-content: space-between; padding-right: 15px; }
  .menu-toggle i.fa-chevron-down { transition: transform 0.3s ease; }
  .menu-toggle.open i.fa-chevron-down { transform: rotate(180deg); }

  .main { margin-left: 220px; padding: 15px; transition: margin-left 0.3s ease; overflow: hidden; }
  .collapsed + .main { margin-left: 70px; }

  .topbar { display: flex; justify-content: space-between; align-items: center; background: #f2f2f2; padding: 10px 15px; margin-bottom: 15px; }
  .toggle-btn { font-size: 20px; cursor: pointer; color: #0a5f4e; }
  #content-area { background: #fff; padding: 20px; min-height: 80vh; border: 1px solid #ccc; border-radius: 5px; }
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <h3>COMS</h3>

  <strong>OPERATION PANEL</strong>
  <a href="{{ route('cashier.dashboard.index') }}" class="{{ request()->routeIs('cashier.dashboard.index') ? 'active' : '' }}"><i class="fa fa-home"></i><span>Home - Dashboard</span></a>
  <a href="{{ route('cashier.deposit.index') }}" class="{{ request()->routeIs('cashier.deposit.index') ? 'active' : '' }}"><i class="fa fa-money-bill"></i><span>Deposit</span></a>
  <a href="{{ route('cashier.pos') }}" class="{{ request()->routeIs('cashier.pos') ? 'active' : '' }}"><i class="fa fa-money-bill"></i><span>POS - Point of Sales</span></a>
  <a href="{{ route('cashier.payments') }}" class="{{ request()->routeIs('cashier.payments') ? 'active' : '' }}"><i class="fa fa-list"></i><span>Payment List</span></a>
  <a href="{{ route('cashier.incompletepayments') }}" class="{{ request()->routeIs('cashier.incompletepayments') ? 'active' : '' }}"><i class="fa fa-money-check-alt"></i><span>Incomplete Payments</span></a>

  <!-- Expenditure Dropdown -->
  <div class="menu-toggle" onclick="toggleSubmenu('expenditureSubmenu', this)">
    <a href="javascript:void(0);" style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
      <div><i class="fa fa-credit-card"></i><span>Expenditure Management</span></div>
      <i class="fa fa-chevron-down"></i>
    </a>
  </div>
  <div class="submenu" id="expenditureSubmenu">{{-- Now these will work with the corrected routes --}}
    <a href="{{ route('cashier.expenditure.category') }}" class="{{ request()->routeIs('cashier.expenditure.category') ? 'active' : '' }}"><i class="fa fa-folder-open"></i><span>Expenditure Category</span></a>
    <a href="{{ route('cashier.expenditure.list') }}" class="{{ request()->routeIs('cashier.expenditure.list') ? 'active' : '' }}"><i class="fa fa-list"></i><span>Expenditure List</span></a>
</div>

  <a href="{{ route('cashier.customers.index') }}" class="{{ request()->routeIs('cashier.customers.index') ? 'active' : '' }}"><i class="fa fa-users"></i><span>Customers</span></a>
  <a href="{{ route('cashier.quotations.index') }}" class="{{ request()->routeIs('cashier.quotations.index') ? 'active' : '' }}"><i class="fa fa-quote-right"></i><span>Quotation</span></a>
  <a href="{{ route('cashier.invoices.index') }}" class="{{ request()->routeIs('cashier.invoices.index') ? 'active' : '' }}"><i class="fa fa-receipt"></i><span>Invoice</span></a>

  {{-- Logout via POST --}}
  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
      @csrf
  </form>
  <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
      <i class="fa fa-sign-out-alt"></i><span>Logout</span>
  </a>
</div>

<!-- Main -->
<div class="main" id="main">
  <div class="topbar">
    <div class="toggle-btn" id="toggleBtn"><i class="fa fa-bars"></i></div>
    <div>IT Support: +255 626742729 | 0675211869 | Email: nesostar0@gmail.com</div>
    <div><i class="fa fa-user"></i> {{ Auth::user()->name ?? 'Cashier' }} ({{ Auth::user()->role ?? 'Role' }})</div>
  </div>

  <div id="content-area">
    @yield('content')
  </div>
</div>

<script>
  const toggleBtn = document.getElementById('toggleBtn');
  const sidebar = document.getElementById('sidebar');
  const main = document.getElementById('main');

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
    main.classList.toggle('collapsed');
  });

  // Submenu toggle
  function toggleSubmenu(id, element) {
    const submenu = document.getElementById(id);
    const caret = element.querySelector('i.fa-chevron-down');
    submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
    element.classList.toggle('open');
  }
</script>

</body>
</html>
