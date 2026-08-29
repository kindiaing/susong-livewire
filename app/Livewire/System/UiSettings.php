<?php

namespace App\Livewire\System;

use App\Models\SystemConfig;
use App\Models\UserPreference;
use App\Support\Setting;
use Livewire\Component;

class UiSettings extends Component
{
    public bool $closeOnOutside = true;
    public bool $categoryTreeExpanded = false;
    public string $contentWidth = 'default'; // default | wide | full

    public function mount(): void
    {
        $this->closeOnOutside = (bool) Setting::get('ui_close_on_outside', true);

        // 优先级：用户偏好 > 系统配置 > false
        $userId = auth()->id();
        $userPref = UserPreference::getPreference($userId, 'category_tree_expanded');
        if ($userPref !== null) {
            $this->categoryTreeExpanded = (bool) $userPref;
        } else {
            $this->categoryTreeExpanded = (bool) Setting::get('ui_category_tree_expanded', false);
        }

        // 屏幕宽度（用户级）
        $widthPref = UserPreference::getPreference($userId, 'content_width');
        $this->contentWidth = $widthPref ?? 'default';
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

    public function setContentWidth(string $width): void
    {
        $this->contentWidth = $width;

        UserPreference::setPreference(auth()->id(), 'content_width', $width);

        // 同步到前端 Alpine store
        $this->js(<<<JS
            if (Alpine.store('uiSettings')) {
                Alpine.store('uiSettings').contentWidth = '{$width}';
                Alpine.store('uiSettings').applyContentWidth();
            }
        JS);

        $this->js("window.\$toast.success('宽度已切换');");
    }

    public function toggleCategoryTreeExpanded(): void
    {
        $this->categoryTreeExpanded = !$this->categoryTreeExpanded;

        UserPreference::setPreference(auth()->id(), 'category_tree_expanded', $this->categoryTreeExpanded);

        $this->js("window.\$toast.success('设置已保存');");
    }

    public function render()
    {
        return view('livewire.system.ui-settings');
    }
}
