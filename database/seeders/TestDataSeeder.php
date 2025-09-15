<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\Category;
use App\Models\Item;
use App\Models\VendorItemMapping;
use App\Models\Store;
use App\Models\Bin;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Categories
        $categories = [
            ['name' => 'Brake Parts', 'description' => 'Brake pads, discs, and components'],
            ['name' => 'Engine Parts', 'description' => 'Engine components and accessories'],
            ['name' => 'Transmission', 'description' => 'Transmission parts and fluids'],
            ['name' => 'Suspension', 'description' => 'Suspension components and shock absorbers'],
            ['name' => 'Electrical', 'description' => 'Electrical components and wiring'],
            ['name' => 'Body Parts', 'description' => 'Body panels and exterior components'],
            ['name' => 'Filters', 'description' => 'Oil, air, and fuel filters'],
            ['name' => 'Belts & Hoses', 'description' => 'Timing belts, drive belts, and hoses'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create Vendors
        $vendors = [
            [
                'name' => 'AutoParts Direct',
                'contact_person' => 'John Smith',
                'phone' => '+1-555-0101',
                'email' => 'john@autopartsdirect.com',
                'address' => '123 Industrial Ave, Detroit, MI',
            ],
            [
                'name' => 'Brake Masters Supply',
                'contact_person' => 'Sarah Johnson',
                'phone' => '+1-555-0102',
                'email' => 'sarah@brakemasters.com',
                'address' => '456 Commerce St, Chicago, IL',
            ],
            [
                'name' => 'Engine Pro Components',
                'contact_person' => 'Mike Davis',
                'phone' => '+1-555-0103',
                'email' => 'mike@enginepro.com',
                'address' => '789 Motor Way, Cleveland, OH',
            ],
            [
                'name' => 'Universal Auto Supply',
                'contact_person' => 'Lisa Chen',
                'phone' => '+1-555-0104',
                'email' => 'lisa@universalauto.com',
                'address' => '321 Parts Blvd, Phoenix, AZ',
            ],
            [
                'name' => 'Premium Parts Co',
                'contact_person' => 'Robert Wilson',
                'phone' => '+1-555-0105',
                'email' => 'robert@premiumparts.com',
                'address' => '654 Quality Rd, Denver, CO',
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::create($vendor);
        }

        // Create Stores and Bins
        $mainStore = Store::create([
            'store_name' => 'Main Warehouse',
            'store_location' => 'Main Building - Ground Floor',
        ]);

        $annexStore = Store::create([
            'store_name' => 'Annex Storage',
            'store_location' => 'Annex Building - Floor 1',
        ]);

        // Create bins for main store
        $bins = [
            'A-01', 'A-02', 'A-03', 'B-01', 'B-02', 'B-03',
            'C-01', 'C-02', 'C-03', 'D-01', 'D-02', 'D-03',
        ];

        foreach ($bins as $binName) {
            Bin::create([
                'store_id' => $mainStore->id,
                'bin_name' => $binName,
                'description' => "Storage bin {$binName}",
            ]);
        }

        // Create Items
        $items = [
            // Brake Parts
            [
                'category_id' => 1,
                'name' => 'Front Brake Pad Set',
                'item_no' => 'BP-F001',
                'description' => 'Premium ceramic front brake pads',
                'unit_cost' => 45.00,
                'selling_price' => 75.00,
                'min_stock' => 10,
                'max_stock' => 100,
                'reorder_level' => 20,
                'unit_of_measure' => 'SET',
                'manufacturer_name' => 'BrakeTech',
                'vat' => 10.0,
                'is_active' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Rear Brake Pad Set',
                'item_no' => 'BP-R001',
                'description' => 'Premium ceramic rear brake pads',
                'unit_cost' => 35.00,
                'selling_price' => 60.00,
                'min_stock' => 8,
                'max_stock' => 80,
                'reorder_level' => 15,
                'unit_of_measure' => 'SET',
                'manufacturer_name' => 'BrakeTech',
                'vat' => 10.0,
                'is_active' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Brake Disc Rotor - Front',
                'item_no' => 'BD-F001',
                'description' => 'Ventilated front brake disc rotor',
                'unit_cost' => 65.00,
                'selling_price' => 110.00,
                'min_stock' => 5,
                'max_stock' => 50,
                'reorder_level' => 10,
                'unit_of_measure' => 'PCS',
                'manufacturer_name' => 'RotorMax',
                'vat' => 10.0,
                'is_active' => true,
            ],

            // Engine Parts
            [
                'category_id' => 2,
                'name' => 'Engine Oil Filter',
                'item_no' => 'OF-001',
                'description' => 'High-performance oil filter',
                'unit_cost' => 8.50,
                'selling_price' => 15.00,
                'min_stock' => 25,
                'max_stock' => 200,
                'reorder_level' => 50,
                'unit_of_measure' => 'PCS',
                'manufacturer_name' => 'FilterPro',
                'vat' => 10.0,
                'is_active' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Spark Plug Set',
                'item_no' => 'SP-001',
                'description' => 'Platinum spark plugs - set of 4',
                'unit_cost' => 28.00,
                'selling_price' => 45.00,
                'min_stock' => 15,
                'max_stock' => 120,
                'reorder_level' => 30,
                'unit_of_measure' => 'SET',
                'manufacturer_name' => 'IgniteTech',
                'vat' => 10.0,
                'is_active' => true,
            ],

            // Transmission
            [
                'category_id' => 3,
                'name' => 'Transmission Fluid',
                'item_no' => 'TF-001',
                'description' => 'Automatic transmission fluid - 1L',
                'unit_cost' => 12.00,
                'selling_price' => 20.00,
                'min_stock' => 20,
                'max_stock' => 150,
                'reorder_level' => 40,
                'unit_of_measure' => 'LTR',
                'manufacturer_name' => 'FluidMax',
                'vat' => 10.0,
                'is_active' => true,
            ],

            // Suspension
            [
                'category_id' => 4,
                'name' => 'Shock Absorber - Front',
                'item_no' => 'SA-F001',
                'description' => 'Gas-filled front shock absorber',
                'unit_cost' => 85.00,
                'selling_price' => 140.00,
                'min_stock' => 6,
                'max_stock' => 60,
                'reorder_level' => 12,
                'unit_of_measure' => 'PCS',
                'manufacturer_name' => 'SuspensionPro',
                'vat' => 10.0,
                'is_active' => true,
            ],

            // Electrical
            [
                'category_id' => 5,
                'name' => 'Car Battery 12V 60Ah',
                'item_no' => 'BAT-001',
                'description' => 'Maintenance-free car battery',
                'unit_cost' => 75.00,
                'selling_price' => 125.00,
                'min_stock' => 8,
                'max_stock' => 40,
                'reorder_level' => 15,
                'unit_of_measure' => 'PCS',
                'manufacturer_name' => 'PowerCell',
                'vat' => 10.0,
                'is_active' => true,
            ],

            // Filters
            [
                'category_id' => 7,
                'name' => 'Air Filter',
                'item_no' => 'AF-001',
                'description' => 'High-flow air filter',
                'unit_cost' => 15.00,
                'selling_price' => 25.00,
                'min_stock' => 20,
                'max_stock' => 100,
                'reorder_level' => 35,
                'unit_of_measure' => 'PCS',
                'manufacturer_name' => 'AirFlow',
                'vat' => 10.0,
                'is_active' => true,
            ],

            // Belts & Hoses
            [
                'category_id' => 8,
                'name' => 'Timing Belt',
                'item_no' => 'TB-001',
                'description' => 'Rubber timing belt with teeth',
                'unit_cost' => 35.00,
                'selling_price' => 60.00,
                'min_stock' => 10,
                'max_stock' => 80,
                'reorder_level' => 20,
                'unit_of_measure' => 'PCS',
                'manufacturer_name' => 'BeltTech',
                'vat' => 10.0,
                'is_active' => true,
            ],
        ];

        $createdItems = [];
        foreach ($items as $item) {
            $createdItems[] = Item::create($item);
        }

        // Create Vendor Item Mappings
        $mappings = [
            // AutoParts Direct mappings
            [
                'vendor_id' => 1, // AutoParts Direct
                'item_id' => 1,   // Front Brake Pad Set
                'vendor_item_code' => 'APD-BP-F001',
                'vendor_item_name' => 'Ceramic Front Brake Pads',
                'vendor_cost' => 42.00,
                'is_preferred' => true,
            ],
            [
                'vendor_id' => 1, // AutoParts Direct
                'item_id' => 4,   // Engine Oil Filter
                'vendor_item_code' => 'APD-OF-001',
                'vendor_item_name' => 'Premium Oil Filter',
                'vendor_cost' => 7.50,
                'is_preferred' => false,
            ],
            [
                'vendor_id' => 1, // AutoParts Direct
                'item_id' => 9,   // Air Filter
                'vendor_item_code' => 'APD-AF-001',
                'vendor_item_name' => 'High-Flow Air Filter',
                'vendor_cost' => 14.00,
                'is_preferred' => true,
            ],

            // Brake Masters Supply mappings
            [
                'vendor_id' => 2, // Brake Masters Supply
                'item_id' => 1,   // Front Brake Pad Set
                'vendor_item_code' => 'BMS-FRONT-PAD-001',
                'vendor_item_name' => 'Professional Front Brake Pads',
                'vendor_cost' => 44.50,
                'is_preferred' => false,
            ],
            [
                'vendor_id' => 2, // Brake Masters Supply
                'item_id' => 2,   // Rear Brake Pad Set
                'vendor_item_code' => 'BMS-REAR-PAD-001',
                'vendor_item_name' => 'Professional Rear Brake Pads',
                'vendor_cost' => 33.00,
                'is_preferred' => true,
            ],
            [
                'vendor_id' => 2, // Brake Masters Supply
                'item_id' => 3,   // Brake Disc Rotor
                'vendor_item_code' => 'BMS-ROTOR-F001',
                'vendor_item_name' => 'Ventilated Front Rotor',
                'vendor_cost' => 62.00,
                'is_preferred' => true,
            ],

            // Engine Pro Components mappings
            [
                'vendor_id' => 3, // Engine Pro Components
                'item_id' => 4,   // Engine Oil Filter
                'vendor_item_code' => 'EPC-OIL-FILTER-001',
                'vendor_item_name' => 'Professional Grade Oil Filter',
                'vendor_cost' => 8.00,
                'is_preferred' => true,
            ],
            [
                'vendor_id' => 3, // Engine Pro Components
                'item_id' => 5,   // Spark Plug Set
                'vendor_item_code' => 'EPC-SPARK-4SET',
                'vendor_item_name' => 'Platinum Spark Plug Set',
                'vendor_cost' => 26.50,
                'is_preferred' => true,
            ],
            [
                'vendor_id' => 3, // Engine Pro Components
                'item_id' => 10,  // Timing Belt
                'vendor_item_code' => 'EPC-TIMING-BELT-001',
                'vendor_item_name' => 'Heavy Duty Timing Belt',
                'vendor_cost' => 32.00,
                'is_preferred' => true,
            ],

            // Universal Auto Supply mappings
            [
                'vendor_id' => 4, // Universal Auto Supply
                'item_id' => 6,   // Transmission Fluid
                'vendor_item_code' => 'UAS-TRANS-FLUID-1L',
                'vendor_item_name' => 'ATF Transmission Fluid',
                'vendor_cost' => 11.50,
                'is_preferred' => true,
            ],
            [
                'vendor_id' => 4, // Universal Auto Supply
                'item_id' => 8,   // Car Battery
                'vendor_item_code' => 'UAS-BATTERY-60AH',
                'vendor_item_name' => 'Maintenance Free Battery 60Ah',
                'vendor_cost' => 72.00,
                'is_preferred' => true,
            ],
            [
                'vendor_id' => 4, // Universal Auto Supply
                'item_id' => 9,   // Air Filter
                'vendor_item_code' => 'UAS-AIR-FILTER-001',
                'vendor_item_name' => 'Standard Air Filter',
                'vendor_cost' => 15.50,
                'is_preferred' => false,
            ],

            // Premium Parts Co mappings
            [
                'vendor_id' => 5, // Premium Parts Co
                'item_id' => 7,   // Shock Absorber
                'vendor_item_code' => 'PPC-SHOCK-FRONT',
                'vendor_item_name' => 'Premium Gas Shock Absorber',
                'vendor_cost' => 82.00,
                'is_preferred' => true,
            ],
            [
                'vendor_id' => 5, // Premium Parts Co
                'item_id' => 1,   // Front Brake Pad Set
                'vendor_item_code' => 'PPC-BRAKE-PAD-FRONT',
                'vendor_item_name' => 'Ultra Premium Front Pads',
                'vendor_cost' => 47.00,
                'is_preferred' => false,
            ],
            [
                'vendor_id' => 5, // Premium Parts Co
                'item_id' => 5,   // Spark Plug Set
                'vendor_item_code' => 'PPC-SPARK-PLATINUM-4',
                'vendor_item_name' => 'Premium Platinum Spark Plugs',
                'vendor_cost' => 29.00,
                'is_preferred' => false,
            ],
        ];

        foreach ($mappings as $mapping) {
            VendorItemMapping::create($mapping);
        }

        $this->command->info('Test data created successfully!');
        $this->command->info('Created:');
        $this->command->info('- ' . count($categories) . ' categories');
        $this->command->info('- ' . count($vendors) . ' vendors');
        $this->command->info('- ' . count($items) . ' items');
        $this->command->info('- ' . count($mappings) . ' vendor item mappings');
        $this->command->info('- 2 stores with ' . count($bins) . ' bins');
    }
}