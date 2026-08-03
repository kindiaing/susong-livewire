/**
 * Command Palette — Alpine.js 组件
 *
 * 菜单数据从 window.__commandGroups 注入（Blade 模板中由 config/menu.php 生成），
 * 不再硬编码，保持单一数据源。
 */
export function commandPalette() {
    return {
        open: false,
        query: '',
        activeIndex: null,
        groups: window.__commandGroups || [],

        get filteredGroups() {
            if (!this.query.trim()) return this.groups;
            const q = this.query.toLowerCase();
            return this.groups
                .map(g => ({
                    ...g,
                    items: g.items.filter(item =>
                        item.label.toLowerCase().includes(q)
                    )
                }))
                .filter(g => g.items.length > 0);
        },

        get allItems() {
            const keys = [];
            this.filteredGroups.forEach(g => {
                g.items.forEach((_, idx) => {
                    keys.push(g.label + '-' + idx);
                });
            });
            return keys;
        },

        init() {
            this.$watch('open', (val) => {
                if (val) {
                    this.query = '';
                    this.activeIndex = null;
                    this.$nextTick(() => {
                        this.$refs.searchInput?.focus();
                    });
                }
            });
        },

        navigateDown() {
            const all = this.allItems;
            if (all.length === 0) return;
            const idx = all.findIndex(k => k === this.activeIndex);
            this.activeIndex = all[(idx + 1) % all.length];
        },

        navigateUp() {
            const all = this.allItems;
            if (all.length === 0) return;
            const idx = all.findIndex(k => k === this.activeIndex);
            this.activeIndex = all[(idx - 1 + all.length) % all.length];
        },

        selectItem() {
            if (!this.activeIndex) return;
            const [groupLabel, idxStr] = this.activeIndex.split('-');
            const group = this.filteredGroups.find(g => g.label === groupLabel);
            if (!group) return;
            const item = group.items[parseInt(idxStr)];
            if (item) this.goTo(item);
        },

        goTo(item) {
            this.open = false;
            if (item.url && item.url !== '#') {
                window.location.href = item.url;
            }
        }
    };
}
