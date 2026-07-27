<?php

use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test('demo')
        ->assertStatus(200);
});
