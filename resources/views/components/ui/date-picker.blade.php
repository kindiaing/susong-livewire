@props([
    'label' => null,
    'hint' => null,
    'error' => null,
    'disabled' => false,
    'placeholder' => '选择日期...',
    'format' => 'YYYY-MM-DD',
    'size' => 'default',
])

@php
$sizeClasses = match($size) {
    'sm' => 'h-8 px-3 text-xs',
    'default' => 'h-9 px-3 text-sm',
    'lg' => 'h-10 px-4 text-sm',
};

$errorClasses = $error ? 'border-red-600 focus-visible:ring-red-600' : 'border-input focus-visible:ring-ring';
$disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed bg-muted' : '';

// Extract wire:* and x-* attributes for the hidden input
$inputWireAttrs = $attributes->whereStartsWith('wire:');
$inputXAttrs = $attributes->whereStartsWith('x-');
$inputOtherAttrs = $attributes->whereDoesntStartWith('wire:')->whereDoesntStartWith('x-')->except('class');
@endphp

<div {{ $attributes->whereDoesntStartWith('wire:')->whereDoesntStartWith('x-')->merge(['class' => 'grid gap-1.5']) }}>
    @if($label)
        <label class="text-sm font-medium leading-none text-foreground peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
            {{ $label }}
        </label>
    @endif

    <div
        x-data="datePicker('{{ $format }}')"
        x-init="init()"
        class="relative"
        @click.away="open = false"
        @keydown.escape.window="if(open) open = false"
    >
        {{-- Input Trigger --}}
        <div
            @click="@if(!{{ $disabled ? 'true' : 'false' }}) open = !open @endif"
            class="flex items-center justify-between rounded-md border bg-background cursor-pointer transition-colors focus-within:ring-2 focus-within:ring-offset-2 {{ $sizeClasses }} {{ $errorClasses }} {{ $disabledClasses }}"
        >
            <span
                x-text="displayDate || '{{ $placeholder }}'"
                :class="displayDate ? 'text-foreground' : 'text-muted-foreground'"
                class="flex-1 truncate"
            ></span>
            <svg class="pointer-events-none mr-2 h-4 w-4 text-muted-foreground shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>

        {{-- Hidden input for Livewire binding --}}
        <input
            type="hidden"
            {{ $inputWireAttrs }}
            {{ $inputXAttrs }}
            {{ $inputOtherAttrs }}
            x-ref="hiddenInput"
        />

        {{-- Calendar Dropdown --}}
        <div
            x-show="open"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            x-cloak
            class="absolute left-0 top-full z-50 mt-1 rounded-md border border-border bg-popover p-3 shadow-md"
        >
            {{-- Header: Month/Year Navigation --}}
            <div class="flex items-center justify-between mb-2">
                <button type="button" @click="prevMonth" class="inline-flex items-center justify-center rounded-md p-1 hover:bg-accent hover:text-accent-foreground">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <span x-text="monthYearLabel" class="text-sm font-medium text-foreground"></span>
                <button type="button" @click="nextMonth" class="inline-flex items-center justify-center rounded-md p-1 hover:bg-accent hover:text-accent-foreground">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            {{-- Weekday Headers --}}
            <div class="grid grid-cols-7 mb-1">
                <template x-for="day in weekDays" :key="day">
                    <div class="text-center text-xs font-medium text-muted-foreground py-1" x-text="day"></div>
                </template>
            </div>

            {{-- Days Grid --}}
            <div class="grid grid-cols-7">
                <template x-for="day in calendarDays" :key="day.date">
                    <button
                        type="button"
                        @click="selectDay(day)"
                        :class="{
                            'bg-primary text-primary-foreground': day.isSelected,
                            'hover:bg-accent hover:text-accent-foreground': !day.isSelected && !day.isOutside,
                            'text-muted-foreground opacity-50': day.isOutside,
                            'bg-accent': day.isToday && !day.isSelected
                        }"
                        class="inline-flex items-center justify-center rounded-md text-sm h-8 w-8 transition-colors"
                        x-text="day.label"
                        :disabled="day.isOutside && !allowOutside"
                    ></button>
                </template>
            </div>

            {{-- Today shortcut --}}
            <div class="flex justify-end mt-2 pt-2 border-t border-border">
                <button type="button" @click="selectToday" class="text-xs text-muted-foreground hover:text-foreground transition-colors">今天</button>
            </div>
        </div>
    </div>

    @if($error)
        <p class="text-xs text-red-600">{{ $error }}</p>
    @elseif($hint)
        <p class="text-xs text-muted-foreground">{{ $hint }}</p>
    @endif
