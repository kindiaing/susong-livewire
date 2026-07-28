<?php

namespace App\Livewire\System;

use Livewire\Component;
use App\Support\Setting;

class UiSettings extends Component
{
    public bool $closeOnOutside = true;

    public function mount(): void
    {
        $this->closeOnOutside = (bool) Setting::get('ui_close_on_outside', true);
    }

    public function save(): void
    {
        Setting::set('ui_close_on_outside', $this->closeOnOutside ? '1' : '0');

        $closeOnOutsideJs = $this->closeOnOutside ? 'true' : 'false';

        $this->js("window.__UI_CLOSE_ON_OUTSIDE = {$closeOnOutsideJs};");
        $this->js(<<<JS
            if (Alpine.store('uiSettings')) {
                Alpine.store('uiSettings').closeOnOutside = {$closeOnOutsideJs};
            }
        JS);
        $this->js("window.\$toast.success('界面设置已保存');");
    }

    public function render()
    {
        return view('livewire.system.ui-settings');
    }
}
