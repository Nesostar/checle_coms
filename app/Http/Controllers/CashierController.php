<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deposit;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Item;
use App\Models\payments;
use App\Models\incomplete;
use App\Models\Expenditure;
use App\Models\ExpenditureCategory;
use App\Models\Customer;
use App\Models\Quotation;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\EntryType;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Subcategory;

class CashierController extends Controller
{
    // Cashier dashboard
    public function index()
    {
        return view('cashier.dashboard');
    }
// =================
// DASBOARD
// ===============
public function dashboard()
{
    $today = now()->toDateString();

// Inventory metrics
$items = Item::with('subcategory.category')->orderBy('name')->get();
$transactions = InventoryTransaction::with('entryType')->orderBy('created_at')->get();

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
$expiredItems = Item::whereNotNull('expiry_date')->where('expiry_date', '<', $today)->count();

$topProducts = SaleItem::whereHas('sale', function ($q) use ($today) {
    $q->whereDate('sale_date', $today)
      ->where('user_id', auth()->id());
})
->select('item_id')
->selectRaw('SUM(quantity) as qty, SUM(subtotal) as total')
->groupBy('item_id')
->orderByDesc('qty')
->limit(5)
->with('item')
->get();

// Cashier metrics
$todaySales = Sale::whereDate('sale_date', $today)
    ->where('user_id', auth()->id())
    ->sum('total_amount');

$todayExpenses = Expenditure::whereDate('date', $today)
    ->where('user_id', auth()->id())
    ->sum('amount');

return view('cashier.dashboard', compact(
    'todaySales',
    'todayExpenses',
    'outOfStock',
    'expiredItems',
    'topProducts',
    'items',
    'itemBalances'
));

}

  // Display only deposits made by this cashier
    public function depositIndex()
    {
        $deposits = Deposit::where('user_id', auth()->id())->latest()->get();
        return view('cashier.deposit', compact('deposits'));
    }

    // Store a new deposit
    public function depositStore(Request $request)
    {
        $request->validate([
            'depositor_name' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'deposit_date' => 'required|date',
        ]);

        Deposit::create([
            'user_id'        => Auth::id(),           // Who performed the deposit
            'role'           => Auth::user()->role,   // 'cashier'
            'depositor_name' => $request->depositor_name,
            'amount'         => $request->amount,
            'payment_method' => $request->payment_method,
            'deposit_date'   => $request->deposit_date,
            'description'    => $request->description,
        ]);

        return back()->with('success', 'Deposit recorded successfully.');
    }


