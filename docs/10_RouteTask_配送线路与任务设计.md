# 配送线路与配送任务 — 功能设计方案 v2（线路固定 + 任务按需生成）

> 本文档为修订版，核心调整：
> 1. **配送线路提前固定规划**，线路明细 = 商家列表，支持拖拽排序和手动修改排序；
> 2. **配送任务由运营按需勾选单据生成**，非按固定日期自动生成；
> 3. **配送顺序表**按线路顺序自动生成，支持标记加急/重要，但**线路顺序本身不变**；
> 4. 司机特殊情况可自行调整配送顺序。

---

## 一、核心概念与分层

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              固定层（提前规划）                                │
│  ┌──────────────────────────────────────────────────────────────────────┐  │
│  │  配送线路（Route）                                                     │  │
│  │  · 提前规划好，长期稳定                                                 │  │
│  │  · 线路明细 = 商家列表，有固定顺序（sequence_no）                         │  │
│  │  · 支持拖拽排序、手动修改顺序                                           │  │
│  │  · 包含所有可能配送的商家（不管今天有没有订单）                          │  │
│  └──────────────────────────────────────────────────────────────────────┘  │
│                                    │                                        │
│                                    ▼ 按线路顺序映射                           │
├─────────────────────────────────────────────────────────────────────────────┤
│                              动态层（按需生成）                                │
│  ┌──────────────────────┐      ┌──────────────────────┐      ┌────────────┐ │
│  │  配送任务（Task）     │      │  配送任务明细        │      │ 配送顺序表  │ │
│  │  · 运营按需勾选单据   │─────▶│  · 勾选的配送单据    │─────▶│ · 按线路顺序│ │
│  │  · 指定送达日期       │      │  · 送达日期          │      │   排列      │ │
│  │  · 分配司机/车辆      │      │  · 下单日期          │      │ · 有单才出现│ │
│  │  · 一条线路一个任务   │      │  · 商品/数量/重量    │      │ · 支持加急  │ │
│  │                      │      │  · 商家信息          │      │   重要标记  │ │
│  └──────────────────────┘      └──────────────────────┘      └────────────┘ │
│                                                                      │      │
│                                                                      ▼      │
│                                                               ┌────────────┐│
│                                                               │ 司机执行    ││
│                                                               │ · 按顺序表  ││
│                                                               │   顺序配送  ││
│                                                               │ · 加急/重要 ││
│                                                               │   醒目提示  ││
│                                                               │ · 特殊情况  ││
│                                                               │   可自行调整││
│                                                               └────────────┘│
└─────────────────────────────────────────────────────────────────────────────┘
```

| 层级 | 概念 | 稳定性 | 说明 |
|:-----|:-----|:-------|:-----|
| **固定层** | 配送线路 + 线路明细 | 高 | 提前规划，季度微调，顺序固定 |
| **动态层** | 配送任务 + 任务明细 + 顺序表 | 低 | 每天/每次按需生成，灵活调整 |

---

## 二、关键设计原则

### 2.1 线路固定化

- 线路是**提前规划**的，包含该区域**所有可能配送的商家**，按最优路径排序
- 线路上的商家顺序（`sequence_no`）是**核心资产**，决定配送效率
- 支持**拖拽排序**和**手动修改**，但修改后对所有未来任务生效
- 商家没有订单时**自然跳过**，但在线路中的位置不变

### 2.2 任务动态化（按需勾选）

- 不是按固定日期自动生成任务，而是**运营主动按需勾选单据**
- 配送任务的核心是**勾选哪些单据、指定送达日期**
- 系统根据勾选的单据，自动按**线路顺序**生成配送顺序表
- 同一个送达日期可以生成多批任务（如上午一批、下午一批）

### 2.3 顺序表与线路的关系

```
线路 A 的固定顺序：
  ① 商家甲 → ② 商家乙 → ③ 商家丙 → ④ 商家丁 → ⑤ 商家戊

今天勾选的配送单据：
  · 商家乙（2单）· 商家丁（1单）· 商家戊（3单）

生成的配送顺序表（按线路顺序，跳过无单的商家）：
  ① 商家乙（sequence=2，base_sequence=2）✓ 有单
  ② 商家丁（sequence=3，base_sequence=4）✓ 有单，标记为「加急」
  ③ 商家戊（sequence=4，base_sequence=5）✓ 有单，标记为「重要」

