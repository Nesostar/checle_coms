<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Deposit;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Item;
use App\Models\EntryType;
use App\Models\InventoryTransaction;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Income;
use App\Models\Expenditure;
use App\Models\ExpenditureCategory;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Quotation;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use App\Models\Regional;
use App\Models\District;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;


class AdminController extends Controller
{
    // ===============================
    // CORE MODULES
    // ===============================
     
      // Display the admin dashboard.
     
    public function admin()
    {
        return view('admin.dashboard');
    }

    public function purchase()
    {
        return view('admin.purchase');
    }

    public function deposit()
    {
        return view('admin.deposit');
    }

    public function customers()
    {
        return view('admin.customers');
    }

    public function suppliers()
    {
        return view('admin.suppliers');
    }

    public function quotation()
    {
        return view('admin.quotation');
    }

    public function invoice()
    {
        return view('admin.invoice');
    }

    public function backup()
    {
        return view('admin.backup');
    }

// ======================
// DASHBOARD
// ======================
public function dashboard()
{
    $today = now()->toDateString();

    // ----------------------------
    // 1️⃣ Inventory Metrics (for both admin & cashier)
    // ----------------------------
    $items = \App\Models\Item::with('subcategory.category')
                ->orderBy('name')
                ->get();

    $transactions = \App\Models\InventoryTransaction::with('entryType')
                        ->orderBy('created_at')
                        ->orderBy('id')
                        ->get();

    $itemBalances = [];
    foreach ($transactions as $transaction) {
        $id = $transaction->item_id;
        $itemBalances[$id] ??= 0;

        switch ($transaction->entryType->direction ?? 'in') {
            case 'in': $itemBalances[$id] += $transaction->quantity; break;
            case 'out': case 'damage': $itemBalances[$id] -= $transaction->quantity; break;
            case 'adjustment': $itemBalances[$id] = $transaction->quantity; break;
        }
    }

    foreach ($items as $item) {
        $itemBalances[$item->id] ??= $item->quantity;
    }

    $outOfStock = collect($itemBalances)->filter(fn($b) => $b <= 0)->count();
    $expiredItems = \App\Models\Item::whereNotNull('expiry_date')
                        ->where('expiry_date', '<', $today)
                        ->count();

    $topProducts = \App\Models\SaleItem::whereHas('sale', function ($q) use ($today) {
        $q->whereDate('sale_date', $today)
          ->where('user_id', auth()->id()); // only admin’s own sales
    })
    ->select('item_id')
    ->selectRaw('SUM(quantity) as qty, SUM(subtotal) as total')
    ->groupBy('item_id')
    ->orderByDesc('qty')
    ->limit(5)
    ->with('item')
    ->get();

    // ----------------------------
    // 2️⃣ Admin Sales & Expenses (optional, admin-only)
    // ----------------------------
    $todaySales = \App\Models\Sale::whereDate('sale_date', $today)
                    ->where('user_id', auth()->id()) // only admin
                    ->sum('total_amount');

    $todayExpenses = \App\Models\Expenditure::whereDate('date', $today)
                        ->where('user_id', auth()->id()) // only admin
                        ->sum('amount');

    return view('admin.dashboard', compact(
        'todayExpenses',
        'todaySales',
        'outOfStock',
        'expiredItems',
        'topProducts',
        'items',
        'itemBalances'
    ));
}







// ========================
    // PURCHASE
// ========================

public function purchaseIndex()
{
    // fetch items and purchases and pass to view
    $items = Item::all();
    $purchases = Purchase::with('item')->latest()->get();
    return view('admin.purchase', compact('items', 'purchases'));
}

public function purchaseStore(Request $request)
{
    $request->validate([
        'item_id' => 'required|exists:items,id',
        'quantity' => 'required|integer|min:1',
        'purchase_price' => 'nullable|numeric',
        'supplier' => 'nullable|string',
        'purchase_date' => 'required|date',
    ]);

    $purchase = Purchase::create([
        'item_id' => $request->item_id,
        'quantity' => $request->quantity,
        'purchase_price' => $request->purchase_price,
        'supplier' => $request->supplier,
        'purchase_date' => $request->purchase_date,
        'note' => $request->note,
    ]);

    // create inventory transaction (assumes entry_type_id 1 is Purchase / Stock IN)
    InventoryTransaction::create([
        'item_id' => $request->item_id,
        'entry_type_id' => 1,
        'quantity' => $request->quantity,
        'note' => 'Stock purchased (#' . $purchase->id . ')'
    ]);

    return redirect()->route('admin.purchase.index')->with('success', 'Purchase recorded and stock updated successfully.');
}

// =========================
// DEPOSIT
// ========================

// Display all deposits (admin sees everything)
public function depositIndex()
{
    $deposits = Deposit::with('user')->latest()->get(); // include user info
    return view('admin.deposit', compact('deposits'));
}

// Store a new deposit
public function depositStore(Request $request)
{
    $request->validate([
        'depositor_name' => 'required|string',
        'amount' => 'required|numeric',
        'deposit_date' => 'required|date',
        'payment_method' => 'required|string',
    ]);

    Deposit::create([
        'depositor_name' => $request->depositor_name,
        'amount' => $request->amount,
        'deposit_date' => $request->deposit_date,
        'payment_method' => $request->payment_method,
        'description' => $request->description,
        'user_id' => auth()->id(),
        'role' => auth()->user()->role, // should be 'admin'
    ]);

    return back()->with('success', 'Deposit recorded successfully.');
}


// ===============================
    // ITEMS MANAGEMENT
    // ===============================
    public function itemCategory()
    {
        // Show list of categories (this is your main "Item Category" page)
        $categories = Category::latest()->get();
        return view('admin.items.category', compact('categories'));
    }

