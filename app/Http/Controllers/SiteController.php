<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\SiteCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SiteController extends Controller
{
    /**
     * Display user's sites.
     */
    public function mySites()
    {
        $sites = Site::with('category')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('sites.user.my-sites', compact('sites'));
    }

    /**
     * Show the form for creating a new site.
     */
    public function create()
    {
        $categories = SiteCategory::with('children')
            ->active()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('sites.user.create', compact('categories'));
    }

    /**
     * Store a newly created site.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:site_categories,id',
            'url' => 'required|url|max:500',
            'contact_email' => 'required|email|max:255',
            'description' => ['nullable', 'string', 'max:65535', function ($attribute, $value, $fail) {
                if ($value) {
                    $data = json_decode($value, true);
                    if (!$data || !isset($data['blocks'])) {
                        $fail('Неверный формат описания.');
                    }
                }
            }],
            'images' => 'nullable|json',
        ]);

        // Generate unique slug
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        while (Site::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Handle logo upload via hidden field (already uploaded)
        $logo = $request->input('logo_path');

        // Parse images JSON
        $images = null;
        if ($request->filled('images')) {
            $imagesJson = json_decode($request->input('images'), true);
            if (is_array($imagesJson) && !empty($imagesJson)) {
                $images = $imagesJson;
            }
        }

        $site = Site::create([
            'user_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'url' => $validated['url'],
            'logo' => $logo,
            'description' => $validated['description'],
            'images' => $images,
            'contact_email' => $validated['contact_email'],
            'status' => Site::STATUS_PENDING,
        ]);

        return redirect()->route('sites.my')
            ->with('success', 'Сайт отправлен на модерацию. Вы получите уведомление на email после проверки.');
    }

    /**
     * Show the form for editing the site.
     */
    public function edit($id)
    {
        $site = Site::findOrFail($id);

        // Check ownership
        if ($site->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'У вас нет прав на редактирование этого сайта');
        }

        $categories = SiteCategory::with('children')
            ->active()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('sites.user.edit', compact('site', 'categories'));
    }

    /**
     * Update the site.
     */
    public function update(Request $request, $id)
    {
        $site = Site::findOrFail($id);

        // Check ownership
        if ($site->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'У вас нет прав на редактирование этого сайта');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:site_categories,id',
            'url' => 'required|url|max:500',
            'contact_email' => 'required|email|max:255',
            'description' => ['nullable', 'string', 'max:65535', function ($attribute, $value, $fail) {
                if ($value) {
                    $data = json_decode($value, true);
                    if (!$data || !isset($data['blocks'])) {
                        $fail('Неверный формат описания.');
                    }
                }
            }],
            'images' => 'nullable|json',
        ]);

        // Handle logo
        $logo = $site->logo;
        if ($request->filled('logo_path')) {
            // Delete old logo if exists
            if ($site->logo) {
                Storage::disk('public')->delete($site->logo);
            }
            $logo = $request->input('logo_path');
        }

        // Handle logo deletion
        if ($request->has('delete_logo') && $site->logo) {
            Storage::disk('public')->delete($site->logo);
            $logo = null;
        }

        // Parse images JSON
        $images = $site->images;
        if ($request->filled('images')) {
            $imagesJson = json_decode($request->input('images'), true);
            if (is_array($imagesJson)) {
                $images = !empty($imagesJson) ? $imagesJson : null;
            }
        }

        $site->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'url' => $validated['url'],
            'logo' => $logo,
            'description' => $validated['description'],
            'images' => $images,
            'contact_email' => $validated['contact_email'],
        ]);

        return redirect()->route('sites.my')
            ->with('success', 'Сайт успешно обновлен');
    }

    /**
     * Delete the site.
     */
    public function destroy($id)
    {
        $site = Site::findOrFail($id);

        // Check ownership
        if ($site->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'У вас нет прав на удаление этого сайта');
        }

        // Delete logo
        if ($site->logo) {
            Storage::disk('public')->delete($site->logo);
        }

        // Delete additional images
        if ($site->images) {
            foreach ($site->images as $image) {
                if (isset($image['path'])) {
                    Storage::disk('public')->delete($image['path']);
                }
            }
        }

        $siteName = $site->name;
        $site->delete();

        return redirect()->route('sites.my')
            ->with('success', 'Сайт "' . $siteName . '" удален');
    }
}
