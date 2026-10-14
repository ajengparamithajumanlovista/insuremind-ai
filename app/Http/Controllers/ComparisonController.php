<?php

namespace App\Http\Controllers;

use App\Models\InsuranceProduct;
use App\Services\InsuranceComparisonService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComparisonController extends Controller
{
    public function __construct(
        private readonly InsuranceComparisonService $comparison,
    ) {}

    /**
     * Perbandingan produk asuransi (maksimal 3 produk berdampingan).
     */
    public function index(Request $request): View
    {
        $category = $request->query('category');
        $search = $request->query('q');

        $products = $this->comparison->products(
            is_string($category) ? $category : null,
            is_string($search) ? $search : null,
        );

        $selectedIds = collect((array) $request->query('ids'))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->take(3)
            ->values();

        if ($selectedIds->isEmpty()) {
            $selectedIds = $products->take(3)->pluck('id');
        }

        $selected = $products->whereIn('id', $selectedIds)->values();

        $profile = $request->user()->profileOrDefault();
        $profile = $profile->isReady() ? $profile : null;

        $comparison = $this->comparison->compare($selected);

        $suitability = $selected->mapWithKeys(fn (InsuranceProduct $product) => [
            $product->id => $this->comparison->suitability($product, $profile),
        ])->all();

        return view('comparison.index', [
            'products' => $products,
            'selected' => $selected,
            'comparison' => $comparison,
            'suitability' => $suitability,
            'category' => $category,
            'search' => $search,
        ]);
    }
}
