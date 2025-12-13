<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\SiteCategory;
use Illuminate\Http\Request;

class SiteCatalogController extends Controller
{
    /**
     * Display listing of approved sites.
     */
    public function index(Request $request)
    {
        $query = Site::with(['category', 'user'])
            ->approved()
            ->orderBy('created_at', 'desc');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('url', 'ilike', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $categoryId = $request->category;
            $query->where(function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                  ->orWhereHas('category', function ($sq) use ($categoryId) {
                      $sq->where('parent_id', $categoryId);
                  });
            });
        }

        $sites = $query->paginate(12);

        // Get categories for filter
        $categories = SiteCategory::with('children')
            ->active()
            ->root()
            ->orderBy('sort_order')
            ->get();

        return view('sites.index', compact('sites', 'categories'));
    }

    /**
     * Display sites in a specific category.
     */
    public function category($slug)
    {
        $category = SiteCategory::where('slug', $slug)->active()->firstOrFail();

        // Get sites from this category and its children
        $categoryIds = [$category->id];
        $childIds = $category->children()->active()->pluck('id')->toArray();
        $categoryIds = array_merge($categoryIds, $childIds);

        $sites = Site::with(['category', 'user'])
            ->approved()
            ->whereIn('category_id', $categoryIds)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Get all categories for sidebar
        $categories = SiteCategory::with('children')
            ->active()
            ->root()
            ->orderBy('sort_order')
            ->get();

        return view('sites.category', compact('sites', 'category', 'categories'));
    }

    /**
     * Display detailed page for a site.
     */
    public function show($slug)
    {
        $site = Site::with(['category', 'user'])
            ->where('slug', $slug)
            ->approved()
            ->firstOrFail();

        // Increment views
        $site->incrementViews();

        // Get related sites from same category
        $relatedSites = Site::with('category')
            ->approved()
            ->where('category_id', $site->category_id)
            ->where('id', '!=', $site->id)
            ->limit(4)
            ->get();

        return view('sites.show', compact('site', 'relatedSites'));
    }
}
