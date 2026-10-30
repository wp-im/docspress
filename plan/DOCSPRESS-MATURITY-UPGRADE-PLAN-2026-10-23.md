# Docspress 完整升级计划：双侧栏、移动阅读与长期资料库

方案日期：2026-10-23（Asia/Tokyo）；实施验收：2026-10-24。状态：**已实施并部署 0.9.1；本轮不提交/推送 GitHub，不提交 WordPress.org。** 逐条验收见 [ACCEPTANCE.md](../docs/audits/2026-10-23-upgrade/ACCEPTANCE.md)。

初始基线：本地和线上主题 `0.2.4`。方案制定时重看源码、线上首页/章节/文档/普通单篇/Notes，并测量文档页十档宽度；初始观察、截图和局限见 [基线审查](../docs/audits/2026-10-23/REVIEW.md)。原始设计保存在 `reference/`，继续保持不变。以下问题表记录升级前发现，不作为0.9.1未解决清单。

用户确定的硬约束：**缺省所有页面都有 leftbar 和 rightbar**，支持原生 Widgets 配置；窄屏分别从左、右滑出；手机左抽屉外宽不超过视口 70%，右抽屉不超过 90%。**顶部菜单是第三套独立导航，窄屏有自己的菜单按钮；文章 TOC 属于文章内部，不放入 rightbar。** 优先优化手机单篇文章，支持长期资料库，并重点覆盖登录后 WordPress toolbar。

这段产品约定优先于旧设计文档及本计划早期草稿。普通 Page、搜索、404、归档也不例外；不能因模板不同或尚未配置 widgets 就默认取消某一侧栏。

本次交付包括项目内方案、主题实现、生产演示站、设计规范、安装/回退包和验收证据。既有 `docs/qa-report.md`、`plan/PLAN.md` 的历史和用户未提交修正保留；后续 GitHub/目录发布仍需独立授权。

## 1. 产品方向与设计判断

Docspress 应成为“可以持续积累、找到、引用、重读资料”的 Classic Theme。主价值是稳定的信息结构与舒适的阅读，而不是更多可见控件。

保留白底、浅灰导航区、少量橙色、清晰标题、原生 Pages 层级，以及宽屏 editorial rail 的识别度。三项改变贯穿本轮：

1. **双侧栏是全站默认。** 左侧负责资料/栏目导航，右侧负责用户配置的辅助资料与 widgets；Docs、普通文章、归档、搜索和 404 共用此结构。
2. **先分配正文，再分配导航。** 可读宽度不足时先收起外侧栏和内侧 rail，正文不为保住三栏而缩小。
3. **全站菜单和文章目录各归其位。** 顶部菜单独立于左栏；TOC 在文章内部，不能充当 rightbar。三种用途不混用按钮或内容区域。
4. **登录态是一等布局。** toolbar、header、侧栏控制条、抽屉、锚点共用顶部几何规则，不能逐页补 top 值。

“main 与正文一致”解释为：同类页面共享内容轴线、标题起点、段落宽度和底部边界；首页目录、媒体、表格可以有明确的 wider 变体。文章的内侧 rail、站点左栏、正文列是三个不同概念，不能混用宽度。

## 2. 当前优先解决的问题

| 优先级 | 已确认问题或待验证风险 | 第一处理入口 |
| --- | --- | --- |
| P0 | fixed 抽屉被 `main` 的 stacking context 困住，关闭按钮被 header/local bar 遮挡 | shell DOM 与 `main.css`，不是继续增加 z-index |
| P0 | toolbar 没有统一偏移处理；真实登录态尚未实测 | 顶部几何、drawer host、登录测试环境 |
| P0 | 左抽屉 390px 下约 84%，超过 70% | drawer 尺寸 token 与 box-sizing |
| P1 | 981–1280px 丢失页内目录入口，现有 right-toc 被误当作右栏 | TOC 迁入文章内部；全站 rightbar 单独实现 |
| P1 | Widgets 仅有 `sidebar-1`；普通文章手机右栏变成文后堆叠 | `inc/widgets.php`、`sidebar.php`、`single.php` |
| P1 | 链接橙色、部分 metadata 对比度不足；关闭按钮命中区过小 | 颜色角色、按钮 hit area、真实 block widgets 样式 |
| P1 | editorial rail 切换让正文宽度突变；普通文章与文档排版分裂 | 阅读 tokens 与响应式宽度预算 |
| P1 | 模态语义、背景 inert、可见焦点筛选、无 JS 可达性不完整 | `navigation.js`、面板模板、渐进增强 |
| P2 | 大资料树重复构建、锚点耐久性、搜索上下文、打印和编辑器验收不足 | `inc/docs.php`、`inc/toc.php`、标准模板与测试 |

P0 表示基础交互或遮挡阻塞，先于新增功能。具体数值、命中检测和截图在审查文档，不把历史“测试通过”沿用为本轮结论。

## 3. 统一 shell 与侧栏职责

### 3.1 页面组合

| 页面 | leftbar（默认有） | 中央内容 | rightbar（默认有） |
| --- | --- | --- | --- |
| 资料库首页 | 全站 Docs Tree/左栏菜单 + 左侧 widgets | 简介、搜索、章节索引 | 全站右侧 widgets 或默认辅助内容 |
| 章节索引 | 同上，展开当前路径 | 章节简介、子目录 | 同一右侧区域 |
| 文档 Page | 同上，标明当前条目 | 文档标题、独立篇内 TOC、正文、媒体、前后篇 | 同一右侧区域；**不含本篇 TOC** |
| 普通单篇 Post | 同一全站左栏 | 标题、独立篇内 TOC、正文、文章导航、评论 | 同一右侧区域；**不含本篇 TOC** |
| 普通 Page | 同一全站左栏 | 原生内容、适用时篇内 TOC；不自动套 Docs rail | 同一右侧区域 |
| Notes、分类、标签、作者、日期归档 | 同一全站左栏 | 原生查询、摘要、分页 | 同一右侧区域 |
| 搜索、无结果、404 | 同一全站左栏，提供资料入口 | 搜索词、结果上下文、恢复入口 | 同一右侧区域 |

