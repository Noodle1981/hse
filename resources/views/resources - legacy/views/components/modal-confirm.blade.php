<div x-data="{ 
         open: false, 
         id: null, 
         title: '', 
         message: '', 
         action: null,
         confirmLabel: 'Confirmar',
         cancelLabel: 'Cancelar',
         confirmColor: 'bg-ep-primary shadow-ep-primary/20',
         
         trigger(data) {
             this.id = data.id;
             this.title = data.title || '¿Estás seguro?';
             this.message = data.message || 'Esta acción no se puede deshacer.';
             this.action = data.action;
             this.confirmLabel = data.confirmLabel || 'Confirmar';
             this.cancelLabel = data.cancelLabel || 'Cancelar';
             this.confirmColor = data.confirmColor || 'bg-ep-primary shadow-ep-primary/20';
             this.open = true;
         },
         
         confirm() {
             if (this.id && this.action) {
                 this.$wire[this.action](this.id);
             } else if (this.action) {
                 this.$wire[this.action]();
             }
             this.open = false;
         }
     }"
     x-on:open-confirm-modal.window="trigger($event.detail)"
     class="relative z-[100]"
     x-show="open"
     x-cloak>
    
    {{-- Backdrop --}}
    <div x-show="open" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/60 backdrop-blur-md transition-opacity" 
         @click="open = false"></div>

    {{-- Content --}}
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-3xl bg-ep-dark text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-white/10">
                
                <div class="px-4 pb-4 pt-5 sm:p-8">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-white/5 border border-white/10 sm:mx-0 sm:h-12 sm:w-12 shadow-inner">
                            <svg class="h-7 w-7 text-ep-primary" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-4 text-center sm:ml-6 sm:mt-0 sm:text-left">
                            <h3 class="text-xl font-black text-white tracking-tight italic">
                                <span class="text-ep-primary" x-text="title.split(' ')[0]"></span>
                                <span x-text="title.split(' ').slice(1).join(' ')"></span>
                            </h3>
                            <div class="mt-3">
                                <p class="text-sm font-medium text-indigo-100/70 italic leading-relaxed" x-text="message"></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-black/20 px-6 py-4 sm:flex sm:flex-row-reverse gap-3 rounded-b-3xl border-t border-white/10">
                    <button type="button" 
                            class="inline-flex w-full justify-center rounded-xl px-6 py-2.5 text-xs font-black text-white shadow-xl hover:brightness-110 transition-all sm:w-auto uppercase tracking-widest"
                            :class="confirmColor"
                            @click="confirm()"
                            x-text="confirmLabel">
                    </button>
                    <button type="button" 
                            class="mt-3 inline-flex w-full justify-center rounded-xl bg-white/5 px-6 py-2.5 text-xs font-black text-indigo-100 shadow-sm border border-white/10 hover:bg-white/10 transition-all sm:mt-0 sm:w-auto uppercase tracking-widest"
                            @click="open = false"
                            x-text="cancelLabel">
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
