<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(15);
        return view('Categories.index', compact('categories'));
    }

    public function create()
    {
        return view('Categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        Category::create($request->only(['name', 'description']));

        return redirect()->route('categories.index')
            ->with('success', 'category created successfully');
    }

    public function show(Category $category)
    {
        return view('category.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('Categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
        ]);

        $category->update($request->only(['name', 'description']));

        return redirect()->route('categories.index')
            ->with('success', 'category updated successfully');
    }

    // Export categories to CSV
    public function exportCsv()
    {
        // Fetch all Category
        $Category = Category::all();

        // Define CSV headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="category.csv"',
        ];

        // Open output stream
        $callback = function () use ($Category) {
            $file = fopen('php://output', 'w');

            // Add CSV column headers
            fputcsv($file, ['ID', 'Name', 'Description', 'Created At']);

            // Add vendor data
            foreach ($Category as $vendor) {
                fputcsv($file, [
                    $vendor->id,
                    $vendor->name,
                    $vendor->description,
                    $vendor->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
