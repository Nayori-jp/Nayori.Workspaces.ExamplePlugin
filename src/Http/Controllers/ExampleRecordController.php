<?php

namespace Plugins\ExamplePlugin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Crud\ListFilters;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Plugins\ExamplePlugin\Models\ExampleRecord;

class ExampleRecordController extends Controller
{
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
            'crud' => [
                'eyebrow' => 'Example Plugin',
                'heading' => 'Example records',
                'description' => 'This plugin demonstrates how future add-ons can declare one CRUD index definition instead of hand-writing list UI.',
                'searchPlaceholder' => 'Search example records',
                'emptyMessage' => 'No example records found.',
                'columns' => [
                    ['key' => 'name', 'label' => 'Name'],
                    ['key' => 'slug', 'label' => 'Slug'],
                    ['key' => 'summary', 'label' => 'Summary'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
                'statusOptions' => [
                    ['value' => 'active', 'label' => 'Active'],
                    ['value' => 'inactive', 'label' => 'Inactive'],
                    ['value' => 'archived', 'label' => 'Archived'],
                ],
            ],
            'rows' => $records->map(fn (ExampleRecord $record) => [
                'id' => $record->id,
                'cells' => [
                    'name' => [
                        'type' => 'link',
                        'label' => $record->name,
                        'href' => route('example-plugin.examples.show', $record),
                        'workspace' => [
                            'kind' => 'detail',
                            'title' => $record->name,
                        ],
                    ],
                    'slug' => [
                        'type' => 'text',
                        'value' => $record->slug,
                    ],
                    'summary' => [
                        'type' => 'text',
                        'value' => $record->summary,
                        'empty' => 'No summary',
                    ],
                    'status' => [
                        'type' => 'status',
                        'value' => $record->status,
                    ],
                ],
                'actions' => [
                    [
                        'label' => 'View',
                        'href' => route('example-plugin.examples.show', $record),
                        'method' => 'GET',
                        'variant' => 'secondary',
                        'workspace' => [
                            'kind' => 'detail',
                            'title' => $record->name,
                        ],
                    ],
                ],
            ])->values()->all(),
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

    public function show(ExampleRecord $record): Response
    {
        return Inertia::render('ExamplePlugin/Records/Show', [
            'header' => [
                'eyebrow' => 'Example Plugin',
                'heading' => $record->name,
                'description' => $record->summary,
                'actions' => [
                    [
                        'href' => route('example-plugin.examples.index'),
                        'label' => 'Back to examples',
                    ],
                ],
            ],
            'stats' => [
                ['label' => 'Slug', 'value' => $record->slug],
                ['label' => 'Status', 'type' => 'status', 'value' => $record->status],
                ['label' => 'Record ID', 'value' => '#'.$record->id],
            ],
            'sidebarPanels' => [
                [
                    'tone' => 'light',
                    'title' => 'Why this exists',
                    'description' => 'This detail screen shows how a plugin can reuse the same shared header, stat card, and badge patterns as the core app without creating its own design system.',
                ],
            ],
        ]);
    }
}
