<x-app-layout>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Создать обращение в поддержку</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Тема обращения -->
                            <div class="mb-3">
                                <label for="subject" class="form-label">Тема обращения <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                       id="subject" name="subject" value="{{ old('subject') }}" required>
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Телефон -->
                            <div class="mb-3">
                                <label class="form-label">Номер телефона <span class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-md-3">
                                        <select class="form-select @error('country_code') is-invalid @enderror"
                                                name="country_code" id="country_code">
                                            <option value="+7" {{ old('country_code', '+7') == '+7' ? 'selected' : '' }}>+7 (РФ)</option>
                                            <option value="+375" {{ old('country_code') == '+375' ? 'selected' : '' }}>+375 (BY)</option>
                                            <option value="+380" {{ old('country_code') == '+380' ? 'selected' : '' }}>+380 (UA)</option>
                                            <option value="+998" {{ old('country_code') == '+998' ? 'selected' : '' }}>+998 (UZ)</option>
                                            <option value="+996" {{ old('country_code') == '+996' ? 'selected' : '' }}>+996 (KG)</option>
                                            <option value="+992" {{ old('country_code') == '+992' ? 'selected' : '' }}>+992 (TJ)</option>
                                            <option value="+7" {{ old('country_code') == '+7' ? 'selected' : '' }}>+7 (KZ)</option>
                                        </select>
                                        @error('country_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-9">
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                               name="phone" id="phone" placeholder="9001234567"
                                               value="{{ old('phone') }}" required
                                               pattern="[0-9]{10,15}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Введите номер без пробелов и дефисов</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Сообщение -->
                            <div class="mb-3">
                                <label for="message" class="form-label">Описание проблемы <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('message') is-invalid @enderror"
                                          id="message" name="message" rows="6" required>{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Файлы -->
                            <div class="mb-3">
                                <label for="attachments" class="form-label">Прикрепить файлы (скриншоты, документы)</label>
                                <input type="file" class="form-control @error('attachments.*') is-invalid @enderror"
                                       id="attachments" name="attachments[]" multiple
                                       accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt">
                                @error('attachments.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Максимальный размер файла: 10 МБ. Допустимые форматы: JPG, PNG, PDF, DOC, DOCX, TXT</small>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Назад к списку
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send"></i> Создать обращение
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
