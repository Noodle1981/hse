<x-layouts.app title="Redactar Informe Maestro">
    <div class="py-4 sm:py-8">
        <div class="max-w-screen-2xl mx-auto px-1 sm:px-6 lg:px-8">
            @livewire('master-reports.master-report-wizard', ['mode' => 'edit', 'reportId' => $id])
        </div>
    </div>
</x-layouts.app>
