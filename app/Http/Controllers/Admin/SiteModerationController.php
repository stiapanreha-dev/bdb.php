<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\SiteCategory;
use App\Mail\SiteApprovedMail;
use App\Mail\SiteRejectedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SiteModerationController extends Controller
{
    /**
     * Show the form for creating a new site (admin).
     */
    public function create()
    {
        $categories = SiteCategory::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.sites.moderation.create', compact('categories'));
    }

    /**
     * Store a newly created site (admin) - auto approved.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:site_categories,id',
            'url' => 'required|url|max:500',
            'contact_email' => 'required|email|max:255',
            'description' => 'nullable|string',
            'logo_path' => 'nullable|string',
            'images' => 'nullable|string',
        ]);

        // Generate unique slug
        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;
        while (Site::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $site = Site::create([
            'user_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'url' => $validated['url'],
            'logo' => $validated['logo_path'] ?? null,
            'description' => $validated['description'] ?? null,
            'images' => $validated['images'] ? json_decode($validated['images'], true) : null,
            'contact_email' => $validated['contact_email'],
            'status' => Site::STATUS_APPROVED,
            'moderated_by' => Auth::id(),
            'moderated_at' => now(),
        ]);

        return redirect()->route('admin.sites.moderation.index')
            ->with('success', 'Сайт "' . $site->name . '" успешно добавлен');
    }

    /**
     * Display a listing of sites for moderation.
     */
    public function index(Request $request)
    {
        $query = Site::with(['user', 'category', 'moderator']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // By default show pending sites first
            $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END");
        }

        $query->orderBy('created_at', 'desc');

        $sites = $query->paginate(20);

        // Count by status
        $counts = [
            'pending' => Site::pending()->count(),
            'approved' => Site::approved()->count(),
            'rejected' => Site::rejected()->count(),
        ];

        return view('admin.sites.moderation.index', compact('sites', 'counts'));
    }

    /**
     * Display the specified site for moderation.
     */
    public function show($id)
    {
        $site = Site::with(['user', 'category', 'moderator'])->findOrFail($id);

        return view('admin.sites.moderation.show', compact('site'));
    }

    /**
     * Approve a site.
     */
    public function approve(Request $request, $id)
    {
        $site = Site::findOrFail($id);

        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $site->status = Site::STATUS_APPROVED;
        $site->moderated_by = Auth::id();
        $site->moderated_at = now();
        $site->moderation_comment = $request->input('comment');
        $site->save();

        // Send email notification
        try {
            Mail::to($site->contact_email)->send(new SiteApprovedMail($site));
        } catch (\Exception $e) {
            \Log::error('Failed to send site approval email', [
                'site_id' => $site->id,
                'email' => $site->contact_email,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('admin.sites.moderation.index')
            ->with('success', 'Сайт "' . $site->name . '" одобрен');
    }

    /**
     * Reject a site.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $site = Site::findOrFail($id);

        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $site->status = Site::STATUS_REJECTED;
        $site->moderated_by = Auth::id();
        $site->moderated_at = now();
        $site->moderation_comment = $request->input('reason');
        $site->save();

        // Send email notification with rejection reason
        try {
            Mail::to($site->contact_email)->send(new SiteRejectedMail($site, $request->input('reason')));
        } catch (\Exception $e) {
            \Log::error('Failed to send site rejection email', [
                'site_id' => $site->id,
                'email' => $site->contact_email,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('admin.sites.moderation.index')
            ->with('success', 'Сайт "' . $site->name . '" отклонен');
    }

    /**
     * Delete a site.
     */
    public function destroy($id)
    {
        $site = Site::findOrFail($id);

        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $siteName = $site->name;
        $site->delete();

        return redirect()->route('admin.sites.moderation.index')
            ->with('success', 'Сайт "' . $siteName . '" удален');
    }
}
