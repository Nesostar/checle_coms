<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Admin Dashboard - COMS')</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

@stack('scripts')
<style>
  body { margin: 0; font-family: Arial, sans-serif; background: #f8f8f8; }

  /* Sidebar */
  .sidebar {
    width: 240px;
    background: #00695c;
    color: #fff;
    position: fixed;
    top: 0; left: 0;
    height: 100vh;
    overflow-y: auto;
    transition: width 0.3s ease;
  }
  .sidebar::-webkit-scrollbar { width: 6px; }
  .sidebar::-webkit-scrollbar-thumb { background-color: #004d40; border-radius: 3px; }

  .sidebar.collapsed { width: 70px; }
  .sidebar.collapsed .submenu { display: none !important; }
  .sidebar.collapsed h3, 
  .sidebar.collapsed strong, 
  .sidebar.collapsed a span { display: none; }

  .sidebar h3 { text-align: center; font-size: 16px; margin: 10px 0; }
  .sidebar strong { display: block; padding: 5px 15px; font-size: 11px; text-transform: uppercase; color: #ccc; }

  .sidebar a {
    display: flex; align-items: center;
    color: #fff; padding: 8px 15px;
    text-decoration: none; font-size: 13px;
    white-space: nowrap;
  }
  .sidebar a:hover, .sidebar a.active { background: #004d40; }
  .sidebar a i { margin-right: 10px; width: 18px; text-align: center; }

  .submenu {
    display: none;
    background: #004d40;
    padding-left: 25px;
  }
  .submenu a {
    padding: 6px 15px;
    font-size: 12px;
  }

  .sidebar .has-submenu { cursor: pointer; position: relative; }
  .sidebar .has-submenu::after {
    content: "\f107"; /* FontAwesome chevron-down */
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    position: absolute;
    right: 15px;
    transition: transform 0.3s;
  }
  .sidebar .has-submenu.open::after { transform: rotate(180deg); }
  .sidebar .has-submenu.open + .submenu { display: block; }

  /* Main */
  .main { margin-left: 240px; padding: 15px; transition: margin-left 0.3s ease; }
  .collapsed + .main { margin-left: 70px; }

  /* Topbar */
  .topbar {
    display: flex; justify-content: space-between; align-items: center;
    background: #f2f2f2; padding: 10px 15px; margin-bottom: 15px;
  }
  .toggle-btn { font-size: 20px; cursor: pointer; color: #00695c; }

  /* Content area */
  #content-area {
    background: #fff; padding: 20px; min-height: 80vh;
    border: 1px solid #ccc; border-radius: 5px;
  }

  /* =========================
   RESPONSIVE SIDEBAR FIX
========================= */
@media (max-width: 768px) {

  body {
    overflow-x: hidden;
  }

  .sidebar {
    position: fixed;
    top: 0;
    left: -260px; /* hidden by default */
    width: 240px;
    height: 100vh;
    transition: left 0.3s ease;
    z-index: 9999;
  }

  .sidebar.show {
    left: 0;
  }

  .main {
    margin-left: 0 !important;
    padding: 10px;
  }
}

.topbar {
    flex-wrap: wrap;
    gap: 10px;
  }

  #content-area {
    padding: 15px;
  }
</style>

 <!-- Cache-busting CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <h3>COMS</h3>

  <strong>OPERATION PANEL</strong>
  <a href="{{ route('admin.dashboard.index') }}" class="{{ request()->routeIs('admin.dashboard.index') ? 'active' : '' }}"><i class="fa fa-home"></i><span>Home - Dashboard</span></a>
  <a href="{{ route('admin.purchase.index') }}" class="{{ request()->routeIs('admin.purchase.index') ? 'active' : '' }}"><i class="fa fa-shopping-cart"></i><span>Purchase</span></a>
<a href="{{ route('admin.deposit.index') }}" class="{{ request()->routeIs('admin.deposit.index') ? 'active' : '' }}"><i class="fa fa-wallet"></i><span>Deposit</span></a>
  <!-- Items Management -->
  <a class="has-submenu"><i class="fa fa-box"></i><span>Items Management</span></a>
  <div class="submenu">
    <a href="{{ route('admin.items.category') }}"><i class="fa fa-list"></i><span>Item Category</span></a>
    <a href="{{ route('admin.items.subcategory') }}"><i class="fa fa-list-alt"></i><span>Item Sub Category</span></a>
    <a href="{{ route('admin.items.name') }}"><i class="fa fa-tag"></i><span>Item Name</span></a>
    <a href="{{ route('admin.items.entryType') }}"><i class="fa fa-plus-square"></i><span>Entry Type</span></a>
    <a href="{{ route('admin.items.stock') }}"><i class="fa fa-warehouse"></i><span>Inventory / Stock</span></a>
    <a href="{{ route('admin.items.adjustment') }}"><i class="fa fa-sliders-h"></i><span>Stock Adjustment</span></a>
    <a href="{{ route('admin.items.outofstock') }}"><i class="fa fa-exclamation-triangle"></i><span>Items Out of Stock</span></a>
    <a href="{{ route('admin.items.expired') }}"><i class="fa fa-clock"></i><span>Items Expired</span></a>
  </div>

  <!-- Item Billing Management -->
  <a class="has-submenu"><i class="fa fa-file-invoice"></i><span>Item Billing Management</span></a>
  <div class="submenu">
    <a href="{{ route('admin.billing.pos') }}"><i class="fa fa-cash-register"></i><span>POS - Point of Sales</span></a>
    <a href="{{ route('admin.billing.paidList') }}"><i class="fa fa-check"></i><span>Paid List</span></a>
    <a href="{{ route('admin.billing.incompletePayments') }}"><i class="fa fa-exclamation-triangle"></i><span>Incomplete Payment List</span></a>
  </div>

  <!-- Expenditure Management -->
  <a class="has-submenu"><i class="fa fa-credit-card"></i><span>Expenditure Management</span></a>
  <div class="submenu">
    <a href="{{ route('admin.expenditure.category') }}"><i class="fa fa-layer-group"></i><span>Expenditure Category</span></a>
    <a href="{{ route('admin.expenditure.list') }}"><i class="fa fa-list"></i><span>Expenditure List</span></a>
  </div>

  <!-- Report Management -->
  <a class="has-submenu"><i class="fa fa-chart-line"></i><span>Report Management</span></a>
  <div class="submenu">
    <a href="{{ route('admin.report.sales') }}"><i class="fa fa-shopping-cart"></i><span>Sales Report</span></a>
    <a href="{{ route('admin.report.cashbook') }}"><i class="fa fa-book"></i><span>Cash Book Report</span></a>
    <a href="{{ route('admin.report.income') }}"><i class="fa fa-money-bill"></i><span>Income Report</span></a>
  </div>

  <a href="{{ route('admin.customers.index') }}"><i class="fa fa-users"></i><span>Customers</span></a>
  <a href="{{ route('admin.suppliers.index') }}"><i class="fa fa-truck"></i><span>Suppliers</span></a>
  <a href="{{ route('admin.quotations.index') }}"><i class="fa fa-quote-right"></i><span>Quotation</span></a>
  <a href="{{ route('admin.invoices.index') }}"><i class="fa fa-receipt"></i><span>Invoice</span></a>

  <strong>SYSTEM SETTING</strong>
  <!-- Staff Management -->
  <a class="has-submenu"><i class="fa fa-user-cog"></i><span>Staff Management</span></a>
  <div class="submenu">
    <a href="{{ route('admin.staff.list') }}"><i class="fa fa-users"></i><span>Staff List</span></a>
    <a href="{{ route('admin.staff.department') }}"><i class="fa fa-building"></i><span>Department List</span></a>
    <a href="{{ route('admin.staff.position') }}"><i class="fa fa-briefcase"></i><span>Position List</span></a>
    <a href="{{ route('admin.staff.user') }}"><i class="fa fa-user-shield"></i><span>User Management</span></a>
  </div>

  <!-- Geographical Location -->
  <a class="has-submenu"><i class="fa fa-map-marker-alt"></i><span>Geographical Location</span></a>
  <div class="submenu">
    <a href="{{ route('admin.geo.regional') }}"><i class="fa fa-map"></i><span>Regional</span></a>
    <a href="{{ route('admin.geo.district') }}"><i class="fa fa-map-pin"></i><span>District</span></a>
  </div>

  <a href="{{ route('admin.backup') }}"><i class="fa fa-database"></i><span>Database Backup</span></a>

  {{-- Logout --}}
  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
  <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    <i class="fa fa-sign-out-alt"></i><span>Logout</span>
  </a>
</div>

<!-- Main -->
<div class="main" id="main">
  <div class="topbar">
    <div class="toggle-btn" id="toggleBtn"><i class="fa fa-bars"></i></div>
    <div>IT Support: +255 626742729 | Email: nesostar0@gmail.com</div>
    <div><i class="fa fa-user"></i> {{ Auth::user()->name ?? 'Admin' }} ({{ Auth::user()->role ?? 'Role' }})</div>
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

    if (window.innerWidth <= 768) {
      // MOBILE: slide in/out
      sidebar.classList.toggle('show');
    } else {
      // DESKTOP: collapse sidebar
      sidebar.classList.toggle('collapsed');
      main.classList.toggle('collapsed');
    }

  });

  // Close sidebar when clicking outside (mobile only)
  document.addEventListener('click', function (e) {
    if (window.innerWidth <= 768) {
      if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
        sidebar.classList.remove('show');
      }
    }
  });

  // Submenu toggle (desktop + mobile)
  document.querySelectorAll('.has-submenu').forEach(menu => {
    menu.addEventListener('click', () => {
      menu.classList.toggle('open');
    });
  });
</script>

<!-- Cache-busting JS -->
    <script src="{{ asset('js/app.js') }}?v={{ time() }}"></script>
</body>
</html>