注：商家甲、丙 无单 → 不显示在顺序表中（自然跳过）
```

- 顺序表的 `base_sequence_no` = 线路上的原始顺序（不变）
- 顺序表的 `sequence_no` = 本次任务中的实际顺序（按 base_sequence 连续排列）
- 加急/重要**不改变 base_sequence**，仅作为标记用于司机端提醒

### 2.4 加急与重要标记

| 标记 | 含义 | 对顺序的影响 | 司机端表现 |
|:-----|:-----|:-----------|:----------|
| **加急** | 商家要求尽快送达 | **不改变顺序**，但高优先级提醒 | 红色高亮 + 语音播报"加急" + 角标置顶提示 |
| **重要** | 重点商家/VIP/大单 | **不改变顺序**，但需特别关照 | 橙色高亮 + 到达时强制拍照/签名确认 |
| 同时标记 | 又加急又重要 | 同上 | 红色+橙色组合提示 |

---

## 三、功能清单

### 模块一：配送线路管理（固定层）

#### 3.1.1 线路基础管理

| 功能 | 说明 | 操作人 |
|:-----|:-----|:-------|
| **线路列表** | 展示所有线路：名称、编码、点位数量、默认司机、默认车辆、状态 | 运营 |
| **新增线路** | 创建新线路：名称、编码、出发仓库、默认司机/车辆、线路颜色 | 运营 |
| **编辑线路** | 修改线路基础信息（名称、司机、车辆、颜色） | 运营 |
| **停用/启用线路** | 停用后不再参与任务生成，但历史保留 | 运营 |
| **删除线路** | 无历史任务时可删除 | 运营 |
| **线路详情** | 查看线路完整信息 + 线路明细（商家列表+地图） | 运营 |

#### 3.1.2 线路明细 — 商家列表排序（核心功能）

| 功能 | 说明 | 操作人 |
|:-----|:-----|:-------|
| **商家列表查看** | 展示某线路的所有商家：顺序号、商家名称、地址、预计停留时间 | 运营 |
| **拖拽排序** | **可视化拖拽**调整商家顺序，拖拽后自动重算所有商家的 `sequence_no` | 运营 |
| **手动修改顺序** | 直接编辑某个商家的顺序号，系统自动重排后续商家 | 运营 |
| **插入商家** | 在指定位置插入新商家，后续商家顺序自动后移 | 运营 |
| **移除商家** | 从线路中移除某商家，后续商家顺序自动前移 | 运营 |
| **商家上移/下移** | 点击按钮将商家向上/向下移动一位 | 运营 |
| **置顶/置底** | 将商家移到第一位/最后一位 | 运营 |
| **批量导入商家** | Excel 导入商家列表（含顺序号），导入后自动排序 | 运营 |
| **商家搜索** | 在线路中搜索某个商家，快速定位 | 运营 |
| **线路地图展示** | 地图上连线展示所有商家位置，验证路线合理性 | 运营 |

#### 3.1.3 线路配送策略配置

| 功能 | 说明 | 操作人 |
|:-----|:-----|:-------|
| **默认出发时间** | 设置该线路的默认出发时间（如 06:00） | 运营 |
| **默认司机绑定** | 设置默认司机 | 运营 |
| **默认车辆绑定** | 设置默认车辆 | 运营 |
| **预计总时长** | 根据各段行驶时间+停留时间自动计算 | 系统 |
| **预计总里程** | 根据商家坐标自动计算 | 系统 |

---

### 模块二：配送任务生成（按需勾选单据）

#### 3.2.1 待配送单据池

| 功能 | 说明 | 操作人 |
|:-----|:-----|:-------|
| **单据池列表** | 展示所有"待配送"状态的单据：送达日期、下单日期、商家、商品、数量、重量 | 运营 |
| **按送达日期筛选** | 筛选指定送达日期的单据 | 运营 |
| **按线路筛选** | 筛选指定线路的单据 | 运营 |
| **按商家筛选** | 筛选指定商家的单据 | 运营 |
| **按日期范围筛选** | 筛选某日期范围内的单据 | 运营 |
| **单据搜索** | 按单据号/商家名搜索 | 运营 |
| **单据状态** | 展示单据当前状态：待配送、已生成任务、配送中、已送达、已取消 | 运营 |

#### 3.2.2 勾选生成配送任务

| 功能 | 说明 | 操作人 |
|:-----|:-----|:-------|
| **勾选单据** | 运营从单据池中**按需勾选**需要配送的单据（支持全选、批量选、跨页选） | 运营 |
| **已勾选预览** | 底部悬浮显示已勾选的单据数量和涉及的商家数 | 系统 |
| **涉及线路分析** | 系统分析勾选的单据涉及哪些线路，各线路有多少单 | 系统 |
| **生成任务预览** | 预览将要生成的任务：每条线路会生成几个任务、每个任务包含哪些商家 | 系统 |
| **指定送达日期** | 为本次生成的任务指定送达日期（默认今天/明天） | 运营 |
| **确认生成** | 确认后，系统按线路分组，每条线路生成一个配送任务 | 运营 |
| **任务生成成功** | 展示生成的任务列表，可进入任务详情查看配送顺序表 | 系统 |

#### 3.2.3 配送任务管理

| 功能 | 说明 | 操作人 |
|:-----|:-----|:-------|
| **任务列表** | 按送达日期、线路、司机、状态查看所有配送任务 | 运营 |
| **任务看板** | 日历视图：每天各线路的任务状态一览 | 运营 |
| **任务详情** | 查看任务的完整信息：送达日期、线路、司机、车辆、单据明细、配送顺序表 | 运营/司机 |
| **任务取消** | 取消未开始的任务，单据回退到"待配送"池 | 运营 |
| **任务分配司机** | 为任务指定/更换司机 | 运营 |
| **任务分配车辆** | 为任务指定/更换车辆 | 运营 |
| **重新生成顺序表** | 任务生成后单据有变化，可重新生成配送顺序表（不影响线路顺序） | 运营 |
| **任务状态跟踪** | 实时查看任务进度：已完成几家、配送中、未开始 | 运营 |

---

### 模块三：配送顺序表管理（动态排序+标记）

#### 3.3.1 配送顺序表生成与查看

| 功能 | 说明 | 操作人 |
|:-----|:-----|:-------|
| **顺序表自动生成** | 任务生成时，系统按线路 `sequence_no` 自动排列有单据的商家 | 系统 |
| **顺序表查看** | 展示配送顺序：序号、商家、地址、单据数量、预计到达时间 | 运营/司机 |
| **顺序表地图展示** | 地图上按顺序连线展示本次配送路线 | 运营/司机 |
| **预计到达时间** | 根据出发时间+各段行驶时间+停留时间，自动计算每家预计到达时间 | 系统 |

#### 3.3.2 加急/重要标记

| 功能 | 说明 | 操作人 |
|:-----|:-----|:-------|
| **标记加急** | 选择顺序表中的某商家，标记为「加急」 | 运营 |
| **取消加急** | 取消某商家的加急标记 | 运营 |
| **标记重要** | 选择顺序表中的某商家，标记为「重要」 | 运营 |
| **取消重要** | 取消某商家的重要标记 | 运营 |
| **批量标记** | 同时选中多家商家，批量标记加急/重要 | 运营 |
| **标记原因** | 标记时填写原因（如"商家投诉过""大单""VIP客户"） | 运营 |
| **标记通知** | 标记后实时推送给司机（App消息+语音播报） | 系统 |

#### 3.3.3 顺序表调整（不改变线路顺序）

| 功能 | 说明 | 操作人 |
|:-----|:-----|:-------|
| **临时移除点位** | 从本次顺序表中移除某商家（该商家单据转其他批次/次日） | 运营 |
| **临时添加点位** | 将某商家的单据临时加入本次任务（从其他批次移过来） | 运营 |
| **顺序表刷新** | 单据有变化时，重新按线路顺序生成顺序表 | 运营 |
| **调整记录** | 查看对顺序表的所有调整操作 | 运营 |

> **重要约束**：以上调整**只影响本次任务的配送顺序表**，**不改变线路上的固定顺序**。下次生成新任务时，仍然按线路原始顺序排列。

---

### 模块四：司机配送执行

#### 3.4.1 任务接收与查看

| 功能 | 说明 | 操作人 |
|:-----|:-----|:-------|
| **今日任务列表** | 司机登录后查看今天分配的所有配送任务 | 司机 |
| **任务卡片** | 显示：线路名、点位数量、送达日期、预计总时长 | 司机 |
| **开始配送** | 点击开始，记录实际开始时间 | 司机 |
| **查看配送顺序表** | 列表形式查看本次配送的所有商家顺序 | 司机 |

#### 3.4.2 地图导航

| 功能 | 说明 | 操作人 |
|:-----|:-----|:-------|
| **线路地图** | 地图上展示本次配送路线，点位按顺序连线 | 司机 |
| **当前点位高亮** | 当前要配送的商家高亮显示 | 系统 |
| **已配送/未配送标识** | 已送达绿色、未送达灰色、进行中蓝色、加急红色、重要橙色 | 系统 |
| **一键导航** | 点击任意未配送商家，调用腾讯地图导航 | 司机 |
| **顺序导航** | 按顺序表自动推荐下一家，一键导航 | 司机 |

#### 3.4.3 配送确认（多种方式）

| 功能 | 说明 | 操作人 |
|:-----|:-----|:-------|
| **到达确认** | 到达商家后点击"到达"，记录到达时间和GPS | 司机 |
| **扫码签收** | 扫描商家收货码确认 | 司机/商家 |
| **拍照签收** | 拍照上传作为配送凭证（重要商家强制要求） | 司机 |
| **签名签收** | 商家电子签名确认 | 司机/商家 |
| **手工确认** | 无码时手动确认 | 司机 |
| **批量确认** | 同一商家多张单据可批量确认 | 司机 |
| **配送备注** | 填写备注（如"放门口""交前台"） | 司机 |
| **跳过该商家** | 商家关门等情况，标记跳过并填原因 | 司机 |

#### 3.4.4 自行调整顺序（司机自主）

| 功能 | 说明 | 操作人 |
|:-----|:-----|:-------|
| **自由跳转** | 司机可以不按顺序表，自行选择先去哪家 | 司机 |
| **返回顺序表** | 自由配送后可以随时回到顺序表继续按序配送 | 司机 |
| **下一站推荐** | 系统根据当前位置推荐最近的未配送商家 | 系统 |
| **加急商家醒目提示** | 加急/重要商家在地图和列表中始终醒目显示，提醒司机优先处理 | 系统 |
| **语音播报** | 新任务分配、加急标记、到达提醒等语音播报 | 系统 |

#### 3.4.5 车辆故障处理

| 功能 | 说明 | 操作人 |
|:-----|:-----|:-------|
| **车辆故障上报** | 拍照+描述上报 | 司机 |
| **任务转移** | 运营后台将任务转给其他司机/车辆 | 运营 |
| **继续配送** | 故障解决后继续当前任务 | 司机 |

---

### 模块五：配送抵达流水

| 功能 | 说明 | 操作人 |
|:-----|:-----|:-------|
| **自动记录到达** | 司机点击"到达"时自动记录：时间、GPS坐标 | 系统 |
| **自动记录离开** | 司机点击"完成"或"前往下一家"时记录离开时间 | 系统 |
| **流水列表** | 按任务/商家/日期查看所有到达/离开/配送记录 | 运营 |
| **流水导出** | 导出 Excel：任务号、商家、到达时间、离开时间、停留时长、GPS坐标 | 运营 |
| **准点率统计** | 统计各商家的预计到达 vs 实际到达偏差 | 系统 |
| **GPS 轨迹回放** | 查看某次任务的完整行驶轨迹（如开启 GPS 记录） | 运营 |

---

## 四、数据库表设计

### 4.1 表总览

| # | 表名 | 说明 | 数据量预估 | 稳定性 |
|:---|:-----|:-----|:-----------|:-------|
| 1 | `delivery_routes` | 配送线路定义 | 10-20 条 | 固定 |
| 2 | `delivery_route_stops` | **线路明细 — 商家列表（支持排序）** | 1,000 条 | 固定 |
| 3 | `delivery_tasks` | 配送任务（运营按需生成） | 10,000+/年 | 动态 |
| 4 | `delivery_task_details` | 配送任务明细（勾选的配送单据） | 200,000+/年 | 动态 |
| 5 | `delivery_task_sequences` | **配送顺序表（按线路顺序，支持标记）** | 200,000+/年 | 动态 |
| 6 | `delivery_arrival_logs` | 抵达时间流水 | 600,000+/年 | 记录 |
| 7 | `vehicles` | 车辆 | 5-10 条 | 固定 |
| 8 | `vehicle_issues` | 车辆故障记录 | 少量 | 记录 |

### 4.2 详细表结构

#### 4.2.1 `delivery_routes` — 配送线路（固定规划）

```sql
CREATE TABLE delivery_routes (
    id                  BIGSERIAL PRIMARY KEY,
    name                VARCHAR(100) NOT NULL COMMENT '线路名称，如：城东1号线',
    code                VARCHAR(50) UNIQUE NOT NULL COMMENT '线路编码，如：E01',
    warehouse_id        BIGINT COMMENT '出发仓库ID',
    default_driver_id   BIGINT COMMENT '默认司机（用户ID）',
    default_vehicle_id  BIGINT COMMENT '默认车辆ID',
    color               VARCHAR(20) DEFAULT '#3B82F6' COMMENT '地图显示颜色',
    departure_time      TIME DEFAULT '06:00:00' COMMENT '默认出发时间',
    estimated_duration  INT COMMENT '预计总时长（分钟）',
    estimated_distance  DECIMAL(8,2) COMMENT '预计总里程（公里）',
    status              SMALLINT DEFAULT 1 COMMENT '状态：0=停用 1=启用',
    remark              VARCHAR(500) COMMENT '备注',
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

COMMENT ON TABLE delivery_routes IS '配送线路定义表：提前规划好的固定配送路线';
CREATE INDEX idx_routes_status ON delivery_routes(status);
```

#### 4.2.2 `delivery_route_stops` — 线路明细（商家列表，核心排序表）

```sql
CREATE TABLE delivery_route_stops (
    id              BIGSERIAL PRIMARY KEY,
    route_id        BIGINT NOT NULL COMMENT '所属线路ID',
    merchant_id     BIGINT NOT NULL COMMENT '商家ID',
    sequence_no     INT NOT NULL COMMENT '【核心】顺序号，拖拽排序即改此字段；1,2,3...连续',
    
    -- 商家配送配置
    address         VARCHAR(255) COMMENT '配送地址（冗余，方便查看）',
    latitude        DECIMAL(10,8) COMMENT '纬度',
    longitude       DECIMAL(11,8) COMMENT '经度',
    default_service_time INT DEFAULT 10 COMMENT '默认停留时间（分钟）',
    
    -- 状态
    is_active       SMALLINT DEFAULT 1 COMMENT '是否启用：0=停用 1=启用；停用后任务生成时跳过',
    remark          VARCHAR(500) COMMENT '备注（如"电梯上楼需预留5分钟"）',
    
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_stop_route FOREIGN KEY (route_id) REFERENCES delivery_routes(id) ON DELETE CASCADE,
    CONSTRAINT fk_stop_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id),
    CONSTRAINT uk_route_sequence UNIQUE (route_id, sequence_no),
    CONSTRAINT uk_route_merchant UNIQUE (route_id, merchant_id)
);