    // ===============================
    // ITEM CATEGORY CRUD
    // ===============================
    public function categoryIndex()
{
    $categories = Category::all();   // or paginate if needed
    return view('admin.items.category', compact('categories'));
}

    public function categoryCreate()
    {
        // Show create form inside the same module
        return view('admin.items.category');
    }

    public function categoryStore(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name|max:100',
        ]);

        Category::create([
            'name' => $request->name,
            'created_by' => Auth::user()->name ?? 'Admin',
        ]);

        return redirect()->route('admin.items.category')
            ->with('success', 'Category created successfully.');
    }

    public function categoryEdit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.items.category', compact('category'));
    }

    public function categoryUpdate(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id . '|max:100',
        ]);

        $category->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.items.category')
            ->with('success', 'Category updated successfully.');
    }

    public function categoryDestroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.items.category')
            ->with('success', 'Category deleted successfully.');
    }


// ===============================
// ITEM SUBCATEGORY MANAGEMENT
// ===============================
 //  Display list + form
 public function itemSubCategory()
{
    $categories     = Category::all();
    $subcategories  = Subcategory::with('category')->get();
    $subcategory    = null; // No editing on initial load

    return view('admin.items.subcategory', compact('categories', 'subcategories', 'subcategory'));
}

public function subcategoryEdit($id)
{
    $subcategory    = Subcategory::findOrFail($id);
    $categories     = Category::all();
    $subcategories  = Subcategory::with('category')->get();

    return view('admin.items.subcategory', compact('categories', 'subcategories', 'subcategory'));
}

public function subcategoryStore(Request $request)
{
    Subcategory::create($request->all());
    return back()->with('success', 'Subcategory added');
}

public function subcategoryUpdate(Request $request, $id)
{
    $subcategory = Subcategory::findOrFail($id);
    $subcategory->update($request->all());
    return back()->with('success', 'Subcategory updated');
}

public function subcategoryDestroy($id)
{
    Subcategory::destroy($id);
    return back()->with('success', 'Subcategory deleted');
}



// ===============================
// ITEM NAME MANAGEMENT
// ===============================
public function itemName()
{
    $items = Item::with('category', 'subcategory')->latest()->get();
    $categories = Category::all();
    $subcategories = Subcategory::all();
    return view('admin.items.name', compact('items', 'categories', 'subcategories'));
}

public function itemNameCreate()
{
    $categories = Category::all();
    $subcategories = Subcategory::all();
    return view('admin.items.name.create', compact('categories', 'subcategories'));
}

public function itemNameStore(Request $request)
{
    $request->validate([
        'name' => 'required|max:100|unique:items,name',
        'subcategory_id' => 'required|exists:subcategories,id',
    ]);

    $subcategory = Subcategory::find($request->subcategory_id);

    Item::create([
        'name' => $request->name,
        'subcategory_id' => $request->subcategory_id,
        'category_id' => $subcategory->category_id, // <-- auto assign category
        'created_by' => Auth::user()->name ?? 'Admin',
    ]);

    return redirect()->route('admin.items.name')->with('success', 'Item added successfully.');
}

public function itemNameEdit($id)
{
    $item = Item::findOrFail($id);
    $categories = Category::all();
    $subcategories = Subcategory::all();
    return view('admin.items.name.edit', compact('item', 'categories', 'subcategories'));
}

public function itemNameUpdate(Request $request, $id)
{
    $item = Item::findOrFail($id);

    $request->validate([
        'name' => 'required|max:100|unique:items,name,' . $item->id,
        'category_id' => 'required|exists:categories,id',
        'subcategory_id' => 'required|exists:subcategories,id',
    ]);

    $item->update([
        'name' => $request->name,
        'category_id' => $request->category_id,
        'subcategory_id' => $request->subcategory_id,
    ]);

    return redirect()->route('admin.items.name')->with('success', 'Item updated successfully.');
}

public function itemNameDestroy($id)
{
    $item = Item::findOrFail($id);
    $item->delete();

    return redirect()->route('admin.items.name')->with('success', 'Item deleted successfully.');
}

////////////
//  ENTRY TYPE
/////////////

public function entryType()
{
    $entryTypes = EntryType::orderBy('id')->get();
    return view('admin.items.entry_type', compact('entryTypes'));
}

// Store a new entry type
public function entryTypeStore(Request $request)
{
    $request->validate([
        'name' => 'required|max:100',
        'direction' => 'required|in:in,out,damage,adjustment',
        'description' => 'nullable|string',
    ]);

    EntryType::create([
        'name' => $request->name,
        'direction' => $request->direction,
        'description' => $request->description,
    ]);

    return back()->with('success', 'Entry type added successfully.');
}

