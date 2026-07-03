<?php

namespace Plugins\ExamplePlugin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Crud\IndexAction;
use App\Support\Crud\IndexColumn;
use App\Support\Crud\IndexDefinition;
use App\Support\Crud\ListFilters;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Plugins\ExamplePlugin\Models\ExampleRecord;

class ExampleRecordController extends Controller
{
    public function index(Request $request): View
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

        $definition = IndexDefinition::make('Example records', route('example-plugin.examples.index'))
            ->eyebrow('Example Plugin')
            ->description('This plugin demonstrates how future add-ons can declare one CRUD index definition instead of hand-writing list UI.')
            ->searchPlaceholder('Search example records')
            ->statusOptions([
                'active' => 'Active',
                'inactive' => 'Inactive',
                'archived' => 'Archived',
            ])
            ->columns([
                IndexColumn::make('name', 'Name')->value(fn (ExampleRecord $record) => '<a href="'.e(route('example-plugin.examples.show', $record)).'" class="hover:underline font-medium text-slate-950">'.e($record->name).'</a>'),
                IndexColumn::make('slug', 'Slug'),
                IndexColumn::make('summary', 'Summary')->empty('No summary'),
                IndexColumn::make('status', 'Status')->asStatusBadge(),
            ])
            ->actions([
                IndexAction::make('View', fn (ExampleRecord $record) => route('example-plugin.examples.show', $record)),
            ])
            ->emptyMessage('No example records found.');

        return view('example-plugin::example-plugin.index', [
            'definition' => $definition,
            'filters' => $filters,
            'records' => $records,
        ]);
    }

    public function show(ExampleRecord $record): View
    {
        return view('example-plugin::example-plugin.show', [
            'record' => $record,
        ]);
    }
}