所有页面默认相同 shell；宽屏三列，窄屏为正文加两个侧栏按钮。空 widget 区使用安全且可覆盖的默认导航/辅助内容，不自动隐藏侧栏。只有站长显式选择隐藏时才改变该默认行为，不能用“auto”暗中取消。

全站顶部菜单始终独立；它在宽屏为 header 菜单，在窄屏为 header 的“菜单”按钮及其展开区域。文章 TOC 仅在单篇内容内出现，不属于以上两个侧栏。

对应关系的简图：

```text
宽屏
┌ WordPress toolbar（实际显示时）────────────────────┐
├ 品牌                         独立顶部菜单          ┤
├ leftbar ────┬ main / article ─────────┬ rightbar ──┤
│ 资料导航    │ 标题、摘要、metadata    │ widgets    │
│ 左侧widgets │ 本篇目录（文章内）     │ 辅助资料   │
│             │ 正文、引用、前后篇     │ 无文章TOC  │
└─────────────┴────────────────────────┴────────────┘

窄屏
┌ WordPress toolbar（按核心规则显示/滚走）───────────┐
├ 品牌                                  [菜单]     ┤
├ [资料导航]                            [侧栏]     ┤
│ 标题、摘要、metadata                              │
│ [本篇目录 ▾] ← 在文章里展开                        │
│ 正文、图片、引用                                  │
└───────────────────────────────────────────────────┘
左侧按钮 → 从左滑出，≤70% 宽
右侧按钮 → 从右滑出，≤90% 宽
菜单按钮 → 独立顶部菜单面板
```

### 3.2 DOM 与层叠方案

优先在现有 PHP 模板和 `get_template_part()` 中整理 shell，建立下列结构职责：

```text
body
├─ WordPress toolbar（由核心输出）
├─ site header（品牌 + 独立顶部菜单/菜单按钮）
├─ sidebar controls（窄屏：左导航按钮 / 右侧栏按钮）
├─ site shell（不制造困住 fixed 面板的 stacking context）
│  ├─ primary navigation panel（desktop aside / narrow left drawer）
│  ├─ main#primary（文章、索引或列表；文章内部拥有自己的 TOC）
│  └─ widget sidebar panel（desktop aside / narrow right drawer；无文章 TOC）
├─ site footer
└─ shared backdrop / 必要的 root-level overlay host
```

方案要点：

- 左右内容面板各只有一份实际 DOM；通过 CSS 从常驻列切换为 off-canvas，不复制 `dynamic_sidebar()` 结果。
- 抽屉和 backdrop 不处于 `isolation`、transform、filter、contain 等可能改变层叠/定位关系的祖先内。
- 背景绘制交给受 shell 边界约束的列背景或装饰层，保留 0.2.4 已解决的 footer 边界；不恢复 viewport-fixed 背景。
- `main` 只承担主要内容语义；主题导航作为 sibling，skip link 有唯一明确目标。
- 普通文章和 Docs 各保留内容模板，统一 shell 参数和面板入口；不把全站改为前端路由应用。
- 顶部菜单的展开区域归 header；篇内 TOC 归 article。二者不作为 leftbar/rightbar 的内容来源。
- 优先用普通 aside + 窄屏模态语义，接续既有 JS。仅当原型证明原生 `<dialog>` 明显更稳，且 toolbar/桌面语义均通过时才替换机制。
- `get_sidebar()`、命名 sidebar 模板、标准 hooks 与子主题覆盖能力继续保留。

验收：关闭按钮可见、可点击、可聚焦；footer 与所有侧栏背景在短文和长文都无穿越；页面中没有重复 widget/form ID。

## 4. Widgets、菜单与 Customizer

### 4.1 最小原生配置模型

| 位置 | ID/来源 | 默认内容与顺序 | 升级兼容 |
| --- | --- | --- | --- |
| Leftbar Navigation | 新增主题菜单位置 `docspress-primary-sidebar` | 全站左栏独立菜单，可与 Docs Tree 组合；无配置走默认公开资料导航 | 与 header 的 `primary` 独立，不复制或改写用户菜单 |
| Left Sidebar Widgets | 新增 `docspress-left-sidebar` | 全站 Docs Tree/左栏菜单之后显示；整体左栏始终有默认内容 | 可放原生搜索、导航菜单、文本、分类等 widgets |
| Right Sidebar Widgets（全站右栏） | **保留 `sidebar-1`**，后台说明由 Blog Sidebar 扩展为全站右栏 | 所有页面显示用户现有 widgets；无配置时显示默认辅助内容；**不放文章 TOC** | 不改 ID，不把现有配置移入 inactive widgets |
| Footer Widgets | 新增 `docspress-footer` | 可选，独立于法律/版权行；可响应式一至三列 | 空时保持当前简洁 footer |

这是 3 个 widget 区域（1 个保留、2 个新增）。全站先使用同一右栏配置，不增加 Docs 专用右栏或文章 TOC widget。

默认内容规则：左栏优先有效 Docs Root 的资料树与可选独立左栏菜单；未配置时回退公开 Pages 导航及站点搜索。右栏优先 `sidebar-1` 现有实例；空时可用核心最近文章/分类等实际资料与站点信息、feed 入口组合。没有文章时显示站点信息与有效入口，不输出虚构文章或死链接。fallback 只在模板内展示，不自动往数据库塞入 widget 实例；用户一旦配置该区域，即以其配置为准。页数多时使用分支/索引策略，不把全树无限铺开。

Docs Tree 和篇内 TOC 是主题展示模块；TOC 的开关和标题深度在阅读设置中管理，不要求安装自定义 widget。

