<x-layouts.app title="Example records">
    <div class="xl:col-span-2">
        <x-crud.index-page :definition="$definition" :records="$records" :filters="$filters" />
    </div>

    <div class="space-y-4 xl:col-span-1">
        <div class="rounded-[24px] border border-slate-200 bg-slate-950 p-6 text-white shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-300">Plugin example</p>
            <p class="mt-3 text-lg font-semibold">Metadata-driven list page</p>
            <p class="mt-2 text-sm leading-6 text-slate-300">
                The controller only defines filters, columns, and row actions. The shared CRUD renderer handles the rest.
            </p>
        </div>
    </div>
</x-layouts.app>