    // =====================================================
// CASHIER ITEMS MANAGEMENT
// =====================================================

/**
 * =====================================================
 * ITEM CATEGORY
 * =====================================================
 */

/**
 * Display item categories
 */
public function itemCategory()
{
    $categories = Category::latest()->get();

    return view('cashier.items.category', compact('categories'));
}

/**
 * Category index
 */
public function categoryIndex()
{
    $categories = Category::latest()->get();

    return view('cashier.items.category', compact('categories'));
}

/**
 * Category create
 */
public function categoryCreate()
{
    $categories = Category::latest()->get();

    return view('cashier.items.category', compact('categories'));
}

/**
 * Store category
 */
public function categoryStore(Request $request)
{
    $request->validate([
        'name' => 'required|unique:categories,name|max:100',
    ]);

    Category::create([
        'name' => $request->name,
        'created_by' => Auth::user()->name ?? 'Cashier',
    ]);

    return redirect()
        ->route('cashier.items.category')
        ->with('success', 'Category created successfully.');
}

/**
 * Edit category
 */
public function categoryEdit($id)
{
    $category = Category::findOrFail($id);

    $categories = Category::latest()->get();

    return view('cashier.items.category', compact(
        'category',
        'categories'
    ));
}

/**
 * Update category
 */
public function categoryUpdate(Request $request, $id)
{
    $category = Category::findOrFail($id);

    $request->validate([
        'name' => 'required|unique:categories,name,' . $category->id . '|max:100',
    ]);

    $category->update([
        'name' => $request->name,
    ]);

    return redirect()
        ->route('cashier.items.category')
        ->with('success', 'Category updated successfully.');
}

/**
 * Delete category
 */
public function categoryDestroy($id)
{
    $category = Category::findOrFail($id);

    $category->delete();

    return redirect()
        ->route('cashier.items.category')
        ->with('success', 'Category deleted successfully.');
}


// =====================================================
// ITEM SUBCATEGORY MANAGEMENT
// =====================================================

/**
 * Display subcategories
 */
public function itemSubCategory()
{
    $categories = Category::all();

    $subcategories = Subcategory::with('category')
        ->latest()
        ->get();

    $subcategory = null;

    return view('cashier.items.subcategory', compact(
        'categories',
        'subcategories',
        'subcategory'
    ));
}

/**
 * Edit subcategory
 */
public function subcategoryEdit($id)
{
    $subcategory = Subcategory::findOrFail($id);

    $categories = Category::all();

    $subcategories = Subcategory::with('category')
        ->latest()
        ->get();

    return view('cashier.items.subcategory', compact(
        'categories',
        'subcategories',
        'subcategory'
    ));
}

/**
 * Store subcategory
 */
public function subcategoryStore(Request $request)
{
    $request->validate([
        'name' => 'required|max:100',
        'category_id' => 'required|exists:categories,id',
    ]);

    Subcategory::create([
        'name' => $request->name,
        'category_id' => $request->category_id,
    ]);

    return redirect()
        ->route('cashier.items.subcategory')
        ->with('success', 'Subcategory added successfully.');
}

/**
 * Update subcategory
 */
public function subcategoryUpdate(Request $request, $id)
{
    $subcategory = Subcategory::findOrFail($id);

    $request->validate([
        'name' => 'required|max:100',
        'category_id' => 'required|exists:categories,id',
    ]);

    $subcategory->update([
        'name' => $request->name,
        'category_id' => $request->category_id,
    ]);

    return redirect()
        ->route('cashier.items.subcategory')
        ->with('success', 'Subcategory updated successfully.');
}

/**
 * Delete subcategory
 */
public function subcategoryDestroy($id)
{
    $subcategory = Subcategory::findOrFail($id);

    $subcategory->delete();

    return redirect()
        ->route('cashier.items.subcategory')
        ->with('success', 'Subcategory deleted successfully.');
}


// =====================================================
// ITEM NAME MANAGEMENT
// =====================================================

/**
 * Display items
 */
public function itemName()
{
    $items = Item::with('category', 'subcategory')
        ->latest()
        ->get();

    $categories = Category::all();

    $subcategories = Subcategory::all();

    return view('cashier.items.name', compact(
        'items',
        'categories',
        'subcategories'
    ));
}

/**
 * Create item
 */
public function itemNameCreate()
{
    $categories = Category::all();

    $subcategories = Subcategory::all();

    return view('cashier.items.name.create', compact(
        'categories',
        'subcategories'
    ));
}

/**
 * Store item
 */
public function itemNameStore(Request $request)
{
    $request->validate([
        'name' => 'required|max:100|unique:items,name',
        'subcategory_id' => 'required|exists:subcategories,id',
    ]);

    $subcategory = Subcategory::findOrFail(
        $request->subcategory_id
    );

    Item::create([
        'name' => $request->name,
        'subcategory_id' => $request->subcategory_id,
        'category_id' => $subcategory->category_id,
        'created_by' => Auth::user()->name ?? 'Cashier',
    ]);

    return redirect()
        ->route('cashier.items.name')
        ->with('success', 'Item added successfully.');
}

/**
 * Edit item
 */
public function itemNameEdit($id)
{
    $item = Item::findOrFail($id);

    $categories = Category::all();

    $subcategories = Subcategory::all();

    return view('cashier.items.name.edit', compact(
        'item',
        'categories',
        'subcategories'
    ));
}

/**
 * Update item
 */
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

