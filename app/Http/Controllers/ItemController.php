<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with('category')->paginate(15);
        return view('items.index', compact('items'));
    }

    public function create(Category $category)
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }


    public function show(Item $item)
    {
        return view('items.show', compact('item'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_no' => 'required|string|max:255|unique:items,item_no',
            'description' => 'required|string',
            'vat' => 'nullable|numeric|min:0|max:100',
            'manufacturer_name' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id', // matches categories PK
            'unit_of_measure' => 'required|string|max:50',
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'is_serialized' => 'boolean',
        ]);

        Item::create([
            'item_no' => $request->item_no,
            'description' => $request->description,
            'vat' => $request->vat ?? 0,
            'manufacturer_name' => $request->manufacturer_name,
            'category_id' => $request->category_id,
            'unit_of_measure' => $request->unit_of_measure,
            'min_stock' => $request->min_stock ?? 0,
            'max_stock' => $request->max_stock,
            'is_serialized' => $request->has('is_serialized'),
        ]);

        return redirect()->route('items.index')
            ->with('success', 'Item created successfully');
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        return view('items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'item_no' => 'required|string|max:255|unique:items,item_no,' . $item->item_id . ',item_id',
            'description' => 'required|string',
            'vat' => 'nullable|numeric|min:0|max:100',
            'manufacturer_name' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_of_measure' => 'required|string|max:50',
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'is_serialized' => 'boolean',
        ]);

        $item->update([
            'item_no' => $request->item_no,
            'description' => $request->description,
            'vat' => $request->vat ?? 0,
            'manufacturer_name' => $request->manufacturer_name,
            'category_id' => $request->category_id,
            'unit_of_measure' => $request->unit_of_measure,
            'min_stock' => $request->min_stock ?? 0,
            'max_stock' => $request->max_stock,
            'is_serialized' => $request->has('is_serialized'),
        ]);

        return redirect()->route('items.index')
            ->with('success', 'Item updated successfully');
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'item deleted successfully');
    }

    public function exportCsv()
    {
        // Fetch all items with category relationship
        $items = Item::with('category')->get();

        // CSV headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="items.csv"',
        ];

        $callback = function () use ($items) {
            $file = fopen('php://output', 'w');

            // CSV column headers
            fputcsv($file, [
                'ID',
                'Item Number',
                'Description',
                'VAT (%)',
                'Manufacturer',
                'Category',
                'Unit of Measure',
                'Min Stock',
                'Max Stock',
                'Serialized',
                'Created At',
            ]);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->item_id,
                    $item->item_no,
                    $item->description,
                    number_format($item->vat, 2),
                    $item->manufacturer_name ?? 'N/A',
                    $item->category->name ?? 'N/A',   // safe if no category
                    $item->unit_of_measure,
                    $item->min_stock,
                    $item->max_stock ?? 'N/A',
                    $item->is_serialized ? 'Yes' : 'No',
                    $item->created_at ? $item->created_at->format('d/m/Y H:i') : '',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    //import CSV
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');

        try {
            $csv = Reader::createFromPath($file->getRealPath(), 'r');
            $csv->setHeaderOffset(0);
            $records = $csv->getRecords();

            $imported = 0;
            $errors = [];

            foreach ($records as $index => $row) {
                //Normalize category name
                $categoryName = trim($row['category_name']);

                //create category if not exists
                $category = Category::firstOrCreate(
                    ['name' => $categoryName],
                    ['description' => 'Created via CSV import']
                );

                //Find category by name
                $category = Category::whereRaw('LOWER(name) = ?', [strtolower($categoryName)])->first();

                if (!$category) {
                    $errors[$index + 2] = ["Category '{$categoryName}' does not exist."];
                    continue;
                }

                // Validate other fields
                $validator = Validator::make($row, [
                    'item_no' => 'required|string|max:255|unique:items,item_no',
                    'description' => 'required|string',
                    'vat' => 'nullable|numeric|min:0|max:100',
                    'unit_of_measure' => 'required|string|max:50',
                    'manufacturer_name' => 'nullable|string|max:255',
                    'min_stock' => 'nullable|integer|min:0',
                    'max_stock' => 'nullable|integer|min:0',
                    'is_serialized' => 'nullable|in:0,1',
                ]);

                if ($validator->fails()) {
                    $errors[$index + 2] = $validator->errors()->all();
                    continue;
                }

                // Insert item with category_id from lookup
                Item::create([
                    'item_no' => $row['item_no'],
                    'description' => $row['description'],
                    'vat' => $row['vat'] ?? 0,
                    'unit_of_measure' => $row['unit_of_measure'],
                    'manufacturer_name' => $row['manufacturer_name'] ?? null,
                    'category_id' => $category->id,  // 👈 mapped ID
                    'min_stock' => $row['min_stock'] ?? 0,
                    'max_stock' => $row['max_stock'] ?? null,
                    'is_serialized' => isset($row['is_serialized']) ? (bool)$row['is_serialized'] : false,
                ]);

                $imported++;
            }

            $message = "$imported items imported successfully.";
            if (!empty($errors)) {
                $message .= " Some rows had errors and were skipped.";
                session()->flash('csv_errors', $errors);
            }

            return redirect()->route('items.index')->with('success', $message);
        } catch (Exception $e) {
            return redirect()->route('items.index')
                ->with('error', 'Failed to read CSV file: ' . $e->getMessage());
        }
    }
}