// Update entry type
public function entryTypeUpdate(Request $request, $id)
{
    $request->validate([
        'name' => 'required|max:100',
        'direction' => 'required|in:in,out,damage,adjustment',
        'description' => 'nullable|string',
    ]);

    $entryType = EntryType::findOrFail($id);
    $entryType->update([
        'name' => $request->name,
        'direction' => $request->direction,
        'description' => $request->description,
    ]);

    return back()->with('success', 'Entry type updated successfully.');
}

// Delete entry type
public function entryTypeDestroy($id)
{
    $entryType = EntryType::findOrFail($id);
    $entryType->delete();

    return back()->with('success', 'Entry type deleted successfully.');
}

//INVENTORY/STOCK

public function inventory()
{
    // Get all items with subcategory & category
    $items = Item::with('subcategory.category')->get();

    // Get all stock types
    $entryTypes = EntryType::all();

    // Get all transactions with related item & entryType, ordered chronologically
    $transactions = InventoryTransaction::with('item', 'entryType')
        ->orderBy('created_at')
        ->orderBy('id')
        ->get();

    return view('admin.items.inventory', compact('items', 'entryTypes', 'transactions'));
}

/**
 * Store new stock entry
 */
public function inventoryStore(Request $request)
{
    $request->validate([
        'item_id'       => 'required|exists:items,id',
        'entry_type_id' => 'required|exists:entry_types,id',
        'quantity'      => 'required|numeric|min:1',
        'note'          => 'nullable|string',
        'retail_price'  => 'nullable|numeric',
        'whole_price'   => 'nullable|numeric',
        'expiry_date'   => 'nullable|date',
    ]);

    $item = Item::findOrFail($request->item_id);
    $entryType = EntryType::findOrFail($request->entry_type_id);

    // Apply stock movement
    switch ($entryType->direction) {
        case 'in':
            $item->quantity += $request->quantity;
            break;

        case 'out':
        case 'damage':
            if ($item->quantity < $request->quantity) {
                return back()->withErrors('Insufficient stock available.');
            }
            $item->quantity -= $request->quantity;
            break;

        case 'adjustment':
            $item->quantity = $request->quantity;
            break;
    }

    // Update prices & expiry if provided
    if ($request->retail_price) $item->retail_price = $request->retail_price;
    if ($request->whole_price) $item->whole_price = $request->whole_price;
    if ($request->expiry_date) $item->expiry_date = $request->expiry_date;

    $item->save();

    InventoryTransaction::create([
        'item_id'       => $item->id,
        'entry_type_id' => $entryType->id,
        'quantity'      => $request->quantity,
        'note'          => $request->note,
        'expiry_date'   => $request->expiry_date,
    ]);

    return redirect()->back()->with('success', 'Stock entry added successfully.');
}


/**
 * Update existing stock entry
 */
public function inventoryUpdate(Request $request, $id)
{
    $transaction = InventoryTransaction::findOrFail($id);
    $item = Item::findOrFail($transaction->item_id);
    $entryType = EntryType::findOrFail($transaction->entry_type_id);

    $request->validate([
        'quantity' => 'required|numeric|min:1',
        'note'     => 'nullable|string',
    ]);

    /**
     * STEP 1: Revert old stock
     */
    if ($entryType->direction === 'in') {
        $item->quantity -= $transaction->quantity;
    } elseif ($entryType->direction === 'out') {
        $item->quantity += $transaction->quantity;
    }

    /**
     * STEP 2: Apply new stock
     */
    if ($entryType->direction === 'in') {
        $item->quantity += $request->quantity;
    } elseif ($entryType->direction === 'out') {
        if ($item->quantity < $request->quantity) {
            return back()->withErrors('Insufficient stock after update.');
        }
        $item->quantity -= $request->quantity;
    }

    $item->save();

    $transaction->update([
        'quantity' => $request->quantity,
        'note'     => $request->note,
    ]);

    return redirect()->back()->with('success', 'Stock entry updated successfully.');
}

/**
 * Delete stock entry
 */
public function inventoryDestroy($id)
{
    $transaction = InventoryTransaction::findOrFail($id);
    $item = Item::findOrFail($transaction->item_id);
    $entryType = EntryType::findOrFail($transaction->entry_type_id);

    // Restore stock before deleting
    if ($entryType->direction === 'in') {
        $item->quantity -= $transaction->quantity;
    } elseif ($entryType->direction === 'out') {
        $item->quantity += $transaction->quantity;
    }

    $item->save();
    $transaction->delete();

    return redirect()->back()->with('success', 'Stock entry deleted successfully.');
}


// ===============================
// STOCK ADJUSTMENT
// ===============================
public function stockAdjustment()
{
    $transactions = InventoryTransaction::with('item', 'entryType')->latest()->get();
    $items = Item::all();
    $entryTypes = EntryType::all();

    return view('admin.items.stock_adjustment', compact('transactions', 'items', 'entryTypes'));
}

