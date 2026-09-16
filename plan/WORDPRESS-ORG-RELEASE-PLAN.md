# Docspress WordPress.org 发布升级方案

状态：`CANDIDATE 0.2.4 / 达标工程已完成 / 不提交 WordPress.org`

更新时间：2026-09-16

目标：把当前已能运行、已在 `wordpress.im/docspress/` 自我展示的 Docspress 0.1.0 工作预览，升级为达到 WordPress.org Theme Directory 审核标准的 Classic Theme；当前只完成工程标准和站点验证，不提交 WordPress.org。

本方案不是“把 ZIP 上传到 WordPress.org”的操作清单，而是发布前的工程、设计、内容边界、国际化、可访问性、许可和审查证据的完整门槛。WordPress.org 官方要求主题在提交时已经完整，且审查通过前不能把未解决的结构问题留给 reviewer。

## 1. 当前基线

### 1.1 已有基础

当前 `docspress/theme/` 已具备：

- Classic Theme 根目录 `style.css`、`index.php`、`comments.php`；
- `screenshot.png`，当前为 1200 × 900、4:3；
- `readme.txt`、`LICENSE`、语言目录和标准模板层级；
- `wp_head()`、`wp_body_open()`、`body_class()`、`post_class()`、`wp_footer()`；
- `title-tag`、Feed、HTML5、custom logo、菜单、widgets、editor styles；
- Native Pages → Docs Root → Docs Tree；
- H2/H3 TOC、深度优先 Previous/Next、Section Index、移动抽屉；
- 没有主题自有 Custom Post Type、Shortcode、Custom Block、直接 SQL、远程运行时资源或自定义上传入口；
- 既有本地 PHP/JS/CSS/WPCS、ZIP、 disposable WordPress 和生产工作预览证据。

### 1.2 当前明确阻塞项

| 优先级 | 发现 | 证据 | 处理方向 |
| --- | --- | --- | --- |
| P0 | 使用 `get_search_form()`，但没有 `searchform.php` | `404.php` 与 `template-parts/content/content-none.php` 调用 `get_search_form()`；主题根目录缺少 `searchform.php` | 增加标准 `searchform.php`，所有搜索入口统一使用可访问的 WordPress search form |
| P0 | 发布身份仍需最终核验 | `readme.txt` 的 `Contributors: docspress-project` 是否为真实 WordPress.org 用户名尚未确认 | 确认提交账号、Contributor 用户名、作者/版权归属和 Theme URI |
| P1 | `readme.txt` 不完整 | 目前有基本介绍和 Changelog，但没有完整 FAQ、Upgrade Notice、Resources/第三方资源清单 | 按官方格式补齐并重新检查短描述、贡献者、许可证和资源来源 |
| P1 | Companion Plugin 推荐存在目录资格问题 | `readme.txt` 提到 Docspress Editor plugin；WordPress.org 主题只能推荐 WordPress.org 托管插件 | 插件未上架前不要在公开主题 readme 中推荐具体外部插件；改为通用说明或先单独完成插件上架 |
| P1 | CSS 已能工作，但级联层次变复杂 | `main.css` 后段存在 WordPress integration、integration corrections、媒体兼容规则等追加层 | 先做无视觉变化的 CSS 重构，再做视觉微调 |
| P1 | 视觉类名仍有通用命名 | `.article`、`.section`、`.note`、`.quote`、`.pair`、`.meta` 等可能与内容/插件冲突 | 新增和重构时采用 `docspress-*` 命名空间，旧类只保留明确的 scoped compatibility |
| P1 | 键盘焦点与抽屉行为需要达到发布标准 | 当前有 skip link、ARIA、inert、Escape 和 focus return，但焦点可见性与 Tab 边界仍需逐项验收 | 加入完整 `:focus-visible` 系统、抽屉焦点边界、滚动条稳定和 resize 恢复测试 |
| P1 | `:has()` 仍参与图片布局核心判断 | 图片 margin/breakout/float 的 wrapper 规则依赖 `:has()` | 服务器端/编辑器输出直接提供 wrapper class，`:has()` 只作为渐进增强 |
| P2 | 主题名/slug 有目录命名风险 | WordPress.org 已有 `DocuPress` 主题和 `DocsPress` 插件；不等于必然拒绝，但需要提交前核验 | 暂保留 Docspress 产品决定，同时在上传前做名称、slug、商标和目录冲突复核，准备备用命名方案 |

