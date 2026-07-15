<?php

namespace Plugins\ExamplePlugin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Support\Crud\CrudResourceRegistry;
use App\Support\Crud\ListFilters;
use App\Support\Crud\MutationPayload;
use App\Support\Crud\WorkspaceVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
        $business = $this->currentBusinessOrFail();
        Gate::authorize('view-example-plugin', $business);
        $filters = ListFilters::fromRequest($request);

        $records = ExampleRecord::query()
            ->whereBelongsTo($business)
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
        $business = $this->currentBusinessOrFail();
        Gate::authorize('manage-example-plugin', $business);

        return Inertia::render('ExamplePlugin/Records/Create', $this->resource()->createForm()->toArray());
    }

    public function store(Request $request): RedirectResponse
    {
        $business = $this->currentBusinessOrFail();
        Gate::authorize('manage-example-plugin', $business);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('example_plugin_records', 'slug')->where(fn ($query) => $query->where('business_id', $business->id))],
            'status' => ['required', 'in:active,inactive,archived'],
            'summary' => ['nullable', 'string', 'max:2000'],
        ]);

        $record = ExampleRecord::query()->create([
            ...$data,
            'business_id' => $business->id,
        ]);
        MutationPayload::flash(
            $request,
            'example-plugin.records',
            'created',
            $record->id,
            ['example-plugin.records'],
            ['version' => WorkspaceVersion::compose([$record->updated_at])],
        );

        return redirect()
            ->route('example-plugin.examples.show', $record)
            ->with('status', 'Example record created.');
    }

    public function edit(ExampleRecord $record): Response
    {
        $business = $this->currentBusinessOrFail();
        Gate::authorize('manage-example-plugin', $business);
        $this->assertBusinessRecord($business, $record);

        return Inertia::render('ExamplePlugin/Records/Edit', $this->resource()->editForm($record)->toArray());
    }

    public function update(Request $request, ExampleRecord $record): RedirectResponse
    {
        $business = $this->currentBusinessOrFail();
        Gate::authorize('manage-example-plugin', $business);
        $this->assertBusinessRecord($business, $record);
        WorkspaceVersion::assertCurrent($request, WorkspaceVersion::compose([$record->updated_at]));

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('example_plugin_records', 'slug')->where(fn ($query) => $query->where('business_id', $business->id))->ignore($record->id)],
            'status' => ['required', 'in:active,inactive,archived'],
            'summary' => ['nullable', 'string', 'max:2000'],
        ]);

        $record->update($data);
        $record->refresh();
        MutationPayload::flash(
            $request,
            'example-plugin.records',
            'updated',
            $record->id,
            ['example-plugin.records'],
            ['version' => WorkspaceVersion::compose([$record->updated_at])],
        );

        return redirect()
            ->route('example-plugin.examples.show', $record)
            ->with('status', 'Example record updated.');
    }

    public function show(ExampleRecord $record): Response
    {
        $business = $this->currentBusinessOrFail();
        Gate::authorize('view-example-plugin', $business);
        $this->assertBusinessRecord($business, $record);

        return Inertia::render('ExamplePlugin/Records/Show', $this->resource()->showPayload($record));
    }

    public function destroy(Request $request, ExampleRecord $record): RedirectResponse
    {
        $business = $this->currentBusinessOrFail();
        Gate::authorize('manage-example-plugin', $business);
        $this->assertBusinessRecord($business, $record);

        $record->update(['status' => 'archived']);
        $record->refresh();
        MutationPayload::flash(
            $request,
            'example-plugin.records',
            'archived',
            $record->id,
            ['example-plugin.records'],
            ['version' => WorkspaceVersion::compose([$record->updated_at])],
        );

        return redirect()->route('example-plugin.examples.index')->with('status', 'Example record archived.');
    }

    public function restore(Request $request, ExampleRecord $record): RedirectResponse
    {
        $business = $this->currentBusinessOrFail();
        Gate::authorize('manage-example-plugin', $business);
        $this->assertBusinessRecord($business, $record);

        $record->update(['status' => 'active']);
        $record->refresh();
        MutationPayload::flash(
            $request,
            'example-plugin.records',
            'restored',
            $record->id,
            ['example-plugin.records'],
            ['version' => WorkspaceVersion::compose([$record->updated_at])],
        );

        return redirect()->route('example-plugin.examples.index')->with('status', 'Example record restored.');
    }

    protected function currentBusinessOrFail(): Business
    {
        $business = current_business();

        abort_if(! $business instanceof Business, 422, 'Select a business first.');

        return $business;
    }

    protected function assertBusinessRecord(Business $business, ExampleRecord $record): void
    {
        abort_unless($record->business_id === $business->id, 404);
    }

    protected function resource()
    {
        return $this->crudResources->for('example-plugin.records');
    }
}