public function stockAdjustmentStore(Request $request)
{
    $request->validate([
        'item_id' => 'required|exists:items,id',
        'entry_type_id' => 'required|exists:entry_types,id',
        'quantity' => 'required|integer|min:1',
        'note' => 'nullable|string|max:255'
    ]);

    $entryType = EntryType::find($request->entry_type_id);

    // Apply entry effect (+ or -)
    $finalQty = $entryType->effect == '+' 
                ? $request->quantity 
                : -$request->quantity;

    InventoryTransaction::create([
        'item_id' => $request->item_id,
        'entry_type_id' => $request->entry_type_id,
        'quantity' => $finalQty,
        'note' => $request->note,
    ]);

    return back()->with('success', 'Stock successfully adjusted.');
}


// ===============================
// OUT OF STOCK
// ===============================
public function outOfStock()
{
    $items = Item::with('category', 'subcategory')
        ->get()
        ->filter(function ($item) {
            return $item->getCurrentStock() <= 0;
        });

    return view('admin.items.out_of_stock', compact('items'));
}



// ===============================
// EXPIRED ITEMS
// ===============================
public function expiredItems()
{
    $today = now()->toDateString();

    $expired = InventoryTransaction::with('item')
        ->whereNotNull('expiry_date')
        ->where('expiry_date', '<', $today)
        ->where('quantity', '>', 0)
        ->get()
        ->groupBy('item_id');

    return view('admin.items.expired', compact('expired'));
}


    // ===============================
    // ITEM BILLING MANAGEMENT
    // ===============================
    public function pos()
    {
        // Get all items with their subcategory and category
        $items = Item::with('subcategory.category')
            ->orderBy('name')
            ->get();
    
        // Calculate running balances
        $itemBalances = [];
        $transactions = InventoryTransaction::with('item', 'entryType')
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    
        foreach ($transactions as $transaction) {
            $itemId = $transaction->item_id;
    
            if (!isset($itemBalances[$itemId])) {
                $itemBalances[$itemId] = 0;
            }
    
            $direction = $transaction->entryType->direction ?? 'in';
    
            switch ($direction) {
                case 'in':
                    $itemBalances[$itemId] += $transaction->quantity;
                    break;
                case 'out':
                case 'damage':
                    $itemBalances[$itemId] -= $transaction->quantity;
                    break;
                case 'adjustment':
                    $itemBalances[$itemId] = $transaction->quantity;
                    break;
            }
        }
    
        // For items with no transactions, use database quantity
        foreach ($items as $item) {
            if (!isset($itemBalances[$item->id])) {
                $itemBalances[$item->id] = $item->quantity;
            }
        }
    
        return view('admin.billing.pos', compact('items', 'itemBalances'));
    }
    
    public function posStore(Request $request)
{
    $request->validate([
        'customer_name'        => 'nullable|string|max:255',
        'amount_paid'          => 'required|numeric|min:0',
        'items'                => 'required|array|min:1',
        'items.*.item_id'      => 'required|exists:items,id',
        'items.*.quantity'     => 'required|numeric|min:0.1',
        'items.*.price'        => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();

    try {
        // 1️⃣ Calculate total
        $totalAmount = collect($request->items)
            ->sum(fn ($i) => $i['quantity'] * $i['price']);

        // 2️⃣ Payment status
        $paymentStatus = $request->amount_paid >= $totalAmount
            ? 'paid'
            : 'pending';

        // 3️⃣ Create SALE
        $sale = Sale::create([
            'user_id'       => auth()->id(),
            'customer_name' => $request->customer_name ?? 'Walk-in Customer',
            'sale_date'     => now(),
            'total_amount'  => $totalAmount,
            'amount_paid'   => $request->amount_paid,
            'balance'       => $totalAmount - $request->amount_paid,
            'payment_status'=> $paymentStatus,
        ]);

        // 4️⃣ OUT entry type
        $outType = EntryType::where('direction', 'out')->firstOrFail();

        // 5️⃣ Process sale items (ONLY ONCE ✅)
        foreach ($request->items as $itemData) {

            $item = Item::findOrFail($itemData['item_id']);

            // ✅ Check stock
            $currentStock = $item->getCurrentStock();
            if ($currentStock < $itemData['quantity']) {
                throw new \Exception(
                    "Insufficient stock for {$item->name}. Available: {$currentStock} Kg"
                );
            }

            // ✅ Save SALE ITEM (THIS IS POS)
            SaleItem::create([
                'sale_id'    => $sale->id,
                'item_id'    => $item->id,
                'quantity'   => $itemData['quantity'],   // ✅ FIXED
                'price'      => $itemData['price'],
                'price_type' => $itemData['price_type'],
                'subtotal'   => $itemData['price'] * $itemData['quantity'], // ✅ FIXED
            ]);
            
            
                  

            // ✅ Inventory OUT
            InventoryTransaction::create([
                'item_id'       => $item->id,
                'entry_type_id' => $outType->id,
                'quantity'      => $itemData['quantity'],
                'note'          => "POS Sale #{$sale->id} - {$item->name}",
            ]);
        }

        DB::commit();

        return redirect()
            ->route('admin.billing.pos')
            ->with('success', "Sale #{$sale->id} completed successfully!");

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage())->withInput();
    }
}

    

    
    // ===============================
    // PAID SALES LIST (COMPLETED)
    // ===============================
    public function paidList()
    {
        $sales = Sale::where('payment_status', 'paid')->orderBy('id', 'DESC')->get();
        return view('admin.billing.paid_list', compact('sales'));
    }

    // ===============================
    // INCOMPLETE PAYMENTS (CREDIT)
    // ===============================
    public function incompletePayments()
    {
        $sales = Sale::where('payment_status', 'incomplete')->orderBy('id', 'DESC')->get();
        return view('admin.billing.incomplete_payments', compact('sales'));
    }

    // Mark Incomplete Sale as Paid
    public function markPaid($id)
    {
        $sale = Sale::find($id);

        if (!$sale) {
            return redirect()->back()->with('error', 'Sale not found.');
        }

        $sale->payment_status = 'paid';
        $sale->save();

        return redirect()->back()->with('success', 'Payment Updated to PAID.');
    }

    // ===============================
    // EXPENDITURE MANAGEMENT
    // ===============================

    // Show Expenditure List
    public function expenditureList()
    {
        $expenditures = Expenditure::with('category')->orderBy('created_at','desc')->get();
        $categories = ExpenditureCategory::all(); // pass categories to the view
        return view('admin.expenditure.list', compact('expenditures', 'categories'));
    }

    // Store new expenditure
    public function expenditureStore(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|exists:expenditure_categories,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'spent_on' => 'required|date',
            'description' => 'nullable|string'
        ]);

        Expenditure::create([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->spent_on,
            'user_id' => auth()->id(),
        ]);
        

        return redirect()->back()->with('success', 'Expenditure added successfully.');
    }