P0 表示不能进入 WordPress.org 提交包；P1 表示必须在候选版本前解决；P2 表示不一定阻塞初审，但会影响长期维护、审美一致性或 reviewer 反馈。

## 2. 发布原则

### 2.1 主题和内容严格分离

发布 ZIP 只包含可安装的 Docspress Theme。不得包含：

- `reference/`、`docs/`、`plan/`、`site-content/`、`build/`；
- Git 元数据、日志、数据库、导入 XML、测试截图、临时文件；
- Humanities Docs 静态 HTML、研究底稿或 ChatGPT 对话存档；
- ZIP 内嵌的 plugin、自动安装器、demo importer 或下载脚本；
- 密钥、token、私有 URL、内部服务器信息。

主题展示自己可以通过用户创建的 Pages/Posts 完成，不能把 Docspress 的站点内容写死进 Theme。用户切换主题后，Pages、Posts、Media、菜单和普通内容必须仍然存在并可用。

### 2.2 主题只保留表现层

符合 WordPress.org 主题边界的主题职责：

- 模板层级、布局、CSS、前端交互；
- Docs Root 设置与 Docs Tree 的展示；
- H2/H3 TOC 的显示层处理；
- 标准菜单、logo、widgets、comments、search、archive、404、feed；
- editor styles、响应式和视觉状态。

不放入主题：

- Custom Post Type；
- Shortcode；
- Custom Block；
- Custom Role、Custom MIME Type；
- 联系表单、SEO、Analytics、Tracking、Social Like/Share；
- 需要跨主题保存的编辑数据；
- 自动安装或下载 plugin。

如果未来有 Docspress Editor companion plugin，必须独立开发、独立打包、独立审计，并且主题在 plugin 缺席时仍能渲染普通 WordPress 内容。主题公开文档中只能推荐 WordPress.org 托管插件，且安装必须由用户主动执行。

## 3. 分阶段升级

## Gate 0 — 冻结产品与提交身份

目标：在改代码之前，锁定 WordPress.org 会看到的公开身份和授权边界。

### 0.1 产品身份

- Theme Name：`Docspress`；
- folder/slug：`docspress`；
- text domain：`docspress`；
- PHP 公共前缀：`docspress_`；
- CSS/DOM 新命名空间：`docspress-*`；
- 初始许可证：GPLv2 or later；
- 发布形态：standalone Classic Theme，不是 child theme；
- 内容模型：Native Pages、Posts、Menus、Media、Excerpt、Categories、Tags、Authors、Comments、Search、Feeds；
- 不包含第三方远程资源，不依赖 CDN、Google Fonts 或外部运行时服务。

### 0.2 作者、版权和来源

建立一份内部但可随发布复核的资源清单：

| 项目 | 必须确认 |
| --- | --- |
| 主题作者 | 与 WordPress.org 账户一致或具有明确授权 |
| Contributor | 必须是真实 WordPress.org 用户名，而不是项目昵称 |
| 主题版权 | 记录 Docspress 项目自己的 copyright 表述 |
| 代码来源 | 自有代码、GPL 兼容代码或明确许可的代码 |
| 图片/图标/字体 | 每个随 ZIP 分发的资源都记录作者、许可、来源和文件用途 |
| 静态参考 | 仅作为内部设计参考，不放入发布包，不冒充第三方授权资产 |

WordPress.org 要求 ZIP 内所有代码、数据和图片均为 GPL 或 GPL-compatible；同时要求列出随主题分发的资源。不能因为一个文件“只是截图”或“只是图标”就跳过许可确认。

### 0.3 名称预检查

