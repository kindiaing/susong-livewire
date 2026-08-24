# TeleAgent 跨电脑迁移操作指南

> 适用于将当前电脑上的 TeleAgent（含 AI 历史思考流水、长期记忆、技能、定时任务等）完整迁移到另一台新电脑。

---

## 一、整体架构概览

TeleAgent 在 Windows 上的数据分布在两个根目录：

| 目录 | 作用 | 是否需迁移 |
|---|---|---|
| `C:\Users\{用户名}\.config\TeleAgent\` | 技能定义、插件、工具配置 | **必须** |
| `C:\Users\{用户名}\.local\share\TeleAgent\` | 核心数据库、记忆、日志、运行时、工作空间 | **必须** |
| 项目目录（如 `D:\WWW\livewire`） | 你的实际项目代码 | **必须**（走 Git） |

> 以下用 `%CONFIG%` 代替 `C:\Users\{用户名}\.config\TeleAgent`
> 以下用 `%DATA%` 代替 `C:\Users\{用户名}\.local\share\TeleAgent`

---

## 二、需要迁移的文件清单

### 2.1 核心数据（`%DATA%` 目录）

| 路径 | 说明 | 大小参考 | 必须迁移 |
|---|---|---|---|
| `teleagent.db` + `.db-shm` + `.db-wal` | **主数据库**，含所有会话历史、AI 思考流水、消息记录 | ~350 MB | **是** |
| `memory/` | **长期记忆**（USER.md、MEMORY.md、daily-log/ 每日日志） | ~0.3 MB | **是** |
| `scheduler/` | **定时任务**数据库及状态 | ~0.1 MB | **是** |
| `im-service/` | **IM 桥接**数据库（钉钉/飞书等 IM 授权与消息） | ~1.3 MB | **是** |
| `auth.json` | 登录认证令牌 | 极小 | **是** |
| `onboarding.json` | 引导完成状态 | 极小 | 是（可选） |
| `user-agent-config.json` | 用户级功能开关（水印、技能进化等） | 极小 | 是（可选） |
| `channel-config.json` | 渠道配置 | 极小 | 是（可选） |
| `skills-metadata.json` | 技能元数据索引 | ~12 KB | 是（可选） |
| `session-status.json` | 会话状态快照 | 极小 | 是（可选） |
| `deleted-session-ids.json` | 已删除会话 ID 记录 | 极小 | 是（可选） |
| `TeleAgent的工作空间/` | 工作空间文件（演示、生成文档等） | ~21 MB | 按需 |
| `log/` | 运行日志文件 | ~270 MB | **否**（可丢弃） |
| `Cache/`、`Code Cache/`、`GPUCache/` 等 | Electron 缓存 | 不定 | **否** |
| `runtimes/` | 内置运行时（Node、Python、ripgrep 等） | ~620 MB | **否**（新机器安装自动重建） |
| `bin/` | 内置工具二进制 | ~6 MB | **否**（自动重建） |
| `blob_storage/`、`Local Storage/`、`Session Storage/` 等 | Electron 运行时存储 | 不定 | **否** |

### 2.2 技能与配置（`%CONFIG%` 目录）

| 路径 | 说明 | 大小参考 | 必须迁移 |
|---|---|---|---|
| `skills/` | **技能定义**（SKILL.md + 随附脚本/模板） | ~29 MB | **是** |
| `tools/` | 工具适配器配置（.ts/.txt） | 极小 | 是（可选） |
| `plugins/` | 插件（水印等） | ~66 MB | 是（可选） |
| `package.json` | 插件依赖声明 | 极小 | 是（可选） |
| `node_modules/` | Node 依赖 | 不定 | **否**（新机器 `npm install` 自动安装） |

### 2.3 项目代码（独立目录，如 `D:\WWW\livewire`）

| 内容 | 迁移方式 |
|---|---|
| 项目源代码 | **Git 仓库**：`git clone` 或 `git push` + 新机器 `git pull` |
| `.env` 环境配置 | **手动复制**（.env 不在 Git 版本控制中） |
| `docs/` 开发文档 | 随 Git 仓库一起迁移 |
| `database/migrations/` | 随 Git 仓库 |
| `database/seeders/` | 随 Git 仓库 |
| `vendor/`（PHP 依赖） | **否**，新机器 `composer install` |
| `node_modules/`（前端依赖） | **否**，新机器 `npm install` |

---

## 三、AI 历史思考流水详解

### 什么是"AI 历史思考流水"

TeleAgent 的 AI 历史思考流水 = **所有会话的完整消息记录**，包括：

- 用户的每次提问
- AI 的每次回复
- AI 的推理过程（thinking/推理内容）
- 工具调用记录（调用了什么工具、参数、返回结果）
- 代码编辑记录
- 文件操作记录

这些数据**全部存储在 `teleagent.db` 中**。

### teleagent.db 结构说明

| 存储内容 | 迁移方式 |
|---|---|
| 会话列表（sessions 表） | 随 db 文件整体迁移 |
| 消息记录（messages 表） | 随 db 文件整体迁移 |
| AI 推理/思考链 | 随 db 文件整体迁移 |
| 工具调用结果（tool-output/） | 随 db + `%DATA%\tool-output\` 迁移 |
| 会话差异快照 | `%DATA%\storage\session_diff\` |

### 如何单独打包"历史思考流水"

如果只需要迁移 AI 思考流水（不含其他数据），最小化打包：

```powershell
# 在源机器上执行
$source = "$env:LOCALAPPDATA\..\..\..\..\.local\share\TeleAgent"
# 实际路径为 C:\Users\{用户名}\.local\share\TeleAgent

