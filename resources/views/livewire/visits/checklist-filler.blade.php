<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="close"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
            
            <!-- Header -->
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 border-b border-gray-100">
                <div class="sm:flex sm:items-start justify-between">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Completar: {{ $assignment->checklist->title }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $assignment->checklist->categoryRelation?->name }} / {{ $assignment->checklist->subcategoryRelation?->name }}
                        </p>
                    </div>
                    <button type="button" wire:click="close" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Cerrar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Scrollable Content -->
            <div class="bg-white px-4 py-5 sm:p-6 overflow-y-auto max-h-[60vh]">
                
                <!-- General Questions (No Section) -->
                @if($assignment->checklist->questions->whereNull('section_id')->count() > 0)
                    <div class="mb-6 space-y-4">
                        @foreach($assignment->checklist->questions->whereNull('section_id')->sortBy('order') as $question)
                            @include('livewire.visits.question-renderer', ['question' => $question])
                        @endforeach
                    </div>
                @endif

                <!-- Sections -->
                @foreach($sections as $section)
                    <div class="mb-8 border border-gray-100 rounded-lg p-4 bg-gray-50">
                        <h4 class="font-medium text-gray-800 mb-4 border-b border-gray-200 pb-2">{{ $section->title }}</h4>
                        @if($section->description)
                            <p class="text-sm text-gray-500 mb-4">{{ $section->description }}</p>
                        @endif

                        <div class="space-y-6">
                            @foreach($section->questions as $question)
                                @include('livewire.visits.question-renderer', ['question' => $question])
                            @endforeach
                        </div>
                    </div>
                @endforeach

            </div>

            <!-- Footer / Actions -->
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                <button 
                    type="button" 
                    wire:click="save"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-ep-primary text-base font-medium text-white hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ep-primary sm:ml-3 sm:w-auto sm:text-sm"
                >
                    Guardar y Completar
                </button>
                <button 
                    type="button" 
                    wire:click="close"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                >
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
