<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Support\Collection;

class CustomerService
{
    /**
     * Search customers by name, contact, or type
     */
    public function searchCustomers(string $searchTerm, int $limit = 10): Collection
    {
        return Customer::where('name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('contact', 'LIKE', "%{$searchTerm}%")
            ->orWhere('type', 'LIKE', "%{$searchTerm}%")
            ->limit($limit)
            ->get();
    }

    /**
     * Create new customer during POS transaction
     */
    public function createCustomer(array $customerData): Customer
    {
        return Customer::create([
            'name' => $customerData['name'],
            'contact' => $customerData['contact'] ?? null,
            'address' => $customerData['address'] ?? null,
            'type' => $customerData['type'] ?? 'Retail',
        ]);
    }

    /**
     * Get customer transaction history
     */
    public function getCustomerHistory(int $customerId, int $limit = 50): Collection
    {
        return Sale::where('customer_id', $customerId)
            ->with(['saleItems.item'])
            ->orderBy('sale_date', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get customer statistics
     */
    public function getCustomerStats(int $customerId): array
    {
        $customer = Customer::find($customerId);
        $sales = Sale::where('customer_id', $customerId)->get();

        return [
            'customer' => $customer,
            'total_sales' => $sales->count(),
            'total_amount' => $sales->sum('total_amount'),
            'average_sale' => $sales->avg('total_amount'),
            'last_purchase' => $sales->max('sale_date'),
            'total_items_purchased' => $sales->sum(function ($sale) {
                return $sale->saleItems()->sum('quantity');
            }),
        ];
    }

    /**
     * Update customer type (Retail, Insurance, Wholesale, etc.)
     */
    public function updateCustomerType(int $customerId, string $type): Customer
    {
        $customer = Customer::findOrFail($customerId);
        $customer->update(['type' => $type]);
        return $customer;
    }

    /**
     * Get customers by type
     */
    public function getCustomersByType(string $type): Collection
    {
        return Customer::where('type', $type)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get frequent customers (by transaction count)
     */
    public function getFrequentCustomers(int $limit = 10): Collection
    {
        return Customer::withCount('sales')
            ->having('sales_count', '>', 0)
            ->orderBy('sales_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top customers by spending
     */
    public function getTopCustomersBySpending(int $limit = 10): Collection
    {
        return Customer::with('sales')
            ->get()
            ->map(function ($customer) {
                $customer->total_spent = $customer->sales->sum('total_amount');
                return $customer;
            })
            ->where('total_spent', '>', 0)
            ->sortByDesc('total_spent')
            ->take($limit);
    }

    /**
     * Quick customer creation with minimal data for POS
     */
    public function quickCreateCustomer(string $name, ?string $contact = null): Customer
    {
        return $this->createCustomer([
            'name' => $name,
            'contact' => $contact,
            'type' => 'Retail'
        ]);
    }

    /**
     * Get or create customer by name (for POS)
     */
    public function getOrCreateCustomer(string $name, ?string $contact = null): Customer
    {
        // Try to find existing customer
        $customer = Customer::where('name', $name)
            ->when($contact, function ($query, $contact) {
                return $query->where('contact', $contact);
            })
            ->first();

        // Create if not found
        if (!$customer) {
            $customer = $this->quickCreateCustomer($name, $contact);
        }

        return $customer;
    }

    /**
     * Validate customer data
     */
    public function validateCustomerData(array $data): array
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'Customer name is required';
        }

        if (!empty($data['contact']) && strlen($data['contact']) < 10) {
            $errors['contact'] = 'Contact number should be at least 10 digits';
        }

        if (!empty($data['type']) && !in_array($data['type'], ['Retail', 'Insurance', 'Wholesale', 'Corporate'])) {
            $errors['type'] = 'Invalid customer type';
        }

        return $errors;
    }
}