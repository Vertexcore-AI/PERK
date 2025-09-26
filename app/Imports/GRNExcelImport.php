<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GRNExcelImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $collection)
    {
        // We'll just return the collection for processing
        // The actual processing will be done in the controller/service
        return $collection;
    }

    public function rules(): array
    {
        return [
            '*.item_code' => 'required|string|max:50',
            '*.description' => 'required|string|max:255',
            '*.unit_cost' => 'required|numeric|min:0',
            '*.quantity' => 'required|integer|min:1',
            '*.selling_price' => 'nullable|numeric|min:0',
            '*.location' => 'nullable|string|max:50',
            '*.vat' => 'nullable|numeric|min:0|max:100',
            '*.discount' => 'nullable|numeric|min:0|max:100',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.item_code.required' => 'Item Code is required in row :attribute',
            '*.description.required' => 'Description is required in row :attribute',
            '*.unit_cost.required' => 'Unit Cost Price is required in row :attribute',
            '*.unit_cost.numeric' => 'Unit Cost Price must be a number in row :attribute',
            '*.quantity.required' => 'GRN QTY is required in row :attribute',
            '*.quantity.integer' => 'GRN QTY must be a whole number in row :attribute',
            '*.selling_price.numeric' => 'Unit Sales Price must be a number in row :attribute',
            '*.location.string' => 'Location must be text in row :attribute',
            '*.vat.numeric' => 'VAT must be a number in row :attribute',
            '*.discount.numeric' => 'Discount must be a number in row :attribute',
        ];
    }

    /**
     * Prepare the data for easier processing
     */
    public function prepareForValidation($data, $index)
    {
        // Normalize column names (handle different variations)
        $normalizedData = [];

        foreach ($data as $key => $value) {
            $normalizedKey = $this->normalizeColumnName($key);
            $normalizedData[$normalizedKey] = $value;
        }

        return $normalizedData;
    }

    /**
     * Normalize column names to handle variations
     */
    public function normalizeColumnName($columnName)
    {
        $originalColumnName = trim($columnName);
        $columnName = strtolower($originalColumnName);

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
            'unit cost price' => 'unit_cost',
            'unit_cost_price' => 'unit_cost',
            'unitcostprice' => 'unit_cost',
            'price' => 'unit_cost',
            'cost' => 'unit_cost',
            'purchase price' => 'unit_cost',
            'purchase_price' => 'unit_cost',
            'buy price' => 'unit_cost',
            'buy_price' => 'unit_cost',

            // Selling Price variations
            'selling price' => 'selling_price',
            'selling_price' => 'selling_price',
            'unit sales price' => 'selling_price',
            'unit_sales_price' => 'selling_price',
            'unitsalesprice' => 'selling_price',
            'sale price' => 'selling_price',
            'sale_price' => 'selling_price',
            'retail price' => 'selling_price',
            'retail_price' => 'selling_price',

            // Quantity variations
            'quantity' => 'quantity',
            'qty' => 'quantity',
            'grn qty' => 'quantity',
            'grn_qty' => 'quantity',
            'grnqty' => 'quantity',
            'received quantity' => 'quantity',
            'received_quantity' => 'quantity',
            'received qty' => 'quantity',
            'received_qty' => 'quantity',
            'count' => 'quantity',

            // Location/Bin variations
            'location' => 'location',
            'bin location' => 'location',
            'bin_location' => 'location',
            'binlocation' => 'location',
            'bin code' => 'location',
            'bin_code' => 'location',
            'bincode' => 'location',
            'bin' => 'location',

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
            'amount' => 'total_value',
        ];

        return $mappings[$columnName] ?? $columnName;
    }

    /**
     * Validate that required columns are present in the file
     */
    public static function validateRequiredColumns($headers)
    {
        $normalizedHeaders = [];
        foreach ($headers as $header) {
            $instance = new self();
            $normalizedHeaders[] = $instance->normalizeColumnName($header);
        }

        $requiredColumns = ['item_code', 'description', 'location', 'quantity', 'unit_cost'];
        $missingColumns = [];

        foreach ($requiredColumns as $required) {
            if (!in_array($required, $normalizedHeaders)) {
                $missingColumns[] = $required;
            }
        }

        if (!empty($missingColumns)) {
            $expectedVariations = [
                'item_code' => 'ITEM_CODE, Item Code, Part No, Part Number, Code',
                'description' => 'DESCRIPTION, Description, Name, Item Name, Product Name',
                'location' => 'Location, Bin Location, Bin Code, Bin',
                'quantity' => 'GRN QTY, QTY, Quantity, Received Quantity, Count',
                'unit_cost' => 'Unit Cost Price, Unit Price, Unit Cost, Price, Cost'
            ];

            $errorMessage = "Missing required columns. Found columns: " . implode(', ', $headers) . "\n\n";
            $errorMessage .= "Missing required fields and their accepted variations:\n";

            foreach ($missingColumns as $missing) {
                $errorMessage .= "• {$missing}: " . $expectedVariations[$missing] . "\n";
            }

            return [
                'valid' => false,
                'message' => $errorMessage,
                'debug_info' => [
                    'found_headers' => $headers,
                    'normalized_headers' => $normalizedHeaders,
                    'required_columns' => $requiredColumns,
                    'missing_columns' => $missingColumns
                ]
            ];
        }

        return [
            'valid' => true,
            'debug_info' => [
                'found_headers' => $headers,
                'normalized_headers' => $normalizedHeaders,
                'required_columns' => $requiredColumns
            ]
        ];
    }

    /**
     * Inspect file content for debugging purposes
     */
    public static function inspectFileContent($file)
    {
        try {
            $tempPath = $file->getPathname();

            // Basic file info
            $fileInfo = [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'temp_path' => $tempPath,
                'is_readable' => is_readable($tempPath)
            ];

            // Try to read first few rows without using Laravel Excel
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            if (!$reader->canRead($tempPath)) {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            }

            if ($reader->canRead($tempPath)) {
                $spreadsheet = $reader->load($tempPath);
                $worksheet = $spreadsheet->getActiveSheet();

                $fileInfo['spreadsheet_info'] = [
                    'sheet_count' => $spreadsheet->getSheetCount(),
                    'active_sheet_title' => $worksheet->getTitle(),
                    'highest_row' => $worksheet->getHighestRow(),
                    'highest_column' => $worksheet->getHighestColumn(),
                    'data_range' => 'A1:' . $worksheet->getHighestColumn() . $worksheet->getHighestRow()
                ];

                // Get first 5 rows of data
                $firstRows = [];
                for ($row = 1; $row <= min(5, $worksheet->getHighestRow()); $row++) {
                    $rowData = [];
                    for ($col = 'A'; $col <= $worksheet->getHighestColumn(); $col++) {
                        $cellValue = $worksheet->getCell($col . $row)->getCalculatedValue();
                        $rowData[] = $cellValue;
                    }
                    $firstRows[] = $rowData;
                }
                $fileInfo['first_5_rows'] = $firstRows;
            }

            return $fileInfo;
        } catch (\Exception $e) {
            return [
                'error' => 'Failed to inspect file: ' . $e->getMessage(),
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ];
        }
    }

    /**
     * Get the expected column headers for template
     */
    public static function getExpectedHeaders()
    {
        return [
            'ITEM_CODE',
            'DESCRIPTION',
            'Location',
            'GRN QTY',
            'Unit Cost Price',
            'Unit Sales Price'
        ];
    }

    /**
     * Generate a sample Excel template
     */
    public static function getSampleData()
    {
        return [
            [
                'ITEM_CODE' => 'BP001',
                'DESCRIPTION' => 'Brake Pad Set - Front',
                'Location' => 'A1-B2-C3',
                'GRN QTY' => 10,
                'Unit Cost Price' => 25.00,
                'Unit Sales Price' => 35.00
            ],
            [
                'ITEM_CODE' => 'OF002',
                'DESCRIPTION' => 'Oil Filter - Standard',
                'Location' => 'B2-C3-D4',
                'GRN QTY' => 25,
                'Unit Cost Price' => 8.50,
                'Unit Sales Price' => 12.00
            ],
            [
                'ITEM_CODE' => 'SF003',
                'DESCRIPTION' => 'Spark Plug Set',
                'Location' => 'C3-D4-E5',
                'GRN QTY' => 8,
                'Unit Cost Price' => 15.75,
                'Unit Sales Price' => 22.00
            ]
        ];
    }
}