    return redirect()
        ->route('cashier.items.name')
        ->with('success', 'Item updated successfully.');
}

/**
 * Delete item
 */
public function itemNameDestroy($id)
{
    $item = Item::findOrFail($id);

    $item->delete();

    return redirect()
        ->route('cashier.items.name')
        ->with('success', 'Item deleted successfully.');
}


// =====================================================
// ENTRY TYPE MANAGEMENT
// =====================================================

/**
 * Display entry types
 */
public function entryType()
{
    $entryTypes = EntryType::with('item')
        ->orderBy('id')
        ->get();

    $items = Item::orderBy('name')->get();

    return view('cashier.items.entry_type', compact(
        'entryTypes',
        'items'
    ));
}

/**
 * Store entry type
 */
public function entryTypeStore(Request $request)
{
    $request->validate([
        'item_id' => 'required|exists:items,id',
        'name' => 'required|max:100',
        'direction' => 'required|in:in,out,damage,adjustment',
        'description' => 'nullable|string',
    ]);

    EntryType::create([
        'item_id' => $request->item_id,
        'name' => $request->name,
        'direction' => $request->direction,
        'description' => $request->description,
    ]);

    return redirect()
        ->route('cashier.items.entryType')
        ->with('success', 'Entry type added successfully.');
}

/**
 * Update entry type
 */
public function entryTypeUpdate(Request $request, $id)
{
    $request->validate([
        'item_id' => 'required|exists:items,id',
        'name' => 'required|max:100',
        'direction' => 'required|in:in,out,damage,adjustment',
        'description' => 'nullable|string',
    ]);

    $entryType = EntryType::findOrFail($id);

    $entryType->update([
        'item_id' => $request->item_id,
        'name' => $request->name,
        'direction' => $request->direction,
        'description' => $request->description,
    ]);

    return redirect()
        ->route('cashier.items.entryType')
        ->with('success', 'Entry type updated successfully.');
}

/**
 * Delete entry type
 */
public function entryTypeDestroy($id)
{
    $entryType = EntryType::findOrFail($id);

    $entryType->delete();

    return redirect()
        ->route('cashier.items.entryType')
        ->with('success', 'Entry type deleted successfully.');
}


// =====================================================
// INVENTORY / STOCK
// =====================================================

/**
 * Display inventory / stock
 */
public function inventory()
{
    $items = Item::with('subcategory.category')->get();

    $entryTypes = EntryType::all();

    $transactions = InventoryTransaction::with(
        'item',
        'entryType'
    )
        ->orderBy('created_at')
        ->orderBy('id')
        ->get();

    return view('cashier.items.inventory', compact(
        'items',
        'entryTypes',
        'transactions'
    ));
}

/**
 * Store stock entry
 */
public function inventoryStore(Request $request)
{
    $request->validate([
        'item_id' => 'required|exists:items,id',
        'entry_type_id' => 'required|exists:entry_types,id',
        'quantity' => 'required|numeric|min:1',
        'note' => 'nullable|string',
        'retail_price' => 'nullable|numeric',
        'whole_price' => 'nullable|numeric',
        'expiry_date' => 'nullable|date',
    ]);

    $item = Item::findOrFail($request->item_id);

    $entryType = EntryType::findOrFail(
        $request->entry_type_id
    );

    switch ($entryType->direction) {

        case 'in':

            $item->quantity += $request->quantity;

            break;

        case 'out':

        case 'damage':

            if ($item->quantity < $request->quantity) {
                return back()->withErrors(
                    'Insufficient stock available.'
                );
            }

            $item->quantity -= $request->quantity;

            break;

        case 'adjustment':

            $item->quantity = $request->quantity;

            break;
    }

    if ($request->retail_price !== null) {
        $item->retail_price = $request->retail_price;
    }

    if ($request->whole_price !== null) {
        $item->whole_price = $request->whole_price;
    }

    if ($request->expiry_date !== null) {
        $item->expiry_date = $request->expiry_date;
    }

    $item->save();

    InventoryTransaction::create([
        'item_id' => $item->id,
        'entry_type_id' => $entryType->id,
        'quantity' => $request->quantity,
        'note' => $request->note,
        'expiry_date' => $request->expiry_date,
        'user_id' => Auth::id(),
    ]);

    return redirect()
        ->route('cashier.items.stock')
        ->with('success', 'Stock entry added successfully.');
}