public function expenditureUpdate(Request $request, $id)
{
    $expenditure = Expenditure::findOrFail($id);

    $request->validate([
        'category_id' => 'required|exists:expenditure_categories,id',
        'amount' => 'required|numeric|min:0',
        'description' => 'nullable|string',
        'date' => 'required|date',
    ]);

    $expenditure->update($request->all());

    return back()->with('success', 'Expenditure updated successfully.');
}

public function expenditureDestroy($id)
{
    $expenditure = Expenditure::findOrFail($id);
    $expenditure->delete();

    return back()->with('success', 'Expenditure deleted successfully.');
}

// Expenditure Category
 // Show Expenditure Categories
 public function expenditureCategory()
 {
     $categories = ExpenditureCategory::all();
     return view('admin.expenditure.category', compact('categories'));
 }

 // Store New Category
public function expenditureCategoryStore(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string'
    ]);

    ExpenditureCategory::create([
        'name' => $request->name,
        'description' => $request->description
    ]);

    return back()->with('success', 'Category added successfully.');
}

// Update Category
public function expenditureCategoryUpdate(Request $request, $id)
{
    $category = ExpenditureCategory::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string'
    ]);

    $category->update([
        'name' => $request->name,
        'description' => $request->description
    ]);

    return back()->with('success', 'Category updated successfully.');
}

 // Delete Category
 public function expenditureCategoryDestroy($id)
 {
     $category = ExpenditureCategory::findOrFail($id);
     $category->delete();

     return redirect()->back()->with('success', 'Category deleted successfully.');
 }


    // ===============================
    // REPORT MANAGEMENT
    // ===============================
    // SALES REPORT
public function salesReport(Request $request)
{
    $from = $request->from_date ?? now()->subMonth()->toDateString();
    $to   = $request->to_date ?? now()->toDateString();

    $sales = Sale::with('items.item')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to)
                ->orderBy('created_at','desc')
                ->get();

    $total = $sales->sum('total_amount');
    $paidTotal = $sales->sum('amount_paid');

    return view('admin.report.sales', compact('sales','from','to','total','paidTotal'));
}

// CASH BOOK REPORT
public function cashbookReport(Request $request)
{
    $from = $request->from_date ?? now()->subMonth()->toDateString();
    $to   = $request->to_date ?? now()->toDateString();

    // Money IN: sales (amount_paid) + incomes
    $salesIn = Sale::whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to)->sum('amount_paid');
    $incomes = Income::whereDate('income_date','>=',$from)->whereDate('income_date','<=',$to)->sum('amount');


    $moneyIn = $salesIn + $incomes;

    // Money OUT: expenditures + purchases (if you track purchases as cash out)
    $expenses = Expenditure::whereDate('date','>=',$from)->whereDate('date','<=',$to)->sum('amount');
    $purchases = Purchase::whereDate('purchase_date', '>=', $from)->whereDate('purchase_date', '<=', $to)->get()->sum(function($purchase) {return $purchase->quantity * $purchase->purchase_price;});


    $moneyOut = $expenses + $purchases;

    $balance = $moneyIn - $moneyOut;

    // fetch lists to show in view
    $salesList = Sale::whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to)->orderBy('created_at','desc')->get();
    $incomeList = Income::whereDate('income_date','>=',$from)->whereDate('income_date','<=',$to)->orderBy('income_date','desc')->get();
    $expenseList = Expenditure::whereDate('date','>=',$from)->whereDate('date','<=',$to)->orderBy('date','desc')->get();
    $purchaseList = Purchase::whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to)->orderBy('created_at','desc')->get();

    return view('admin.report.cashbook', compact('from','to','moneyIn','moneyOut','balance', 'salesList','incomeList','expenseList','purchaseList'));
}

