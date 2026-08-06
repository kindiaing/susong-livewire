<?php

namespace App\Livewire\Demo;

use Livewire\Component;

class DemoPage extends Component
{
    public function render()
    {
        return view('livewire.demo.demo-page')
            ->layout('components.app-layout')
            ->title('Demo');
    }
}
