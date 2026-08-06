<div class="space-y-10">

    {{-- ==================== 图标对比（已选定 B: pencil-square） ==================== --}}
    <section>
        <h2 class="text-lg font-semibold mb-1">图标样式对比</h2>
        <p class="text-sm text-muted-foreground mb-4">编辑图标已选定 <code class="text-xs bg-green-50 text-green-700 px-1.5 py-0.5 rounded">pencil-square</code>（B），详情图标使用 <code class="text-xs bg-green-50 text-green-700 px-1.5 py-0.5 rounded">eye</code>，以下为对比参考</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-lg border bg-card p-4">
                <div class="flex items-center gap-2 mb-3">
                    <x-ui.icon name="pencil" class="w-4 h-4 text-muted-foreground" />
                    <span class="text-sm font-medium">A. pencil（旧）</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="p-1 rounded text-muted-foreground" title="编辑"><x-ui.icon name="pencil" class="w-3.5 h-3.5" /></button>
                    <span class="text-xs text-muted-foreground">笔+横线，像删除线</span>
                </div>
            </div>
            <div class="rounded-lg border-2 border-green-300 bg-card p-4">
                <div class="flex items-center gap-2 mb-3">
                    <x-ui.icon name="pencil-square" class="w-4 h-4 text-green-600" />
                    <span class="text-sm font-semibold text-green-700">B. pencil-square（已选定）</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="p-1 rounded text-blue-600 hover:bg-blue-50" title="编辑"><x-ui.icon name="pencil-square" class="w-3.5 h-3.5" /></button>
                    <button type="button" class="p-1 rounded text-green-600 hover:bg-green-50" title="详情"><x-ui.icon name="eye" class="w-3.5 h-3.5" /></button>
                    <button type="button" class="p-1 rounded text-red-600 hover:bg-red-50" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                    <span class="text-xs text-green-700">笔+方框，语义清晰</span>
                </div>
            </div>
            <div class="rounded-lg border bg-card p-4">
                <div class="flex items-center gap-2 mb-3">
                    <x-ui.icon name="pencil-simple" class="w-4 h-4 text-muted-foreground" />
                    <span class="text-sm font-medium">C. pencil-simple</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="p-1 rounded text-muted-foreground" title="编辑"><x-ui.icon name="pencil-simple" class="w-3.5 h-3.5" /></button>
                    <span class="text-xs text-muted-foreground">纯铅笔，最简洁</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== 采购单 + 明细示例 ==================== --}}
    <section>
        <h2 class="text-lg font-semibold mb-1">采购单管理（业务示例）</h2>
        <p class="text-sm text-muted-foreground mb-4">单据列表 + 明细详情的标准交互模式，后续其他模块参考此风格</p>

        {{-- 采购单列表 --}}
        <div class="rounded-lg border bg-card overflow-hidden mb-4">
            <div class="px-4 py-3 border-b flex items-center justify-between">
                <h3 class="text-sm font-semibold">采购单列表</h3>
                <button type="button" class="inline-flex items-center gap-1 rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700">
                    <x-ui.icon name="plus" class="w-3.5 h-3.5" />新增采购单
                </button>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider bg-muted/30">
                        <th class="px-4 py-2.5 text-left w-10"><input type="checkbox" class="rounded" /></th>
                        <th class="px-4 py-2.5 text-left">采购单号</th>
                        <th class="px-4 py-2.5 text-left">供应商</th>
                        <th class="px-4 py-2.5 text-left">采购日期</th>
                        <th class="px-4 py-2.5 text-left">状态</th>
                        <th class="px-4 py-2.5 text-right">总金额</th>
                        <th class="px-4 py-2.5 text-right w-24">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b hover:bg-muted/30 transition-colors">
                        <td class="px-4 py-2"><input type="checkbox" class="rounded" /></td>
                        <td class="px-4 py-2"><a href="#detail-1" class="font-mono text-blue-600 hover:text-blue-700">PO-20260806001</a></td>
                        <td class="px-4 py-2">鲜达生鲜供应</td>
                        <td class="px-4 py-2">2026-08-06</td>
                        <td class="px-4 py-2"><span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700">备货中</span></td>
                        <td class="px-4 py-2 text-right">{{ money_format(248000) }}</td>
                        <td class="px-4 py-2 text-right">
                            <div class="inline-flex items-center gap-0.5">
                                <a href="#detail-1" class="p-1 rounded text-green-600 hover:bg-green-50" title="详情"><x-ui.icon name="eye" class="w-3.5 h-3.5" /></a>
                                <button type="button" class="p-1 rounded text-blue-600 hover:bg-blue-50" title="编辑"><x-ui.icon name="pencil-square" class="w-3.5 h-3.5" /></button>
                                <button type="button" class="p-1 rounded text-red-600 hover:bg-red-50" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b hover:bg-muted/30 transition-colors">
                        <td class="px-4 py-2"><input type="checkbox" class="rounded" /></td>
                        <td class="px-4 py-2"><a href="#detail-2" class="font-mono text-blue-600 hover:text-blue-700">PO-20260805003</a></td>
                        <td class="px-4 py-2">绿源果蔬批发</td>
                        <td class="px-4 py-2">2026-08-05</td>
                        <td class="px-4 py-2"><span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-700">待接单</span></td>
                        <td class="px-4 py-2 text-right">{{ money_format(83200) }}</td>
                        <td class="px-4 py-2 text-right">
                            <div class="inline-flex items-center gap-0.5">
                                <a href="#detail-2" class="p-1 rounded text-green-600 hover:bg-green-50" title="详情"><x-ui.icon name="eye" class="w-3.5 h-3.5" /></a>
                                <button type="button" class="p-1 rounded text-blue-600 hover:bg-blue-50" title="编辑"><x-ui.icon name="pencil-square" class="w-3.5 h-3.5" /></button>
                                <button type="button" class="p-1 rounded text-red-600 hover:bg-red-50" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-muted/30 transition-colors">
                        <td class="px-4 py-2"><input type="checkbox" class="rounded" /></td>
                        <td class="px-4 py-2"><a href="#detail-3" class="font-mono text-blue-600 hover:text-blue-700">PO-20260804002</a></td>
                        <td class="px-4 py-2">海味轩水产</td>
                        <td class="px-4 py-2">2026-08-04</td>
                        <td class="px-4 py-2"><span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-600">已作废</span></td>
                        <td class="px-4 py-2 text-right">{{ money_format(156000) }}</td>
                        <td class="px-4 py-2 text-right">
                            <div class="inline-flex items-center gap-0.5">
                                <a href="#detail-3" class="p-1 rounded text-green-600 hover:bg-green-50" title="详情"><x-ui.icon name="eye" class="w-3.5 h-3.5" /></a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- 采购单明细 --}}
        <div id="detail-1" class="rounded-lg border bg-card overflow-hidden">
            <div class="px-4 py-3 border-b flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="#" class="text-muted-foreground hover:text-foreground transition-colors"><x-ui.icon name="arrow-left" class="w-4 h-4" /></a>
                    <h3 class="text-sm font-semibold">PO-20260806001 · 鲜达生鲜供应</h3>
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700">备货中</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="inline-flex items-center gap-1 rounded-md bg-orange-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-orange-700">
                        <x-ui.icon name="arrow-path" class="w-3.5 h-3.5" />状态流转
                    </button>
                    <button type="button" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-xs font-medium hover:bg-accent">
                        <x-ui.icon name="arrow-up-tray" class="w-3.5 h-3.5" />导出
                    </button>
                </div>
            </div>
            {{-- 基本信息区 --}}
            <div class="px-4 py-3 border-b bg-muted/20 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div><span class="text-muted-foreground">采购日期：</span><span>2026-08-06</span></div>
                <div><span class="text-muted-foreground">创建时间：</span><span>2026-08-06 09:30:25</span></div>
                <div><span class="text-muted-foreground">操作人：</span><span>seeding</span></div>
                <div><span class="text-muted-foreground">备注：</span><span>本周常规补货</span></div>
            </div>
            {{-- Tab 导航 + 工具栏 --}}
            <div x-data="{ activeTab: 'items' }">
                <div class="flex items-center border-b px-4 py-2">
                    <button type="button" @click="activeTab = 'items'" :class="activeTab === 'items' ? 'border-b-2 border-blue-600 text-foreground font-medium' : 'text-muted-foreground hover:text-foreground'" class="px-2 py-1.5 text-sm transition-colors -mb-[9px]">
                        采购明细
                    </button>
                    <button type="button" @click="activeTab = 'logs'" :class="activeTab === 'logs' ? 'border-b-2 border-blue-600 text-foreground font-medium' : 'text-muted-foreground hover:text-foreground'" class="px-2 py-1.5 text-sm transition-colors -mb-[9px]">
                        状态变更记录
                        <span class="ml-1 inline-flex items-center justify-center rounded-full bg-blue-100 text-blue-700 px-1.5 py-0.5 text-[10px] font-medium leading-none">2</span>
                    </button>
                    <div class="ml-auto" x-show="activeTab === 'items'">
                        <button type="button" class="inline-flex items-center gap-1 rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700">
                            <x-ui.icon name="plus" class="w-3.5 h-3.5" />添加明细
                        </button>
                    </div>
                </div>

                {{-- 采购明细 Tab --}}
                <div x-show="activeTab === 'items'">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-xs font-medium text-muted-foreground bg-muted/10">
                                <th class="px-4 py-2 text-left">SKU</th>
                                <th class="px-4 py-2 text-left">规格</th>
                                <th class="px-4 py-2 text-right">数量</th>
                                <th class="px-4 py-2 text-right">单价</th>
                                <th class="px-4 py-2 text-right">小计</th>
                                <th class="px-4 py-2 text-right w-16">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b hover:bg-muted/30 transition-colors">
                                <td class="px-4 py-2">西红柿 500g</td>
                                <td class="px-4 py-2 text-muted-foreground">500g/份</td>
                                <td class="px-4 py-2 text-right">50</td>
                                <td class="px-4 py-2 text-right">{{ money_format(320) }}</td>
                                <td class="px-4 py-2 text-right font-medium">{{ money_format(16000) }}</td>
                                <td class="px-4 py-2 text-right">
                                    <button type="button" class="p-1 rounded text-red-600 hover:bg-red-50" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                                </td>
                            </tr>
                            <tr class="border-b hover:bg-muted/30 transition-colors">
                                <td class="px-4 py-2">黄瓜 300g</td>
                                <td class="px-4 py-2 text-muted-foreground">300g/份</td>
                                <td class="px-4 py-2 text-right">80</td>
                                <td class="px-4 py-2 text-right">{{ money_format(180) }}</td>
                                <td class="px-4 py-2 text-right font-medium">{{ money_format(14400) }}</td>
                                <td class="px-4 py-2 text-right">
                                    <button type="button" class="p-1 rounded text-red-600 hover:bg-red-50" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                                </td>
                            </tr>
                            <tr class="border-b hover:bg-muted/30 transition-colors">
                                <td class="px-4 py-2">生菜 200g</td>
                                <td class="px-4 py-2 text-muted-foreground">200g/份</td>
                                <td class="px-4 py-2 text-right">100</td>
                                <td class="px-4 py-2 text-right">{{ money_format(450) }}</td>
                                <td class="px-4 py-2 text-right font-medium">{{ money_format(45000) }}</td>
                                <td class="px-4 py-2 text-right">
                                    <button type="button" class="p-1 rounded text-red-600 hover:bg-red-50" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-muted/20 font-medium">
                                <td class="px-4 py-2" colspan="2">合计</td>
                                <td class="px-4 py-2 text-right">230</td>
                                <td class="px-4 py-2"></td>
                                <td class="px-4 py-2 text-right">{{ money_format(75400) }}</td>
                                <td class="px-4 py-2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- 状态变更记录 Tab --}}
                <div x-show="activeTab === 'logs'">
                    <div class="divide-y">
                        <div class="flex items-center gap-3 px-4 py-2 text-sm">
                            <span class="text-muted-foreground text-xs w-40 shrink-0">2026-08-06 09:30:25</span>
                            <span class="font-medium">提交</span>
                            <span class="text-muted-foreground">待接单</span>
                            <x-ui.icon name="arrow-right" class="w-3 h-3 text-muted-foreground" />
                            <span class="font-medium">备货中</span>
                            <span class="text-muted-foreground ml-auto">操作人：seeding</span>
                        </div>
                        <div class="flex items-center gap-3 px-4 py-2 text-sm">
                            <span class="text-muted-foreground text-xs w-40 shrink-0">2026-08-06 09:30:03</span>
                            <span class="font-medium">创建</span>
                            <span class="text-muted-foreground">-</span>
                            <x-ui.icon name="arrow-right" class="w-3 h-3 text-muted-foreground" />
                            <span class="font-medium">待接单</span>
                            <span class="text-muted-foreground ml-auto">操作人：seeding</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== 旧 UI 组件库 Demo ==================== --}}
    <section>
        <h2 class="text-lg font-semibold mb-1">Button 按钮</h2>
        <p class="text-sm text-muted-foreground mb-4">多色按钮变体，支持 default / blue / green / orange / red / yellow / purple / outline / ghost / link</p>
        <div class="rounded-lg border bg-card p-6 space-y-4">
            <div class="flex flex-wrap items-center gap-3">
                <x-ui.button>Default</x-ui.button>
                <x-ui.button variant="blue">Blue</x-ui.button>
                <x-ui.button variant="green">Green</x-ui.button>
                <x-ui.button variant="orange">Orange</x-ui.button>
                <x-ui.button variant="red">Red</x-ui.button>
                <x-ui.button variant="yellow">Yellow</x-ui.button>
                <x-ui.button variant="purple">Purple</x-ui.button>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <x-ui.button variant="outline">Outline</x-ui.button>
                <x-ui.button variant="outline-blue">Blue</x-ui.button>
                <x-ui.button variant="outline-green">Green</x-ui.button>
                <x-ui.button variant="outline-orange">Orange</x-ui.button>
                <x-ui.button variant="outline-red">Red</x-ui.button>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <x-ui.button variant="ghost">Ghost</x-ui.button>
                <x-ui.button variant="link">Link</x-ui.button>
                <x-ui.button variant="secondary">Secondary</x-ui.button>
                <x-ui.button variant="blue" size="xs">XS</x-ui.button>
                <x-ui.button variant="blue" size="sm">SM</x-ui.button>
                <x-ui.button variant="blue" size="default">Default</x-ui.button>
                <x-ui.button variant="blue" size="lg">LG</x-ui.button>
            </div>
        </div>
    </section>

    <section>
        <h2 class="text-lg font-semibold mb-1">Badge 徽标</h2>
        <p class="text-sm text-muted-foreground mb-4">状态标签、分类标记，支持 solid / outline 双风格</p>
        <div class="rounded-lg border bg-card p-6 space-y-4">
            <div class="flex flex-wrap items-center gap-3">
                <x-ui.badge>Default</x-ui.badge>
                <x-ui.badge variant="blue">Blue</x-ui.badge>
                <x-ui.badge variant="green">Green</x-ui.badge>
                <x-ui.badge variant="orange">Orange</x-ui.badge>
                <x-ui.badge variant="red">Red</x-ui.badge>
                <x-ui.badge variant="yellow">Yellow</x-ui.badge>
                <x-ui.badge variant="purple">Purple</x-ui.badge>
                <x-ui.badge variant="secondary">Secondary</x-ui.badge>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <x-ui.badge variant="outline">Outline</x-ui.badge>
                <x-ui.badge variant="outline-blue">Blue</x-ui.badge>
                <x-ui.badge variant="outline-green">Green</x-ui.badge>
                <x-ui.badge variant="outline-orange">Orange</x-ui.badge>
                <x-ui.badge variant="outline-red">Red</x-ui.badge>
            </div>
        </div>
    </section>

    <section>
        <h2 class="text-lg font-semibold mb-1">Alert 提示</h2>
        <p class="text-sm text-muted-foreground mb-4">信息 / 成功 / 警告 / 危险四种风格，左侧彩色边框高亮</p>
        <div class="space-y-4">
            <x-ui.alert variant="info"><strong>信息提示</strong> — 系统将于今晚 23:00 进行例行维护，预计 30 分钟完成。</x-ui.alert>
            <x-ui.alert variant="success"><strong>操作成功</strong> — 采购单已成功提交，等待供应商确认。</x-ui.alert>
            <x-ui.alert variant="warning"><strong>注意</strong> — 当前库存低于预警值，请及时补货。</x-ui.alert>
            <x-ui.alert variant="destructive"><strong>操作失败</strong> — 付款金额超出账户余额，请充值后重试。</x-ui.alert>
        </div>
    </section>

    <section>
        <h2 class="text-lg font-semibold mb-1">Card 卡片</h2>
        <p class="text-sm text-muted-foreground mb-4">可组合 CardHeader + CardTitle + CardDescription + CardContent + CardFooter</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-ui.card>
                <x-ui.card-header>
                    <x-ui.card-title>今日订单</x-ui.card-title>
                    <x-ui.card-description>今日已完成订单数量</x-ui.card-description>
                </x-ui.card-header>
                <x-ui.card-content>
                    <p class="text-2xl font-bold">1,284</p>
                    <p class="text-xs text-muted-foreground">较昨日 +12.5%</p>
                </x-ui.card-content>
                <x-ui.card-footer><x-ui.button variant="outline" size="sm">查看详情</x-ui.button></x-ui.card-footer>
            </x-ui.card>
            <x-ui.card>
                <x-ui.card-header>
                    <x-ui.card-title>待结算金额</x-ui.card-title>
                    <x-ui.card-description>采购结算单汇总</x-ui.card-description>
                </x-ui.card-header>
                <x-ui.card-content>
                    <p class="text-2xl font-bold">{{ money_format(52380000) }}</p>
                    <p class="text-xs text-muted-foreground">3 笔待审核</p>
                </x-ui.card-content>
                <x-ui.card-footer><x-ui.button variant="outline" size="sm">去结算</x-ui.button></x-ui.card-footer>
            </x-ui.card>
            <x-ui.card>
                <x-ui.card-header>
                    <x-ui.card-title>库存预警</x-ui.card-title>
                    <x-ui.card-description>低于预警值的 SKU</x-ui.card-description>
                </x-ui.card-header>
                <x-ui.card-content>
                    <p class="text-2xl font-bold">17</p>
                    <p class="text-xs text-muted-foreground">需紧急补货 5 项</p>
                </x-ui.card-content>
                <x-ui.card-footer><x-ui.button variant="outline" size="sm">处理预警</x-ui.button></x-ui.card-footer>
            </x-ui.card>
        </div>
    </section>

    <section>
        <h2 class="text-lg font-semibold mb-1">业务状态组合</h2>
        <p class="text-sm text-muted-foreground mb-4">典型业务场景中的状态标签搭配</p>
        <div class="rounded-lg border bg-card p-6 space-y-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm text-muted-foreground w-20">订单状态:</span>
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-700">待确认</span>
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700">已确认</span>
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-orange-100 text-orange-700">配送中</span>
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-600">已完成</span>
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-red-100 text-red-700">已取消</span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm text-muted-foreground w-20">审核状态:</span>
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-700">待审核</span>
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700">已通过</span>
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-red-100 text-red-700">已拒绝</span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm text-muted-foreground w-20">结算状态:</span>
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-orange-100 text-orange-700">待付款</span>
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-700">部分付款</span>
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700">已结清</span>
            </div>
        </div>
    </section>
</div>
