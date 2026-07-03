<x-layouts.app :title="$record->name">
    <div class="xl:col-span-2 space-y-4">
        <x-crud.panel>
            <x-crud.detail-header eyebrow="Example Plugin" :heading="$record->name" :description="$record->summary">
                <x-slot:actions>
                    <a href="{{ route('example-plugin.examples.index') }}" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-medium text-slate-700 shadow-sm">Back to examples</a>
                </x-slot:actions>
            </x-crud.detail-header>

            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <x-crud.stat-card label="Slug">
                    <p class="text-lg font-semibold text-slate-950">{{ $record->slug }}</p>
                </x-crud.stat-card>
                <x-crud.stat-card label="Status">
                    <x-crud.status-badge :value="$record->status" />
                </x-crud.stat-card>
                <x-crud.stat-card label="Record ID">
                    <p class="text-lg font-semibold text-slate-950">#{{ $record->id }}</p>
                </x-crud.stat-card>
            </div>
        </x-crud.panel>
    </div>

    <div class="space-y-4 xl:col-span-1">
        <div class="rounded-[24px] border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-950">Why this exists</h3>
            <p class="mt-3 text-sm leading-6 text-slate-600">
                This detail screen shows how a plugin can reuse the same shared header, stat card, and badge patterns as the core app without creating its own design system.
            </p>
        </div>
    </div>
</x-layouts.app>
