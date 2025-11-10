<x-app-layout>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <!-- Заголовок тикета -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-ticket-perforated"></i> Обращение #{{ $ticket->ticket_number }}
                            </h5>
                            <span class="badge {{ $ticket->getStatusBadgeClass() }}">
                                {{ $ticket->getStatusName() }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-2"><strong>Тема:</strong> {{ $ticket->subject }}</h6>
                        <p class="text-muted mb-2">
                            <i class="bi bi-person"></i> Пользователь: {{ $ticket->user->name }} ({{ $ticket->user->email }})
                        </p>
                        <p class="text-muted mb-2">
                            <i class="bi bi-telephone"></i> Телефон: {{ $ticket->country_code }} {{ $ticket->phone }}
                        </p>
                        <p class="text-muted mb-0">
                            <i class="bi bi-calendar"></i> Создано: {{ $ticket->created_at->format('d.m.Y H:i') }}
                        </p>
                    </div>
                </div>

                <!-- Переписка -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Переписка</h6>
                    </div>
                    <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                        @forelse($ticket->messages as $message)
                            <div class="mb-3 {{ $message->is_admin ? 'text-end' : 'text-start' }}">
                                <div class="d-inline-block text-start" style="max-width: 70%;">
                                    <div class="card {{ $message->is_admin ? 'bg-success text-white' : 'bg-light' }}">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="fw-bold">
                                                    @if($message->is_admin)
                                                        <i class="bi bi-person-badge"></i> {{ $message->user->name }} (Администратор)
                                                    @else
                                                        <i class="bi bi-person"></i> {{ $message->user->name }}
                                                    @endif
                                                </small>
                                                <small class="{{ $message->is_admin ? 'text-white-50' : 'text-muted' }}">
                                                    {{ $message->created_at->format('d.m.Y H:i') }}
                                                </small>
                                            </div>
                                            <p class="mb-0" style="white-space: pre-wrap;">{{ $message->message }}</p>

                                            @if($message->attachments && count($message->attachments) > 0)
                                                <div class="mt-2">
                                                    <small class="{{ $message->is_admin ? 'text-white-50' : '' }}">
                                                        <i class="bi bi-paperclip"></i> Прикрепленные файлы:
                                                    </small>
                                                    <ul class="list-unstyled mb-0 mt-1">
                                                        @foreach($message->attachments as $attachment)
                                                            <li>
                                                                <a href="{{ asset('storage/' . $attachment) }}"
                                                                   target="_blank"
                                                                   class="{{ $message->is_admin ? 'text-white' : 'text-primary' }}">
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
                        @empty
                            <p class="text-muted text-center">Сообщений пока нет</p>
                        @endforelse
                    </div>
                </div>

                <!-- Форма ответа администратора -->
                <div class="card mt-3">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-reply"></i> Ответить пользователю
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.tickets.addMessage', $ticket) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <textarea class="form-control @error('message') is-invalid @enderror"
                                          name="message"
                                          rows="5"
                                          placeholder="Введите ваш ответ..."
                                          required></textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Прикрепить файлы (необязательно)</label>
                                <input type="file"
                                       class="form-control @error('attachments.*') is-invalid @enderror"
                                       name="attachments[]"
                                       multiple
                                       accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt">
                                <small class="text-muted">
                                    Максимальный размер файла: 10 МБ. Форматы: JPG, PNG, PDF, DOC, DOCX, TXT
                                </small>
                                @error('attachments.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> К списку тикетов
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-send"></i> Отправить ответ
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Боковая панель с управлением -->
            <div class="col-md-4">
                <!-- Информация о тикете -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-info-circle"></i> Информация
                        </h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Номер:</strong><br>#{{ $ticket->ticket_number }}</p>
                        <p><strong>Пользователь:</strong><br>
                            <a href="{{ route('admin.users') }}?search={{ $ticket->user->email }}">
                                {{ $ticket->user->name }}
                            </a>
                        </p>
                        <p><strong>Email:</strong><br>{{ $ticket->user->email }}</p>
                        <p><strong>Телефон:</strong><br>{{ $ticket->country_code }} {{ $ticket->phone }}</p>
                        <p><strong>Создано:</strong><br>{{ $ticket->created_at->format('d.m.Y H:i') }}</p>
                        <p><strong>Обновлено:</strong><br>{{ $ticket->updated_at->format('d.m.Y H:i') }}</p>
                        @if($ticket->closed_at)
                            <p><strong>Закрыто:</strong><br>{{ $ticket->closed_at->format('d.m.Y H:i') }}</p>
                        @endif
                        <p class="mb-0"><strong>Сообщений:</strong><br>{{ $ticket->messages->count() }}</p>
                    </div>
                </div>

                <!-- Управление статусом -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-gear"></i> Управление статусом
                        </h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Текущий статус:</strong></p>
                        <p>
                            <span class="badge {{ $ticket->getStatusBadgeClass() }} fs-6">
                                {{ $ticket->getStatusName() }}
                            </span>
                        </p>

                        <form action="{{ route('admin.tickets.updateStatus', $ticket) }}" method="POST" class="mt-3">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label class="form-label">Изменить статус:</label>
                                <select name="status" class="form-select" required>
                                    <option value="new" {{ $ticket->isNew() ? 'selected' : '' }}>Новое</option>
                                    <option value="in_progress" {{ $ticket->isInProgress() ? 'selected' : '' }}>В работе</option>
                                    <option value="closed" {{ $ticket->isClosed() ? 'selected' : '' }}>Закрыто</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle"></i> Обновить статус
                            </button>
                        </form>

                        @if(!$ticket->isClosed())
                            <form action="{{ route('admin.tickets.updateStatus', $ticket) }}" method="POST" class="mt-2">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="closed">
                                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Вы уверены, что хотите закрыть это обращение?')">
                                    <i class="bi bi-x-circle"></i> Закрыть обращение
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
