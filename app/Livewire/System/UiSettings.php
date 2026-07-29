<?php

namespace App\Livewire\System;

use App\Models\SystemConfig;
use App\Support\Setting;
use Livewire\Component;

class UiSettings extends Component
{
    public bool $closeOnOutside = true;

    public function mount(): void
    {
        $this->closeOnOutside = (bool) Setting::get('ui_close_on_outside', true);
    }

    public function toggleCloseOnOutside(): void
    {
        $this->closeOnOutside = !$this->closeOnOutside;

        SystemConfig::setValue('ui_close_on_outside', $this->closeOnOutside);

        $jsBool = $this->closeOnOutside ? 'true' : 'false';
        $this->js(<<<JS
            if (Alpine.store('uiSettings')) {
                Alpine.store('uiSettings').closeOnOutside = {$jsBool};
            }
        JS);

        $this->js("window.\$toast.success('设置已保存');");
    }

    public function render()
    {
        return view('livewire.system.ui-settings');
    }
}
