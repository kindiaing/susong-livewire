<div class="p-6">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">商家管理</h1>
            <p class="text-muted-foreground mt-1">管理商家信息及结算方式</p>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            新增商家
        </button>
    </div>

    {{-- 搜索栏 --}}
    <div class="flex items-center gap-3 mb-4">
        <input
            type="text"
            wire:model.live="search"
            class="flex h-9 w-64 rounded-md border border-input bg-background px-3 text-sm"
            placeholder="搜索商家名称/联系人/电话..."
        />
        <button wire:click="resetFilters" class="text-sm text-muted-foreground hover:text-foreground transition-colors">重置</button>
        <div class="flex-1"></div>
        <button wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">列配置</button>
        <button wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">导入</button>
        <button wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            <button wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
            <button wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    {{-- 商家列表 --}}
    <div class="rounded-lg border bg-card overflow-x-auto">
        <div class="grid grid-cols-[40px_60px_1fr_100px_120px_80px_100px_100px_80px_100px] gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider min-w-[900px]">
            <div><input type="checkbox" wire:model.live="selectAll" class="rounded" /></div>
            <div>ID</div>
            <div>商家名称</div>
            <div>联系人</div>
            <div>联系电话</div>
            <div>结算方式</div>
            <div>起送额</div>
            <div>信用额度</div>
            <div>状态</div>
            <div>操作</div>
        </div>

        @forelse($merchants as $merchant)
            <div class="grid grid-cols-[40px_60px_1fr_100px_120px_80px_100px_100px_80px_100px] gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors min-w-[900px]"
                 wire:key="merchant-{{ $merchant->id }}">
                <div><input type="checkbox" value="{{ $merchant->id }}" wire:model.live="selectedIds" class="rounded" /></div>
                <div class="text-sm text-muted-foreground">{{ $merchant->id }}</div>
                <div class="text-sm font-medium text-foreground truncate">{{ $merchant->name }}</div>
                <div class="text-sm text-foreground">{{ $merchant->contact_name ?? '-' }}</div>
                <div class="text-sm text-foreground">{{ $merchant->contact_phone ?? '-' }}</div>
                <div class="text-sm text-foreground">{{ \App\Models\Merchant::settlementTypeMap()[$merchant->settlement_type] ?? '-' }}</div>
                <div class="text-sm text-foreground">{{ money_format($merchant->min_order_amount) }}</div>
                <div class="text-sm text-foreground">{{ money_format($merchant->credit_limit) }}</div>
                <div>
                    @if($merchant->status === 1)
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-green-100 text-green-700">启用</span>
                    @else
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-gray-100 text-gray-600">禁用</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="openEditModal({{ $merchant->id }})" class="text-blue-600 hover:text-blue-700 text-sm">编辑</button>
                    <button wire:click="confirmDelete({{ $merchant->id }})" class="text-red-600 hover:text-red-700 text-sm">删除</button>
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无商家数据</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $merchants->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeModal"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <h2 class="text-lg font-semibold text-foreground mb-4">{{ $editingId ? '编辑商家' : '新增商家' }}</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">商家名称 <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="formName" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="请输入商家名称" />
                    @error('formName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">联系人 <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="formContactName" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="联系人姓名" />
                        @error('formContactName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">联系电话 <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="formContactPhone" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="联系电话" />
                        @error('formContactPhone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">地址 <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="formAddress" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="详细地址" />
                    @error('formAddress') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">配送线路</label>
                        <select wire:model="formDeliveryRouteId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="0">请选择</option>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}">{{ $route->name }}</option>
                            @endforeach
                        </select>
                        @error('formDeliveryRouteId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">配送顺序</label>
                        <input type="number" wire:model="formDeliverySort" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" />
                        @error('formDeliverySort') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">起送金额（厘） <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="formMinOrderAmount" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" placeholder="单位：厘" />
                        @error('formMinOrderAmount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">信用额度（厘） <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="formCreditLimit" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" placeholder="单位：厘" />
                        @error('formCreditLimit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">结算方式 <span class="text-red-500">*</span></label>
                        <select wire:model="formSettlementType" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="1">现结</option>
                            <option value="2">账期</option>
                            <option value="3">预付款</option>
                        </select>
                        @error('formSettlementType') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">状态 <span class="text-red-500">*</span></label>
                        <select wire:model="formStatus" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="1">启用</option>
                            <option value="2">禁用</option>
                        </select>
                        @error('formStatus') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                    <textarea wire:model="formRemark" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                    @error('formRemark') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button wire:click="closeModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 删除确认弹窗 --}}
    @if($showDeleteConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeDeleteConfirm"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">确认删除</h2>
            <p class="text-sm text-muted-foreground mb-6">确定要删除该商家吗？此操作不可恢复。</p>
            <div class="flex justify-end gap-3">
                <button wire:click="closeDeleteConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="delete" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif

    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
    @include('partials.delete-confirm')
</div>
