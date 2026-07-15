<?php

namespace Plugins\ExamplePlugin\Tests\Feature;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Plugins\ExamplePlugin\Models\ExampleRecord;
use Tests\TestCase;

class ExamplePluginWorkspaceIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_only_returns_records_from_the_current_business(): void
    {
        [$user, $currentBusiness] = $this->workspaceUser('owner');
        $otherBusiness = $this->business('other-business');

        ExampleRecord::query()->create($this->recordData($currentBusiness, 'visible'));
        ExampleRecord::query()->create($this->recordData($otherBusiness, 'hidden'));

        $response = $this->actingAs($user)
            ->withSession(['current_business_id' => $currentBusiness->id])
            ->get('/example-plugin/records');

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('ExamplePlugin/Records/Index', false)
                ->has('rows', 1)
                ->where('rows.0.cells.name.label', 'visible'));
    }

    public function test_record_from_another_business_cannot_be_viewed_or_updated(): void
    {
        [$user, $currentBusiness] = $this->workspaceUser('owner');
        $otherRecord = ExampleRecord::query()->create(
            $this->recordData($this->business('other-business'), 'hidden')
        );

        $this->actingAs($user)
            ->withSession(['current_business_id' => $currentBusiness->id])
            ->get("/example-plugin/records/{$otherRecord->id}")
            ->assertNotFound();

        $this->actingAs($user)
            ->withSession(['current_business_id' => $currentBusiness->id])
            ->put("/example-plugin/records/{$otherRecord->id}", [
                'name' => 'Changed',
                'slug' => 'changed',
                'status' => 'active',
                'summary' => null,
                'workspace_version' => 'irrelevant-before-scope-check',
            ])
            ->assertNotFound();
    }

    public function test_member_can_view_but_cannot_create_or_edit_records(): void
    {
        [$user, $business] = $this->workspaceUser('member');
        $record = ExampleRecord::query()->create($this->recordData($business, 'visible'));

        $this->actingAs($user)
            ->withSession(['current_business_id' => $business->id])
            ->get("/example-plugin/records/{$record->id}")
            ->assertOk();

        $this->actingAs($user)
            ->withSession(['current_business_id' => $business->id])
            ->get('/example-plugin/records/create')
            ->assertForbidden();

        $this->actingAs($user)
            ->withSession(['current_business_id' => $business->id])
            ->get("/example-plugin/records/{$record->id}/edit")
            ->assertForbidden();
    }

    public function test_owner_create_archive_and_restore_emit_workspace_mutations(): void
    {
        [$user, $business] = $this->workspaceUser('owner');

        $this->actingAs($user)
            ->withSession(['current_business_id' => $business->id])
            ->post('/example-plugin/records', [
                'name' => 'Lifecycle record',
                'slug' => 'lifecycle-record',
                'status' => 'active',
                'summary' => null,
            ])
            ->assertRedirect();

        $record = ExampleRecord::query()->whereBelongsTo($business)->sole();
        $this->assertDatabaseHas('workspace_mutations', [
            'business_id' => $business->id,
            'resource' => 'example-plugin.records',
            'action' => 'created',
            'record_id' => (string) $record->id,
        ]);

        $this->actingAs($user)
            ->withSession(['current_business_id' => $business->id])
            ->delete("/example-plugin/records/{$record->id}")
            ->assertRedirect('/example-plugin/records');

        $this->assertDatabaseHas('example_plugin_records', [
            'id' => $record->id,
            'status' => 'archived',
        ]);
        $this->assertDatabaseHas('workspace_mutations', [
            'resource' => 'example-plugin.records',
            'action' => 'archived',
            'record_id' => (string) $record->id,
        ]);

        $this->actingAs($user)
            ->withSession(['current_business_id' => $business->id])
            ->put("/example-plugin/records/{$record->id}/restore")
            ->assertRedirect('/example-plugin/records');

        $this->assertDatabaseHas('example_plugin_records', [
            'id' => $record->id,
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('workspace_mutations', [
            'resource' => 'example-plugin.records',
            'action' => 'restored',
            'record_id' => (string) $record->id,
        ]);
    }

    protected function workspaceUser(string $role): array
    {
        $user = User::factory()->create(['status' => 'active']);
        $business = $this->business('current-business');
        $business->users()->attach($user->id, ['role' => $role]);

        return [$user, $business];
    }

    protected function business(string $slug): Business
    {
        return Business::query()->create([
            'name' => str($slug)->headline()->toString(),
            'slug' => $slug,
            'status' => 'active',
        ]);
    }

    protected function recordData(Business $business, string $slug): array
    {
        return [
            'business_id' => $business->id,
            'name' => $slug,
            'slug' => $slug,
            'status' => 'active',
            'summary' => null,
        ];
    }
}