/**
 * Update stock entry
 */
public function inventoryUpdate(Request $request, $id)
{
    $transaction = InventoryTransaction::findOrFail($id);

    $item = Item::findOrFail(
        $transaction->item_id
    );

    $entryType = EntryType::findOrFail(
        $transaction->entry_type_id
    );

    $request->validate([
        'quantity' => 'required|numeric|min:1',
        'note' => 'nullable|string',
    ]);

    // Revert previous stock movement
    if ($entryType->direction === 'in') {

        $item->quantity -= $transaction->quantity;

    } elseif ($entryType->direction === 'out') {

        $item->quantity += $transaction->quantity;
    }

    // Apply new stock movement
    if ($entryType->direction === 'in') {

        $item->quantity += $request->quantity;

    } elseif ($entryType->direction === 'out') {

        if ($item->quantity < $request->quantity) {
            return back()->withErrors(
                'Insufficient stock after update.'
            );
        }

        $item->quantity -= $request->quantity;
    }

    $item->save();

    $transaction->update([
        'quantity' => $request->quantity,
        'note' => $request->note,
        'user_id' => Auth::id(),
    ]);

    return redirect()
        ->route('cashier.items.stock')
        ->with('success', 'Stock entry updated successfully.');
}

/**
 * Delete stock entry
 */
public function inventoryDestroy($id)
{
    $transaction = InventoryTransaction::findOrFail($id);

    $item = Item::findOrFail(
        $transaction->item_id
    );

    $entryType = EntryType::findOrFail(
        $transaction->entry_type_id
    );

    // Restore stock before deleting transaction
    if ($entryType->direction === 'in') {

        $item->quantity -= $transaction->quantity;

    } elseif ($entryType->direction === 'out') {

        $item->quantity += $transaction->quantity;
    }

    $item->save();

    $transaction->delete();

    return redirect()
        ->route('cashier.items.stock')
        ->with('success', 'Stock entry deleted successfully.');
}


// =====================================================
// STOCK ADJUSTMENT
// =====================================================

/**
 * Display stock adjustments
 */
public function stockAdjustment()
{
    $transactions = InventoryTransaction::with(
        'item',
        'entryType'
    )
        ->latest()
        ->get();

    $items = Item::all();

    $entryTypes = EntryType::all();

    return view('cashier.items.stock_adjustment', compact(
        'transactions',
        'items',
        'entryTypes'
    ));
}

/**
 * Store stock adjustment
 */
public function stockAdjustmentStore(Request $request)
{
    $request->validate([
        'item_id' => 'required|exists:items,id',
        'entry_type_id' => 'required|exists:entry_types,id',
        'quantity' => 'required|integer|min:1',
        'note' => 'nullable|string|max:255',
    ]);

    $entryType = EntryType::findOrFail(
        $request->entry_type_id
    );

    $finalQty = $entryType->effect == '+'
        ? $request->quantity
        : -$request->quantity;

    InventoryTransaction::create([
        'item_id' => $request->item_id,
        'entry_type_id' => $request->entry_type_id,
        'quantity' => $finalQty,
        'note' => $request->note,
        'user_id' => Auth::id(),
    ]);

    return redirect()
        ->route('cashier.items.adjustment')
        ->with('success', 'Stock successfully adjusted.');
}


// =====================================================
// OUT OF STOCK
// =====================================================

/**
 * Display items that are out of stock
 */
