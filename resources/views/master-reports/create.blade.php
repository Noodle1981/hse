<x-layouts.app title="Solicitar Nuevo Informe Maestro">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @livewire('master-reports.master-report-wizard', ['mode' => 'create'])
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
