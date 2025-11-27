<x-app-layout>
<div class="row mb-3">
    <div class="col-md-12">
        <h2>Управление пользователями</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Телефон</th>
                        <th>Баланс</th>
                        <th>Роль</th>
                        <th>Дата регистрации</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.users.update-balance', $user) }}" class="d-inline">
                                @csrf
                                <div class="input-group input-group-sm" style="width: 150px;">
                                    <input type="number"
                                           name="balance"
                                           class="form-control form-control-sm"
                                           value="{{ $user->balance }}"
                                           step="0.01"
                                           min="0">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        Сохранить
                                    </button>
                                </div>
                            </form>
                        </td>
                        <td>
                            @if($user->isAdmin())
                                <span class="badge bg-danger">Admin</span>
                            @else
                                <span class="badge bg-secondary">User</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $user->isAdmin() ? 'btn-warning' : 'btn-success' }}">
                                    {{ $user->isAdmin() ? 'Снять admin' : 'Назначить admin' }}
                                </button>
                            </form>

                            @if(!$user->isAdmin())
                            <form id="delete-user-{{ $user->id }}" method="POST" action="{{ route('admin.users.delete', $user) }}" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                            <button type="button" class="btn btn-sm btn-danger"
                                    title="Удалить пользователя"
                                    x-data
                                    @click="$dispatch('confirm', {
                                        title: 'Удалить пользователя?',
                                        message: '{{ $user->name }} ({{ $user->email }}). Внимание: это действие необратимо!',
                                        type: 'danger',
                                        confirmText: 'Удалить',
                                        form: 'delete-user-{{ $user->id }}'
                                    })">
                                <i class="bi bi-trash"></i> Удалить
                            </button>
                            @endif
                            @else
                                <span class="text-muted">Вы</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">Пользователей нет</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>
</div>
</x-app-layout>
