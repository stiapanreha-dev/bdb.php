<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tariff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TariffController extends Controller
{
    /**
     * Display a listing of the tariffs.
     */
    public function index()
    {
        $tariffs = Tariff::with(['subscriptions' => function ($query) {
            $query->where('is_active', true)
                  ->where('expires_at', '>', now());
        }])->orderBy('duration_days')->get();

        return view('admin.tariffs.index', compact('tariffs'));
    }

    /**
     * Show the form for creating a new tariff.
     */
    public function create()
    {
        return view('admin.tariffs.create');
    }

    /**
     * Store a newly created tariff in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Tariff::create($validated);

        return redirect()->route('admin.tariffs.index')
            ->with('success', 'Тариф успешно создан');
    }

    /**
     * Display the specified tariff with history.
     */
    public function show(Tariff $tariff)
    {
        $tariff->load(['history.changedBy', 'subscriptions.user']);

        return view('admin.tariffs.show', compact('tariff'));
    }

    /**
     * Show the form for editing the specified tariff.
     */
    public function edit(Tariff $tariff)
    {
        return view('admin.tariffs.edit', compact('tariff'));
    }

    /**
     * Update the specified tariff in storage.
     */
    public function update(Request $request, Tariff $tariff)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        DB::beginTransaction();
        try {
            // Логируем изменения
            foreach (['name', 'price', 'duration_days'] as $field) {
                if (isset($validated[$field]) && $tariff->$field != $validated[$field]) {
                    $tariff->logChange(
                        $field,
                        $tariff->$field,
                        $validated[$field],
                        auth()->id()
                    );
                }
            }

            if (isset($validated['is_active']) && $tariff->is_active != $validated['is_active']) {
                $tariff->logChange(
                    'is_active',
                    $tariff->is_active ? 'true' : 'false',
                    $validated['is_active'] ? 'true' : 'false',
                    auth()->id()
                );
            }

            $tariff->update($validated);

            DB::commit();

            return redirect()->route('admin.tariffs.index')
                ->with('success', 'Тариф успешно обновлён');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ошибка при обновлении тарифа: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified tariff from storage.
     */
    public function destroy(Tariff $tariff)
    {
        // Проверяем, есть ли активные подписки
        $activeSubscriptions = $tariff->subscriptions()
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->count();

        if ($activeSubscriptions > 0) {
            return back()->with('error',
                "Невозможно удалить тариф. Есть {$activeSubscriptions} активных подписок.");
        }

        $tariff->delete();

        return redirect()->route('admin.tariffs.index')
            ->with('success', 'Тариф успешно удалён');
    }
}
