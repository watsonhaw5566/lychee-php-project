# Lychee Admin 开箱即用后台管理系统

基于 [Lychee PHP](https://lychee-php.watsonhaw.top/) 框架与 Vue 3 + Arco Design 的前后端分离后台管理骨架，内置管理员登录、用户管理与控制台统计，克隆即可二次开发。

## 功能特性

- 邮箱 + 密码登录，基于 Sa-Token 的接口鉴权（`Authorization: Bearer <token>`）
- 控制台用户数据概览（用户总数 / 管理员 / 正常 / 禁用）
- 用户管理：分页列表、用户名/邮箱模糊搜索、角色与状态过滤、新增、编辑、启用禁用
- Vue 3 + Arco Design Pro 管理端，内置荔枝粉主题与多主题色切换、暗色模式、中英文国际化
- 数据库迁移 + 数据填充，一条命令初始化默认管理员

## 技术栈

| 端   | 技术                                                        |
|-----|-----------------------------------------------------------|
| 后端  | PHP 8.2+、Lychee PHP                                       |
| 前端  | Vue 3、TypeScript、Rsbuild、Arco Design Vue、Pinia、Vue Router |
| 数据库 | MySQL 5.7+ / 8.0                                          |

## 环境要求

- PHP >= 8.2、Composer
- Node.js >= 20、Yarn
- MySQL

## 快速开始

### 1. 启动后端（端口 8000）

```bash
composer install
cp .env.example .env       # 填写 DB_DATABASE / DB_USERNAME / DB_PASSWORD

php lee migrate:run        # 创建 user 等数据表
php lee seed:run           # 写入默认管理员
php lee run                # 启动开发服务器 http://127.0.0.1:8000
```

### 2. 启动管理前端（端口 5173）

```bash
cd web/admin
yarn install
yarn admin-dev             # http://localhost:5173
```

前端开发服务器已将 `/api` 请求代理到 `http://localhost:8000`（见 `web/admin/config/rsbuild.config.ts`），无需处理跨域。

### 3. 登录

| 项目 | 值 |
| --- | --- |
| 邮箱 | `admin@qq.com` |
| 密码 | `admin123` |

## 目录结构

```
app/
  controller/
    UserController.php       # 登录/登出/当前用户 + 用户资源 CRUD
    DashboardController.php  # 控制台统计
  model/
    User.php                 # 用户模型（密码写入自动哈希，输出隐藏 password）
  middleware/                # 中间件
  view/                      # Twig 模板
config/                      # 框架配置（database、satoken 等）
database/
  migrations/                # 数据库迁移（user 表）
  seeders/                   # 数据填充（默认管理员）
public/
  index.php                  # Web 入口文件
runtime/                     # 运行时缓存/日志/会话
web/admin/                   # Vue 3 管理前端
  src/
    api/                     # 接口封装（user、dashboard …）
    views/login              # 登录页
    views/dashboard/workplace# 控制台
    views/account/list       # 用户管理
  config/rsbuild.config.ts   # Rsbuild 配置（端口、/api 代理）
lee                          # 命令行入口
```

## HTTP 接口

除登录外均需携带请求头 `Authorization: Bearer <token>`。

| 方法 | 路径 | 说明 | 鉴权 |
| --- | --- | --- | --- |
| POST | `/api/user/login` | 邮箱登录，返回 token | 否 |
| POST | `/api/user/logout` | 退出登录 | 是 |
| POST | `/api/user/info` | 当前登录用户信息 | 是 |
| GET | `/api/user` | 用户分页列表（`current`、`pageSize`、`username`、`email`、`role`、`status`） | 是 |
| POST | `/api/user` | 新建用户 | 是 |
| PUT | `/api/user/{id}` | 更新用户（密码留空表示不修改） | 是 |
| DELETE | `/api/user/{id}` | 删除用户 | 是 |
| GET | `/api/dashboard/stats` | 用户数统计 | 是 |

统一响应格式：

```json
{ "errno": 0, "code": 200, "msg": "success", "data": {} }
```

## 常用命令

```bash
# —— 后端 ——
php lee run                  # 启动开发服务器
php lee list                 # 查看所有命令
php lee route:list           # 查看路由列表
php lee migrate:run          # 执行迁移
php lee migrate:rollback     # 回滚迁移
php lee seed:run             # 执行数据填充
php lee cron:run             # 运行定时任务
php lee queue:work           # 启动队列消费者

# —— 前端（web/admin 目录下）——
yarn admin-dev               # 启动开发服务器（5173）
yarn admin-build             # 生产构建，产物输出到 dist/
yarn preview                 # 本地预览生产产物
yarn lint                    # ESLint 检查
yarn type:check              # TypeScript 类型检查
```

## 生产部署

1. 在 `web/admin` 下执行 `yarn admin-build`，将 `dist/` 产物交由 Nginx 等 Web 服务器托管；
2. 后端以 `public/` 为站点根目录，配置 URL 重写将非静态请求转发到 `index.php`；
3. 前端的 `/api` 路径反向代理到 PHP 服务（PHP-FPM / `php lee run`）；
4. 生产环境 `.env` 中设置 `APP_DEBUG=false`。

## 框架文档

更多 Lychee PHP 框架用法见官方文档 [https://lychee-php.watsonhaw.top](https://lychee-php.watsonhaw.top)，涵盖控制器、ORM、鉴权、缓存、队列、验证、部署等主题。