COMMENT ON TABLE delivery_route_stops IS '【核心】线路明细 — 商家列表。拖拽排序通过修改 sequence_no 实现，sequence_no 必须连续（1,2,3...）。';
CREATE INDEX idx_route_stops_route ON delivery_route_stops(route_id);
CREATE INDEX idx_route_stops_sequence ON delivery_route_stops(route_id, sequence_no);
CREATE INDEX idx_route_stops_merchant ON delivery_route_stops(merchant_id);
```

> **拖拽排序实现**：前端拖拽后，将新的顺序数组 `[merchant_id_2, merchant_id_5, merchant_id_1...]` 传给后端，后端批量更新 `sequence_no` 为 `1,2,3...`。

#### 4.2.3 `delivery_tasks` — 配送任务（按需生成）

```sql
CREATE TABLE delivery_tasks (
    id                  BIGSERIAL PRIMARY KEY,
    task_no             VARCHAR(50) UNIQUE NOT NULL COMMENT '任务编号，如：T-E01-20260810-001',
    route_id            BIGINT NOT NULL COMMENT '所属线路ID',
    
    -- 日期信息
    delivery_date       DATE NOT NULL COMMENT '【送达日期】本任务的送达日期',
    generated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '任务生成时间',
    
    -- 分配
    driver_id           BIGINT COMMENT '分配司机ID',
    vehicle_id          BIGINT COMMENT '分配车辆ID',
    
    -- 状态
    status              VARCHAR(20) DEFAULT 'pending' COMMENT '状态：pending=待配送 assigned=已分配 in_progress=配送中 paused=暂停 completed=已完成 cancelled=已取消',
    
    -- 时间
    planned_start_time  TIMESTAMP COMMENT '计划出发时间',
    actual_start_time   TIMESTAMP COMMENT '实际出发时间',
    actual_complete_time TIMESTAMP COMMENT '实际完成时间',
    
    -- 统计
    total_stops         INT DEFAULT 0 COMMENT '总配送商家数',
    completed_stops     INT DEFAULT 0 COMMENT '已完成商家数',
    skipped_stops       INT DEFAULT 0 COMMENT '跳过商家数',
    total_orders        INT DEFAULT 0 COMMENT '关联单据总数',
    
    -- 标记
    has_urgent          SMALLINT DEFAULT 0 COMMENT '是否包含加急：0=否 1=是',
    has_important       SMALLINT DEFAULT 0 COMMENT '是否包含重要：0=否 1=是',
    
    remark              VARCHAR(500) COMMENT '备注',
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_task_route FOREIGN KEY (route_id) REFERENCES delivery_routes(id),
    CONSTRAINT fk_task_driver FOREIGN KEY (driver_id) REFERENCES users(id),
    CONSTRAINT fk_task_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
);

