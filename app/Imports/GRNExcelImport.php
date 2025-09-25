<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

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
            '*.unit_price' => 'required|numeric|min:0',
            '*.quantity' => 'required|integer|min:1',
            '*.vat' => 'nullable|numeric|min:0|max:100',
            '*.discount' => 'nullable|numeric|min:0|max:100',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.item_code.required' => 'Item Code is required in row :attribute',
            '*.description.required' => 'Description is required in row :attribute',
            '*.unit_price.required' => 'Unit Price is required in row :attribute',
            '*.unit_price.numeric' => 'Unit Price must be a number in row :attribute',
            '*.quantity.required' => 'Quantity is required in row :attribute',
            '*.quantity.integer' => 'Quantity must be a whole number in row :attribute',
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
    private function normalizeColumnName($columnName)
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
            'price' => 'unit_cost',
            'cost' => 'unit_cost',
            'purchase price' => 'unit_cost',
            'purchase_price' => 'unit_cost',
            'buy price' => 'unit_cost',
            'buy_price' => 'unit_cost',

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

        $requiredColumns = ['item_code', 'description', 'unit_cost', 'quantity'];
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
                'unit_cost' => 'Unit Price, Unit Cost, Price, Cost, Purchase Price',
                'quantity' => 'QTY, Quantity, Received Quantity, Amount, Count'
            ];

            $errorMessage = "Missing required columns. Found columns: " . implode(', ', $headers) . "\n\n";
            $errorMessage .= "Missing required fields and their accepted variations:\n";

            foreach ($missingColumns as $missing) {
                $errorMessage .= "• {$missing}: " . $expectedVariations[$missing] . "\n";
            }

            return [
                'valid' => false,
                'message' => $errorMessage
            ];
        }

        return ['valid' => true];
    }

    /**
     * Get the expected column headers for template
     */
    public static function getExpectedHeaders()
    {
        return [
            'Item Code',
            'Description',
            'Unit Price',
            'Selling Price',
            'Quantity',
            'VAT (%)',
            'Discount (%)'
        ];
    }

    /**
     * Generate a sample Excel template
     */
    public static function getSampleData()
    {
        return [
            [
                'Item Code' => 'BP001',
                'Description' => 'Brake Pad Set - Front',
                'Unit Price' => 25.00,
                'Selling Price' => 35.00,
                'Quantity' => 10,
                'VAT (%)' => 15,
                'Discount (%)' => 5
            ],
            [
                'Item Code' => 'OF002',
                'Description' => 'Oil Filter - Standard',
                'Unit Price' => 8.50,
                'Selling Price' => 12.00,
                'Quantity' => 25,
                'VAT (%)' => 15,
                'Discount (%)' => 0
            ],
            [
                'Item Code' => 'SF003',
                'Description' => 'Spark Plug Set',
                'Unit Price' => 15.75,
                'Selling Price' => 22.00,
                'Quantity' => 8,
                'VAT (%)' => 15,
                'Discount (%)' => 2.5
            ]
        ];
    }
}