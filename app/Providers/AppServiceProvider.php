<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share news_count and ideas_count with all views
        view()->composer('*', function ($view) {
            $news_count = \App\Models\News::where('is_published', true)->count();
            $ideas_count = \App\Models\Idea::approved()->count(); // Only approved ideas

            $view->with('news_count', $news_count);
            $view->with('ideas_count', $ideas_count);
        });

        // Register custom Blade directive for Editor.js rendering
        \Blade::directive('editorJsRender', function ($expression) {
            return "<?php
                \$content = {$expression};
                try {
                    // Пытаемся распарсить как JSON
                    \$decoded = json_decode(\$content);
                    if (json_last_error() === JSON_ERROR_NONE && isset(\$decoded->blocks)) {
                        // Это валидный JSON Editor.js, пытаемся рендерить
                        try {
                            echo editorjs()->render(\$content);
                        } catch (\Exception \$renderEx) {
                            // Fallback: рендерим вручную простые блоки
                            foreach (\$decoded->blocks as \$block) {
                                if (isset(\$block->type) && isset(\$block->data)) {
                                    switch (\$block->type) {
                                        case 'paragraph':
                                            echo '<p>' . e(\$block->data->text ?? '') . '</p>';
                                            break;
                                        case 'header':
                                            \$level = \$block->data->level ?? 2;
                                            echo '<h' . \$level . '>' . e(\$block->data->text ?? '') . '</h' . \$level . '>';
                                            break;
                                        case 'list':
                                            \$tag = (\$block->data->style ?? 'unordered') === 'ordered' ? 'ol' : 'ul';
                                            echo '<' . \$tag . '>';
                                            foreach (\$block->data->items ?? [] as \$item) {
                                                \$text = is_object(\$item) ? (\$item->content ?? '') : (is_string(\$item) ? \$item : '');
                                                echo '<li>' . e(\$text) . '</li>';
                                            }
                                            echo '</' . \$tag . '>';
                                            break;
                                        case 'image':
                                            \$url = \$block->data->file->url ?? (\$block->data->url ?? '');
                                            \$caption = \$block->data->caption ?? '';
                                            \$stretched = !empty(\$block->data->stretched);
                                            \$withBorder = !empty(\$block->data->withBorder);
                                            \$withBackground = !empty(\$block->data->withBackground);
                                            if (\$url) {
                                                \$figureClass = 'editorjs-image';
                                                if (\$stretched) \$figureClass .= ' editorjs-image--stretched';
                                                if (\$withBorder) \$figureClass .= ' editorjs-image--bordered';
                                                if (\$withBackground) \$figureClass .= ' editorjs-image--backgrounded';
                                                echo '<figure class=\"' . \$figureClass . '\">';
                                                echo '<img src=\"' . e(\$url) . '\" alt=\"' . e(\$caption) . '\">';
                                                if (\$caption) echo '<figcaption>' . e(\$caption) . '</figcaption>';
                                                echo '</figure>';
                                            }
                                            break;
                                        case 'quote':
                                            echo '<blockquote>' . e(\$block->data->text ?? '') . '</blockquote>';
                                            break;
                                        case 'delimiter':
                                            echo '<hr>';
                                            break;
                                        case 'table':
                                            if (isset(\$block->data->content) && is_array(\$block->data->content)) {
                                                echo '<table class=\"table table-bordered\">';
                                                \$isFirstRow = true;
                                                foreach (\$block->data->content as \$row) {
                                                    if (is_array(\$row)) {
                                                        echo '<tr>';
                                                        \$tag = (\$isFirstRow && (\$block->data->withHeadings ?? false)) ? 'th' : 'td';
                                                        foreach (\$row as \$cell) {
                                                            echo '<' . \$tag . '>' . e(\$cell ?? '') . '</' . \$tag . '>';
                                                        }
                                                        echo '</tr>';
                                                        \$isFirstRow = false;
                                                    }
                                                }
                                                echo '</table>';
                                            }
                                            break;
                                    }
                                }
                            }
                        }
                    } else {
                        // Обычный текст
                        echo nl2br(e(\$content));
                    }
                } catch (\Exception \$e) {
                    // Полный fallback
                    echo nl2br(e(\$content));
                }
            ?>";
        });
    }
}