public function outOfStock()
{
    $items = Item::with(
        'category',
        'subcategory'
    )
        ->get()
        ->filter(function ($item) {
            return $item->getCurrentStock() <= 0;
        });

    return view(
        'cashier.items.out_of_stock',
        compact('items')
    );
}


// =====================================================
// EXPIRED ITEMS
// =====================================================

/**
 * Display expired items
 */
public function expiredItems()
{
    $today = now()->toDateString();

    $expired = InventoryTransaction::with('item')
        ->whereNotNull('expiry_date')
        ->where('expiry_date', '<', $today)
        ->where('quantity', '>', 0)
        ->get()
        ->groupBy('item_id');

    return view(
        'cashier.items.expired',
        compact('expired')
    );
}


    // ==============
    //POS
    // =============
    public function pos()
    {
        $items = Item::with('subcategory.category')
            ->orderBy('name')
            ->get();

        $itemBalances = [];

        foreach ($items as $item) {
            $itemBalances[$item->id] = $this->calculateCurrentStock($item->id);
        }

        return view('cashier.pos', compact('items', 'itemBalances'));
    }

    /* =========================================================
       STORE POS SALE (NO INVOICES)
    ========================================================= */

public function posStore(Request $request)
{
    $request->validate([
        'customer_name'        => 'nullable|string|max:255',
        'amount_paid'          => 'required|numeric|min:0',
        'items'                => 'required|array|min:1',
        'items.*.item_id'      => 'required|exists:items,id',
        'items.*.qty'          => 'required|numeric|min:0.1',
        'items.*.price_type'   => 'required|in:retail,wholesale',
    ]);

    DB::beginTransaction();

    try {

        /* =============================
           1. CALCULATE TOTAL
        ============================= */

        $totalAmount = 0;

        foreach ($request->items as $row) {

            $item = Item::findOrFail($row['item_id']);

            $price = $row['price_type'] === 'wholesale'
                ? $item->wholesale_price
                : $item->retail_price;

            $totalAmount += $price * $row['qty'];
        }


        /* =============================
           2. CALCULATE BALANCE
        ============================= */

        $amountPaid = (float) $request->amount_paid;

        $balance = max(
            0,
            $totalAmount - $amountPaid
        );


        /* =============================
           3. CREATE SALE
        ============================= */

        $sale = Sale::create([
            'user_id'        => Auth::id(),
            'customer_name'  => $request->customer_name ?? 'Walk-in Customer',
            'sale_date'      => now(),
            'total_amount'   => $totalAmount,
            'amount_paid'    => $amountPaid,
            'balance'        => $balance,
            'payment_status' => $balance > 0 ? 'pending' : 'paid',
        ]);


        /* =============================
           4. PROCESS EACH ITEM
        ============================= */

        foreach ($request->items as $row) {

            $item = Item::findOrFail($row['item_id']);

            $qty = (float) $row['qty'];


            /* =============================
               4.1 DETERMINE ACTUAL PRICE
            ============================= */

            $price = $row['price_type'] === 'wholesale'
                ? $item->wholesale_price
                : $item->retail_price;


            /* =============================
               4.2 STOCK CHECK
            ============================= */

            $available = $this->calculateCurrentStock($item->id);

            if ($available < $qty) {

                throw new \Exception(
                    "Insufficient stock for {$item->name}. Available: {$available}"
                );
            }


            /* =============================
               4.3 GET/CREATE OUT ENTRY TYPE
            ============================= */

            $outType = EntryType::firstOrCreate(
                [
                    'item_id'   => $item->id,
                    'direction' => 'out',
                ],
                [
                    'name'        => 'sale',
                    'description' => "POS sales for {$item->name}",
                ]
            );


            /* =============================
               4.4 SAVE SALE ITEM
            ============================= */

            SaleItem::create([
                'sale_id'    => $sale->id,
                'item_id'    => $item->id,
                'quantity'   => $qty,
                'price'      => $price,
                'price_type' => $row['price_type'],
                'subtotal'   => $price * $qty,
            ]);


            /* =============================
               4.5 RECORD INVENTORY OUT
            ============================= */

            InventoryTransaction::create([
                'item_id'       => $item->id,
                'entry_type_id' => $outType->id,
                'quantity'      => $qty,
                'note'          => "POS Sale #{$sale->id} - {$item->name}",
                'user_id'       => Auth::id(),
            ]);
        }


        /* =============================
           5. COMMIT TRANSACTION
        ============================= */

        DB::commit();


        return redirect()
            ->route('cashier.pos')
            ->with(
                'success',
                "Sale #{$sale->id} completed successfully!"
            );


    } catch (\Exception $e) {

        DB::rollBack();

        return back()
            ->with('error', $e->getMessage())
            ->withInput();
    }
}


