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
        $columnName = strtolower(trim($columnName));

        // Map common variations to our expected format
        $mappings = [
            'item code' => 'item_code',
            'itemcode' => 'item_code',
            'vendor item code' => 'item_code',
            'vendor_item_code' => 'item_code',
            'part number' => 'item_code',
            'part_number' => 'item_code',

            'description' => 'description',
            'item description' => 'description',
            'item_description' => 'description',
            'name' => 'description',
            'item name' => 'description',
            'item_name' => 'description',

            'unit price' => 'unit_price',
            'unitprice' => 'unit_price',
            'unit_price' => 'unit_price',
            'price' => 'unit_price',
            'cost' => 'unit_price',

            'quantity' => 'quantity',
            'qty' => 'quantity',
            'received quantity' => 'quantity',
            'received_quantity' => 'quantity',
            'received qty' => 'quantity',
            'received_qty' => 'quantity',

            'vat' => 'vat',
            'vat%' => 'vat',
            'vat percent' => 'vat',
            'vat_percent' => 'vat',
            'tax' => 'vat',

            'discount' => 'discount',
            'discount%' => 'discount',
            'discount percent' => 'discount',
            'discount_percent' => 'discount',
        ];

        return $mappings[$columnName] ?? $columnName;
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