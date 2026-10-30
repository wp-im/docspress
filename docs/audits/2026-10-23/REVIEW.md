# Docspress 0.2.4：升级前视觉与结构审查

审查日期：2026-10-23（Asia/Tokyo）。这是本次升级计划的证据，不是升级完成报告。

## 审查边界

- 本地源码：`theme/`，当前主题头版本 `0.2.4`。
- 线上：<https://wordpress.im/docspress/>；本次读取的线上 `style.css`、`assets/css/main.css` 与本地文件逐字节一致。
- 公开页面 generator 显示 `WordPress 7.1.3`；这是页面观察值，不替代后台及最低版本兼容测试。
- 浏览器实查：首页、Reading system 章节索引、Reading surface 文档、Notes 列表、Maintenance is part of the theme 普通单篇文章。
- 文档页进行了十档视口的布局测量；截图另外保留 1280、768、390px 样本及双抽屉。
- 本次页面是匿名视图，DOM 无 `#wpadminbar`。登录 toolbar 的风险来自源码审查与 WordPress 官方核心核对；**尚未完成真实登录态视觉验收**。
- 没有修改主题、线上设置、widgets、内容或发布状态。打开/关闭抽屉用于检查；临时视口已复原。

本次用户补充的下一版结构：所有页面默认都有 leftbar/rightbar；顶部菜单独立并有自己的窄屏按钮；TOC 归文章内部，不属于 rightbar。下面“右目录”仅描述当前 0.2.4 的实现，不表示下一版仍沿用该结构。

## 实测：文档页 Reading surface

单位为 CSS px；宽度四舍五入。`prose` 是实际段落列宽，不是整个 article 宽度。

| 视口宽度 | article 宽度 | prose 宽度 | 常驻左栏 | 常驻右目录 | local bar / 目录按钮 | 页面横向溢出 |
| --- | ---: | ---: | --- | --- | --- | --- |
| 1440 | 848 | 619 | 有 | 有 | 无 / 无 | 无 |
| 1280 | 924 | 700 | 有 | 无 | 无 / 无 | 无 |
| 1100 | 796 | 576 | 有 | 无 | 无 / 无 | 无 |
| 1024 | 720 | 515 | 有 | 无 | 无 / 无 | 无 |
| 980 | 680 | 510 | 无 | 无 | 有 / 有 | 无 |
| 768 | 680 | 518 | 无 | 无 | 有 / 有 | 无 |
| 641 | 605 | 443 | 无 | 无 | 有 / 有 | 无 |
| 640 | 604 | 604 | 无 | 无 | 有 / 有 | 无 |
| 390 | 354 | 354 | 无 | 无 | 有 / 有 | 无 |
| 320 | 284 | 284 | 无 | 无 | 有 / 有 | 无 |

这说明“没有横向溢出”不足以证明响应式已经成熟：目录入口会中断，641→640px 只差 1px，正文宽度却从 443 跳到 604px。

## 已确认问题

| 编号 | 优先级 | 发现与证据 | 应解决的根因 |
| --- | --- | --- | --- |
| V01 | P0 | 390px 下左抽屉宽 `327.59375px`，约 `84%`，超过新要求 70% | `.chapter-drawer { width:min(84vw,330px) }` |
| V02 | P0 | 左抽屉关闭按钮位于 y=29–34，指针命中 `.header-inner`；右抽屉关闭按钮位于 y=83–88，命中 `.icon-toc`；二者都被顶部 UI 盖住 | `main.doc-frame` 的 `isolation:isolate` 形成 stacking context，内置 fixed 抽屉无法越过外部 header/local bar；单纯提高抽屉 z-index 无效 |
| V03 | P0 | 当前没有 toolbar 顶部几何处理；header 固定 sticky top=0，local top=64，drawer top=0/64，sidebar 和锚点也只计主题 header | 缺少“toolbar 实际可见高度 + 主题顶部层”的共同约定；真实登录验收待补 |
| V04 | P1 | `981–1280px` 无常驻右目录，也没有可见目录按钮 | 右目录在 1280 隐藏，local bar 到 980 才显示，drawer 又被 min-width:981 的规则隐藏 |
| V05 | P1 | 普通单篇文章的 widgets 在 390px 下整体堆在文章后；sidebar 起点 y≈844px，没有右抽屉入口 | 普通文章走 `standard-layout-with-sidebar`，未复用 Docs 抽屉控制 |
| V06 | P1 | 只有一个 `sidebar-1`（Blog Sidebar）；Docs 左栏和右目录均无用户配置的 widget 扩展区 | `inc/widgets.php` 与两套 Docs sidebar 模板职责分离，缺少统一侧栏组合 |
| V07 | P1 | 左右关闭按钮的可命中盒高只有约 5px；全局菜单按钮为 38×38px | 图标尺寸与实际 button hit area 未分别定义 |
| V08 | P1 | 模态抽屉无 `role=dialog`/`aria-modal`；代码只把关闭的面板设 inert，未使其余页面背景 inert；背景仍出现在打开状态 AX 树 | 模态语义、背景交互、焦点状态没有完整关联 |
| V09 | P1 | 默认链接橙色 `#e87917` 对白底约 2.93:1；metadata `#7a7a80` 约 4.27:1；普通文章 metadata `#85858b` 约 3.67:1 | 装饰色与文字/焦点颜色使用同一 token；小字号辅助文字过淡 |
| V10 | P1 | 普通文章 block widget 的 Recent Posts 等标题明显大于预期 `.widget-title`；搜索按钮、输入框与主题 searchform 风格不同 | core block widget 的实际 markup 没有被传统 widget 标题规则覆盖 |
| V11 | P1 | 文档正文随断点从 619→700→515px，再在 641→640px 由 443→604px；普通文章又有独立 920/720px 规则 | 内侧 editorial rail、外侧双栏、标准文章采用分散的宽度预算 |
| V12 | P2 | 普通文章正文仍提到错误的双重 `/docspress/docspress/` 站点地址，发布文案仍以 0.1.0 预览为主 | 演示站内容尚未进入随版本维护的流程，不是主题模板缺陷 |