/* =========================================================
   CALCULATE CURRENT STOCK
========================================================= */

private function calculateCurrentStock($itemId)
{
    $transactions = InventoryTransaction::where('item_id', $itemId)
        ->with('entryType')
        ->orderBy('created_at')
        ->orderBy('id')
        ->get();

    if ($transactions->isEmpty()) {
        return Item::find($itemId)->quantity ?? 0;
    }

    $balance = 0;

    foreach ($transactions as $t) {

        $direction = $t->entryType->direction ?? 'in';

        if ($direction === 'in') {

            $balance += $t->quantity;

        } elseif (in_array($direction, ['out', 'damage'])) {

            $balance -= $t->quantity;

        } elseif ($direction === 'adjustment') {

            $balance = $t->quantity;
        }
    }

    return $balance;
}


// ================
// PAYMENT LIST
// =================
public function paymentList()
{
    $cashierId = auth()->id();

    // POS payments
    $sales = Sale::with('items.item')
        ->where('user_id', $cashierId)
        ->get()
        ->map(function ($sale) {
            return [
                'type' => 'POS Sale',
                'reference' => 'SALE-' . $sale->id,
                'customer' => $sale->customer_name ?? 'Walk-in',
                'amount' => $sale->total_amount,
                'paid' => $sale->amount_paid,
                'balance' => $sale->balance,
                'method' => $sale->payment_method,
                'date' => $sale->created_at,
                'items' => $sale->items,
            ];
        });

    // Manual deposits
    $deposits = Deposit::where('user_id', $cashierId)
        ->get()
        ->map(function ($d) {
            return [
                'type' => 'Deposit',
                'reference' => 'DEP-' . $d->id,
                'customer' => $d->depositor_name,
                'amount' => $d->amount,
                'paid' => $d->amount,
                'balance' => 0,
                'method' => $d->payment_method,
                'date' => $d->created_at,
                'items' => [],
            ];
        });

    $payments = collect($sales)
        ->merge($deposits)
        ->sortByDesc('date');

    return view('cashier.payments', compact('payments'));
}

// ============================
// INCOMPLETE PAYMENTS (CASHIER)
// ============================
public function incompletePayments()
{
    $sales = Sale::with('items.item')
        ->where('user_id', auth()->id())   // 🔐 only this cashier
        ->where('balance', '>', 0)         // unpaid / partial
        ->latest()
        ->get();

    return view('cashier.incompletepayments', compact('sales'));
}

// ============================
// PAY PENDING SALE
// ============================
public function paySale(Request $request, Sale $sale)
{
    // 🔐 Security check
    if ($sale->user_id !== auth()->id()) {
        abort(403, 'Unauthorized action');
    }

    $request->validate([
        'amount' => 'required|numeric|min:1|max:' . $sale->balance,
    ]);

    DB::transaction(function () use ($request, $sale) {

        $sale->amount_paid += $request->amount;
        $sale->balance -= $request->amount;

        // prevent negative
        if ($sale->balance < 0) {
            $sale->balance = 0;
        }

        $sale->save();

        // OPTIONAL: record payment history (recommended)
        // Payment::create([...]);
    });

    return back()->with('success', 'Payment received successfully');
}


