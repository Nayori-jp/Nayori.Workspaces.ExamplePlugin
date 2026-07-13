<?php

namespace Plugins\ExamplePlugin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Crud\CrudResourceRegistry;
use App\Support\Crud\ListFilters;
use App\Support\Crud\WorkspaceVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Plugins\ExamplePlugin\Models\ExampleRecord;

class ExampleRecordController extends Controller
{
    public function __construct(protected CrudResourceRegistry $crudResources)
    {
    }

    public function index(Request $request): Response
    {
        $filters = ListFilters::fromRequest($request);

        $records = ExampleRecord::query()
            ->when($filters['search'] !== '', fn ($query) => ListFilters::applySearch($query, $filters['search'], [
                'name',
                'slug',
                'summary',
            ]))
            ->when($filters['status'] !== null, fn ($query) => ListFilters::applyStatus($query, $filters['status']))
            ->orderBy('name')
            ->get();

        return Inertia::render('ExamplePlugin/Records/Index', [
            'filters' => $filters,
            'crud' => $this->resource()->indexDefinition()->toArray(),
            'rows' => $this->resource()->indexRows($records),
            'sidebarPanels' => [
                [
                    'tone' => 'dark',
                    'eyebrow' => 'Plugin example',
                    'title' => 'Metadata-driven list page',
                    'description' => 'The controller only defines filters, columns, and row actions. The shared CRUD renderer handles the rest.',
                ],
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('ExamplePlugin/Records/Create', $this->resource()->createForm()->toArray());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('example_plugin_records', 'slug')],
            'status' => ['required', 'in:active,inactive,archived'],
            'summary' => ['nullable', 'string', 'max:2000'],
        ]);

        $record = ExampleRecord::query()->create($data);

        return redirect()
            ->route('example-plugin.examples.show', $record)
            ->with('status', 'Example record created.');
    }

    public function edit(ExampleRecord $record): Response
    {
        return Inertia::render('ExamplePlugin/Records/Edit', $this->resource()->editForm($record)->toArray());
    }

    public function update(Request $request, ExampleRecord $record): RedirectResponse
    {
        WorkspaceVersion::assertCurrent($request, WorkspaceVersion::compose([$record->updated_at]));

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('example_plugin_records', 'slug')->ignore($record->id)],
            'status' => ['required', 'in:active,inactive,archived'],
            'summary' => ['nullable', 'string', 'max:2000'],
        ]);

        $record->update($data);

        return redirect()
            ->route('example-plugin.examples.show', $record)
            ->with('status', 'Example record updated.');
    }

    public function show(ExampleRecord $record): Response
    {
        return Inertia::render('ExamplePlugin/Records/Show', $this->resource()->showPayload($record));
    }

    protected function resource()
    {
        return $this->crudResources->for('example-plugin.records');
    }
}