目前 WordPress.org 已存在相近的 `DocuPress` 主题和 `DocsPress` 插件。这个事实不自动决定 Docspress 不能提交，但会产生名称可辨识度、slug 和商标/品牌审查风险。

在真正上传前：

1. 检查 WordPress.org Themes、Plugins 和商标相关公开信息；
2. 确认 `Docspress` 不会被视为对现有产品的误导性近似；
3. 保留用户要求的名称作为当前产品决策；
4. 准备一个备用名称/slug，但不擅自更名；
5. 如果风险无法判断，在上传前向 Themes team 询问，而不是先创建多个提交票据。

## Gate 1 — 补齐 Classic Theme 发布结构

### 1.1 根目录文件

提交前必须逐项确认：

- `style.css`：根目录、完整主题头信息；
- `index.php`：有效 DOCTYPE 通过 `get_header()` 输出，具备 fallback；
- `comments.php`：支持 threaded comments、trackbacks/标准 comment 输出路径；
- `searchform.php`：因为主题已经使用 `get_search_form()`，这是当前第一个明确阻塞项；
- `screenshot.png`：1200 × 900、4:3、非广告式、代表真实主题表现；
- `readme.txt`：WordPress.org 发布格式；
- `LICENSE` 或等价 GPL 许可证文件；
- `languages/docspress.pot`：与最终源码字符串同步。

### 1.2 Search form 统一方案

当前有三类搜索入口：header、front page lead search、`get_search_form()` fallback。升级时统一为一个可维护的 search form contract：

- `searchform.php` 负责标准结构和可翻译 label/placeholder；
- header 和首页如需不同视觉，只通过 wrapper class 变体，不复制不同的表单语义；
- `<form role="search">`、`method="get"`、正确的 home URL 和 `name="s"` 保持一致；
- 输入框有屏幕阅读器 label，placeholder 不是唯一标签；
- 手机 global menu 提供搜索路径；
- 404、空搜索结果和普通 Posts surface 共用同一安全实现。

### 1.3 `readme.txt` 发布格式

最终 readme 至少包含：

```text
=== Docspress ===
Contributors: <真实 WordPress.org 用户名>
Requires at least: <已验证版本>
Tested up to: <实际测试的版本>
Requires PHP: <实际支持的最低版本>
Stable tag: <发布版本>
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Short description under the directory limit.

== Description ==
== Frequently Asked Questions ==
== Installation ==
== Changelog ==
== Upgrade Notice ==
== Resources ==
```

特别要求：

- 所有 WordPress 公共文案拼写为 `WordPress`；
- short description 不写营销夸张、关键词堆砌或未验证功能；
- FAQ 明确 Docs Root、Native Pages、Section Index、可选插件和 fallback；
- Upgrade Notice 只写对用户有意义的升级理由；
- Resources 列出图片、图标、字体、第三方代码及其许可证/来源；
- 未上架 WordPress.org 的 Docspress Editor plugin 不作为具体可安装推荐写入提交版 readme；
- 不在 readme 或主题前端宣传 GitHub、付费服务、affiliate 或外部下载。

### 1.4 主题头信息

最终检查 `style.css`：

- `Theme Name`、`Author`、`Description`、`Version`；
- `Requires at least`、`Tested up to`、`Requires PHP` 与真实测试证据一致；
- `License`、`License URI`、`Text Domain`；
- 如使用 `Theme URI`，必须指向 Docspress 主题本身；
- 如使用 `Author URI`，必须指向作者/项目，不伪装成 WordPress.org 官方页面；
- 如使用 Tags，只使用 WordPress.org 允许的主题标签；
- `Domain Path` 与语言目录一致时明确写入，避免翻译路径歧义。

## Gate 2 — WordPress.org 代码与安全合规

### 2.1 Classic Theme 标准调用

对每个模板做静态和运行时检查：