V02 为实测遮挡，不能因焦点节点存在、或 width>0 就判定关闭控件可用。V03 为高优先级待实测风险，不能写作本次已见到登录错位。

## 源码层额外风险

- `docspress_build_page_tree()` 每次递归都过滤整份 pages 列表，大规模时有 O(n²) 风险；`get_docs_pages()` 仅请求内缓存 pages，树及 flatten 结果并未统一复用。
- Docs 桌面和移动版分别渲染导航/TOC；新增 widgets 时若照搬复制，会造成第三方 widget 的 HTML ID、form label、初始化脚本重复。
- `getFocusable()` 没有排除被折叠树节点隐藏的链接，焦点环绕的候选列表不等于实际可见候选列表。
- `inc/toc.php` 使用正则重建 H2/H3，自动锚点随标题变化；现有 ID 只在 H2/H3 集合内去重，未检查文章中非 heading 的 ID；还需覆盖 block/group/嵌套 HTML 等复杂内容。
- `main.css` 已有多段 integration corrections、WordPress fallback 和 scoped compatibility；应整理现有入口，避免再形成一层追加覆盖。
- 未见专门 print 样式；`rtl.css` 存在，但本次没有 RTL 浏览器验收，也没有证明所有 enqueue 路径正确。
- `single.php` 固定输出带右栏的 grid，`sidebar.php` 在 widgets 空时返回；需要检查并修复“空 sidebar 仍预留列”的情况。
- 无 JS 时移动全局导航和 Docs Tree 的可达性需要专门验证；不能以桌面链接存在替代手机无 JS 路径。

## 截图

- [1280px：文档与目录入口缺口](docs-1280.jpg)
- [768px：平板文章内侧 rail](docs-768.jpg)
- [390px：文档阅读](docs-390.jpg)
- [390px：左抽屉与顶部遮挡](drawer-left-390.jpg)
- [390px：右抽屉与顶部遮挡](drawer-right-390.jpg)
- [390px：普通单篇文章](post-390.jpg)
- [1440px：普通单篇文章及 block widgets](post-1440.jpg)

## toolbar 核对来源

截至本次查阅的 WordPress 官方核心：常规 toolbar 高 32px，≤782px 时 46px，≤600px 时 toolbar 定位为 absolute，可随页面滚走；核心自身给 html 增加顶部占位。因此主题不能只在 `.admin-bar` 下写一条固定 `top:32px`，也不能重复给 body 加同样 margin/padding。

来源：[WordPress admin-bar.css](https://github.com/WordPress/wordpress-develop/blob/trunk/src/wp-includes/css/admin-bar.css)、[wp_enqueue_admin_bar_bump_styles](https://developer.wordpress.org/reference/functions/wp_enqueue_admin_bar_bump_styles/)。这些是官方当前源码约定，实施时仍须对目标站实际输出复核。

完整改进方案见 [升级计划](../../../plan/DOCSPRESS-MATURITY-UPGRADE-PLAN-2026-10-23.md)。