COMMENT ON TABLE delivery_tasks IS '配送任务表：运营按需勾选单据生成，每条线路+每个送达日期可生成多个任务（如上午/下午批次）';
CREATE INDEX idx_tasks_delivery_date ON delivery_tasks(delivery_date);
CREATE INDEX idx_tasks_route_date ON delivery_tasks(route_id, delivery_date);
CREATE INDEX idx_tasks_driver ON delivery_tasks(driver_id);
CREATE INDEX idx_tasks_status ON delivery_tasks(status);
```

#### 4.2.4 `delivery_task_details` — 配送任务明细（勾选的配送单据）

```sql
CREATE TABLE delivery_task_details (
    id                  BIGSERIAL PRIMARY KEY,
    task_id             BIGINT NOT NULL COMMENT '所属配送任务ID',
    order_id            BIGINT COMMENT '关联的原始订单ID（如来自订单系统）',
    
    -- 商家信息（冗余，方便查询）
    merchant_id         BIGINT NOT NULL COMMENT '商家ID',
    merchant_name       VARCHAR(100) COMMENT '商家名称（冗余）',
    merchant_address    VARCHAR(255) COMMENT '配送地址（冗余）',
    
    -- 日期信息
    order_date          DATE COMMENT '【下单日期】原始单据的下单日期',
    delivery_date       DATE NOT NULL COMMENT '【送达日期】要求送达的日期',
    
    -- 商品信息
    product_summary     VARCHAR(500) COMMENT '商品摘要（如"土豆x5kg, 西红柿x3kg"）',
    total_quantity      DECIMAL(10,2) COMMENT '总数量',
    total_weight        DECIMAL(10,2) COMMENT '总重量（kg）',
    
    -- 单据来源
    source_type         VARCHAR(20) DEFAULT 'order' COMMENT '来源类型：order=订单 direct=直配单 merge=合并单',
    source_id           BIGINT COMMENT '来源单据ID',
    
    -- 状态
    status              VARCHAR(20) DEFAULT 'pending' COMMENT '状态：pending=待配送 in_progress=配送中 delivered=已送达 cancelled=已取消',
    
    -- 配送确认
    delivered_at        TIMESTAMP COMMENT '实际送达时间',
    delivery_method     VARCHAR(20) COMMENT '配送方式：manual=手工 scan=扫码 photo=拍照 signature=签名',
    delivery_photos     JSONB COMMENT '配送照片[{url, taken_at}]',
    delivery_remark     VARCHAR(500) COMMENT '配送备注',
    
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_detail_task FOREIGN KEY (task_id) REFERENCES delivery_tasks(id) ON DELETE CASCADE,
    CONSTRAINT fk_detail_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id)
);