- 有效 DOCTYPE 和 `language_attributes()`；
- `get_header()`、`get_footer()`、`get_sidebar()`、`get_template_part()` 用于相应模板/部件；
- `wp_head()` 紧邻 `</head>` 前；
- `wp_body_open()` 紧邻 `<body>` 后；
- `body_class()` 在 body；
- `post_class()` 在可识别文章/Page；
- `wp_footer()` 紧邻 `</body>` 前；
- `wp_link_pages()` 在支持分页内容的文章/Page；
- `title-tag`、automatic-feed-links 等支持与实际模板一致；
- 静态首页和 Posts Page 都遵循 WordPress front-page 设置。

如果主题包含 `searchform.php`、`sidebar.php`、`comments.php` 等标准文件，确保调用链确实通过 WordPress 约定函数进入，而不是只把文件放进去满足目录检查。

### 2.2 Prefix 和命名

官方要求公共命名空间使用唯一前缀，至少四个字母。Docspress 当前 PHP 前缀方向正确，但提交前需要全量审计：

- functions、global variables、constants；
- theme mods/settings/customizer keys；
- enqueue handles；
- data attributes、DOM IDs、JS globals；
- future image sizes、filters、actions、meta keys；
- CSS public selectors。

允许保留 WordPress 标准的 menu location/sidebar IDs，但不新增短、泛、无前缀的公开功能标识。

### 2.3 数据和输出

- 保存前按数据类型 sanitize/validate；
- 输出时按 URL、attribute、text、HTML 上下文分别 escape；
- `the_content` 先经过标准过滤，再做受限的 heading decoration；
- `wp_kses_post()` 只用于允许的编辑 HTML，不能成为绕过上下文 escaping 的总开关；
- capability 使用 `edit_theme_options` 或实际对象能力，不依赖角色名称；
- 不使用 deprecated 函数；
- 不打印 PHP warning、notice、JS error 或 CSS parse error；
- 不读取或输出秘密、服务器路径、内部调试信息。

### 2.4 主题设置和数据库

当前 Customizer 使用 `theme_mod`，方向可以保留。提交前仍需确认：

- Docs Root 选择使用 `absint` 且对删除/非 Page/无权限状态安全降级；
- footer text 使用 `sanitize_text_field`，前端只以纯文本输出；
- 没有多余 option、激活即写入数据库或未经同意修改核心设置；
- 没有 activation redirect、不可关闭的 admin notice 或遮挡 admin bar 的代码；
- 不把 demo content 自动插入主题激活流程；
- admin UI 使用核心 Customizer 控件，不泄漏样式到其他后台页面。

### 2.5 功能边界和隐私

- 无 Analytics、Tracking、Remote Fonts、CDN 或隐式数据收集；
- 无 contact form、SEO 设置、缓存系统、社交分享写入、affiliate 跳转；
- 无远程请求、自动下载或自动安装插件；
- 如果未来增加数据收集，必须默认关闭、明确 opt-in，并在 readme 中说明；
- 主题前端 footer 只能显示用户的版权信息，不显示主题作者强制版权宣传。

## Gate 3 — 视觉系统硬化

详细设计契约见 [`docs/design.md`](../docs/design.md)，当前实施必须遵守以下顺序。

### 3.1 先锁定 Token，不先调局部像素

把静态基准统一成语义 Token：

```text
surface / soft / sidebar / ink / muted / faint / line / accent
header height / local height / left width / right width / page padding
article max / prose target / rail / rail gap / z-index layers
```

禁止在单个组件中重新发明同义颜色和尺寸。所有精调必须先问：这是全局 Token 变化，还是组件特例？

### 3.2 CSS 级联重构

当前 `main.css` 已经包含设计基准、WordPress integration 和后置 corrections。重构必须先保持截图相同，再做美学调整。

建议源代码顺序：

1. tokens/reset；
2. base typography/forms/links；
3. shell/header/footer；
4. Docs Tree/local navigation/drawers/TOC；
5. article rail/prose/editorial blocks/media；
6. Home/Section index；
7. standard WordPress surfaces；
8. responsive states；
9. accessibility/reduced motion/RTL；
10. documented compatibility rules。

必须消除：