// INCOME REPORT
public function incomeReport(Request $request)
{
    $from = $request->from_date ?? now()->subMonth()->toDateString();
    $to   = $request->to_date ?? now()->toDateString();

    // Use the correct column name: income_date
    $query = Income::with('category')->whereBetween('income_date', [$from, $to]);

    $incomes = $query->orderBy('income_date','desc')->get();
    $totalIncome = $incomes->sum('amount');

    // Grouped by category
    $byCategory = $incomes->groupBy(function($i) {
        return $i->category->name ?? 'Uncategorized';
    })->map(function($group) {
        return $group->sum('amount');
    });

    return view('admin.report.income', compact('incomes','from','to','totalIncome','byCategory'));
}

/* =========================
   CASH BOOK PDF
========================= */
public function cashBookPdf(Request $request)
{
    $data = $this->cashBookReport($request)->getData();

    $pdf = Pdf::loadView('admin.pdf.cashbook', (array) $data)
        ->setPaper('A4', 'portrait');

    return $pdf->download('Cash_Book_Report.pdf');
}

/* =========================
   INCOME REPORT PDF
========================= */
public function incomeReportPdf(Request $request)
{
    $data = $this->incomeReport($request)->getData();

    $pdf = Pdf::loadView('admin.pdf.income', (array) $data)
        ->setPaper('A4', 'portrait');

    return $pdf->download('Income_Report.pdf');
}

/* =========================
   SALES REPORT PDF
========================= */
public function salesReportPdf(Request $request)
{
    $data = $this->salesReport($request)->getData();

    $pdf = Pdf::loadView('admin.pdf.sales', (array) $data)
        ->setPaper('A4', 'landscape');

    return $pdf->download('Sales_Report.pdf');
}

  // ===============
 // CUSTOMERS
 // ===============

 // LIST CUSTOMERS
 public function customersIndex()
 {
     $customers = Customer::orderBy('name')->get();
     return view('admin.customers', compact('customers'));
 }
 
 // STORE CUSTOMER
 public function customersStore(Request $request)
 {
     $request->validate([
         'name' => 'required',
     ]);
 
     Customer::create($request->all());
 
     return redirect()->back()->with('success', 'Customer added successfully.');
 }
 
 // UPDATE CUSTOMER
 public function customersUpdate(Request $request, $id)
 {
     $customer = Customer::findOrFail($id);
     $customer->update($request->all());
 
     return redirect()->back()->with('success', 'Customer updated successfully.');
 }
 
 // DELETE CUSTOMER
 public function customersDelete($id)
 {
     Customer::findOrFail($id)->delete();
 
     return redirect()->back()->with('success', 'Customer deleted successfully.');
 }

 
 //===============
 // SUPPLIERS
 //=================

 // LIST SUPPLIERS
 public function index()
 {
     $suppliers = Supplier::orderBy('name')->get();
     return view('admin.suppliers', compact('suppliers'));

 }

 // STORE SUPPLIER
 public function store(Request $request)
 {
     $request->validate([
         'name'   => 'required|string|max:255',
         'phone'  => 'nullable|string|max:30',
         'email'  => 'nullable|email|max:255',
         'address'=> 'nullable|string',
         'details'=> 'nullable|string',
     ]);

     Supplier::create([
         'name'    => $request->name,
         'phone'   => $request->phone,
         'email'   => $request->email,
         'address' => $request->address,
         'details' => $request->details,
     ]);

     return redirect()->back()->with('success', 'Supplier added successfully.');
 }

 // UPDATE SUPPLIER
 public function update(Request $request, $id)
 {
     $supplier = Supplier::findOrFail($id);

     $request->validate([
         'name'   => 'required|string|max:255',
         'phone'  => 'nullable|string|max:30',
         'email'  => 'nullable|email|max:255',
         'address'=> 'nullable|string',
         'details'=> 'nullable|string',
     ]);

     $supplier->update([
         'name'    => $request->name,
         'phone'   => $request->phone,
         'email'   => $request->email,
         'address' => $request->address,
         'details' => $request->details,
     ]);

     return redirect()->back()->with('success', 'Supplier updated successfully.');
 }

 // DELETE SUPPLIER
 public function delete($id)
 {
     Supplier::findOrFail($id)->delete();

     return redirect()->back()->with('success', 'Supplier deleted successfully.');
 }


  // ====================
// QUOTATIONS - SINGLE PAGE OPERATIONS
// ====================

public function quotations()
{
    // Fetch all quotations with customer & items for display
    $quotations = Quotation::with('customer', 'items')->orderBy('id','desc')->get();

    // Fetch customers for dropdown in form
    $customers = Customer::orderBy('name')->get();

    return view('admin.quotations', compact('quotations','customers'));
}