COMMENT ON TABLE delivery_task_details IS '配送任务明细：运营从单据池勾选的每张单据，包含送达日期、下单日期等关键信息';
CREATE INDEX idx_task_details_task ON delivery_task_details(task_id);
CREATE INDEX idx_task_details_merchant ON delivery_task_details(merchant_id);
CREATE INDEX idx_task_details_delivery_date ON delivery_task_details(delivery_date);
CREATE INDEX idx_task_details_order_date ON delivery_task_details(order_date);
CREATE INDEX idx_task_details_status ON delivery_task_details(status);
```

#### 4.2.5 `delivery_task_sequences` — 配送顺序表（核心：按线路顺序 + 标记）

```sql
CREATE TABLE delivery_task_sequences (
    id                  BIGSERIAL PRIMARY KEY,
    task_id             BIGINT NOT NULL COMMENT '所属配送任务ID',
    task_detail_ids     JSONB NOT NULL COMMENT '【本商家在本任务中的所有明细ID数组】如 [12, 15, 18]',
    
    -- 商家信息
    merchant_id         BIGINT NOT NULL COMMENT '商家ID',
    merchant_name       VARCHAR(100) COMMENT '商家名称（冗余）',
    merchant_address    VARCHAR(255) COMMENT '地址（冗余）',
    latitude            DECIMAL(10,8) COMMENT '纬度',
    longitude           DECIMAL(11,8) COMMENT '经度',
    
    -- 【核心】顺序控制
    base_sequence_no    INT NOT NULL COMMENT '【来自线路的原始顺序号】= delivery_route_stops.sequence_no，永远不变',
    sequence_no         INT NOT NULL COMMENT '【本次任务中的实际顺序号】按 base_sequence_no 升序连续排列（1,2,3...）',
    
    -- 预计时间
    estimated_arrival   TIMESTAMP COMMENT '预计到达时间',
    estimated_departure TIMESTAMP COMMENT '预计离开时间',
    
    -- 实际时间
    actual_arrival      TIMESTAMP COMMENT '实际到达时间',
    actual_departure    TIMESTAMP COMMENT '实际离开时间',
    actual_delivered_at TIMESTAMP COMMENT '实际送达/签收时间',
    
    -- 【核心】加急/重要标记（不改变顺序，仅用于提醒）
    is_urgent           SMALLINT DEFAULT 0 COMMENT '是否加急：0=否 1=是',
    urgent_reason       VARCHAR(255) COMMENT '加急原因',
    is_important        SMALLINT DEFAULT 0 COMMENT '是否重要：0=否 1=是',
    important_reason    VARCHAR(255) COMMENT '重要原因',
    
    -- 状态
    status              VARCHAR(20) DEFAULT 'pending' COMMENT '状态：pending=待配送 in_progress=配送中 arrived=已到达 delivered=已送达 skipped=已跳过 failed=失败',
    
    -- 配送确认信息
    delivery_method     VARCHAR(20) COMMENT '确认方式',
    delivery_photos     JSONB COMMENT '配送照片',
    signature_image     VARCHAR(500) COMMENT '签名图片URL',
    gps_latitude        DECIMAL(10,8) COMMENT '送达时纬度',
    gps_longitude       DECIMAL(11,8) COMMENT '送达时经度',
    
    skip_reason         VARCHAR(255) COMMENT '跳过原因',
    fail_reason         VARCHAR(255) COMMENT '失败原因',
    remark              VARCHAR(500),
    
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_seq_task FOREIGN KEY (task_id) REFERENCES delivery_tasks(id) ON DELETE CASCADE,
    CONSTRAINT fk_seq_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id)
);