# 创建打包目录
$stage = "D:\teleagent-backup"
New-Item -ItemType Directory -Path "$stage\data" -Force

# 复制主数据库（含所有会话和思考流水）
Copy-Item "$source\teleagent.db" "$stage\data\" -Force
Copy-Item "$source\teleagent.db-shm" "$stage\data\" -Force -ErrorAction SilentlyContinue
Copy-Item "$source\teleagent.db-wal" "$stage\data\" -Force -ErrorAction SilentlyContinue

# 复制工具输出（部分工具结果以文件存储）
Copy-Item "$source\tool-output" "$stage\data\tool-output" -Recurse -Force -ErrorAction SilentlyContinue

# 复制会话差异快照
Copy-Item "$source\storage\session_diff" "$stage\data\session_diff" -Recurse -Force -ErrorAction SilentlyContinue

# 打包为 zip
Compress-Archive -Path "$stage\data\*" -DestinationPath "D:\teleagent-thinking-stream.zip" -Force
```

> 文件大小参考：teleagent.db 约 350 MB，压缩后约 50-100 MB。

---

## 四、完整迁移操作步骤

### 第一步：源机器 — 关闭 TeleAgent 并打包

```
1. 完全退出 TeleAgent 桌面端（托盘右键 → 退出）
2. 确认没有残留进程：任务管理器搜索 "TeleAgent"，结束所有进程
```

```powershell
# ===== 源机器打包脚本 =====
# 运行前先完全退出 TeleAgent

[Console]::OutputEncoding = [System.Text.Encoding]::UTF8

$src = "C:\Users\Seeding\.local\share\TeleAgent"
$cfg = "C:\Users\Seeding\.config\TeleAgent"
$backup = "D:\TeleAgent-Full-Backup"

# 创建备份目录结构
$dirs = @(
    "$backup\local-share",
    "$backup\config",
    "$backup\project"
)
foreach ($d in $dirs) { New-Item -ItemType Directory -Path $d -Force | Out-Null }

# --- 1. 核心 ---
# 主数据库（含 AI 历史思考流水）
Copy-Item "$src\teleagent.db" "$backup\local-share\" -Force
Copy-Item "$src\teleagent.db-shm" "$backup\local-share\" -Force -ErrorAction SilentlyContinue
Copy-Item "$src\teleagent.db-wal" "$backup\local-share\" -Force -ErrorAction SilentlyContinue

# 长期记忆
Copy-Item "$src\memory" "$backup\local-share\memory" -Recurse -Force

# 定时任务
Copy-Item "$src\scheduler" "$backup\local-share\scheduler" -Recurse -Force

# IM 桥接
Copy-Item "$src\im-service" "$backup\local-share\im-service" -Recurse -Force

# 工具输出
Copy-Item "$src\tool-output" "$backup\local-share\tool-output" -Recurse -Force -ErrorAction SilentlyContinue

# 会话差异快照
Copy-Item "$src\storage" "$backup\local-share\storage" -Recurse -Force

# 关键配置文件
$cfgFiles = @(
    "auth.json", "onboarding.json", "user-agent-config.json",
    "channel-config.json", "skills-metadata.json",
    "session-status.json", "deleted-session-ids.json",
    "skill-evolution-review-usage.json", "updater.json"
)
foreach ($f in $cfgFiles) {
    $srcFile = "$src\$f"
    if (Test-Path $srcFile) {
        Copy-Item $srcFile "$backup\local-share\" -Force -ErrorAction SilentlyContinue
    }
}

# --- 2. 技能与插件 ---
Copy-Item "$cfg\skills" "$backup\config\skills" -Recurse -Force
Copy-Item "$cfg\tools" "$backup\config\tools" -Recurse -Force
Copy-Item "$cfg\plugins" "$backup\config\plugins" -Recurse -Force
Copy-Item "$cfg\package.json" "$backup\config\" -Force -ErrorAction SilentlyContinue