public function quotationStore(Request $request)
{
    $request->validate([
        'customer_id' => 'required',
        'date'        => 'required|date',
        'total'       => 'required|numeric',
        'items'       => 'required|array|min:1',
        'items.*.name'     => 'required|string',
        'items.*.qty'      => 'required|numeric|min:1',
        'items.*.price'    => 'required|numeric|min:0',
        'items.*.subtotal' => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();

    try {
        // Create or update quotation
        $quotation = Quotation::updateOrCreate(
            ['id' => $request->id ?? null],
            [
                'customer_id' => $request->customer_id,
                'date'        => $request->date,
                'reference'   => $request->reference ?? null,
                'notes'       => $request->notes ?? null,
                'total'       => $request->total,
            ]
        );

        // Remove old items if updating
        if($request->id){
            QuotationItem::where('quotation_id', $quotation->id)->delete();
        }

        // Save quotation items
        foreach ($request->items as $item) {
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'item_name'    => $item['name'],
                'quantity'     => $item['qty'],
                'price'        => $item['price'],
                'subtotal'     => $item['subtotal'],
            ]);
        }

        DB::commit();
        return back()->with('success', 'Quotation saved successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error: '.$e->getMessage());
    }
}

public function quotationDelete($id)
{
    Quotation::findOrFail($id)->delete();
    return back()->with('success','Quotation deleted.');
}

public function quotationShow($id)
{
    $quotation = Quotation::with('items','customer')->findOrFail($id);
    return view('admin.quotations.show', compact('quotation'));
}

public function quotationPrint($id)
{
    $quotation = Quotation::with('items','customer')->findOrFail($id);
    return view('admin.quotations.print', compact('quotation'));
}

// ========================
// INVOICE
// ======================
public function invoices()
{
    $invoices = Invoice::with('customer')
        ->orderBy('id', 'desc')
        ->get();

    $customers = Customer::orderBy('name')->get();
    $items = Item::orderBy('name')->get(); // products

    return view('admin.invoice', compact(
        'invoices',
        'customers',
        'items'
    ));
}

/* =========================
   STORE / UPDATE INVOICE
========================== */
public function invoiceStore(Request $request)
{
    $request->validate([
        'customer_id' => 'required|exists:customers,id',
        'invoice_date' => 'required|date',
        'due_date' => 'required|date',
        'status' => 'required',
        'items' => 'required|array|min:1',
        'items.*.item_id' => 'required|exists:items,id',
        'items.*.qty' => 'required|numeric|min:1',
        'items.*.price' => 'required|numeric|min:0',
    ]);

    DB::transaction(function () use ($request) {

        $invoice = Invoice::updateOrCreate(
            ['id' => $request->id],
            [
                'customer_id' => $request->customer_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'status' => $request->status,
                'total' => 0
            ]
        );

        // Delete old items if updating
        InvoiceItem::where('invoice_id', $invoice->id)->delete();

        $total = 0;

        foreach ($request->items as $row) {

            $item = Item::findOrFail($row['item_id']);

            // Use price from the form
            $price = $row['price'];
            $qty = $row['qty'];
            $subtotal = $price * $qty;

            // Create InvoiceItem
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_id' => $item->id,
                'quantity' => $qty,
                'price' => $price,
                'subtotal' => $subtotal,
            ]);

            $total += $subtotal;
        }

        $invoice->update(['total' => $total]);
    });

    return back()->with('success', 'Invoice saved successfully');
}

public function downloadInvoice($id)
{
    $invoice = Invoice::with(['customer', 'items.item'])->findOrFail($id);
    $pdf = Pdf::loadView('admin.pdf', compact('invoice'))->setPaper('A4', 'portrait');
    return $pdf->download('Invoice_'.$invoice->id.'.pdf');
}

/* =========================
   DELETE INVOICE
========================== */
public function invoiceDelete($id)
{
    Invoice::findOrFail($id)->delete();
    return back()->with('success', 'Invoice deleted successfully');
}



    // ===============================
    // STAFF MANAGEMENT
    // ===============================
    // Get all staff
public function staffList()
{
    $staff = User::with('department')->where('role', 'staff')->latest()->get();
    $departments = Department::all();
    return view('admin.staff.list', compact('staff', 'departments'));
}

// Store new staff
public function staffStore(Request $request)
{
    $request->validate([
        'name' => 'required|max:100',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'role' => 'required',
        'department_id' => 'nullable|exists:departments,id',
    ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password), // always hash password
        'role' => $request->role,
        'department_id' => $request->department_id,
        'created_by' => auth()->user()->name ?? 'Admin',
    ]);

    return redirect()->back()->with('success', 'Staff created successfully.');
}

// Update staff
public function staffUpdate(Request $request, $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'name' => 'required|max:100',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'role' => 'required',
        'department_id' => 'nullable|exists:departments,id',
    ]);

    $user->update([
        'name' => $request->name,
        'email' => $request->email,
        'role' => $request->role,
        'department_id' => $request->department_id,
    ]);

    return redirect()->back()->with('success', 'Staff updated successfully.');
}