COMMENT ON TABLE delivery_task_sequences IS '【核心】配送顺序表：按线路 sequence_no 自动生成，有单据的商家才出现。加急/重要标记不改变顺序，仅用于司机端提醒。';
CREATE INDEX idx_sequences_task ON delivery_task_sequences(task_id);
CREATE INDEX idx_sequences_task_seq ON delivery_task_sequences(task_id, sequence_no);
CREATE INDEX idx_sequences_base_seq ON delivery_task_sequences(task_id, base_sequence_no);
CREATE INDEX idx_sequences_status ON delivery_task_sequences(status);
CREATE INDEX idx_sequences_urgent ON delivery_task_sequences(is_urgent);
CREATE INDEX idx_sequences_important ON delivery_task_sequences(is_important);
```

#### 4.2.6 `delivery_arrival_logs` — 抵达时间流水

```sql
CREATE TABLE delivery_arrival_logs (
    id              BIGSERIAL PRIMARY KEY,
    task_id         BIGINT NOT NULL COMMENT '配送任务ID',
    sequence_id     BIGINT COMMENT '关联的配送顺序表ID',
    merchant_id     BIGINT NOT NULL COMMENT '商家ID',
    
    -- 事件
    event_type      VARCHAR(30) NOT NULL COMMENT '事件类型：arrival=到达 departure=离开 delivered=送达 skipped=跳过 gps_enter=进入围栏 gps_leave=离开围栏',
    event_time      TIMESTAMP NOT NULL COMMENT '事件发生时间',
    
    -- GPS
    gps_latitude    DECIMAL(10,8) COMMENT '纬度',
    gps_longitude   DECIMAL(11,8) COMMENT '经度',
    gps_accuracy    DECIMAL(8,2) COMMENT '精度（米）',
    
    -- 来源
    source          VARCHAR(20) DEFAULT 'driver' COMMENT '来源：driver=司机 gps_auto=自动 system=系统 admin=后台',
    operator_id     BIGINT COMMENT '操作人ID',
    
    extra_data      JSONB COMMENT '额外数据',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_log_task FOREIGN KEY (task_id) REFERENCES delivery_tasks(id),
    CONSTRAINT fk_log_sequence FOREIGN KEY (sequence_id) REFERENCES delivery_task_sequences(id)
);