- 同一 selector 在多个“修正”区重复定义；
- generic `.article`、`.section`、`.note` 等无范围类名继续扩散；
- 通过 `!important` 解决本应由层级解决的问题；
- 全局 `overflow-x:hidden` 掩盖真正的溢出问题；
- 仅因为静态截图好看就删除 focus 状态。

### 3.3 视觉验收的逐项对象

对每个对象检查：

- 它在页面结构中的身份是否一眼可辨；
- 它与相邻元素的间距是否表达关系；
- 它在 hover/focus/active/current/disabled 状态是否一致；
- 它在中文、英文、混合文字和长标题下是否仍稳定；
- 它是否在手机上改变了位置但保留了语义；
- 它是否因为边框、背景、阴影而变成不必要的卡片；
- 它是否让主要阅读线、标题层级或 Docs Tree 产生竞争。

### 3.4 重点视觉修改

按优先级：

1. 完成可见 `:focus-visible`，不要只做 hover；
2. 手机 global menu 增加真正可用的 search path；
3. drawers 处理 focus boundary、Escape、backdrop、resize、scrollbar shift；
4. 用明确的 wrapper class 替代 `:has()` 作为图片核心布局依据；
5. 用统一的 SVG/CSS icon 代替平台差异明显的 `☰`、`⌕`、`→`、`×`；
6. 将 H2 rail、TOC indent、caption、table、Prev/Next 的数值收回 Token；
7. 对 `rtl.css` 使用 logical properties 或逐项验证左右方向；
8. editor.css 与前端正文、H1/H2/H3、figure、caption、table、blockquote 保持节奏一致。

## Gate 4 — Classic Editor 和内容鲁棒性

WordPress.org 会用 Theme Unit Test 数据检查主题，而不是只用 Docspress 自己的示例文案。必须同时测试：

### 4.1 文本

- 短标题、长中文标题、混合中英标题、标点和数字；
- 无 excerpt、单行 excerpt、多行 excerpt；
- H2/H3 缺失、重复、已有 ID、多语言 ID；
- H4/H5、列表、嵌套列表、预格式文本、长 URL；
- content pagination 和 `wp_link_pages()`；
- password-protected Page/Post。

### 4.2 媒体和标准内容

- 无特色图、有特色图、原始比例图片；
- WordPress image alignment、caption、gallery；
- inline/margin/breakout/float 的明确降级；
- block editor 生成的标准 HTML 在 Classic Theme 中仍然可读；
- audio/video/embed、blockquote、table、`<hr>`；
- 宽表格只在明确的 `.table-wrap` 内滚动，不造成整个 viewport 横向溢出。

### 4.3 内容边界

- Docs Root 缺失、被删除、不是 Page、没有 children；
- 页面深度超过 3 层；
- Section Index 没有 children；
- primary/footer menu 没有分配；
- plugin 不存在或停用；
- 普通 Page/Post 不在 Docs Tree 中；
- comments、archives、search、404、feeds 仍有明确的标准模板表现。

## Gate 5 — 可访问性和实际视觉 QA

### 5.1 Viewport matrix

必须保存最终候选版截图/记录：

```text
1440 × 900   三栏阅读表面
1280 × 800   右 TOC 消失临界
1024 × 768   左树/本地导航临界
900  × 800   平板导航状态
768  × 1024  平板竖屏
390  × 844   手机单列和 drawers
```

### 5.2 Keyboard matrix

- Skip link 是第一个可感知的焦点元素，并移动到 `#primary`；
- header menu、search、tree toggle、tree link、TOC link、close button 都可 Tab 到达；
- focus ring 在白色和浅灰背景都明显；
- drawer 打开后 focus 不逃出；
- Escape、close button、backdrop、导航链接关闭路径可预测；
- drawer 关闭后焦点回到 opener；
- `aria-expanded`、`aria-hidden`、`aria-current` 与真实状态一致；
- `inert` 不让隐藏 DOM 继续参与键盘顺序；
- reduced motion 关闭 smooth scroll 和长 transition；
- 200% zoom、系统字体放大和键盘导航不破坏布局。