// Delete staff
public function staffDestroy($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return redirect()->back()->with('success', 'Staff deleted successfully.');
}


    public function departmentList()
    {
        $departments = Department::withCount('users')->latest()->get();
        return view('admin.staff.department', compact('departments'));
    }
    
    public function departmentStore(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:departments,name|max:100',
        ]);
    
        Department::create([
            'name' => $request->name,
            'created_by' => Auth::user()->name ?? 'Admin'
        ]);
    
        return back()->with('success', 'Department created successfully.');
    }
    
    public function departmentUpdate(Request $request, $id)
    {
        $department = Department::findOrFail($id);
    
        $request->validate([
            'name' => 'required|max:100|unique:departments,name,' . $department->id,
        ]);
    
        $department->update([
            'name'        => $request->name,
        ]);
    
        return back()->with('success', 'Department updated successfully.');
    }
    
    public function departmentDestroy($id)
    {
        $department = Department::findOrFail($id);
    
        // optional block deletion if department has staff
        if ($department->users()->count() > 0) {
            return back()->with('error', 'Cannot delete — Department has assigned users.');
        }
    
        $department->delete();
        return back()->with('success', 'Department deleted successfully.');
    }

    // Position CRUD
public function positionList()
{
    $positions = Position::latest()->get();
    return view('admin.staff.position', compact('positions'));
}

public function positionStore(Request $request)
{
    $request->validate([
        'name' => 'required|unique:positions,name|max:100',
    ]);

    Position::create([
        'name' => $request->name,
    ]);

    return redirect()->route('admin.positions.index')->with('success', 'Position created successfully.');
}

public function positionUpdate(Request $request, $id)
{
    $position = Position::findOrFail($id);

    $request->validate([
        'name' => 'required|unique:positions,name,' . $position->id . '|max:100',
    ]);

    $position->update(['name' => $request->name]);

    return redirect()->route('admin.positions.index')->with('success', 'Position updated successfully.');
}

public function positionDestroy($id)
{
    $position = Position::findOrFail($id);
    $position->delete();

    return redirect()->route('admin.positions.index')->with('success', 'Position deleted successfully.');
}


    public function userManagement()
{
    $users = User::latest()->get();
    return view('admin.staff.users', compact('users'));
}

/* ----------------- CREATE USER ----------------- */
public function userStore(Request $request)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:4',
        'role' => 'required'
    ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role,
    ]);

    return redirect()->back()->with('success', 'User created successfully!');
}


/* ----------------- UPDATE USER ----------------- */
public function userUpdate(Request $request, $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users,email,' . $id,
        'role' => 'required'
    ]);

    $user->update([
        'name' => $request->name,
        'email' => $request->email,
        'role' => $request->role,
    ]);

    return redirect()->back()->with('success', 'User updated successfully!');
}


/* ----------------- DELETE USER ----------------- */
public function userDelete($id)
{
    User::findOrFail($id)->delete();
    return back()->with('success', 'User deleted successfully.');
}





//.................REGIONAL...............
public function regionalList()
{
    $regions = Regional::latest()->get(); // variable used in Blade
    return view('admin.geo.regional', compact('regions'));
}

public function regionalStore(Request $request)
{
    $request->validate([
        'name' => 'required|unique:regionals,name|max:100',
    ]);

    Regional::create(['name' => $request->name]);

    return redirect()->route('admin.regionals.index')
                     ->with('success', 'Regional added successfully.');
}

public function regionalUpdate(Request $request, $id)
{
    $region = Regional::findOrFail($id);

    $request->validate([
        'name' => 'required|max:100|unique:regionals,name,' . $region->id,
    ]);

    $region->update(['name' => $request->name]);

    return redirect()->route('admin.regionals.index')
                     ->with('success', 'Regional updated successfully.');
}

public function regionalDestroy($id)
{
    $region = Regional::findOrFail($id);
    $region->delete();

    return redirect()->route('admin.regionals.index')
                     ->with('success', 'Regional deleted successfully.');
}



// ===============================
// DISTRICT CRUD
// ===============================

// List all districts (used for index page)
public function districtList()
{
    $districts = District::latest()->get();
    return view('admin.geo.district', compact('districts'));
}

// Store a new district
public function districtStore(Request $request)
{
    $request->validate([
        'name' => 'required|unique:districts,name|max:100',
    ]);

    District::create([
        'name' => $request->name
    ]);

    return redirect()->route('admin.geo.district.index')
                     ->with('success', 'District added successfully.');
}

// Update an existing district
public function districtUpdate(Request $request, $id)
{
    $district = District::findOrFail($id);

    $request->validate([
        'name' => 'required|max:100|unique:districts,name,' . $district->id,
    ]);

    $district->update(['name' => $request->name]);

    return redirect()->route('admin.geo.district.index')
                     ->with('success', 'District updated successfully.');
}

// Delete a district
public function districtDestroy($id)
{
    $district = District::findOrFail($id);
    $district->delete();

    return redirect()->route('admin.geo.district.index')
                     ->with('success', 'District deleted successfully.');
}


//.............. BACKUP ..............


public function backupPage()
{
    $files = Storage::disk('local')->files('laravel-backups');
    return view('admin.backup', compact('files'));
}

public function runBackup()
{
    Artisan::call('backup:run');
    return redirect()->route('admin.backup')->with('success', 'Database backup created successfully.');
}

public function downloadBackup($file)
{
    return Storage::disk('local')->download("laravel-backups/{$file}");
}

public function deleteBackup($file)
{
    Storage::disk('local')->delete("laravel-backups/{$file}");
    return redirect()->route('admin.backup')->with('success', 'Backup file deleted.');
}



}
