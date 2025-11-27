{{-- Global Confirm Modal Component --}}
{{-- Usage: $dispatch('confirm', { title, message, type, confirmText, form }) --}}

<div x-data="confirmModal()"
     x-on:confirm.window="open($event.detail)"
     x-show="show"
     x-cloak
     style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 1060;">

    {{-- Backdrop --}}
    <div x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="close()"
         style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5);"></div>

    {{-- Modal --}}
    <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center; padding: 1rem;">
        <div x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             @click.stop
             @keydown.escape.window="close()"
             class="bg-white rounded shadow"
             style="max-width: 400px; width: 100%; overflow: hidden;">

            {{-- Icon --}}
            <div class="pt-4 pb-2">
                <div class="text-center">
                    <template x-if="type === 'danger'">
                        <div class="mx-auto d-flex align-items-center justify-content-center rounded-circle"
                             style="width: 64px; height: 64px; background-color: #f8d7da;">
                            <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 2rem;"></i>
                        </div>
                    </template>
                    <template x-if="type === 'warning'">
                        <div class="mx-auto d-flex align-items-center justify-content-center rounded-circle"
                             style="width: 64px; height: 64px; background-color: #fff3cd;">
                            <i class="bi bi-question-circle-fill text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </template>
                    <template x-if="type === 'success'">
                        <div class="mx-auto d-flex align-items-center justify-content-center rounded-circle"
                             style="width: 64px; height: 64px; background-color: #d1e7dd;">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                        </div>
                    </template>
                    <template x-if="type === 'info'">
                        <div class="mx-auto d-flex align-items-center justify-content-center rounded-circle"
                             style="width: 64px; height: 64px; background-color: #cfe2ff;">
                            <i class="bi bi-info-circle-fill text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Content --}}
            <div class="px-4 pb-3">
                <h5 x-text="title" class="text-center mb-2"></h5>
                <p x-text="message" class="text-muted text-center small mb-0"></p>
            </div>

            {{-- Buttons --}}
            <div class="px-4 pb-4 d-flex gap-2 justify-content-center">
                <button @click="close()"
                        type="button"
                        class="btn btn-secondary">
                    Отмена
                </button>
                <button @click="doConfirm()"
                        type="button"
                        :class="buttonClass"
                        x-text="confirmText">
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmModal() {
    return {
        show: false,
        title: '',
        message: '',
        type: 'warning',
        confirmText: 'OK',
        formId: null,
        redirectUrl: null,

        open(detail) {
            this.title = detail.title || 'Подтверждение';
            this.message = detail.message || '';
            this.type = detail.type || 'warning';
            this.confirmText = detail.confirmText || 'OK';
            this.formId = detail.form || null;
            this.redirectUrl = detail.redirect || null;
            this.show = true;
            document.body.style.overflow = 'hidden';
        },

        close() {
            this.show = false;
            document.body.style.overflow = '';
        },

        doConfirm() {
            if (this.formId) {
                const form = document.getElementById(this.formId);
                if (form) {
                    form.submit();
                }
            } else if (this.redirectUrl) {
                window.location.href = this.redirectUrl;
            }
            this.close();
        },

        get buttonClass() {
            const classes = {
                'danger': 'btn btn-danger',
                'warning': 'btn btn-warning',
                'success': 'btn btn-success',
                'info': 'btn btn-primary'
            };
            return classes[this.type] || 'btn btn-primary';
        }
    }
}
</script>

{{-- Auto-open modal from session --}}
@if(session('modal'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    window.dispatchEvent(new CustomEvent('confirm', {
        detail: @json(session('modal'))
    }));
});
</script>
@endif
