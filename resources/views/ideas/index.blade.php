@extends('layouts.app')

@section('title', 'Идеи - Business database')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Идеи пользователей</h2>
    <a href="{{ route('ideas.create') }}" class="btn btn-primary">Добавить идею</a>
</div>

@if($ideas->isNotEmpty())
    @foreach($ideas as $idea)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h5 class="card-title">{{ $idea->title }}</h5>
                        <p class="card-text">{{ $idea->description }}</p>
                        <div class="d-flex justify-content-between">
                            <span class="badge bg-success">
                                {{ $idea->status }}
                            </span>
                            <span class="text-muted small">{{ $idea->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                    </div>
                    @auth
                        @if(auth()->user()->isAdmin())
                        <div class="ms-3">
                            <form method="POST" action="{{ route('admin.ideas.delete', $idea) }}" onsubmit="return confirm('Вы уверены, что хотите удалить эту идею?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                            </form>
                        </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="alert alert-info">Идей пока нет</div>
@endif
@endsection
