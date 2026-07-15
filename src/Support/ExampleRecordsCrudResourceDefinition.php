<?php

namespace Plugins\ExamplePlugin\Support;

use App\Support\Crud\CrudResourceDefinition;
use App\Support\Crud\FormDefinition;
use App\Support\Crud\FormField;
use App\Support\Crud\IndexAction;
use App\Support\Crud\IndexColumn;
use App\Support\Crud\IndexDefinition;
use App\Support\Crud\WorkspaceVersion;
use Plugins\ExamplePlugin\Models\ExampleRecord;

class ExampleRecordsCrudResourceDefinition extends CrudResourceDefinition
{
    public function key(): string
    {
        return 'example-plugin.records';
    }

    public function indexDefinition(array $context = []): IndexDefinition
    {
        return IndexDefinition::make('Example records', route('example-plugin.examples.index'))
            ->eyebrow('Example Plugin')
            ->description('This plugin demonstrates how future add-ons can declare one CRUD index definition instead of hand-writing list UI.')
            ->searchPlaceholder('Search example records')
            ->emptyMessage('No example records found.')
            ->statusOptions([
                'active' => 'Active',
                'inactive' => 'Inactive',
                'archived' => 'Archived',
            ])
            ->create(route('example-plugin.examples.create'), 'New example record')
            ->createWorkspace('create', 'New example record')
            ->columns([
                IndexColumn::make('name', 'Name')
                    ->asLink(
                        fn (ExampleRecord $record) => route('example-plugin.examples.show', $record),
                        fn (ExampleRecord $record) => $record->name,
                        'detail',
                        fn (ExampleRecord $record) => $record->name,
                    ),
                IndexColumn::make('slug', 'Slug'),
                IndexColumn::make('summary', 'Summary')->empty('No summary'),
                IndexColumn::make('status', 'Status')->asStatusBadge(),
            ])
            ->actions([
                IndexAction::make('View', fn (ExampleRecord $record) => route('example-plugin.examples.show', $record))
                    ->workspaceTab(fn (ExampleRecord $record) => $record->name, 'detail'),
                IndexAction::make('Edit', fn (ExampleRecord $record) => route('example-plugin.examples.edit', $record))
                    ->workspaceTab(fn (ExampleRecord $record) => 'Edit: '.$record->name, 'edit'),
            ]);
    }

    public function indexRows(iterable $records, array $context = []): array
    {
        return $this->rowsFromDefinition(
            $this->indexDefinition($context),
            $records,
            function (array $row, ExampleRecord $record): array {
                if ($record->status === 'archived') {
                    $row['actions'][] = IndexAction::make(
                        'Restore',
                        route('example-plugin.examples.restore', $record),
                    )->destructive('Restore this example record?', 'PUT')
                        ->staleScopes(['example-plugin.records'])
                        ->toArray($record);
                } else {
                    $row['actions'][] = IndexAction::make(
                        'Archive',
                        route('example-plugin.examples.destroy', $record),
                    )->destructive('Archive this example record?')
                        ->staleScopes(['example-plugin.records'])
                        ->toArray($record);
                }

                return $row;
            },
        );
    }

    public function createForm(array $context = []): FormDefinition
    {
        return FormDefinition::make('example-plugin.records', 'Create example record')
            ->eyebrow('Example Plugin')
            ->description('Create a plugin record using the same shared CRUD form contract as the core app.')
            ->backAction(route('example-plugin.examples.index'), 'Back to examples')
            ->form([
                'name' => '',
                'slug' => '',
                'status' => 'active',
                'summary' => '',
            ])
            ->fields($this->formFields())
            ->routes([
                'index' => route('example-plugin.examples.index'),
                'store' => route('example-plugin.examples.store'),
            ]);
    }

    public function editForm(mixed $record, array $context = []): FormDefinition
    {
        /** @var ExampleRecord $record */
        return FormDefinition::make('example-plugin.records', $record->name)
            ->eyebrow('Example Plugin')
            ->description('Edit a plugin record through the shared registry-driven form flow.')
            ->backAction(route('example-plugin.examples.index'), 'Back to examples')
            ->form([
                'name' => $record->name,
                'slug' => $record->slug,
                'status' => $record->status,
                'summary' => $record->summary ?? '',
            ])
            ->fields($this->formFields())
            ->routes([
                'index' => route('example-plugin.examples.index'),
                'update' => route('example-plugin.examples.update', $record),
            ])
            ->workspaceRecord(
                'example-plugin.records',
                $record->id,
                route('example-plugin.examples.edit', $record),
                WorkspaceVersion::compose([$record->updated_at])
            )
            ->sidebarPanels([
                [
                    'tone' => 'muted',
                    'title' => 'Plugin CRUD contract',
                    'description' => 'This edit form uses the same shared stale-write and workspace tab conventions as core resources.',
                ],
            ]);
    }

    public function showPayload(mixed $record, array $context = []): array
    {
        /** @var ExampleRecord $record */
        return [
            'header' => [
                'eyebrow' => 'Example Plugin',
                'heading' => $record->name,
                'description' => $record->summary,
                'actions' => [
                    [
                        'href' => route('example-plugin.examples.edit', $record),
                        'label' => 'Edit record',
                        'primary' => true,
                        'workspace' => [
                            'kind' => 'edit',
                            'title' => 'Edit: '.$record->name,
                        ],
                    ],
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
        ];
    }

    protected function formFields(): array
    {
        return [
            FormField::make('name', 'Name'),
            FormField::make('slug', 'Slug'),
            FormField::make('status', 'Status')->select($this->optionList([
                'active' => 'Active',
                'inactive' => 'Inactive',
                'archived' => 'Archived',
            ])),
            FormField::make('summary', 'Summary')->textarea()->columnSpan(2),
        ];
    }
}