// =================
// EXPENDITURE
// ================

// Expenditure List
public function expenditureIndex()
{
    $categories = ExpenditureCategory::all();
    $expenditures = Expenditure::with('category')
                    ->where('user_id', auth()->id()) // Only cashier's expenditures
                    ->latest()
                    ->get();
    return view('cashier.expenditureList', compact('expenditures', 'categories'));
}

// Expenditure Category
public function expenditureCategoryIndex()
{
    $categories = ExpenditureCategory::all();
    return view('cashier.expenditureCategory', compact('categories'));
}

// Store Expenditure
public function storeExpenditure(Request $request)
{
    $request->validate([
        'title' => 'required|string',
        'amount' => 'required|numeric|min:0',
        'date' => 'required|date',
        'category_id' => 'required|exists:expenditure_categories,id'
    ]);

    Expenditure::create([
        'user_id' => auth()->id(),
        'category_id' => $request->category_id,
        'title' => $request->title,
        'amount' => $request->amount,
        'date' => $request->date,
        'user_id' => auth()->id(),
        'description' => $request->description,
    ]);

    return back()->with('success','Expenditure recorded successfully');
}

// Store Expenditure Category
public function storeExpenditureCategory(Request $request)
{
    $request->validate([
        'name' => 'required|string|unique:expenditure_categories,name',
        'description' => 'nullable|string',
    ]);

    ExpenditureCategory::create([
        'name' => $request->name,
        'description' => $request->description,
    ]);
    
    return back()->with('success','Category created successfully');
}

// Optional: Delete Expenditure
public function destroyExpenditure($id)
{
    $exp = Expenditure::where('user_id', auth()->id())->findOrFail($id);
    $exp->delete();
    return back()->with('success','Expenditure deleted successfully');
}

// Optional: Delete Category
public function destroyExpenditureCategory($id)
{
    $cat = ExpenditureCategory::findOrFail($id);
    $cat->delete();
    return back()->with('success','Category deleted successfully');
}

// In CashierController.php

// Update Expenditure
public function updateExpenditure(Request $request, $id)
{
    $request->validate([
        'title' => 'required|string',
        'amount' => 'required|numeric|min:0',
        'date' => 'required|date',
        'category_id' => 'required|exists:expenditure_categories,id'
    ]);

    $expenditure = Expenditure::where('user_id', auth()->id())->findOrFail($id);
    
    $expenditure->update([
        'category_id' => $request->category_id,
        'title' => $request->title,
        'amount' => $request->amount,
        'date' => $request->date,
        'description' => $request->description,
    ]);

    return back()->with('success', 'Expenditure updated successfully');
}

// Update Expenditure Category
public function updateExpenditureCategory(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|unique:expenditure_categories,name,' . $id,
        'description' => 'nullable|string',
    ]);

    $category = ExpenditureCategory::findOrFail($id);
    
    $category->update([
        'name' => $request->name,
        'description' => $request->description,
    ]);

    return back()->with('success', 'Category updated successfully');
}

// =================
// CUSTOMER 
// =================

// Show customers
public function customers()
{
    $customers = Customer::latest()->get();
    return view('cashier.customers', compact('customers'));
}

// Store customer
public function storeCustomer(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'nullable|email',
        'phone' => 'nullable|string|max:20',
    ]);

    Customer::create([
        'name'           => $request->name,
        'contact_person' => $request->contact_person,
        'phone'          => $request->phone,
        'email'          => $request->email,
        'address'        => $request->address,
    ]);

    return back()->with('success', 'Customer added successfully');
}

// Delete customer (optional restriction)
public function destroyCustomer($id)
{
    $customer = Customer::findOrFail($id);

    // Optional safety: block delete if customer has invoices
    if ($customer->invoices()->exists()) {
        return back()->with('error', 'Customer has invoices and cannot be deleted');
    }

    $customer->delete();

    return back()->with('success', 'Customer deleted successfully');
}