COMMENT ON TABLE delivery_arrival_logs IS '配送抵达时间流水：每次到达、离开、送达的不可变记录';
CREATE INDEX idx_logs_task ON delivery_arrival_logs(task_id);
CREATE INDEX idx_logs_merchant ON delivery_arrival_logs(merchant_id);
CREATE INDEX idx_logs_event_time ON delivery_arrival_logs(event_time);
```

#### 4.2.7 `vehicles` / `vehicle_issues` — 车辆相关（同 v1 版，略）

```sql
-- 车辆表（同 v1，保持不变）
CREATE TABLE vehicles (
    id                  BIGSERIAL PRIMARY KEY,
    plate_number        VARCHAR(20) UNIQUE NOT NULL COMMENT '车牌号',
    name                VARCHAR(50) COMMENT '车辆名称',
    type                VARCHAR(20) DEFAULT 'van' COMMENT '类型：van=厢式货车 truck=卡车 refrigerated=冷藏车 motorcycle=三轮摩托车',
    capacity_kg         DECIMAL(10,2) COMMENT '载重（公斤）',
    capacity_volume     DECIMAL(8,2) COMMENT '容积（立方米）',
    status              VARCHAR(20) DEFAULT 'active' COMMENT '状态：active=可用 maintenance=维修中 retired=报废',
    last_maintenance_date DATE COMMENT '上次保养日期',
    next_maintenance_date DATE COMMENT '下次保养日期',
    remark              VARCHAR(500),
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 车辆故障表（同 v1，保持不变）
CREATE TABLE vehicle_issues (
    id              BIGSERIAL PRIMARY KEY,
    vehicle_id      BIGINT NOT NULL,
    task_id         BIGINT COMMENT '关联任务ID',
    issue_type      VARCHAR(30) COMMENT '故障类型：breakdown=抛锚 accident=事故 tire=轮胎 battery=电瓶 engine=发动机 other=其他',
    description     TEXT NOT NULL,
    photos          JSONB COMMENT '故障照片',
    reported_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reported_by     BIGINT COMMENT '上报人ID',
    resolved_at     TIMESTAMP COMMENT '解决时间',
    resolved_by     BIGINT COMMENT '处理人ID',
    impact_type     VARCHAR(20) COMMENT '影响类型',
    impact_desc     VARCHAR(500),
    status          VARCHAR(20) DEFAULT 'open' COMMENT '状态：open=处理中 resolved=已解决 closed=已关闭',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_issue_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    CONSTRAINT fk_issue_task FOREIGN KEY (task_id) REFERENCES delivery_tasks(id)
);
```

---

## 五、核心业务流程

### 5.1 线路规划流程（一次性/季度调整）

```
运营进入线路管理
    │
    ▼
┌─────────────────┐
│ 1. 新增/选择线路 │
│ 2. 设置基础信息  │
│    · 名称/编码   │
│    · 默认司机    │
│    · 默认车辆    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ 3. 添加商家到线路│
│    · 搜索商家    │
│    · 逐个添加    │
│    · 或批量导入  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐     ┌─────────────────┐
│ 4. 拖拽排序     │◀───│ 也可手动输入    │
│    · 拖拽商家   │     │    sequence_no  │
│    · 自动重算   │     │    系统重排     │
│      所有顺序   │     │                 │
└────────┬────────┘     └─────────────────┘
         │
         ▼
┌─────────────────┐
│ 5. 地图验证     │
│    · 查看连线   │
│    · 确认合理   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ 6. 保存线路     │
│    · 生成固定的 │
│      sequence_no│
└─────────────────┘
```

### 5.2 配送任务生成流程（按需勾选）

```
运营进入"生成配送任务"页面
    │
    ▼
┌─────────────────────────┐
│ 1. 筛选待配送单据        │
│    · 按送达日期筛选      │
│    · 按线路筛选          │
│    · 按商家筛选          │
│    · 按日期范围筛选      │
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐
│ 2. 单据池展示            │
│    · 列表展示所有         │
│      符合条件的单据       │
│    · 显示：送达日期、下单 │
│      日期、商家、商品、   │
│      数量                 │
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐
│ 3. 运营按需勾选单据      │
│    · 支持单选/多选/全选  │
│    · 支持跨页勾选        │
│    · 底部显示已勾选数    │
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐
│ 4. 系统分析涉及线路      │
│    · 线路A：8单          │
│    · 线路B：5单          │
│    · 线路C：0单（跳过）  │
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐
│ 5. 运营指定送达日期      │
│    · 默认明天            │
│    · 可修改              │
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐
│ 6. 生成预览              │
│    · 预览每条线路的       │
│      配送顺序表           │
│    · 预览预计到达时间     │
│    · 预览总时长/里程      │
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐
│ 7. 确认生成              │
│    · 按线路生成N个任务   │
│    · 每个任务含顺序表     │
│    · 单据状态变为         │
│      "已生成任务"         │
└─────────────────────────┘
```

### 5.3 配送顺序表生成逻辑（系统内部）

```php
/**
 * 根据任务中的单据，按线路顺序生成配送顺序表
 */
function generateSequence(int $taskId): void
{
    $task = DeliveryTask::find($taskId);
    $routeId = $task->route_id;
    
    // 1. 获取线路上的所有商家（按 sequence_no 排序）
    $routeStops = DeliveryRouteStop::where('route_id', $routeId)
        ->where('is_active', 1)
        ->orderBy('sequence_no')
        ->get();
    
    // 2. 获取本次任务中有单据的所有商家
    $merchantsInTask = DeliveryTaskDetail::where('task_id', $taskId)
        ->where('status', 'pending')
        ->pluck('merchant_id')
        ->unique()
        ->toArray();
    
    // 3. 按线路顺序生成顺序表（只包含有单据的商家）
    $sequenceNo = 1;
    foreach ($routeStops as $stop) {
        if (!in_array($stop->merchant_id, $merchantsInTask)) {
            continue; // 无单据 → 自然跳过
        }
        
        // 收集该商家的所有明细ID
        $detailIds = DeliveryTaskDetail::where('task_id', $taskId)
            ->where('merchant_id', $stop->merchant_id)
            ->where('status', 'pending')
            ->pluck('id')
            ->toArray();
        
        // 创建顺序表记录
        DeliveryTaskSequence::create([
            'task_id' => $taskId,
            'task_detail_ids' => json_encode($detailIds),
            'merchant_id' => $stop->merchant_id,
            'base_sequence_no' => $stop->sequence_no, // ← 线路原始顺序，永远不变
            'sequence_no' => $sequenceNo,              // ← 本次任务中的连续顺序
            'merchant_name' => $stop->merchant->name,
            'merchant_address' => $stop->address,
            'latitude' => $stop->latitude,
            'longitude' => $stop->longitude,
            'status' => 'pending',
        ]);
        
        $sequenceNo++;
    }
}
```

### 5.4 加急/重要标记流程

```
运营查看配送顺序表
    │
    ▼
选择某商家 ──┬──▶ 点击"标记加急" ──▶ 填写加急原因 ──▶ 保存
            │                                               │
            ├──▶ 点击"标记重要" ──▶ 填写重要原因 ──▶ 保存   │
            │                                               │
            └──▶ 点击"同时标记" ──▶ 填写原因 ──▶ 保存 ◀────┘
                                                            │
                                                            ▼
                                                    ┌───────────────┐
                                                    │ 1. 更新数据库  │
                                                    │    is_urgent=1 │
                                                    │    或/和       │
                                                    │    is_important=1
                                                    └───────┬───────┘
                                                            │
                                                            ▼
                                                    ┌───────────────┐
                                                    │ 2. 实时推送    │
                                                    │    · App通知   │
                                                    │    · 语音播报  │
                                                    │      "XX商家已 │
                                                    │       标记加急"│
                                                    └───────┬───────┘
                                                            │
                                                            ▼
                                                    ┌───────────────┐
                                                    │ 3. 司机端更新  │
                                                    │    · 列表置顶提示
                                                    │    · 地图红色/ │
                                                    │      橙色高亮  │
                                                    │    · 到达时强制│
                                                    │      确认      │
                                                    └───────────────┘
```

### 5.5 司机配送执行流程

```
司机打开小程序
    │
    ▼
查看今日任务列表
    │
    ▼
选择任务 → 查看配送顺序表
    │
    ▼
点击"开始配送"
    │
    ▼
┌─────────────────────────────────────────────────┐
│ 标准流程（按顺序表）：                            │
│                                                  │
│  ① 导航到第一家 ──▶ 到达确认 ──▶ 卸货签收       │
│       │                                          │
│       ▼                                          │
│  ② 导航到下一家 ──▶ 到达确认 ──▶ 卸货签收       │
│       │                                          │
│       ▼                                          │
│  ...（循环直到全部完成）                         │
│                                                  │
│ 特殊情况（自行调整）：                           │
│  · 看到加急商家可自由跳转优先配送                │
│  · 某商家关门可标记"跳过"                       │
│  · 系统推荐最近未配送商家                        │
└─────────────────────────────────────────────────┘
    │
    ▼
全部完成 → 点击"完成任务"
```

---

## 六、关键设计决策说明

### 6.1 为什么任务要"按需勾选"而不是"按日期自动生成"？

| 维度 | 按需勾选 | 按日期自动生成 |
|:-----|:---------|:-------------|
| **灵活性** | ⭐⭐⭐⭐⭐ 运营完全掌控配送节奏 | ⭐⭐ 每天固定生成，不够灵活 |
| **异常处理** | 临时合并/拆分单据方便 | 需事后调整，容易遗漏 |
| **车辆调度** | 根据勾选总量灵活分配车辆 | 可能某车太少、某车太多 |
| **批次控制** | 上午一批、下午一批，灵活分批 | 一天一次或固定几次 |
| **单据回退** | 未勾选的单据仍在池中，下次继续 | 已生成任务需手动取消 |
| **业务场景** | 更符合"客户每天300+，常用600-700"的实际节奏 | 适合标准化程度极高的场景 |

### 6.2 为什么加急/重要不改变线路顺序？

| 方案 | 优点 | 缺点 |
|:-----|:-----|:-----|
| **不改变顺序（当前方案）** | 线路顺序稳定可预测；司机熟悉路线；加急只是提醒不是强制；特殊情况司机自行处理 | 加急商家如果排在最后，仍需跑完前面商家 |
| **改变顺序（加急置顶）** | 加急商家优先送达 | 打乱优化好的线路顺序，可能增加总里程；频繁变动司机不适应；和"线路固定化"原则冲突 |

**结论**：
- 线路是长期优化出来的最优路径，不宜频繁变动
- 加急/重要是**提醒机制**，不是**强制重排**
- 司机看到加急标记后可以**自行决定**是否优先处理（特殊情况自行过去）
- 真正需要改变顺序的，运营可以手动调整本次任务的顺序表（临时移除/添加），但线路本身不变

### 6.3 base_sequence_no 与 sequence_no 的区别

| 字段 | 含义 | 来源 | 是否可变 |
|:-----|:-----|:-----|:---------|
| `base_sequence_no` | 商家在线路中的**原始固定顺序** | `delivery_route_stops.sequence_no` | **永不改变** |
| `sequence_no` | 本次任务中的**实际配送顺序** | 按 base_sequence_no 升序排列后的连续序号 | 仅本次任务有效 |

**示例**：

```
线路固定顺序：
  甲(1) → 乙(2) → 丙(3) → 丁(4) → 戊(5) → 己(6) → 庚(7)

今天任务中有单据的商家：乙、丁、戊、庚

生成的顺序表：
  ┌────────┬─────────────────┬─────────────┐
  │ seq_no │ base_seq_no     │ 商家        │
  ├────────┼─────────────────┼─────────────┤
  │   1    │       2         │ 乙          │
  │   2    │       4         │ 丁 ← 加急   │
  │   3    │       5         │ 戊          │
  │   4    │       7         │ 庚          │
  └────────┴─────────────────┴─────────────┘

注：base_seq_no 永远来自线路，不变；
    seq_no 是本次任务中的连续序号（1,2,3,4）
```

---

## 七、接口规划（概要）

### 7.1 管理后台 — 线路管理

| 接口 | 方法 | 说明 |
|:-----|:-----|:-----|
| `GET /api/admin/routes` | 线路列表 | |
| `POST /api/admin/routes` | 新增线路 | |
| `PUT /api/admin/routes/{id}` | 编辑线路 | |
| `GET /api/admin/routes/{id}/stops` | 线路明细 — 商家列表 | 含当前顺序 |
| `POST /api/admin/routes/{id}/stops` | 添加商家到线路 | |
| `DELETE /api/admin/routes/{id}/stops/{stopId}` | 移除商家 | |
| `POST /api/admin/routes/{id}/stops/reorder` | **拖拽排序** | 传入新的顺序数组 |
| `PUT /api/admin/routes/{id}/stops/{stopId}/sequence` | **手动修改顺序号** | |
| `POST /api/admin/routes/{id}/stops/import` | 批量导入商家 | Excel |

### 7.2 管理后台 — 配送任务生成

| 接口 | 方法 | 说明 |
|:-----|:-----|:-----|
| `GET /api/admin/delivery-documents` | 待配送单据池 | 支持送达日期/线路/商家筛选 |
| `POST /api/admin/tasks/generate` | **生成配送任务** | 传入勾选的单据ID数组 + 送达日期 |
| `GET /api/admin/tasks` | 任务列表 | 按送达日期/线路/司机/状态筛选 |
| `GET /api/admin/tasks/{id}` | 任务详情 | 含顺序表 |
| `DELETE /api/admin/tasks/{id}` | 取消任务 | 单据回退到待配送池 |
| `PUT /api/admin/tasks/{id}/driver` | 分配/更换司机 | |
| `PUT /api/admin/tasks/{id}/vehicle` | 分配/更换车辆 | |

### 7.3 管理后台 — 配送顺序表

| 接口 | 方法 | 说明 |
|:-----|:-----|:-----|
| `GET /api/admin/tasks/{id}/sequences` | 配送顺序表 | |
| `PUT /api/admin/sequences/{id}/urgent` | **标记/取消加急** | |
| `PUT /api/admin/sequences/{id}/important` | **标记/取消重要** | |
| `POST /api/admin/sequences/{id}/remove` | 临时移除点位 | 仅本次任务 |
| `POST /api/admin/tasks/{id}/sequences/refresh` | 重新生成顺序表 | |

### 7.4 司机端小程序

| 接口 | 方法 | 说明 |
|:-----|:-----|:-----|
| `GET /api/driver/tasks/today` | 今日任务 | |
| `GET /api/driver/tasks/{id}` | 任务详情 | 含顺序表+地图数据 |
| `GET /api/driver/tasks/{id}/sequences` | 配送顺序表 | 司机端专用 |
| `POST /api/driver/tasks/{id}/start` | 开始配送 | |
| `POST /api/driver/sequences/{id}/arrive` | 到达确认 | 含GPS |
| `POST /api/driver/sequences/{id}/deliver` | 配送完成 | 含方式+照片+签名 |
| `POST /api/driver/sequences/{id}/skip` | 跳过 | 需填原因 |
| `GET /api/driver/tasks/{id}/route-map` | 地图数据 | |
| `POST /api/driver/vehicle-issue` | 车辆故障上报 | |

---

## 八、数据归档策略

| 数据类型 | 保留周期 | 归档方式 |
|:---------|:---------|:---------|
| 配送任务（tasks） | 2年 | 2年后转存归档表 |
| 配送顺序表（sequences） | 2年 | 随任务一同归档 |
| 任务明细（details） | 2年 | 随任务一同归档 |
| 抵达流水（arrival_logs） | 1年热数据 | 1年后压缩归档到对象存储，仅保留汇总统计 |
| 线路定义（routes/stops） | 永久 | 主表保留 |

---

> **文档版本**: v2.0（修订版）
> **核心变更**: 任务生成从"按日期自动生成"改为"运营按需勾选单据生成"；线路顺序固定，顺序表按线路顺序自动生成；加急/重要为标记提醒，不改变顺序
> **适用模块**: 配送线路管理、配送任务生成与调度、司机配送执行
> **关联文档**: PRD 配送模块、03_DB 数据库设计、04_API 接口文档
