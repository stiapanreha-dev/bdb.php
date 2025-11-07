<x-app-layout>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <!-- Заголовок тикета -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Обращение #{{ $ticket->ticket_number }}</h5>
                            <span class="badge {{ $ticket->getStatusBadgeClass() }}">
                                {{ $ticket->getStatusName() }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-2"><strong>Тема:</strong> {{ $ticket->subject }}</h6>
                        <p class="text-muted mb-2">
                            <i class="bi bi-calendar"></i> Создано: {{ $ticket->created_at->format('d.m.Y H:i') }}
                        </p>
                        <p class="text-muted mb-2">
                            <i class="bi bi-telephone"></i> Телефон: {{ $ticket->country_code }} {{ $ticket->phone }}
                        </p>
                    </div>
                </div>

                <!-- Переписка -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Переписка</h6>
                    </div>
                    <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                        @foreach($ticket->messages as $message)
                            <div class="mb-3 {{ $message->is_admin ? 'text-start' : 'text-end' }}">
                                <div class="d-inline-block text-start" style="max-width: 70%;">
                                    <div class="card {{ $message->is_admin ? 'bg-light' : 'bg-primary text-white' }}">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="fw-bold">
                                                    @if($message->is_admin)
                                                        <i class="bi bi-person-badge"></i> Поддержка
                                                    @else
                                                        <i class="bi bi-person"></i> {{ $message->user->name }}
                                                    @endif
                                                </small>
                                                <small class="{{ $message->is_admin ? 'text-muted' : 'text-white-50' }}">
                                                    {{ $message->created_at->format('d.m.Y H:i') }}
                                                </small>
                                            </div>
                                            <p class="mb-0" style="white-space: pre-wrap;">{{ $message->message }}</p>

                                            @if($message->attachments && count($message->attachments) > 0)
                                                <div class="mt-2">
                                                    <small class="{{ $message->is_admin ? '' : 'text-white-50' }}">
                                                        <i class="bi bi-paperclip"></i> Прикрепленные файлы:
                                                    </small>
                                                    <ul class="list-unstyled mb-0 mt-1">
                                                        @foreach($message->attachments as $attachment)
                                                            <li>
                                                                <a href="{{ asset('storage/' . $attachment) }}"
                                                                   target="_blank"
                                                                   class="{{ $message->is_admin ? 'text-primary' : 'text-white' }}">
                                                                    <i class="bi bi-file-earmark"></i> {{ basename($attachment) }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Форма ответа -->
                @if(!$ticket->isClosed())
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">Добавить сообщение</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('tickets.addMessage', $ticket) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <textarea class="form-control" name="message" rows="4" placeholder="Введите ваше сообщение..." required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Прикрепить файлы</label>
                                    <input type="file" class="form-control" name="attachments[]" multiple
                                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt">
                                </div>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> К списку
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send"></i> Отправить
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle"></i> Обращение закрыто. Для дальнейшей связи создайте новое обращение.
                    </div>
                    <a href="{{ route('tickets.index') }}" class="btn btn-secondary mt-2">
                        <i class="bi bi-arrow-left"></i> К списку
                    </a>
                @endif
            </div>

            <!-- Боковая панель с информацией -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Информация</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Номер обращения:</strong><br>#{{ $ticket->ticket_number }}</p>
                        <p><strong>Статус:</strong><br>
                            <span class="badge {{ $ticket->getStatusBadgeClass() }}">
                                {{ $ticket->getStatusName() }}
                            </span>
                        </p>
                        <p><strong>Создано:</strong><br>{{ $ticket->created_at->format('d.m.Y H:i') }}</p>
                        <p><strong>Обновлено:</strong><br>{{ $ticket->updated_at->format('d.m.Y H:i') }}</p>
                        @if($ticket->closed_at)
                            <p><strong>Закрыто:</strong><br>{{ $ticket->closed_at->format('d.m.Y H:i') }}</p>
                        @endif
                        <p><strong>Сообщений:</strong><br>{{ $ticket->messages->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