# --- 3. 项目 .env（如有需要）---
# 项目代码通过 Git 迁移，这里只备份 .env
$projectPath = "D:\WWW\livewire"
if (Test-Path "$projectPath\.env") {
    Copy-Item "$projectPath\.env" "$backup\project\.env" -Force
}

# --- 4. 打包 ---
Write-Output "正在压缩备份..."
Compress-Archive -Path "$backup\*" -DestinationPath "D:\TeleAgent-Full-Backup.zip" -Force

# 输出统计
$totalSize = [math]::Round((Get-ChildItem $backup -Recurse -File | Measure-Object -Property Length -Sum).Sum / 1MB, 1)
Write-Output "备份完成！总大小: $totalSize MB"
Write-Output "备份位置: D:\TeleAgent-Full-Backup.zip"
Write-Output "解压目录: D:\TeleAgent-Full-Backup"
```

### 第二步：新机器 — 安装 TeleAgent

```
1. 从官网下载并安装 TeleAgent 桌面端
2. 启动一次后立即退出（让程序创建初始目录结构）
3. 确认以下目录已自动创建：
   - C:\Users\{新用户名}\.local\share\TeleAgent\
   - C:\Users\{新用户名}\.config\TeleAgent\
```

### 第三步：新机器 — 恢复数据

```powershell
# ===== 新机器恢复脚本 =====
# 运行前先完全退出 TeleAgent

[Console]::OutputEncoding = [System.Text.Encoding]::UTF8

# 解压备份
Expand-Archive -Path "D:\TeleAgent-Full-Backup.zip" -DestinationPath "D:\TeleAgent-Full-Backup" -Force

$backup = "D:\TeleAgent-Full-Backup"
$dst = "C:\Users\{新用户名}\.local\share\TeleAgent"   # ← 替换为实际用户名
$cfgDst = "C:\Users\{新用户名}\.config\TeleAgent"       # ← 替换为实际用户名

# --- 1. 恢复核心数据 ---
# 先删除新机器自动生成的 db（避免冲突）
Remove-Item "$dst\teleagent.db" -Force -ErrorAction SilentlyContinue
Remove-Item "$dst\teleagent.db-shm" -Force -ErrorAction SilentlyContinue
Remove-Item "$dst\teleagent.db-wal" -Force -ErrorAction SilentlyContinue

# 复制数据库
Copy-Item "$backup\local-share\teleagent.db" "$dst\" -Force
Copy-Item "$backup\local-share\teleagent.db-shm" "$dst\" -Force -ErrorAction SilentlyContinue
Copy-Item "$backup\local-share\teleagent.db-wal" "$dst\" -Force -ErrorAction SilentlyContinue

# 复制长期记忆
Copy-Item "$backup\local-share\memory" "$dst\memory" -Recurse -Force

# 复制定时任务
Copy-Item "$backup\local-share\scheduler" "$dst\scheduler" -Recurse -Force

# 复制 IM 桥接
Copy-Item "$backup\local-share\im-service" "$dst\im-service" -Recurse -Force

# 复制工具输出
Copy-Item "$backup\local-share\tool-output" "$dst\tool-output" -Recurse -Force -ErrorAction SilentlyContinue

# 复制会话差异快照
Copy-Item "$backup\local-share\storage" "$dst\storage" -Recurse -Force

# 复制关键配置文件
$cfgFiles = @(
    "auth.json", "onboarding.json", "user-agent-config.json",
    "channel-config.json", "skills-metadata.json",
    "session-status.json", "deleted-session-ids.json",
    "skill-evolution-review-usage.json", "updater.json"
)
foreach ($f in $cfgFiles) {
    $srcFile = "$backup\local-share\$f"
    if (Test-Path $srcFile) {
        Copy-Item $srcFile "$dst\" -Force -ErrorAction SilentlyContinue
    }
}

# --- 2. 恢复技能与插件 ---
Copy-Item "$backup\config\skills" "$cfgDst\skills" -Recurse -Force
Copy-Item "$backup\config\tools" "$cfgDst\tools" -Recurse -Force
Copy-Item "$backup\config\plugins" "$cfgDst\plugins" -Recurse -Force
Copy-Item "$backup\config\package.json" "$cfgDst\" -Force -ErrorAction SilentlyContinue

# --- 3. 安装插件依赖 ---
Set-Location $cfgDst
npm install

Write-Output "恢复完成！请启动 TeleAgent 验证。"
```

### 第四步：新机器 — 恢复项目代码

```powershell
# 1. Git clone 项目
cd D:\WWW
git clone <你的仓库地址> livewire
cd livewire

# 2. 恢复 .env
Copy-Item "D:\TeleAgent-Full-Backup\project\.env" "D:\WWW\livewire\.env" -Force

# 3. 安装依赖
composer install
npm install

