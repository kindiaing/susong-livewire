<?php

use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test('auth..login')
        ->assertStatus(200);
});