### 5.3 视觉精度审查

逐项对照静态 HTML：

1. header 的高度、边线、brand mark、nav gap、search field；
2. 左侧 Docs Tree 的背景延伸、开合箭头、当前页/当前祖先状态；
3. Home 的 H1/deck/search/section rows，不变成卡片；
4. Section 的 group rule、编号、子目录缩进；
5. Article 的 H1、deck、metadata、prose measure；
6. H2 rail、H3 prose、TOC active line、scroll offset；
7. margin note/figure 与正文的垂直对应；
8. definition/context/quote/table/caption 的密度；
9. Previous/Next 的边线、对齐和长标题换行；
10. footer、comments、archive、search、404 与 docs surface 的统一程度。

“像截图”不能作为唯一验收。正确标准是：改变标题、语言、层级和内容长度后，页面仍保持同一视觉逻辑。

## Gate 6 — 测试工具和证据包

提交前建立可重复的检查命令和证据目录：

### 6.1 Static checks

- PHP syntax：每个 PHP 文件；
- JavaScript syntax：每个非第三方 JS；
- CSS parse：`style.css`、主 CSS、editor CSS、RTL CSS；
- PHPCS + WordPress Coding Standards；
- Theme Check/current official review tooling；
- translation/string scan；
- prohibited pattern scan；
- no secrets/paths/reference identity scan。

### 6.2 Runtime checks

- disposable WordPress with `WP_DEBUG` and `WP_DEBUG_LOG`；
- Theme activation/deactivation；
- static front page/Posts Page；
- Customizer save/load；
- menus/logo/widgets/editor styles；
- Theme Unit Test content；
- Classic Editor fixture；
- plugin absent/present fixture；
- all required routes and browser console。

### 6.3 Package checks

- reproducible package script；
- exactly one top-level `docspress/` folder；
- no project docs/reference/site content/Git/log/database/secrets；
- `unzip -t`；
- final SHA-256；
- extracted package file manifest compared against intended source；
- exact ZIP installed and tested, not a different local folder。

证据包不放入提交 ZIP，但要留在项目 `build/` 或发布记录中，便于 reviewer 回复和后续更新。

## Gate 7 — WordPress.org 提交

### 7.1 提交前检查

只有以下条件全部满足才允许进入上传动作：

- P0/P1 清零；
- Theme Check/WPCS/运行时/视觉 QA 记录完成；
- 名称和贡献者身份确认；
- GPL/资源清单完成；
- `readme.txt` 完整且无外部未托管插件推荐；
- 最终 ZIP 已安装测试并有 SHA-256；
- 用户明确确认“提交 WordPress.org”这一外部发布动作。

### 7.2 审核期间

- 一次只提交一个新主题；
- 不创建重复主题票据；
- reviewer 提出问题后，按问题编号逐项回复；
- 不用新功能转移审查焦点；
- 每次修订重新生成 ZIP、重跑检查、记录版本和 checksum；
- 如果 reviewer 要求名称、截图、readme 或功能边界调整，先更新设计/发布记录，再改代码。

### 7.3 公开发布后

- 支持入口、升级说明和 changelog 保持有效；
- WordPress.org 版本与项目 source/build/hash 对应；
- 更新只发布完整、可安装、已测试的 Theme ZIP；
- 继续保持主题无远程依赖、无隐式跟踪、无 plugin 打包；
- 每次更新先验证现有 Docspress 内容和普通 WordPress 内容可迁移性。

## 4. 推荐的实施顺序

### Phase A — 发布阻塞修复

1. 增加 `searchform.php`；
2. 统一所有搜索入口；
3. 确认 Contributors/Author/版权；
4. 完整化 `readme.txt`；
5. 建立 Resources 清单和许可证检查；
6. 确认 companion plugin 的 WordPress.org 推荐边界；
7. 复核主题名/slug 风险。

### Phase B — 不改变视觉的代码整理