// ===================
// QUOTATIONS
// ==================

// =========================
    // SHOW QUOTATIONS
    // =========================
    public function quotations()
    {
        $customers  = Customer::orderBy('name')->get();
        $quotations = Quotation::with('customer')
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('cashier.quotations', compact('customers', 'quotations'));
    }

    // =========================
    // STORE / UPDATE QUOTATION
    // =========================
    public function storeQuotation(Request $request)
    {
        $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'quotation_date' => 'required|date',
            'status'         => 'required|string|in:Pending,Approved,Rejected',
            'total'          => 'nullable|numeric|min:0',
        ]);

        Quotation::updateOrCreate(
            ['id' => $request->id], // if id exists → update, else create
            [
                'customer_id'    => $request->customer_id,
                'quotation_date' => $request->quotation_date,
                'status'         => $request->status,
                'total'          => $request->total ?? 0,
            ]
        );

        return back()->with('success', 'Quotation saved successfully.');
    }

    // =========================
    // DELETE QUOTATION
    // =========================
    public function destroyQuotation($id)
    {
        $quotation = Quotation::findOrFail($id);
        $quotation->delete();

        return back()->with('success', 'Quotation deleted successfully.');
    }

    // ===============
    // INVOICE
    // ===============
    public function invoices()
    {
        $invoices = Invoice::with('customer')
            ->orderBy('id', 'desc')
            ->get();
    
        $customers = Customer::orderBy('name')->get();
        $items = Item::orderBy('name')->get(); // products
    
        return view('cashier.invoices', compact(
            'invoices',
            'customers',
            'items'
        ));
    }
    
    /* =========================
       STORE / UPDATE INVOICE
    ========================== */
    public function storeInvoice(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'status' => 'required|in:Draft,Sent,Paid',
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
    
            InvoiceItem::where('invoice_id', $invoice->id)->delete();
    
            $total = 0;
    
            foreach ($request->items as $row) {
    
                $subtotal = $row['qty'] * $row['price'];
    
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_id' => $row['item_id'],
                    'quantity' => $row['qty'],
                    'price' => $row['price'],
                    'subtotal' => $subtotal,
                ]);
    
                $total += $subtotal;
            }
    
            $invoice->update(['total' => $total]);
        });
    
        return back()->with('success', 'Invoice saved successfully');
    }
    
    public function destroyInvoice($id)
    {
        Invoice::findOrFail($id)->delete();
        return back()->with('success', 'Invoice deleted successfully');
    }
    
    public function pdfInvoice($id)
    {
        $invoice = Invoice::with(['customer', 'items.item'])->findOrFail($id);
        $pdf = PDF::loadView('cashier.invoices.pdf', compact('invoice'))->setPaper('A4', 'portrait');
        return $pdf->download('Invoice_'.$invoice->id.'.pdf');
    }
    
    // Add this method to get invoice data for editing
    public function getInvoice($id)
    {
        $invoice = Invoice::with(['customer', 'items'])->findOrFail($id);
        
        // Transform items to match the format expected by the frontend
        $invoice->items->transform(function ($item) {
            return [
                'item_id' => $item->id,
                'qty' => $item->pivot->quantity,
                'price' => $item->pivot->price,
                'subtotal' => $item->pivot->subtotal,
                'item' => $item
            ];
        });
        
        return response()->json($invoice);
    }

    // Other pages remain unchanged
    public function billing() { return view('cashier.incomplete-payments'); }
    public function expenditure() { return view('cashier.expenditure'); }
    public function expenditureList() { return view('cashier.expenditureList'); }
    public function expenditureCategory() { return view('cashier.expenditureCategory'); }
    public function report() { return view('cashier.report'); }
    public function suppliers() { return view('cashier.suppliers'); }
    public function quotation() { return view('cashier.quotations'); }
    public function invoice() { return view('cashier.invoices'); }
    public function logout() { return view('cashier.logout'); }
}
