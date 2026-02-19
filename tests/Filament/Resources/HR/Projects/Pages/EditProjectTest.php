<?php

use App\Filament\Resources\HR\Projects\Pages\EditProject;
use App\Models\HR\Project;
use Illuminate\Support\Str;
use Livewire\Livewire;

it('can render the edit page', function () {
    $record = Project::factory()->create();

    Livewire::test(EditProject::class, ['record' => $record->getRouteKey()])
        ->assertOk();
});

it('can update a record', function () {
    $record = Project::factory()->create();
    $newData = Project::factory()->make();

    Livewire::test(EditProject::class, ['record' => $record->getRouteKey()])
        ->fillForm([
            'name' => $newData->name,
        ])
        ->call('save')
        ->assertNotified();

    $this->assertDatabaseHas(Project::class, [
        'id' => $record->id,
        'name' => $newData->name,
    ]);
});

it('validates the form data', function (array $data, array $errors) {
    $record = Project::factory()->create();
    $newData = Project::factory()->make();

    Livewire::test(EditProject::class, ['record' => $record->getRouteKey()])
        ->fillForm([
            'name' => $newData->name,
            ...$data,
        ])
        ->call('save')
        ->assertHasFormErrors($errors)
        ->assertNotNotified();
})->with([
    '`name` is required' => [['name' => null], ['name' => 'required']],
    '`name` is max 255 characters' => [['name' => Str::random(256)], ['name' => 'max']],
    '`status` is required' => [['status' => null], ['status' => 'required']],
    '`priority` is required' => [['priority' => null], ['priority' => 'required']],
    '`start_date` is required' => [['start_date' => null], ['start_date' => 'required']],
]);
