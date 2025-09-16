<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Vendor;
use App\Models\Item;
use App\Models\Category;
use App\Models\VendorItemMapping;
use App\Services\GRNService;
use App\Services\InventoryService;
use App\Services\BatchService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VendorItemMappingIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $grnService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->grnService = new GRNService(
            new InventoryService(),
            new BatchService()
        );
    }

    /** @test */
    public function it_can_resolve_vendor_item_mapping_when_mapping_exists()
    {
        // Create test data
        $vendor = Vendor::create([
            'name' => 'Test Vendor',
            'contact_person' => 'John Doe',
            'phone' => '123456789',
            'email' => 'test@vendor.com',
        ]);

        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test category description',
        ]);

        $item = Item::create([
            'category_id' => $category->id,
            'name' => 'Test Item',
            'item_no' => 'ITM001',
            'reorder_point' => 10,
            'is_active' => true,
        ]);

        // Create vendor item mapping
        $mapping = VendorItemMapping::create([
            'vendor_id' => $vendor->id,
            'item_id' => $item->id,
            'vendor_item_code' => 'V-BP-001',
            'vendor_item_name' => 'Vendor Brake Pad',
            'is_preferred' => true,
        ]);

        // Test the resolution
        $resolvedItem = $this->grnService->resolveVendorItemMapping($vendor->id, 'V-BP-001');

        $this->assertNotNull($resolvedItem);
        $this->assertEquals($item->id, $resolvedItem->id);
        $this->assertEquals('Test Item', $resolvedItem->name);
        $this->assertEquals('ITM001', $resolvedItem->item_no);
    }

    /** @test */
    public function it_can_resolve_vendor_item_mapping_by_item_no_when_no_mapping_exists()
    {
        // Create test data
        $vendor = Vendor::create([
            'name' => 'Test Vendor',
            'contact_person' => 'John Doe',
            'phone' => '123456789',
            'email' => 'test@vendor.com',
        ]);

        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test category description',
        ]);

        $item = Item::create([
            'category_id' => $category->id,
            'name' => 'Test Item',
            'item_no' => 'ITM002',
            'reorder_point' => 10,
            'is_active' => true,
        ]);

        // Test resolution by item number (no mapping exists)
        $resolvedItem = $this->grnService->resolveVendorItemMapping($vendor->id, 'ITM002');

        $this->assertNotNull($resolvedItem);
        $this->assertEquals($item->id, $resolvedItem->id);

        // Verify that a mapping was auto-created
        $createdMapping = VendorItemMapping::where('vendor_id', $vendor->id)
            ->where('vendor_item_code', 'ITM002')
            ->first();

        $this->assertNotNull($createdMapping);
        $this->assertEquals($item->id, $createdMapping->item_id);
    }

    /** @test */
    public function it_throws_exception_when_item_cannot_be_resolved()
    {
        $vendor = Vendor::create([
            'name' => 'Test Vendor',
            'contact_person' => 'John Doe',
            'phone' => '123456789',
            'email' => 'test@vendor.com',
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Item not found for vendor code: INVALID-CODE. Please create mapping.');

        $this->grnService->resolveVendorItemMapping($vendor->id, 'INVALID-CODE');
    }

    /** @test */
    public function it_can_use_findMapping_helper_method()
    {
        // Create test data
        $vendor = Vendor::create([
            'name' => 'Test Vendor',
            'contact_person' => 'John Doe',
            'phone' => '123456789',
            'email' => 'test@vendor.com',
        ]);

        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test category description',
        ]);

        $item = Item::create([
            'category_id' => $category->id,
            'name' => 'Test Item',
            'item_no' => 'ITM003',
            'reorder_point' => 10,
            'is_active' => true,
        ]);

        $mapping = VendorItemMapping::create([
            'vendor_id' => $vendor->id,
            'item_id' => $item->id,
            'vendor_item_code' => 'V-BP-003',
            'vendor_item_name' => 'Vendor Brake Pad 003',
            'is_preferred' => true,
        ]);

        // Test the helper method directly
        $foundMapping = VendorItemMapping::findMapping($vendor->id, 'V-BP-003');

        $this->assertNotNull($foundMapping);
        $this->assertEquals($mapping->id, $foundMapping->id);
        $this->assertEquals($item->id, $foundMapping->item_id);
    }

    /** @test */
    public function it_can_manage_preferred_vendors()
    {
        // Create test data
        $vendor1 = Vendor::create([
            'name' => 'Vendor 1',
            'contact_person' => 'John Doe',
            'phone' => '123456789',
            'email' => 'vendor1@test.com',
        ]);

        $vendor2 = Vendor::create([
            'name' => 'Vendor 2',
            'contact_person' => 'Jane Smith',
            'phone' => '987654321',
            'email' => 'vendor2@test.com',
        ]);

        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test category description',
        ]);

        $item = Item::create([
            'category_id' => $category->id,
            'name' => 'Test Item',
            'item_no' => 'ITM004',
            'reorder_point' => 10,
            'is_active' => true,
        ]);

        // Create mappings for both vendors
        $mapping1 = VendorItemMapping::create([
            'vendor_id' => $vendor1->id,
            'item_id' => $item->id,
            'vendor_item_code' => 'V1-BP-004',
            'vendor_item_name' => 'Vendor 1 Brake Pad',
            'is_preferred' => true,
        ]);

        $mapping2 = VendorItemMapping::create([
            'vendor_id' => $vendor2->id,
            'item_id' => $item->id,
            'vendor_item_code' => 'V2-BP-004',
            'vendor_item_name' => 'Vendor 2 Brake Pad',
            'is_preferred' => false,
        ]);

        // Test preferred vendor retrieval
        $preferredVendor = VendorItemMapping::getPreferredVendor($item->id);
        $this->assertNotNull($preferredVendor);
        $this->assertEquals($vendor1->id, $preferredVendor->vendor_id);

        // Change preferred vendor
        $mapping2->setAsPreferred();

        // Verify preference changed
        $mapping1->refresh();
        $mapping2->refresh();

        $this->assertFalse($mapping1->is_preferred);
        $this->assertTrue($mapping2->is_preferred);

        $newPreferredVendor = VendorItemMapping::getPreferredVendor($item->id);
        $this->assertEquals($vendor2->id, $newPreferredVendor->vendor_id);
    }
}