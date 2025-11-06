<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    /**
     * Display a listing of articles.
     */
    public function index(Request $request)
    {
        $query = Article::with('user')
            ->where('status', 'active')
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc');

        // Поиск по заголовку и описанию
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

        $articles = $query->paginate(20);

        return view('articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new article.
     */
    public function create()
    {
        // Только авторизованные пользователи могут создавать статьи
        return view('articles.create');
    }

    /**
     * Store a newly created article in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', function ($attribute, $value, $fail) {
                // Проверяем что Editor.js JSON содержит блоки
                $data = json_decode($value, true);
                if (!$data || !isset($data['blocks']) || empty($data['blocks'])) {
                    $fail('Поле описание обязательно для заполнения.');
                }
            }],
            'images' => ['nullable', 'json'],
            'published_at' => ['nullable', 'date'],
        ]);

        $user = Auth::user();

        // Обрабатываем images (из JSON строки в массив)
        $images = null;
        if ($request->has('images') && !empty($request->input('images'))) {
            $imagesJson = json_decode($request->input('images'), true);
            if (is_array($imagesJson) && !empty($imagesJson)) {
                $images = $imagesJson;
            }
        }

        // Создаем статью
        $article = new Article();
        $article->user_id = $user->id;
        $article->title = $validated['title'];
        $article->description = $validated['description'];
        $article->images = $images;
        $article->status = 'active';
        $article->published_at = $validated['published_at'] ?? now();
        $article->save();

        return redirect()->route('articles.index')->with('success', 'Статья успешно создана');
    }

    /**
     * Display the specified article.
     */
    public function show($id)
    {
        $article = Article::with('user')->findOrFail($id);

        return view('articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified article.
     */
    public function edit($id)
    {
        $article = Article::findOrFail($id);

        // Проверка прав доступа
        if ($article->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'У вас нет прав на редактирование этой статьи');
        }

        return view('articles.edit', compact('article'));
    }

    /**
     * Update the specified article in storage.
     */
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        // Проверка прав доступа
        if ($article->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'У вас нет прав на редактирование этой статьи');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', function ($attribute, $value, $fail) {
                // Проверяем что Editor.js JSON содержит блоки
                $data = json_decode($value, true);
                if (!$data || !isset($data['blocks']) || empty($data['blocks'])) {
                    $fail('Поле описание обязательно для заполнения.');
                }
            }],
            'images' => ['nullable', 'json'],
            'published_at' => ['nullable', 'date'],
        ]);

        // Обрабатываем images (из JSON строки в массив)
        $images = null;
        if ($request->has('images') && !empty($request->input('images'))) {
            $imagesJson = json_decode($request->input('images'), true);
            if (is_array($imagesJson) && !empty($imagesJson)) {
                $images = $imagesJson;
            }
        }

        $article->title = $validated['title'];
        $article->description = $validated['description'];
        $article->images = $images;

        // Обновляем дату публикации только если это админ или автор и поле передано
        if ($request->has('published_at')) {
            $article->published_at = $validated['published_at'] ?? $article->published_at;
        }

        $article->save();

        return redirect()->route('articles.show', $article->id)->with('success', 'Статья успешно обновлена');
    }

    /**
     * Remove the specified article from storage.
     */
    public function destroy($id)
    {
        $article = Article::findOrFail($id);

        // Проверка прав доступа
        if ($article->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'У вас нет прав на удаление этой статьи');
        }

        $article->delete();

        return redirect()->route('articles.index')->with('success', 'Статья успешно удалена');
    }

    /**
     * Bulk delete articles (admin only).
     */
    public function bulkDelete(Request $request)
    {
        // Проверка прав администратора
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Доступ запрещен');
        }

        // Получаем массив ID из запроса
        $idsJson = $request->input('ids');

        if (!$idsJson) {
            return redirect()->route('articles.index')->with('error', 'Не выбрано ни одной статьи');
        }

        try {
            $ids = json_decode($idsJson, true);

            if (!is_array($ids) || empty($ids)) {
                return redirect()->route('articles.index')->with('error', 'Некорректные данные');
            }

            // Удаляем статьи
            $deleted = Article::whereIn('id', $ids)->delete();

            \Log::info('[ARTICLE] Bulk delete', [
                'admin_id' => Auth::id(),
                'deleted_count' => $deleted,
                'ids' => $ids
            ]);

            return redirect()->route('articles.index')->with('success', "Успешно удалено статей: {$deleted}");

        } catch (\Exception $e) {
            \Log::error('[ARTICLE] Bulk delete failed', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return redirect()->route('articles.index')->with('error', 'Ошибка при удалении статей');
        }
    }
}
