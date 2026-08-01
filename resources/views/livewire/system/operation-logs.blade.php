<div class="p-6">
    {{-- 页面标题 --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-foreground">操作日志</h1>
        <p class="text-muted-foreground mt-1">管理员操作记录，按时间倒序排列</p>
    </div>

    {{-- 筛选栏 --}}
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <select wire:model="filterMethod" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="">全部方法</option>
            <option value="GET">GET</option>
            <option value="POST">POST</option>
            <option value="PUT">PUT</option>
            <option value="PATCH">PATCH</option>
            <option value="DELETE">DELETE</option>
        </select>

        <input
            type="text"
            wire:model="filterUsername"
            class="flex h-9 w-40 rounded-md border border-input bg-background px-3 text-sm"
            placeholder="用户名"
        />

        <input
            type="text"
            wire:model="filterPath"
            class="flex h-9 w-48 rounded-md border border-input bg-background px-3 text-sm"
            placeholder="请求路径"
        />

        <input
            type="date"
            wire:model="filterDateStart"
            class="flex h-9 rounded-md border border-input bg-background px-3 text-sm"
        />
        <span class="text-sm text-muted-foreground">至</span>
        <input
            type="date"
            wire:model="filterDateEnd"
            class="flex h-9 rounded-md border border-input bg-background px-3 text-sm"
        />

        <button type="button" wire:click="resetFilters" class="text-sm text-muted-foreground hover:text-foreground transition-colors">
            重置
        </button>

        <div class="flex-1"></div>

        @if($selectedCount > 0)
        <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
        <button type="button" wire:click="batchDelete" class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
        <button type="button" wire:click="clearSelection" class="text-xs text-muted-foreground hover:text-foreground">取消选择</button>
        @endif
        <button type="button" wire:click="openColumnModal" class="rounded-md border border-input px-3 py-1.5 text-xs font-medium text-foreground hover:bg-accent transition-colors">列配置</button>
        <button type="button" wire:click="openExportModal" class="rounded-md border border-input px-3 py-1.5 text-xs font-medium text-foreground hover:bg-accent transition-colors">导出</button>
    </div>

    {{-- 日志列表 --}}
    <div class="rounded-lg border bg-card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left w-10"><input type="checkbox" wire:model.live="selectAllPage" class="h-4 w-4 rounded border-input text-blue-600 focus:ring-blue-500" /></th>
                    <th class="px-4 py-2 text-left w-16">ID</th>
                    <th class="px-4 py-2 text-left">方法</th>
                    <th class="px-4 py-2 text-left">操作内容</th>
                    <th class="px-4 py-2 text-left">路径</th>
                    <th class="px-4 py-2 text-left">操作人</th>
                    <th class="px-4 py-2 text-left">IP</th>
                    <th class="px-4 py-2 text-left">时间</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="olog-{{ $log->id }}">
                    <td class="px-4 py-2"><input type="checkbox" value="{{ $log->id }}" wire:model.live="selectedIds" class="h-4 w-4 rounded border-input text-blue-600 focus:ring-blue-500" /></td>
                    <td class="px-4 py-2 text-muted-foreground">{{ $log->id }}</td>

                    {{-- 请求方法 --}}
                    <td class="px-4 py-2">
                        @php $mc = $log->method_color; @endphp
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium {{ $mc === 'green' ? 'bg-green-100 text-green-700' : ($mc === 'blue' ? 'bg-blue-100 text-blue-700' : ($mc === 'orange' ? 'bg-orange-100 text-orange-700' : ($mc === 'red' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600'))) }}">
                            {{ $log->method }}
                        </span>
                    </td>

                    {{-- 操作内容 --}}
                    <td class="px-4 py-2 text-foreground truncate min-w-0">{{ $log->content }}</td>

                    {{-- 路径 --}}
                    <td class="px-4 py-2 text-muted-foreground truncate font-mono min-w-0">{{ $log->path }}</td>

                    {{-- 操作人 --}}
                    <td class="px-4 py-2 text-foreground">{{ $log->username ?? '-' }}</td>

                    {{-- IP --}}
                    <td class="px-4 py-2 text-muted-foreground font-mono">{{ $log->ip ?? '-' }}</td>

                    {{-- 时间 --}}
                    <td class="px-4 py-2 text-muted-foreground">{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-6 py-12 text-center text-muted-foreground">暂无操作日志</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- 分页 --}}
    <div class="mt-4">
        {{ $logs->links() }}
    </div>

    @include('partials.column-modal')
    @include('partials.export-modal')
</div>