# 4. 数据库迁移
php artisan migrate --force
php artisan view:clear
php artisan config:clear

# 5. 如需测试数据
php artisan db:seed --class=DemoDataSeeder
```

---

## 五、用户名不同时的处理

如果两台电脑的用户名不同（如源机器 `Seeding`，新机器 `Zhang`），需注意：

### 5.1 路径替换

脚本中所有 `C:\Users\Seeding\` 替换为新机器的实际用户名路径。

### 5.2 记忆文件中的路径

长期记忆（`USER.md`、`MEMORY.md`、`daily-log/*.md`）中可能记录了源机器的绝对路径（如 `D:\WWW\livewire`），迁移后：

- 如果项目放在相同盘符路径（`D:\WWW\livewire`），无需修改
- 如果路径不同，需要手动搜索替换记忆文件中的旧路径

### 5.3 onboarding.json 中的路径

`onboarding.json` 中 `quickStartDemoFilePaths` 记录了演示文件路径，如路径不同需手动更新或忽略。

---

## 六、不需要迁移的内容

| 内容 | 原因 |
|---|---|
| `runtimes/`（Node、Python 等） | 新机器安装时自动下载，约 620 MB |
| `bin/`（工具二进制） | 自动重建 |
| `node_modules/`（.config 下） | 新机器 `npm install` 自动安装 |
| `Cache/`、`GPUCache/`、`Code Cache/` | Electron 缓存，自动重建 |
| `log/` | 运行日志，历史日志无迁移价值 |
| `blob_storage/`、`Local Storage/`、`Session Storage/` | Electron 运行时数据，自动重建 |
| `Dictionaries/`、`VideoDecodeStats/` | 自动重建 |
| `npm-cache/`、`npm-global/` | 自动重建 |
| `python-userbase/` | 自动重建 |
| `playwright-mcp/` | 自动重建 |
| `lockfile` | 运行时锁文件，删除即可 |

---

## 七、迁移后验证清单

- [ ] 启动 TeleAgent，能正常登录
- [ ] 历史会话列表可见，点击进入能查看完整对话
- [ ] AI 思考流水（推理过程）正常显示
- [ ] 长期记忆：说"你记得我吗"，AI 能回忆出 USER.md 内容
- [ ] 技能：触发某个技能（如"帮我做个PPT"），能正常加载
- [ ] 定时任务：查看定时任务列表，历史任务可见
- [ ] IM 桥接（如使用）：IM 连接状态正常
- [ ] 项目代码：Git 状态正确，能正常开发
- [ ] 项目依赖：`composer install` 和 `npm install` 成功
- [ ] 数据库：`php artisan migrate` 成功
- [ ] 浏览器访问：项目页面正常加载

---

## 八、备份大小参考（当前实际数据）

| 内容 | 文件数 | 大小 |
|---|---|---|
| teleagent.db（主数据库 + AI 思考流水） | 1 | ~353 MB |
| memory/（长期记忆） | 32 | ~0.3 MB |
| scheduler/（定时任务） | 2 | ~0.1 MB |
| im-service/（IM 桥接） | 3 | ~1.3 MB |
| skills/（技能定义） | 470 | ~29 MB |
| tools/（工具配置） | 17 | < 0.1 MB |
| plugins/（插件） | 11 | ~66 MB |
| tool-output/ + storage/ | 不定 | 不定 |
| **打包压缩后预估** | — | **~150-200 MB** |

> 不含 runtimes（620 MB）、log（270 MB）、Cache 等可重建数据。

---

## 九、常见问题

### Q: 迁移后历史会话看不到 AI 的推理过程？

确认 `teleagent.db`、`teleagent.db-wal` 都已复制。WAL 文件含最近未 checkpoint 的写入，缺失会丢最近的思考流水。

### Q: 迁移后定时任务不执行？

1. 确认 `scheduler/scheduler.db` 和 `scheduler/scheduler-daemon-state.json` 已复制
2. 重启 TeleAgent 让调度器守护进程重新加载

### Q: 迁移后 IM 桥接连不上？

1. 确认 `im-service/im-service.db` 已复制
2. IM 授权 Token 可能已过期，需在新机器重新授权

### Q: 迁移后技能不生效？

1. 确认 `%CONFIG%\skills\` 已完整复制
2. 在 `%CONFIG%` 下执行 `npm install` 安装插件依赖
3. 确认 `skills-metadata.json` 已复制

### Q: 可以只迁移记忆不迁移会话吗？

可以。只复制 `memory/` 目录到新机器即可。长期记忆是纯文本 Markdown 文件，与数据库无关。

### Q: 迁移后路径不一致导致 AI 找不到项目？

在对话中告诉 AI 新的项目路径，或手动编辑 `MEMORY.md` 和 `USER.md` 中的旧路径。AI 会在后续对话中自动适配新路径。

> AI生成