Widgets 使用 `register_sidebar()`、`dynamic_sidebar()`、`is_active_sidebar()`，在“外观 → 小工具”和可用的 Customizer 中管理；这沿用 [WordPress Sidebar API](https://developer.wordpress.org/themes/classic-themes/functionality/sidebars/)。

### 4.2 配置项

延续 `inc/customizer.php` 的 theme mods，组织为内容导航、侧栏、阅读、站点外观四组；使用核心 controls，并提供可解释的默认值：

- 保留 Docs Root、Footer Text 和现有 Logo/Menu 配置。
- 全站布局缺省“双侧栏”，适用于全部模板；左右栏只允许站长显式关闭，不按页面类型或 widget 是否为空自动关闭。
- 顶部主菜单沿用 `primary` 菜单位置；左栏如需人工导航使用单独菜单位置，两个菜单独立配置。
- **篇内 TOC**：关闭、H2、H2+H3；建议默认 H2+H3，至少 2 个有效标题时显示；位置在该篇标题/摘要/metadata 之后、正文之前；不进入 rightbar。
- 阅读宽度：舒适（默认）/紧凑两档；正文基本字号：标准/大字两档，均使用 rem。
- 更新日期显示开关、默认段落/标题样式可预览；不增加任意 CSS 输入框。
- 左右抽屉上限固定由设计 contract 管理；后台不提供可突破 70%/90% 的滑杆。
- 手机折叠是自动响应式行为，不再做每设备开关；“站点菜单”“资料导航”“侧栏”“本篇目录”分别命名并可翻译。

初版不做按角色/每文章的自定义布局控制台，不做任意模块拖拽 builder。真正跨主题持久的文档字段留给标准内容或独立插件。

### 4.3 Widget 样式覆盖范围

- 同时处理传统 `.widget-title` 与实际 `.widget_block`、`.wp-block-heading` 等 markup；只在 widget 区域内约束，不能改正文 H2。
- 核心 Search、Navigation Menu、Pages、Categories、Archives、Recent Posts、Recent Comments、Calendar、Text、Image、Custom HTML 及对应 block widgets 都入矩阵。
- 搜索 input/button 统一高度、focus、边框和字号；标题 14–16px、内容 14–16px，手机不把帮助信息缩成小字。
- 长链接折行；图片自然比例；select/表单、第三方 widget 宽度不挤出抽屉。
- 不删用户已有的空评论 widget 或 Uncategorized 数据；可以在演示站维护建议中说明更合适的配置。
- Classic Theme 不等于禁用 Block Editor。兼容 Classic Editor、Classic Widgets 插件以及核心块小工具编辑器；不全局强制关闭任何一种编辑体验。

## 5. 自适应与双向滑出规则

### 5.1 断点预算

下面是实施起点，最终按真实内容/字体与整数宽度边界验证；toolbar 的 782/600px 断点另行处理，不混入主题布局断点。

| 布局范围 | 左栏 | 正文与内侧 rail | 右栏/入口 |
| --- | --- | --- | --- |
| ≥1440px | 常驻约 240px | 正文优先；空间充足才保留 136–160px editorial rail | 默认常驻约 224–240px |
| 1024–1439px | 常驻约 220–240px | 一般取消内侧 rail，正文独立限宽 | 收起为右侧按钮，不能直接消失 |
| 768–1023px | 左侧按钮 | 单列、中心限宽，不为 H2 单独保留 138px 空列 | 右侧按钮 |
| ≤767px | 左侧按钮 | 单列移动阅读；18–24px fluid 边距 | 右侧按钮，进入同一全站右栏 |

若三栏预算不足，提前收起右栏；若主导航/站名很长，提前用菜单按钮，不能依靠把 header 撑成两三行。普通文章可采用同一收起阈值，避免每类页面行为不同。

整体 shell 最大宽度初始保留 1500px；正文英文目标约 60–75 字符/行，中文/日文宽屏目标约 30–38 个全角字符/行，以实渲染为准。外侧栏减少不会让正文突然铺满 900px。

### 5.2 抽屉宽度硬约束

LTR 下左栏物理从左进入，右栏从右进入。外宽包含 padding/border；使用 `box-sizing:border-box`：

```css
/* 建议 contract；待实现和测试。 */
.docspress-panel--left  { inline-size:min(70vw, 20rem); max-inline-size:70vw; }
.docspress-panel--right { inline-size:min(88vw, 24rem); max-inline-size:90vw; }
```

右栏通常使用 88%，90% 是硬上限。不能用 `min-width:280px` 等下限在小手机反向突破百分比。抽屉收纳侧栏与 widgets，不承载完整文章正文。

| 视口宽度 | 左栏最大外宽 | 建议右栏外宽（88%） | 右栏绝对上限 |
| --- | ---: | ---: | ---: |
| 320px | 224px | 281.6px | 288px |
| 360px | 252px | 316.8px | 324px |
| 390px | 273px | 343.2px | 351px |
| 430px | 301px | 378.4px | 387px |

顶部菜单独立定义：宽屏原生 header 导航，空间不足时缩为其自己的“菜单”按钮；展开 header 下的独立导航面板。它不并入左抽屉、不占右抽屉，也不借用“本篇目录”按钮。三个窄屏入口分别是 header 的“菜单”、左侧“资料导航”、右侧“侧栏”。

篇内 TOC 使用文章内独立的 `<nav>` 与原生 `<details>`/disclosure，宽窄屏都在标题区域下方。长目录默认可收起；用户点击“本篇目录”在原处展开，不从 rightbar 滑出，不加第四个全站悬浮抽屉。

RTL 使用逻辑方向镜像：主导航为 inline-start、上下文为 inline-end；70% 属于主导航、90% 属于上下文。LTR 本站仍严格左/右滑出。

### 5.3 抽屉完整行为

1. 左右模态抽屉互斥；顶部菜单有独立控件/内容并与抽屉互斥展开。统一清理状态，但不合并三者的导航职责或按钮。
2. overlay 位于站点 header/local bar 之上；抽屉自带可见标题和至少 44×44px 的关闭按钮。
3. 有效高度由 `100dvh`、toolbar 当前可见高度、安全区确定；抽屉内部滚动，底部 controls 不被浏览器底栏覆盖。
4. 打开后焦点进入面板；只有屏幕实际可见、非 disabled、非 inert 的控件进入 Tab 环。
5. Tab/Shift+Tab 环绕、Escape、关闭按钮、遮罩点击均工作；关闭后回到仍可见的 opener。
6. 模态时设置正确的 dialog 名称和 `aria-modal`，背景进入 inert；包括临时不可交互的 toolbar，并在关闭后精确恢复原状态。
7. 左右栏的同页目标链接正确恢复阅读位置；跨页链接使用正常浏览器导航。篇内 TOC 的 disclosure 单独管理，同页点击到标题后保持焦点/阅读上下文，不被侧栏脚本抢回 opener。
8. resize 跨“常驻/抽屉”边界、方向切换、返回缓存页面时清理过期状态；不能留下 `body.no-scroll`。
9. 锁定背景滚动而保存当前位置；iOS 如需 fixed-body 方案，仅在真实测试证实必要时引入，并恢复 scrollY。
10. 动画目标 160–220ms，仅 transform/opacity；减少动态效果时取消滑动。等待动画稳定后测量最终宽度和命中区。
11. 触屏点击为主，不依赖边缘手势；滑动关闭可后置，不能拦截系统返回手势。
12. 不支持 JS/脚本失败时，导航和 widgets 有正常文流 fallback 或原生 disclosure；增强成功后才隐藏默认内容。

模态键盘及背景规则参照 [WAI-ARIA Dialog Pattern](https://www.w3.org/WAI/ARIA/apg/patterns/dialog-modal/)。这里只复用其语义，不为桌面常驻 aside 加 dialog/focus trap。

顶部菜单采用有 label 的 navigation disclosure，Enter/Space 开关、Escape 返回独立菜单按钮，展开内容不与文章 TOC 共用状态。若原型采用模态式菜单，则补齐同样的背景/焦点约束；默认优先较轻的 header 下拉，不因复用 JS 把它变成左栏。

## 6. WordPress toolbar：独立 P0 专项

### 6.1 官方行为与当前缺口

本次核对的 WordPress 核心：toolbar 通常为 32px，≤782px 为 46px；≤600px 时 `position:absolute`，可能滚离视口。核心还会为 `html` 增加顶部占位。来源：[admin-bar.css](https://github.com/WordPress/wordpress-develop/blob/trunk/src/wp-includes/css/admin-bar.css)、[核心 bump 样式](https://developer.wordpress.org/reference/functions/wp_enqueue_admin_bar_bump_styles/)。实施时核对目标安装版本与插件实际行为。

当前主题只计 `--header-h` / `--local-h`：sticky header top=0、local top=64、抽屉 top=0/64，且 H2 的滚动偏移不计 toolbar。匿名首屏看起来正确，不能作为登录后正确的证明。

### 6.2 统一顶部几何

定义单一来源的布局值：

- `B` / `--dp-bar-visible`：toolbar **当前实际遮住视口顶部的高度**；匿名或 toolbar 已滚走为 0。
- `H` / `--dp-header-h`：站点 header 实际高度，初始桌面 64px、手机目标 56px。
- `L` / `--dp-local-h`：当前可见的侧栏控制条高度；无该条为 0，目标 44px。它只放左右侧栏入口，文章 TOC 留在正文。
- `T = B + H + L`：正常阅读时的顶部遮挡总量。

| 组件 | 顶部位置/尺寸约定 |
| --- | --- |
| sticky header | `top:B`；核心 html 占位照常保留 |
| sticky local bar | `top:B+H` |
| 独立顶部菜单展开区 | 依据 `B+H` 定位；控制条不盖住菜单；菜单关闭后恢复同一布局 |
| 桌面左右栏 sticky 区 | `top:T+16px`，最大高度减去同一顶部栈及底边距 |
| 窄屏抽屉 | `top:B`；盖过主题 header/local bar；高度为可见视口减 B |
| 抽屉 backdrop | 同抽屉的顶部边界；不创建额外内容占位 |
| 文档锚点/skip link/评论目标 | 使用同一 `T`，目标标题与焦点不藏在顶部 UI 后 |

不要重复给 body/main 加 32/46px padding 或 margin。页面文流占位和 sticky/fixed 视口偏移解决不同问题，不能简单相加两次。

### 6.3 更新机制

- 先用 `.admin-bar` 与核心 `--wp-admin--admin-bar--height` 提供首屏 CSS fallback；不要仅按“用户已登录”猜 toolbar 是否显示。
- 在现有导航脚本的独立小函数内读取真实 `#wpadminbar`：display/visibility、position、矩形；固定栏取实际高度，absolute 栏取当前与视口顶部交叠的可见部分。
- 标准核心行为下，可见量可按 `clamp(rect.bottom,0,rect.height)` 推导；若第三方工具条偏移位置，需按实际顶部遮挡区间判断。
- height/布局变化通过 ResizeObserver；scroll 仅在 toolbar 可滚走时用 passive + requestAnimationFrame 更新；只更新必要 CSS 变量，避免每个组件各自监听。
- `100dvh` 优先处理浏览器动态视口；软键盘和 iOS 安全区另测。仅在实测证明必要时接入 VisualViewport，避免重算同一偏移。
- 全站滚动偏移选一个总量来源：例如 `html scroll-padding-block-start:T`，heading 只加 12px 呼吸距离。不要 heading 又加完整 T，形成双重锚点留白。
- TOC 观察高亮所用的可见区也依据当前 T 更新；返回带 hash 的页面和图片加载后的锚点位置纳入测试。
- 主题 header、local、backdrop、drawer 的 z-index 用统一层级表；抽屉高于主题顶部 UI，核心 toolbar 保持原有层级和正常菜单功能。模态期间背景临时 inert，关闭后 toolbar 交互恢复。
- 保留 admin bar 核心输出和 bump hook。官方明确禁止主题阻止 toolbar 出现，不能通过隐藏它解决错位。[主题要求](https://make.wordpress.org/themes/handbook/review/required/#5-functionality-and-features)

### 6.4 登录态验收清单

| 状态 | 检查重点 |
| --- | --- |
| 匿名；登录且 toolbar 开启；登录但个人资料关闭 toolbar | 仅实际出现时预留偏移，无凭空 32/46px 空带 |
| 1440/1280/1024/783px | 固定 32px 区间，header 与 sidebar 顶部一致 |
| 782/781/768px | 46px 高度转换，local controls 不错位 |
| 641/640/601px | 主题布局与核心 toolbar 断点相邻但不同，不能互相覆盖 |
| 600/599/390/320px | 首屏、滚动 1/24/46/200px、滚回顶部；toolbar 可见量逐渐变为 0，无残留空带 |
| 左/右面板、独立顶部菜单；在文中途打开再关闭 | 关闭按钮、内滚动、背景位置正确，三个入口彼此独立，焦点和 toolbar 状态恢复 |
| toolbar 的 My Sites/Edit/New 下拉；Customizer 预览 | 管理菜单正常，主题下拉不盖住它；预览 iframe 内偏移不重复 |
| 直接进入 hash、点击 TOC、skip link、分页后返回 | 目标标题/键盘焦点完整可见 |
| iOS Safari、安卓 Chrome；键盘弹出；横屏短高度 | 动态视口变化不把关闭按钮或搜索提交推到屏幕外 |
| 管理员、编辑、普通已登录用户 | 不依赖 administrator 角色判断 toolbar 或布局 |

本轮只制定上述标准；目前没有真实登录截图，不将匿名测试或合成 toolbar fixture 冒充登录验收。

## 7. 手机单篇阅读与视觉细节

### 7.1 正文与字体

- 文档 Page 和普通 Post 共享 paragraph/list/blockquote/code/table 基础 tokens；普通 Post 不需要编辑专用 class 才能好看。
- 正文初始目标 17–18px（约 1.0625–1.125rem），中文/日文 1.75–1.85 行高，英文 1.65–1.75；最终用三种实际语言长文校准。
- 手机 18–24px 两侧边距，320px 时允许 16–18px；不能为了保 rail 把 prose 压成细条，也不通过缩小到 14px 容纳更多内容。
- 宽屏正文常用目标约 620–680px；平板保持限宽，H2 回到文流。`ch` 只辅助英文估算，中文还要按字体与全角字符实看。
- 手机 H1 建议 28–34px、行高 1.18–1.28；中/日文不沿用过紧负 letter-spacing。超长标题可自然断行，不截断标题文本。
- H2 手机建议 22–25px，H3 19–21px；段前段后形成明确节奏，不再把文中每块都变为粗横线分隔的卡片。
- metadata 13–14px，仍要可读；从首屏压缩重复信息，作者/日期/更新信息按内容类型显示，留出更早进入正文的空间。
- 文字可放大 200%；1280px 下 400% zoom 对应 320 CSS px 的 reflow 也验证。正文不出现全页横向滚动。[W3C Reflow](https://www.w3.org/WAI/WCAG22/Understanding/reflow.html)

### 7.2 颜色、对齐和控件

- 拆分装饰橙色和文字链接色。`#e87917` 可保留在适合的装饰位置；文字候选 `#b45309` 白底约 5.02:1，定稿仍检查 hover、visited、focus 与灰底组合。
- 小文字的 muted 色必须重新测；普通文字目标 ≥4.5:1，大文字 ≥3:1。边框装饰和可交互控件边界不能混作一个判断。[W3C Contrast](https://www.w3.org/WAI/WCAG22/Understanding/contrast-minimum.html)
- 触摸主按钮、close、目录 toggle 的产品目标 ≥44×44px；图标可小，但 hit area 不能只有 5px。WCAG 2.2 AA 的 24×24px（含间距等例外）是另一层最低规范，不冒称 44px 为其 AA 硬要求。[W3C Target Size](https://www.w3.org/WAI/WCAG22/Understanding/target-size-minimum.html)
- 标题、deck、metadata、第一段及文末导航共享正文轴线；首页/章节的对应标题按相同布局模式对齐。
- focus ring 不能被 overflow/sticky 裁掉，正文链接保留下划线；当前项除颜色外保留文字权重/边界等识别方式。
- 手机 header 目标 56px，local bar 44px；先保证按钮稳定可达，再评估自动收缩功能，不在首轮加入滚动隐藏 header。

### 7.3 内容类型清单

| 内容 | 升级处理 |
| --- | --- |
| 段落、H1–H6、长词/URL、列表 | 自然断行；URL 可 wrap，代码标识符不被全局 break-all 破坏；列表缩进适合窄屏 |
| 中文/日文、混排、ruby、英文 | 语言合适的 line-height/letter-spacing；不全局 justify；不手工插入换行排版 |
| 图片、caption、画廊、封面 | `srcset/sizes`、自然比例、可访问 alt；缩略图不冒充原图；图注回到正文流 |
| float/margin figure 与旁注 | 手机取消绕排；旁注在对应段落之后就近阅读，不统一塞到文章最末 |
| 表格 | 语义 caption/th 保留；局部横滚且有可见提示/键盘路径；不强制所有表格最小 620px |
| 代码/长命令 | 等宽字体、局部横滚、保留格式；代码块独立 overflow 不扩散到页面 |
| 引用、提示、定义 | 低对比背景只作区分，正文仍满足对比度；标签/标题有语义 |
| 音视频、iframe、oEmbed | 响应式容器；尊重核心响应式嵌入；大媒体尺寸不诱发明显布局跳动 |
| 篇内 TOC、脚注、参考文献、锚点 | TOC 在 article 内，支持原地展开/收起、深链接和自然键盘导航；不挤占右栏；往返链接目标不被顶部 UI 遮住 |
| 文章分页、前后篇、评论 | 继承正文宽度；标题自然换行；评论可折叠入口但不能屏蔽核心评论功能 |

### 7.4 无干扰与打印

- 默认不加悬浮分享、常驻底部广告条、阅读进度装饰和字体工具面板；先保证文章和资料入口本身。
- 可选阅读模式放入后续验证：在有真实需求时隐藏辅助 chrome，并提供明确退出；不作为解决正常页面拥挤的前提。
- 建立 print 样式：隐藏站点导航/抽屉/backdrop，正文单列、黑白可读；图像与表格不被固定高度截断，参考文献保留，篇内 TOC 按内容长度合理展开。
- 不自动生成 PDF 或离线应用；浏览器打印/PDF 首先能正常工作。

## 8. 长期资料库：组织、检索与耐久性

### 8.1 内容模型

| 原生对象 | 推荐用途 | 持久化原则 |
| --- | --- | --- |
| Page + 父子关系 + menu_order | 稳定主题目录、手册、知识条目 | 延续当前 Docs Root 模型，不创建主题私有 CPT |
| Post + 分类/标签/作者 | 新材料、研究笔记、更新记录、时序文章 | 保留核心归档、分页、feed 与编辑能力 |
| Menu | 精选路径、跨栏目入口 | 不假定目录树能替代全部站点导航 |
| Excerpt | 索引描述、搜索摘要 | 不把大量版式 HTML 存进摘要 |
| Media/正文参考文献 | 附件、出处、引用材料 | 来源/许可信息是内容，不写死在模板 |

Docs Page 和 Post 之间可通过正文/菜单互相引用。若需要 Page 上的主题分类、贡献者、版本、审阅状态、来源 URL、版权字段等可跨主题资料数据，使用独立插件/现成字段机制，主题只呈现。

### 8.2 四条查找路径

1. **浏览目录**：当前路径展开、当前位置明确、长标题完整可读，深层面包屑能回父级。
2. **搜索**：沿用标准 WordPress 搜索入口，结果标注 Page/Post、所属章节/作者等已有上下文；无结果提供章节和重试搜索。
3. **精选索引**：普通编辑 Page 编排专题/入门/参考路径；章节索引显示 excerpt，而不要求读者遍历整棵树。
4. **近期变化**：Posts 记录更新、原生 feed 供订阅；“最近修改资料”如需跨主题能力，由独立 widget/plugin 提供。

全站搜索是默认。Docs-only 检索是明确独立范围，不能暗改所有核心查询、后台和 REST 行为。标题/摘要的展示优化可由主题做；全文相关度、附件/OCR 索引、全文跨语言搜索属于插件或独立搜索服务的可选层。

### 8.3 大树与性能

- 保持 `get_pages()`/核心查询与已有 helper，先按 parent ID 一次分组，再各组稳定排序并遍历；消除每个节点都扫描全列表的构建方式。
- 请求内复用 pages、分组树、flatten 顺序及导航 lookup；左右响应式面板单 DOM 减少重复 HTML。
- 规模样本：30、300、1000 个 Page；覆盖深 6 层、超长标题、孤儿节点、未发布父级、同 menu_order/同标题；同值排序追加稳定 ID tie-break。
- 深树默认展开当前祖先路径；在大规模场景只输出当前相关分支/相邻层级，提供父级索引访问全部内容。不要简单把深层目录砍掉而没有可达路径。
- 不对内容树套复杂 ARIA tree 键盘模型；优先原生嵌套列表、链接和独立 disclosure 按钮。
- 先记录冷/热请求、PHP 内存、查询量和 DOM 数量，再决定是否需要跨请求对象缓存；测量结果决定容量策略。
- 若引入缓存，key 包含 blog/site、root、语言与内容变更版本；明确新增/移动/删除/状态变化的失效路径。带权限内容不能与公共树共用未经区分的结果。
- 不持久缓存经过 `the_content` 的完整个性化 HTML；TOC 最初采用请求内缓存，避免短代码、权限插件或动态内容缓存串人。

性能验收比较相同数据、同环境；1000 页压力案例不强迫在每一页面输出全部树节点。TTFB 绝对值受主机/插件影响，报告区分主题、数据库与服务端因素。

### 8.4 URL、锚点和可信时间

- 不因主题升级更改 slug/permalink；调整父级可能改变 Page 路径，内容维护操作先记录旧→新映射及重定向，重定向交给服务器或插件。
- 尊重用户显式 heading ID；新算法不得随意重命名已存在的 ID。修改标题前提示维护者保留显式锚点。
- TOC 解析逐步使用目标最低 WordPress 支持的 HTML API/可靠解析路径；先补复杂内容样本再替换正则，避免无条件重写所有 heading attrs/嵌套 HTML。
- ID 去重扫描整个相关内容，不仅 H2/H3；保留 `lang/dir/data/aria` 等有效语义，跳过空标题，支持中文/日文、重复标题和 block/group 中的 heading。
- 安全与内容过滤继续走核心 `the_content`；密码保护和权限插件未放行的正文，不得从 TOC、摘要或缓存泄露。
- 显示时间区分“发布”“修改”和“人工审阅”；主题不根据 modified date 宣称内容已经复核。
- 前端 `<time datetime>`、作者链接、文档上级、来源引用要清晰；许可/引用格式用普通内容就能维护。
- 页面合并、撤稿、过期提示作为编辑流程；历史归档保持可解释，不用静默删除掩盖版本变化。

### 8.5 资料维护工作流

- 新条目：标题/slug/父级/excerpt/正文来源/附件信息完整，再检查目录路径和手机预览。
- 季度维护：孤儿页面、死链、重复条目、超深层级、陈旧资料、失效附件、无法搜索到的材料。
- 更新内容优先于模板宣传；修正演示站错误地址、旧发布状态和预览文案，逐条审稿后更新。
- 备份至少包含数据库、uploads、主题/子主题和必要插件/config 清单；WXR 是内容交换副本，不当作完整恢复备份。
- 验证切换主题后 Pages/Posts/媒体/参考文献仍能阅读；这是资料库耐久性的必测项。

## 9. 成熟 Classic Theme 的工程门槛

### 9.1 核心模板与兼容

- 保持 PHP Template Hierarchy，遵循 Reading 设置：静态 front page、Posts Page、最新文章首页各自正确。
- 完整检查 single/page/home/archive/search/404/comments/密码保护/分页文章/attachment 的核心 fallback；分类、标签、作者、日期归档可共享 archive.php。
- 原生主菜单支持有用的嵌套层级；现有 `depth:1` 要与设计一并审查，不能创建菜单后前端静默丢掉子项。
- Widgets 为空、Docs Root 未设置/删除、无 excerpt、无菜单、无 logo、超长站名、无标题和 0/1 条目录都可运行；空配置仍保留默认双侧栏与有效 fallback。
- 封面/featured image、caption、alignleft/right/center/wide/full、常见 core blocks 在 Docs 和 Post 都有合理行为。
- Classic Editor/TinyMCE 和 Block Editor 都能输入普通内容；editor styles 与前端基本字体、链接、表格、列表相符。
- 最低 PHP/WordPress 版本按声明实际运行；现有 PHP 7.4 / WP 6.5 不能用单一 PHP 8.5 / 当前 WordPress 测试替代。若不再支持旧版本，先用结果说明再调整声明。
- 单站和 path-based Multisite 分别测；子站 URL、媒体、search action 不写死主站路径。
- RTL 检查样式实际加载、side panels 方向、箭头、缩进、数字与混合文本；不把文件存在当作已支持。
- Child theme 覆盖模板和 assets 继续走核心路径机制；设置权限使用 `edit_theme_options` 等适当 capability，不硬编码角色。

### 9.2 CSS/JS 整理

- 优先在当前 enqueue 与文件内整理 tokens → base → shell → navigation/panels → prose → widgets → WordPress compatibility → responsive/state/print，删除重复/失效规则须有视觉对照。
- 新组件使用 `docspress-*` 命名空间；旧内容类保留 scoped compatibility，并记录弃用策略。
- 不对裸 `h1/table/a/button` 追加会污染插件/toolbar 的布局约束；把主题样式限定在主题 shell/内容/widget 边界。
- 扩展 `navigation.js` 为闭包内的少量状态：none/left/right/site-menu；顶部几何、三个展开入口及文章内 TOC 各有清楚职责，避免全局 lexical 变量污染。
- 使用已有 hooks 和标准 API；无需引入框架或打包器来完成双抽屉。
- 只有真实性能/维护需求才拆更多 CSS 文件或条件加载，避免为重构新增不必要的网络请求。
- 统一表单、buttons、active/hover/focus/disabled、reduced-motion、forced-colors；高对比模式不只靠 box-shadow 表达焦点。

### 9.3 安全、国际化与发布资格

- 输入白名单和 sanitize、输出 esc、必要操作 nonce/capability，所有用户可见字符串可翻译。
- TOC/树/搜索不泄露私有、草稿、密码保护或插件受限内容；权限策略沿用核心/内容插件。
- 保留 `wp_head/wp_body_open/wp_footer/body_class/post_class/wp_link_pages` 等核心约定。
- GPL 兼容许可和资源清单、作者/贡献者、text domain、截图、readme、最低版本声明同步；公开身份和目录 slug 提交前再核验。
- Theme Check、WPCS、Theme Unit Test、真实运行时与人工 a11y/视觉检查共同验收；自动检查不能替代审查。[主题目录要求](https://make.wordpress.org/themes/handbook/review/required/)
- 不因 Classic Theme 身份禁用核心编辑功能，不在主题中混入私有 CPT/短代码/自定义 block、SEO、分析、权限或业务数据管理；插件边界保持清楚。
- 不将有 `accessibility-ready` 标签等同于自测声明；若使用该目录标签，单独达到其额外审查要求。

### 9.4 包、升级与回退

- 延续 `scripts/check-theme.sh`、`check-content.sh`、现有 helper tests 和 `package-release.sh`；补的是抽屉/toolbar、widget 迁移及实际 runtime 的缺口。
- 新 ZIP 只有 `docspress/`，排除 reference、项目计划、演示内容、审查截图、数据库、QA 依赖和秘密信息。
- 保留 `sidebar-1`、现有菜单位置、theme mods、permalinks；新增设置用安全 defaults，不自动重排现有 widget 实例。
- 先在本地/隔离站创建当前站配置的可恢复基线，再安装精确 ZIP；与前一版对照内容和设置。
- 不在主题激活时自动导入演示内容、安装插件、改变首页或重定向后台。
- 上线记录：主题版本、提交、ZIP SHA-256、备份点、受影响子站、资源一致性、页面/登录验收结果和回退包。
- 回退必须同时说明：主题文件可回退，若内容/数据库配置有更改则还需要相应配置备份；避免只恢复 ZIP 却遗留不可读布局设置。

## 10. 实施顺序与交付

以下是建议里程碑，不代表已承诺发布日期。每步先交可审查的代码/截图/测试结果，再进入后一阶段。

| 阶段 | 建议版本 | 核心工作 | 放行条件 |
| --- | --- | --- | --- |
| A：基线冻结 | 仍为 0.2.4 | 准备隔离 WordPress、真实登录测试用户、widget 现状/设置备份、视觉样本 | 能重现本次 P0；有可回退基线 |
| B：shell 与 toolbar | 0.3.0 候选 | 全页默认双侧栏、原生 widgets、独立顶部菜单、篇内 TOC、70%/90%、层叠和顶部几何 | 本文 §3–6 硬约束通过；既有 widgets 无丢失 |
| C：移动阅读 | 0.4.0 候选 | prose tokens、取消中小屏 rail、对比度、core blocks、表单/评论、打印 | 三语言长文与单篇 Post/Docs 全矩阵可读 |
| D：资料库 | 0.5.0 候选 | 稳定锚点、树性能、搜索上下文、面包屑、索引与内容维护说明 | 30/300/1000 样本、受限内容、主题切换通过 |
| E：成熟度与发行候选 | 0.9.0 | 最低版本、编辑器、RTL、单站/Multisite、Theme Check、许可/包、完整回归 | 所有 P0/P1 清零，已知 P2 有说明 |
| F：1.0.0 | 1.0.0 | self-hosted 演示站与最终包一致，文档同步，回退演练 | 完整验收签收；WordPress.org 提交是独立发布步骤 |

关键路径：**toolbar/层叠 → 双侧栏与 widgets → 移动阅读 → 长期资料库与发行审查**。不要在顶部几何尚不稳时逐页抠 margin，也不要为先增加资料功能跳过基础交互。

### 10.1 文件改造地图

| 现有入口 | 计划职责 |
| --- | --- |
| `inc/widgets.php` | 保留 `sidebar-1` 并扩展为全站右栏，新增左栏/页脚两个区域及默认内容说明 |
| `inc/customizer.php` | 核心 theme mods、白名单配置、合理默认值 |
| `header.php` / `footer.php` | 共用 header/controls/footer，检查 overlay host 与 toolbar 层次 |
| `page.php` / `single.php` / `front-page.php` / section 与列表模板 | 使用共用 shell/布局决策；内容渲染仍走各标准模板 |
| `sidebar.php` / layout 与 navigation template parts | 全站单 DOM 双面板、原生 widget 输出与 fallback；文章内 TOC 独立模板 |
| `assets/js/navigation.js` | 一个面板状态管理、可见焦点、关闭回收、顶部几何、breakpoint cleanup |
| `assets/css/main.css` / `editor.css` / `rtl.css` | 阅读 tokens、布局/状态规范、编辑器/RTL/print 与 core widgets |
| `inc/docs.php` | 一次分组、稳定树/顺序缓存、边界案例 |
| `inc/toc.php` | 安全、保留显式 ID、复杂结构及全内容去重 |
| `tests/` / 现有检查脚本 | 只补关键回归与运行时验收，不复制实现写无意义断言 |
| `docs/design.md` / reviews / readme / site-content | 实施验收后更新设计 contract、使用说明和演示文案 |

## 11. 验收矩阵与可判定标准

实施结果（2026-10-24）：A–E 的主题升级已交付为 0.9.1，演示站与安装包一致；回退演练和文档已完成。1.0 版本晋升、WordPress.org 提交以及新的 GitHub 发布仍是独立决定，不因本轮完成而自动执行。逐条硬验收证据和限制见 [最终验收报告](../docs/audits/2026-10-23-upgrade/ACCEPTANCE.md)。

2026-10-23 实施确认：用户明确要求“跳过这个验收，你用技术验收或者模拟验收即可”。因此真实 iPhone/Android 键盘、安全区和抽屉滚动不再是本轮放行阻塞条件，改用实际浏览器视口、短高度变化、原生焦点/键盘、CSS 几何与安全区规则检查。以下原实机要求保留为后续可选增强，不得把模拟结果称为真实设备结果。

### 11.1 页面、身份和视口

- 页面：资料首页、章节、深层长文、短文/无标题目录文档、普通 Post、普通 Page、Notes、分类/标签/日期/作者归档、搜索有/无结果、404、密码保护、评论分页、文章分页。
- 身份：匿名、登录 toolbar 开启、登录 toolbar 关闭；至少管理员与普通登录用户，另测 Multisite 的 My Sites。
- 基础视口：320、360、390、430、640、768、1024、1280、1440、1920；高度包含 568/667/844 和横屏短高度。
- 断点边界专项：599/600/601、639/640/641、767/768/769、781/782/783、1023/1024/1025、1439/1440/1441。
- 旧缺陷专项：980/981、1280/1281；验证迁移后不再丢目录，不因旧 media query 残留复发。
- 浏览器：Chromium/Chrome、Firefox、Safari；iOS Safari 与安卓 Chrome 的键盘/安全区至少真实设备验收一轮，桌面模拟只算补充。
- 用户偏好：200% 字体/缩放、400% reflow、reduced-motion、forced-colors；RTL、中/日/英及超长站名/菜单文案。

不做所有维度的机械笛卡尔积：关键模板先跑全部宽度；同一 shell 复用的页面抽样。toolbar 边界、手机抽屉、短高度和真实设备为强制组合。

### 11.2 十四项硬验收

1. 手机左面板外宽 ≤0.70×CSS 视口宽，右面板 ≤0.90×；无 min-width/安全区 padding 反向突破。
2. 默认所有页面在宽屏有 leftbar/rightbar，窄屏有对应两个按钮；顶部菜单另有独立按钮；文章 TOC 只在 article 内，不混入 rightbar。
3. 抽屉 close/首尾控件可见、点击命中正确，Tab/Shift+Tab/Escape/焦点返回与背景 inert 均通过。
4. 启用/关闭 toolbar 不会重复顶距；滚走的手机 toolbar 不留下 46px 假空带。
5. sticky header/local/sidebar/TOC 目标按同一顶部栈；锚点和焦点不会被完全遮挡，产品目标为完整可见。[W3C Focus Not Obscured](https://www.w3.org/WAI/WCAG22/Understanding/focus-not-obscured-minimum.html)
6. 页面无全局横向滚动；宽表格、代码局部滚动例外有提示和键盘可达路径。
7. 同布局模式各页 H1/正文主轴偏差目标 ≤2px；长文短文 footer 均不与左背景或 panel 相穿。
8. 正文/辅助文字/链接/焦点满足相应对比度，触屏 controls 达到产品 44px 目标。
9. 图片自然比例，caption/旁注在手机保持阅读关系；文末不强塞整套 desktop widgets。
10. `sidebar-1` 原有 widget 实例不丢；空 widgets 使用有效默认内容，双侧栏不自行消失；新旧编辑器与 block widgets 都正常。
11. 中文/日文 heading、重复/显式锚点、复杂 block 内容、权限/密码保护均不破坏或泄露。
12. JS 失败仍能导航和读正文；跨断点及浏览器返回后无残留滚动锁。
13. 核心运行无新增 PHP/JS 错误，最低声明版本、WPCS/Theme Check/单元与安装包检查有可追溯结果。
14. 升级、回退、主题切换演练完成，内容/URL/小工具/菜单继续可用，精确 ZIP 与线上文件一致。

### 11.3 留证方式

- 每个关键问题留“URL + 身份/toolbar状态 + viewport + scrollY + before/after 截图 + 对应改动 + 检查结果”。
- drawer 动画完成后再测 bounding box；焦点节点存在不等于控件可见，必须核对遮挡与指针命中。
- 几何/交互可自动化，排版节奏、长文阅读和屏幕阅读器需要人工确认；截图只展示匿名态时明确标注。
- 历史 QA 保留日期和版本，新记录单独写；不把本次发现填回旧记录后声称旧版本一直正确。

## 12. 首轮范围与后置项

第一轮必须完成：全页默认双侧栏、独立顶部菜单、文章内部 TOC、shell/层叠、toolbar、双向抽屉宽度、原生 widget 配置、基础移动阅读/对比度、无 JS 路径、既有配置兼容。

后续资料库轮完成：树性能、稳定锚点、搜索上下文、面包屑/专题路径、打印及完整核心内容矩阵。

待真实需求再做：暗色模式、跨设备阅读进度、收藏/批注、全文索引/OCR、版本化文档、离线/PWA、多知识库自定义实体、AI 搜索。它们不进入首轮主题基础修复，也不通过主题私有数据造成锁定。

本方案可直接作为实施任务书。开始实施时先准备 A 阶段的登录态复现和回退基线，再按 B→F 推进；逐阶段交付可安装主题、截图、测试及剩余问题清单。
