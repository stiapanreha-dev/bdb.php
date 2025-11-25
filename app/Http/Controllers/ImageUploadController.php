<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    /**
     * Upload image for Editor.js
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadByFile(Request $request)
    {
        \Log::info('[IMAGE UPLOAD] Upload request received', [
            'has_file' => $request->hasFile('image'),
            'all_files' => $request->allFiles(),
            'all_input' => $request->except('image')
        ]);

        try {
            // Валидация файла
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // макс 5MB
            ]);

            \Log::info('[IMAGE UPLOAD] Validation passed');

            // Получаем файл
            $file = $request->file('image');

            // Генерируем уникальное имя
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Сохраняем в storage/app/public/images
            $path = $file->storeAs('images', $filename, 'public');

            // Получаем полный URL
            $url = Storage::disk('public')->url($path);

            \Log::info('[IMAGE UPLOAD] File uploaded successfully', [
                'path' => $path,
                'url' => $url,
                'filename' => $filename
            ]);

            // Возвращаем ответ в формате Editor.js
            return response()->json([
                'success' => 1,
                'file' => [
                    'url' => $url,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('[IMAGE UPLOAD] Upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => 0,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Upload image by URL for Editor.js
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadByUrl(Request $request)
    {
        try {
            // Валидация URL
            $request->validate([
                'url' => 'required|url',
            ]);

            $url = $request->input('url');

            // Возвращаем URL как есть (не загружаем на сервер)
            return response()->json([
                'success' => 1,
                'file' => [
                    'url' => $url,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Upload shop product image for Editor.js
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadShopImage(Request $request)
    {
        \Log::info('[SHOP IMAGE UPLOAD] Upload request received', [
            'has_file' => $request->hasFile('image'),
            'all_files' => $request->allFiles(),
        ]);

        try {
            // Валидация файла
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // макс 5MB
            ]);

            \Log::info('[SHOP IMAGE UPLOAD] Validation passed');

            // Получаем файл
            $file = $request->file('image');

            // Генерируем уникальное имя
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Сохраняем в storage/app/public/shop/products
            $path = $file->storeAs('shop/products', $filename, 'public');

            // Получаем полный URL
            $url = Storage::disk('public')->url($path);

            \Log::info('[SHOP IMAGE UPLOAD] File uploaded successfully', [
                'path' => $path,
                'url' => $url,
                'filename' => $filename
            ]);

            // Возвращаем ответ в формате Editor.js
            return response()->json([
                'success' => 1,
                'file' => [
                    'url' => $url,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('[SHOP IMAGE UPLOAD] Upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => 0,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Upload multiple announcement images
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadAnnouncementImages(Request $request)
    {
        try {
            \Log::info('[ANNOUNCEMENT IMAGES] Upload request received', [
                'files_count' => count($request->allFiles())
            ]);

            // Валидация файлов (максимум 5 изображений)
            $request->validate([
                'images' => 'required|array|max:5',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120', // макс 5MB каждое
            ]);

            $uploaded = [];

            foreach ($request->file('images') as $file) {
                // Генерируем уникальное имя
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Сохраняем в storage/app/public/images
                $path = $file->storeAs('images', $filename, 'public');

                // Получаем полный URL
                $url = Storage::disk('public')->url($path);

                $uploaded[] = [
                    'path' => $path,
                    'url' => $url,
                ];
            }

            \Log::info('[ANNOUNCEMENT IMAGES] Files uploaded successfully', [
                'count' => count($uploaded)
            ]);

            return response()->json([
                'success' => true,
                'images' => $uploaded
            ]);

        } catch (\Exception $e) {
            \Log::error('[ANNOUNCEMENT IMAGES] Upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
