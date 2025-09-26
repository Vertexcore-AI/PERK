<?php

namespace App\Http\Controllers;

use App\Models\GRN;
use App\Models\Vendor;
use App\Models\Item;
use App\Models\Store;
use App\Models\Bin;
use App\Services\GRNService;
use App\Imports\GRNExcelImport;
use App\Imports\GRNExcelRawImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class GRNController extends Controller
{
    protected $grnService;

    public function __construct(GRNService $grnService)
    {
        $this->grnService = $grnService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $grns = GRN::with(['vendor', 'grnItems'])
            ->latest()
            ->paginate(15);

        $totalGRNs = GRN::count();
        $pendingGRNs = 0; // No longer tracking stored vs received quantities
        $todayGRNs = GRN::whereDate('created_at', today())->count();
        $totalValue = GRN::sum('total_amount');

        return view('grns.index', compact('grns', 'totalGRNs', 'pendingGRNs', 'todayGRNs', 'totalValue'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vendors = Vendor::all();
        $items = Item::where('is_active', true)->get();
        $stores = Store::all();
        $bins = Bin::all();

        return view('grns.create', compact('vendors', 'items', 'stores', 'bins'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'inv_no' => 'required|string|max:50',
            'billing_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.vendor_item_code' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.selling_price' => 'nullable|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
            'items.*.vat' => 'nullable|numeric|min:0|max:100',
            'items.*.store_id' => 'required|exists:stores,id',
            'items.*.bin_code' => 'nullable|string|max:50',
            'items.*.bin_id' => 'nullable|exists:bins,id',
            'items.*.notes' => 'nullable|string',
        ]);

        try {
            \Log::info('GRN Creation Started', ['request_data' => $request->all()]);
            $grn = $this->grnService->processGRN($request->all());
            \Log::info('GRN Created Successfully', ['grn_id' => $grn->grn_id]);

            return redirect()->route('grns.show', $grn->grn_id)
                ->with('success', 'GRN created successfully.');
        } catch (\Exception $e) {
            \Log::error('GRN Creation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating GRN: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(GRN $grn)
    {
        $grn->load([
            'vendor',
            'grnItems.item.category',
            'grnItems.batch',
        ]);

        return view('grns.show', compact('grn'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GRN $grn)
    {
        $vendors = Vendor::all();
        $items = Item::where('is_active', true)->get();
        $stores = Store::all();
        $bins = Bin::all();

        $grn->load('grnItems.item');

        return view('grns.edit', compact('grn', 'vendors', 'items', 'stores', 'bins'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GRN $grn)
    {
        $request->validate([
            'billing_date' => 'required|date',
        ]);

        $grn->update([
            'billing_date' => $request->billing_date,
        ]);

        return redirect()->route('grns.show', $grn->grn_id)
            ->with('success', 'GRN updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GRN $grn)
    {
        // GRNs can now be deleted since we no longer track stored quantities separately

        $grn->delete();

        return redirect()->route('grns.index')
            ->with('success', 'GRN deleted successfully.');
    }


    /**
     * Upload and parse Excel file for GRN import
     */
    public function uploadExcel(Request $request)
    {
        // Force JSON response
        $request->headers->set('Accept', 'application/json');

        // Basic logging to see if method is called
        \Log::info('GRN Upload started', [
            'vendor_id' => $request->vendor_id,
            'file_name' => $request->file('excel_file') ? $request->file('excel_file')->getClientOriginalName() : 'no file',
            'headers' => $request->headers->all()
        ]);

        // Custom validation for file types
        try {
            $request->validate([
                'vendor_id' => 'required|exists:vendors,id',
                'excel_file' => 'required|file|max:2048',
            ]);

            // Additional file extension validation
            $file = $request->file('excel_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $allowedExtensions = ['xlsx', 'xls', 'csv'];

            if (!in_array($extension, $allowedExtensions)) {
                \Log::error('Invalid file extension', [
                    'extension' => $extension,
                    'filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file type. Please upload a CSV (.csv), Excel (.xlsx), or Excel 97-2003 (.xls) file.'
                ], 422);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', array_map(fn($errors) => implode(', ', $errors), $e->errors()))
            ], 422);
        }

        \Log::info('GRN Upload validation passed');

        try {
            $file = $request->file('excel_file');

            // Inspect file content for debugging
            $fileInspection = GRNExcelImport::inspectFileContent($file);
            \Log::info('File inspection results', $fileInspection);

            \Log::info('File received', [
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'original_name' => $file->getClientOriginalName(),
                'extension' => $file->getClientOriginalExtension()
            ]);

            // First try with raw import to get all data
            $rawImport = new GRNExcelRawImport();
            \Log::info('Starting RAW Excel parsing (without heading row)');
            $rawCollection = Excel::toCollection($rawImport, $file);

            \Log::info('Raw collection parsed', [
                'sheets_count' => $rawCollection->count(),
                'sheet_details' => $rawCollection->map(function($sheet, $index) {
                    return [
                        'sheet_' . $index => [
                            'total_rows' => $sheet->count(),
                            'first_3_rows' => $sheet->take(3)->map(function($row) {
                                return $row->toArray();
                            })->toArray()
                        ]
                    ];
                })->toArray()
            ]);

            // Now try with heading row import
            $import = new GRNExcelImport();
            \Log::info('Starting Excel parsing with WithHeadingRow');
            $fullCollection = Excel::toCollection($import, $file);
            \Log::info('Full collection parsed with heading row', [
                'sheets_count' => $fullCollection->count(),
                'first_sheet_rows' => $fullCollection->first() ? $fullCollection->first()->count() : 0
            ]);

            // Try to find a sheet with valid GRN data
            $collection = null;
            $sheetIndex = 0;
            $sheetsInfo = [];
            $requiredHeaders = ['item_code', 'description', 'location', 'quantity', 'unit_cost'];

            foreach ($fullCollection as $index => $sheet) {
                $nonEmptyRows = $sheet->filter(function($row) {
                    return $row->filter()->isNotEmpty();
                })->count();

                $firstRow = $sheet->first();
                $headers = $firstRow ? array_keys($firstRow->toArray()) : [];

                // Normalize headers for this sheet
                $normalizedHeaders = array_map(function($header) {
                    return (new GRNExcelImport())->normalizeColumnName($header);
                }, $headers);

                // Check if this sheet has the required columns
                $hasRequiredColumns = true;
                $missingColumns = [];
                foreach ($requiredHeaders as $required) {
                    if (!in_array($required, $normalizedHeaders)) {
                        $hasRequiredColumns = false;
                        $missingColumns[] = $required;
                    }
                }

                $sheetsInfo[] = [
                    'sheet_index' => $index,
                    'total_rows' => $sheet->count(),
                    'non_empty_rows' => $nonEmptyRows,
                    'first_row' => $sheet->first() ? $sheet->first()->toArray() : null,
                    'headers' => $headers,
                    'normalized_headers' => $normalizedHeaders,
                    'has_required_columns' => $hasRequiredColumns,
                    'missing_columns' => $missingColumns
                ];

                // Use the first sheet that has valid GRN columns
                if ($collection === null && $hasRequiredColumns && $nonEmptyRows > 0) {
                    $collection = $sheet;
                    $sheetIndex = $index;
                    \Log::info('Found valid sheet with GRN data', [
                        'sheet_index' => $index,
                        'headers' => $headers,
                        'rows' => $nonEmptyRows
                    ]);
                }
            }

            \Log::info('Sheets analysis', [
                'sheets_info' => $sheetsInfo,
                'selected_sheet_index' => $sheetIndex
            ]);

            // If no sheet has data with heading row, try raw data
            if ($collection === null || $collection->isEmpty()) {
                \Log::warning('No data found with WithHeadingRow, trying raw data');

                // Try using raw collection instead
                foreach ($rawCollection as $index => $sheet) {
                    if ($sheet->count() > 1) { // More than just header row
                        // Check if first row looks like headers
                        $headers = $sheet->first()->toArray();

                        // Normalize potential headers
                        $normalizedHeaders = array_map(function($header) {
                            return (new GRNExcelImport())->normalizeColumnName((string)$header);
                        }, $headers);

                        // Check if this sheet has required columns in headers
                        $hasRequiredColumns = true;
                        foreach ($requiredHeaders as $required) {
                            if (!in_array($required, $normalizedHeaders)) {
                                $hasRequiredColumns = false;
                                break;
                            }
                        }

                        if ($hasRequiredColumns) {
                            // Convert raw data to headed format
                            $dataRows = $sheet->slice(1);
                            $convertedRows = collect();

                            foreach ($dataRows as $row) {
                                $rowArray = $row->toArray();
                                $associativeRow = [];
                                foreach ($headers as $i => $header) {
                                    if (isset($rowArray[$i])) {
                                        $associativeRow[$header] = $rowArray[$i];
                                    }
                                }
                                // Only add non-empty rows
                                if (!empty(array_filter($associativeRow, function($value) {
                                    return $value !== null && $value !== '';
                                }))) {
                                    $convertedRows->push(collect($associativeRow));
                                }
                            }

                            if ($convertedRows->count() > 0) {
                                $collection = $convertedRows;
                                $sheetIndex = $index;
                                \Log::info('Using raw data conversion from sheet ' . $index, [
                                    'rows_converted' => $convertedRows->count(),
                                    'headers' => $headers,
                                    'normalized_headers' => $normalizedHeaders
                                ]);
                                break;
                            }
                        } else {
                            \Log::debug('Sheet ' . $index . ' does not have required headers in raw format', [
                                'headers' => $headers,
                                'normalized_headers' => $normalizedHeaders
                            ]);
                        }
                    }
                }
            }

            // If still no data, return error
            if ($collection === null || $collection->isEmpty()) {
                \Log::warning('No valid data found in any sheet even with raw parsing', [
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'sheets_in_workbook' => $fullCollection->count(),
                    'all_sheets_info' => $sheetsInfo,
                    'raw_sheets_count' => $rawCollection->count()
                ]);

                // Build a more informative error message
                $sheetDetails = [];
                foreach ($sheetsInfo as $info) {
                    $sheetDetails[] = "Sheet {$info['sheet_index']}: " .
                        ($info['has_required_columns'] ? 'Valid headers found' :
                        'Missing: ' . implode(', ', $info['missing_columns']));
                }

                return response()->json([
                    'success' => false,
                    'message' => 'No valid GRN data found in any sheet. The file should have columns: ITEM_CODE, DESCRIPTION, Location, GRN QTY, Unit Cost Price. Sheet analysis: ' . implode('; ', $sheetDetails),
                    'debug_info' => [
                        'file_size' => $file->getSize(),
                        'sheets_found' => $fullCollection->count(),
                        'sheets_info' => $sheetsInfo,
                        'file_name' => $file->getClientOriginalName(),
                        'raw_data_found' => $rawCollection->first() ? $rawCollection->first()->count() : 0,
                        'expected_columns' => ['ITEM_CODE', 'DESCRIPTION', 'Location', 'GRN QTY', 'Unit Cost Price']
                    ]
                ]);
            }

            \Log::info('Excel parsed successfully', [
                'selected_sheet' => $sheetIndex,
                'rows' => $collection->count(),
                'first_row_sample' => $collection->first() ? $collection->first()->toArray() : null,
                'total_non_empty_rows' => $collection->filter(function($row) {
                    return $row->filter()->isNotEmpty();
                })->count()
            ]);

            // Get headers from first row (assuming first row contains headers)
            $firstRow = $collection->first();
            if (!$firstRow) {
                \Log::error('First row is null', [
                    'collection_count' => $collection->count(),
                    'collection_class' => get_class($collection),
                    'file_info' => [
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime' => $file->getMimeType()
                    ]
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to read file headers. Please check the file format.',
                    'debug_info' => [
                        'collection_count' => $collection->count(),
                        'file_name' => $file->getClientOriginalName()
                    ]
                ]);
            }

            $headers = array_keys($firstRow->toArray());
            \Log::info('Headers found', [
                'headers' => $headers,
                'header_count' => count($headers),
                'first_row_data' => $firstRow->toArray()
            ]);

            $validation = GRNExcelImport::validateRequiredColumns($headers);
            \Log::info('Validation result', array_merge($validation, [
                'provided_headers' => $headers,
                'normalized_headers' => array_map(function($header) {
                    return (new GRNExcelImport())->normalizeColumnName($header);
                }, $headers)
            ]));

            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $validation['message']
                ]);
            }

            // Convert to array and clean data
            $importData = [];
            $skippedRows = 0;
            $processedRows = 0;

            foreach ($collection as $rowIndex => $row) {
                // Skip if row is empty
                if ($row->filter()->isEmpty()) {
                    $skippedRows++;
                    \Log::debug('Skipping empty row', ['row_index' => $rowIndex]);
                    continue;
                }

                // Normalize column names for each row
                $normalizedRow = [];
                foreach ($row as $key => $value) {
                    $normalizedKey = $this->normalizeColumnName($key);
                    $normalizedRow[$normalizedKey] = $value;
                }

                \Log::debug('Processing row', [
                    'row_index' => $rowIndex,
                    'original_row' => $row->toArray(),
                    'normalized_row' => $normalizedRow,
                    'has_item_code' => !empty($normalizedRow['item_code']),
                    'has_description' => !empty($normalizedRow['description'])
                ]);

                // Check if we have required columns
                if (!empty($normalizedRow['item_code']) && !empty($normalizedRow['description'])) {
                    $processedRows++;
                    // Calculate default selling price if not provided
                    // Clean numeric values (remove commas from Excel formatting)
                    $unitCost = $this->cleanNumericValue($normalizedRow['unit_cost'] ?? 0);
                    $sellingPrice = $this->cleanNumericValue($normalizedRow['selling_price'] ?? 0);

                    // Standard business logic: selling price should be higher than unit cost
                    // If no selling price provided, set it to unit cost + 30% markup
                    if ($sellingPrice == 0 && $unitCost > 0) {
                        $sellingPrice = $unitCost * 1.3; // 30% markup
                    }

                    // Debug: Log the raw values to see what's being parsed
                    \Log::debug('Processing Excel row data', [
                        'row_index' => $rowIndex,
                        'raw_unit_cost' => $normalizedRow['unit_cost'] ?? 'missing',
                        'raw_selling_price' => $normalizedRow['selling_price'] ?? 'missing',
                        'parsed_unit_cost' => $unitCost,
                        'parsed_selling_price' => $sellingPrice,
                        'all_normalized_keys' => array_keys($normalizedRow)
                    ]);

                    $importData[] = [
                        'item_code' => trim($normalizedRow['item_code']),
                        'description' => trim($normalizedRow['description']),
                        'quantity' => (int) ($normalizedRow['quantity'] ?? 1),
                        'unit_cost' => $unitCost,
                        'selling_price' => round($sellingPrice, 2),
                        'vat' => (float) ($normalizedRow['vat'] ?? 0),
                        'discount' => (float) ($normalizedRow['discount'] ?? 0),
                        'bin_code' => trim($normalizedRow['location'] ?? $normalizedRow['bin_code'] ?? ''),
                    ];
                }
            }

            \Log::info('Data processing completed', [
                'total_rows_in_collection' => $collection->count(),
                'skipped_empty_rows' => $skippedRows,
                'processed_rows' => $processedRows,
                'valid_import_data_count' => count($importData)
            ]);

            if (empty($importData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid data found in the Excel file. Please ensure your file has rows with both Item Code and Description columns filled.',
                    'debug_info' => [
                        'total_rows' => $collection->count(),
                        'skipped_empty_rows' => $skippedRows,
                        'processed_rows' => $processedRows,
                        'headers_found' => $headers
                    ]
                ]);
            }

            // Resolve mappings using existing GRN service
            $resolvedData = $this->grnService->resolveImportItems($request->vendor_id, $importData);

            // Store in session for processing
            session([
                'grn_import_data' => $resolvedData,
                'grn_vendor_id' => $request->vendor_id
            ]);

            return response()->json([
                'success' => true,
                'data' => $resolvedData,
                'stats' => [
                    'total' => count($importData),
                    'resolved' => count($resolvedData['resolved']),
                    'suggestions' => count($resolvedData['suggestions']),
                    'unresolved' => count($resolvedData['unresolved'])
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Excel import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $file->getClientOriginalName() ?? 'unknown',
                'line' => $e->getLine(),
                'file_path' => $e->getFile()
            ]);

            // Provide more specific error messages based on exception type
            $userMessage = 'Error processing Excel file: ';

            if (strpos($e->getMessage(), 'Undefined array key') !== false ||
                strpos($e->getMessage(), 'Undefined index') !== false) {
                $userMessage = 'Missing required columns in the file. Please check that your file contains: ITEM_CODE, DESCRIPTION, QTY, Unit Price columns.';
            } elseif (strpos($e->getMessage(), 'Permission denied') !== false) {
                $userMessage = 'File access error. Please try uploading the file again.';
            } elseif (strpos($e->getMessage(), 'Invalid file format') !== false) {
                $userMessage = 'Invalid file format. Please upload a CSV, XLS, or XLSX file.';
            } else {
                $userMessage .= $e->getMessage();
            }

            return response()->json([
                'success' => false,
                'message' => $userMessage
            ], 400);
        }
    }

    /**
     * Resolve a mapping suggestion during import
     */
    public function resolveMapping(Request $request)
    {
        // Force JSON response even on validation errors
        $request->headers->set('Accept', 'application/json');

        try {
            $request->validate([
                'row_index' => 'required|integer',
                'action' => 'required|in:map_existing,create_new',
                'item_id' => 'required_if:action,map_existing|exists:items,id',
                'item_data' => 'required_if:action,create_new'
            ]);

            // Parse item_data if it's a JSON string
            $itemData = null;
            if ($request->has('item_data') && is_string($request->item_data)) {
                $itemData = json_decode($request->item_data, true);
                if (!$itemData) {
                    return response()->json(['success' => false, 'message' => 'Invalid item data format.']);
                }
            } else if ($request->has('item_data')) {
                $itemData = $request->item_data;
            }

            \Log::info('Resolve mapping request', [
                'row_index' => $request->row_index,
                'action' => $request->action,
                'item_id' => $request->item_id,
                'item_data' => $itemData ?? null
            ]);

            $importData = session('grn_import_data');
            if (!$importData) {
                \Log::error('No import session found');
                return response()->json(['success' => false, 'message' => 'No import session found.']);
            }

            $rowIndex = $request->row_index;
            $action = $request->action;

            // Find the row in suggestions or unresolved
            // We need to search by the actual item data, not just index, since indices can change
            $targetRow = null;
            $sourceArray = null;
            $actualIndex = null;

            // First check suggestions
            foreach ($importData['suggestions'] ?? [] as $idx => $item) {
                if ($idx == $rowIndex ||
                    (isset($item['original_index']) && $item['original_index'] == $rowIndex)) {
                    $targetRow = $item;
                    $sourceArray = 'suggestions';
                    $actualIndex = $idx;
                    break;
                }
            }

            // If not found in suggestions, check unresolved
            if (!$targetRow) {
                foreach ($importData['unresolved'] ?? [] as $idx => $item) {
                    if ($idx == $rowIndex ||
                        (isset($item['original_index']) && $item['original_index'] == $rowIndex)) {
                        $targetRow = $item;
                        $sourceArray = 'unresolved';
                        $actualIndex = $idx;
                        break;
                    }
                }
            }

            if (!$targetRow) {
                \Log::warning('Row not found', [
                    'row_index' => $rowIndex,
                    'suggestions_count' => count($importData['suggestions'] ?? []),
                    'unresolved_count' => count($importData['unresolved'] ?? [])
                ]);
                return response()->json(['success' => false, 'message' => 'Row not found.']);
            }

            switch ($action) {
                case 'map_existing':
                    // Create mapping and move to resolved
                    $this->grnService->createVendorMapping(
                        session('grn_vendor_id'),
                        $targetRow['item_code'],
                        $request->item_id,
                        $targetRow['description']
                    );

                    $targetRow['item_id'] = $request->item_id;
                    // Preserve selling price if provided
                    if ($request->has('selling_price')) {
                        $targetRow['selling_price'] = (float) $request->selling_price;
                    }
                    $importData['resolved'][] = $targetRow;
                    break;

                case 'create_new':
                    // Create new item and mapping
                    $newItem = $this->grnService->createItemFromImport($itemData);
                    $this->grnService->createVendorMapping(
                        session('grn_vendor_id'),
                        $targetRow['item_code'],
                        $newItem->id,
                        $targetRow['description']
                    );

                    $targetRow['item_id'] = $newItem->id;
                    // Preserve selling price if provided
                    if ($request->has('selling_price')) {
                        $targetRow['selling_price'] = (float) $request->selling_price;
                    }
                    $importData['resolved'][] = $targetRow;
                    break;

            }

            // Remove from original array using the actual index
            unset($importData[$sourceArray][$actualIndex]);
            $importData[$sourceArray] = array_values($importData[$sourceArray]);

            // Update session
            session(['grn_import_data' => $importData]);

            return response()->json([
                'success' => true,
                'data' => $importData,
                'stats' => [
                    'resolved' => count($importData['resolved']),
                    'suggestions' => count($importData['suggestions']),
                    'unresolved' => count($importData['unresolved']),
                    'total' => count($importData['resolved']) + count($importData['suggestions']) + count($importData['unresolved'])
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in resolve mapping', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
                'validation_rules' => [
                    'row_index' => 'required|integer',
                    'action' => 'required|in:map_existing,create_new',
                    'item_id' => 'required_if:action,map_existing|exists:items,id',
                    'item_data' => 'required_if:action,create_new'
                ]
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', array_map(function($errors) { return implode(', ', $errors); }, $e->errors())),
                'errors' => $e->errors(),
                'debug' => [
                    'received_action' => $request->action,
                    'received_row_index' => $request->row_index,
                    'received_item_id' => $request->item_id,
                    'has_item_data' => $request->has('item_data')
                ]
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Resolve mapping error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Process the resolved import data and create GRN
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'inv_no' => 'required|string|max:50',
            'billing_date' => 'required|date',
        ]);

        try {
            $importData = session('grn_import_data');
            if (!$importData || empty($importData['resolved'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No resolved items found to process.'
                ]);
            }

            // Get the default store dynamically
            $defaultStore = \App\Models\Store::first();
            if (!$defaultStore) {
                return response()->json([
                    'success' => false,
                    'message' => 'No stores available. Please create a store first.'
                ]);
            }

            // Prepare GRN data in the format expected by existing GRNService
            $grnData = [
                'vendor_id' => $request->vendor_id,
                'inv_no' => $request->inv_no,
                'billing_date' => $request->billing_date,
                'items' => []
            ];

            foreach ($importData['resolved'] as $item) {
                $grnData['items'][] = [
                    'vendor_item_code' => $item['item_code'],
                    'quantity' => $item['quantity'] ?? 1,
                    'unit_cost' => $item['unit_cost'], // Purchase cost from vendor
                    'selling_price' => $item['selling_price'] ?? ($item['unit_cost'] * 1.3), // Retail price (default 30% markup)
                    'discount' => $item['discount'] ?? 0,
                    'vat' => $item['vat'] ?? 0,
                    'bin_code' => $item['bin_code'] ?? '',
                    'store_id' => $request->default_store_id ?? $defaultStore->id,
                ];
            }

            // Use existing GRN processing logic
            $grn = $this->grnService->processGRN($grnData);

            // Clear import session
            session()->forget(['grn_import_data', 'grn_vendor_id']);

            $itemCount = count($importData['resolved']);
            return response()->json([
                'success' => true,
                'grn_id' => $grn->grn_id,
                'message' => "Import completed successfully! GRN #{$grn->grn_id} created with {$itemCount} items.",
                'redirect' => route('grns.show', $grn->grn_id)
            ]);

        } catch (\Exception $e) {
            \Log::error('Import processing failed', [
                'error' => $e->getMessage(),
                'vendor_id' => $request->vendor_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing import: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Download Excel template for GRN import
     */
    public function downloadTemplate()
    {
        $headers = GRNExcelImport::getExpectedHeaders();
        $sampleData = GRNExcelImport::getSampleData();

        return Excel::download(new class($headers, $sampleData) implements \Maatwebsite\Excel\Concerns\FromArray {
            private $headers;
            private $data;

            public function __construct($headers, $data) {
                $this->headers = $headers;
                $this->data = $data;
            }

            public function array(): array {
                return array_merge([$this->headers], $this->data);
            }
        }, 'grn_import_template.xlsx');
    }

    /**
     * Download the default CSV format from public folder
     */
    public function downloadDefaultFormat()
    {
        $filePath = public_path('inventory.csv');

        if (!file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Default format file not found.'
            ], 404);
        }

        return response()->download($filePath, 'inventory_default_format.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Normalize column names to handle variations
     */
    private function normalizeColumnName($columnName)
    {
        $columnName = strtolower(trim($columnName));

        // Map common variations to our expected format - comprehensive mapping
        $mappings = [
            // Serial/Row number (ignored)
            'no' => 'row_number',
            'no.' => 'row_number',
            'sr' => 'row_number',
            'sr.' => 'row_number',
            'serial' => 'row_number',
            'serial no' => 'row_number',
            'serial_no' => 'row_number',
            'row' => 'row_number',
            'index' => 'row_number',

            // Item Code variations
            'item code' => 'item_code',
            'itemcode' => 'item_code',
            'item_code' => 'item_code',
            'vendor item code' => 'item_code',
            'vendor_item_code' => 'item_code',
            'part number' => 'item_code',
            'part_number' => 'item_code',
            'part no' => 'item_code',
            'part_no' => 'item_code',
            'partno' => 'item_code',
            'code' => 'item_code',
            'product code' => 'item_code',
            'product_code' => 'item_code',

            // Description variations
            'description' => 'description',
            'item description' => 'description',
            'item_description' => 'description',
            'name' => 'description',
            'item name' => 'description',
            'item_name' => 'description',
            'product name' => 'description',
            'product_name' => 'description',

            // Unit Cost/Price variations
            'unit price' => 'unit_cost',
            'unitprice' => 'unit_cost',
            'unit_price' => 'unit_cost',
            'unit cost' => 'unit_cost',
            'unit_cost' => 'unit_cost',
            'price' => 'unit_cost',
            'cost' => 'unit_cost',
            'purchase price' => 'unit_cost',
            'purchase_price' => 'unit_cost',
            'buy price' => 'unit_cost',
            'buy_price' => 'unit_cost',

            // Selling Price variations
            'selling price' => 'selling_price',
            'selling_price' => 'selling_price',
            'sellingprice' => 'selling_price',
            'sale price' => 'selling_price',
            'sale_price' => 'selling_price',
            'retail price' => 'selling_price',
            'retail_price' => 'selling_price',

            // Quantity variations
            'quantity' => 'quantity',
            'qty' => 'quantity',
            'received quantity' => 'quantity',
            'received_quantity' => 'quantity',
            'received qty' => 'quantity',
            'received_qty' => 'quantity',
            'count' => 'quantity',

            // VAT variations
            'vat' => 'vat',
            'vat%' => 'vat',
            'vat percent' => 'vat',
            'vat_percent' => 'vat',
            'tax' => 'vat',
            'tax%' => 'vat',
            'tax percent' => 'vat',
            'tax_percent' => 'vat',

            // Discount variations
            'discount' => 'discount',
            'discount%' => 'discount',
            'disc %' => 'discount',
            'disc' => 'discount',
            'discount percent' => 'discount',
            'discount_percent' => 'discount',

            // Total Value variations (optional field)
            'total value' => 'total_value',
            'total_value' => 'total_value',
            'total cost' => 'total_value',
            'total_cost' => 'total_value',
            'total price' => 'total_value',
            'total_price' => 'total_value',
            'total' => 'total_value',

            // Bin Location variations
            'bin' => 'bin_code',
            'bin code' => 'bin_code',
            'bin_code' => 'bin_code',
            'bin location' => 'bin_code',
            'bin_location' => 'bin_code',
            'location' => 'bin_code',
            'shelf' => 'bin_code',
            'rack' => 'bin_code',
            'position' => 'bin_code',
        ];

        return $mappings[$columnName] ?? $columnName;
    }

    /**
     * Clean numeric values from Excel (remove commas, convert to float)
     */
    private function cleanNumericValue($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        // Remove commas and other formatting, then convert to float
        $cleaned = preg_replace('/[^\d.-]/', '', (string) $value);
        return (float) $cleaned;
    }
}