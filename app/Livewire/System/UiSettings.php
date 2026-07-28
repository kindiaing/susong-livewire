<?php

namespace App\Livewire\System;

use Livewire\Component;
use App\Support\Setting;

class UiSettings extends Component
{
    public bool $closeOnOutside = true;
    public bool $showFooter = true;

    public function mount(): void
    {
        $this->closeOnOutside = (bool) Setting::get('ui_close_on_outside', true);
        $this->showFooter = (bool) Setting::get('ui_show_footer', true);
    }

    public function save(): void
    {
        Setting::set('ui_close_on_outside', $this->closeOnOutside ? '1' : '0');
        Setting::set('ui_show_footer', $this->showFooter ? '1' : '0');

        $closeOnOutsideJs = $this->closeOnOutside ? 'true' : 'false';
        $showFooterJs = $this->showFooter ? 'true' : 'false';

        $this->js("window.__UI_CLOSE_ON_OUTSIDE = {$closeOnOutsideJs};");
        $this->js("window.__UI_SHOW_FOOTER = {$showFooterJs};");
        $this->js(<<<JS
            if (Alpine.store('uiSettings')) {
                Alpine.store('uiSettings').closeOnOutside = {$closeOnOutsideJs};
                Alpine.store('uiSettings').showFooter = {$showFooterJs};
            }
        JS);
        $this->js("window.\$toast.success('界面设置已保存');");
    }

    public function render()
    {
        return view('livewire.system.ui-settings');
    }
}