1. CSS cascade 分层；
2. generic selector 命名空间化；
3. `:has()` 媒体 fallback；
4. JS 面板状态控制整理；
5. searchform、templates、comments、pagination 调用链整理；
6. 更新 POT 和 static contract tests。

### Phase C — 视觉精修

1. Token 校准；
2. header/Docs Tree/Article rail/TOC 像素级对照；
3. focus-visible、drawer、scroll lock；
4. mobile search 和 icon system；
5. CJK、长标题、RTL、200% zoom；
6. editor.css 和普通 WordPress surface；
7. 更新真实主题截图。

### Phase D — 真实 WordPress 验收

1. Theme Unit Test Data；
2. Classic Editor/标准媒体/评论/搜索/归档/Feed；
3. plugin absent/present；
4. viewport/keyboard/reduced-motion matrix；
5. WP_DEBUG、console、network、asset cache；
6. Theme Check/WPCS/包内容。

### Phase E — RC 和提交

1. 发布 `0.9.0-rc.1` 内部候选版；
2. 只修复阻塞和回归，不继续扩大功能；
3. 生成最终 ZIP、manifest、SHA-256；
4. 用户确认 WordPress.org 外部提交；
5. 上传并进入 Theme review；
6. 按 reviewer ticket 迭代；
7. 通过后记录正式版本和公开链接。

## 5. Definition of Done

Docspress 只有在以下条件全部满足时，才可以称为“达到 WordPress.org 发布标准”：

- Classic Theme 必需文件和调用链完整；
- `searchform.php` 与所有 `get_search_form()` 调用一致；
- `readme.txt`、许可证、作者、Contributor、Resources 和 Upgrade Notice 可审查；
- ZIP 只有 Docspress Theme，不含内容、研究材料、插件、脚本、凭据或项目文档；
- 无自有 CPT、Shortcode、Custom Block、远程资源、跟踪、自动安装或非表现层功能；
- 所有公共字符串可翻译，POT 与源码同步；
- PHP/JS/CSS 无错误，WPCS 和 Theme Check 记录明确；
- Theme Unit Test、Classic Editor、标准媒体和普通 WordPress fallback 通过；
- 1440/1280/1024/900/768/390px 视觉验收通过；
- 键盘、焦点、Skip Link、drawer、Escape、inert、reduced motion、200% zoom、RTL 通过；
- 视觉仍然保持文档阅读系统，不变成卡片 dashboard 或营销首页；
- 最终 ZIP 已用与提交相同的文件进行安装测试并记录 SHA-256；
- 用户明确批准 WordPress.org 提交这一不可逆的对外发布动作。

## 6. 官方依据

- [Required Theme Files](https://developer.wordpress.org/themes/releasing-your-theme/required-theme-files/)
- [Releasing Your Theme](https://developer.wordpress.org/themes/releasing-your-theme/)
- [Required Theme Review Requirements](https://make.wordpress.org/themes/handbook/review/required/)
- [Submitting Your Theme to WordPress.org](https://developer.wordpress.org/themes/releasing-your-theme/submitting-your-theme-to-wordpress-org/)
- [Classic Theme Template Files](https://developer.wordpress.org/themes/classic-themes/basics/template-files/)
- [Main Stylesheet `style.css`](https://developer.wordpress.org/themes/classic-themes/basics/main-stylesheet-style-css/)
- [WordPress Theme Structure](https://developer.wordpress.org/themes/core-concepts/theme-structure/)

## 7. 当前结论

Docspress 的核心视觉和内容模型已经成立，当前不需要换成 Block Theme，也不需要重写为插件。升级路线应当是：

```text
先清零 WordPress.org 发布阻塞
→ 再整理 CSS/交互基础
→ 再做视觉细节校准
→ 再用 Theme Unit Test 和真实 WordPress 验收
→ 最后才进行 WordPress.org 外部提交
```

下一步最小实施单元是 Phase A：先处理 `searchform.php`、readme/资源许可、贡献者身份和 plugin 推荐边界；完成后再进入主题代码的视觉硬化。