</div>

@script
<script>
document.addEventListener('alpine:init', () => {
    if (!Alpine.data('datePicker')) {
        Alpine.data('datePicker', (format) => ({
            open: false,
            format: format || 'YYYY-MM-DD',
            currentMonth: new Date().getMonth(),
            currentYear: new Date().getFullYear(),
            selectedDate: null,
            displayDate: '',
            allowOutside: false,

            weekDays: ['一', '二', '三', '四', '五', '六', '日'],

            get monthYearLabel() {
                return `${this.currentYear}年${this.currentMonth + 1}月`;
            },

            get calendarDays() {
                const days = [];
                const firstDay = new Date(this.currentYear, this.currentMonth, 1);
                const startWeekDay = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1; // Monday=0
                const daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                const prevMonthDays = new Date(this.currentYear, this.currentMonth, 0).getDate();
                const today = new Date();
                const todayStr = this.formatDate(today);

                // Previous month padding
                for (let i = startWeekDay - 1; i >= 0; i--) {
                    const day = prevMonthDays - i;
                    const d = new Date(this.currentYear, this.currentMonth - 1, day);
                    days.push({
                        date: this.formatDate(d),
                        label: day,
                        isOutside: true,
                        isToday: false,
                        isSelected: this.formatDate(this.selectedDate) === this.formatDate(d),
                    });
                }

                // Current month
                for (let i = 1; i <= daysInMonth; i++) {
                    const d = new Date(this.currentYear, this.currentMonth, i);
                    const dateStr = this.formatDate(d);
                    days.push({
                        date: dateStr,
                        label: i,
                        isOutside: false,
                        isToday: dateStr === todayStr,
                        isSelected: this.formatDate(this.selectedDate) === this.formatDate(d),
                    });
                }

                // Next month padding
                const remaining = 42 - days.length;
                for (let i = 1; i <= remaining; i++) {
                    const d = new Date(this.currentYear, this.currentMonth + 1, i);
                    days.push({
                        date: this.formatDate(d),
                        label: i,
                        isOutside: true,
                        isToday: false,
                        isSelected: this.formatDate(this.selectedDate) === this.formatDate(d),
                    });
                }

                return days;
            },

            init() {
                // Read initial value from hidden input
                this.$nextTick(() => {
                    const input = this.$refs.hiddenInput;
                    if (input && input.value) {
                        this.selectedDate = new Date(input.value);
                        this.displayDate = this.formatDate(this.selectedDate);
                        this.currentMonth = this.selectedDate.getMonth();
                        this.currentYear = this.selectedDate.getFullYear();
                    }
                });
            },

            prevMonth() {
                this.currentMonth--;
                if (this.currentMonth < 0) {
                    this.currentMonth = 11;
                    this.currentYear--;
                }
            },

            nextMonth() {
                this.currentMonth++;
                if (this.currentMonth > 11) {
                    this.currentMonth = 0;
                    this.currentYear++;
                }
            },

            selectDay(day) {
                if (day.isOutside && !this.allowOutside) return;
                this.selectedDate = new Date(day.date);
                this.displayDate = day.date;
                const input = this.$refs.hiddenInput;
                if (input) {
                    input.value = day.date;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
                this.open = false;
            },

            selectToday() {
                const today = new Date();
                this.currentMonth = today.getMonth();
                this.currentYear = today.getFullYear();
                const dateStr = this.formatDate(today);
                this.selectedDate = today;
                this.displayDate = dateStr;
                const input = this.$refs.hiddenInput;
                if (input) {
                    input.value = dateStr;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
                this.open = false;
            },

            formatDate(d) {
                if (!d || isNaN(d.getTime())) return '';
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            },
        }));
    }
});
</script>
@endscript
