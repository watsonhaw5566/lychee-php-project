# Lychee PHP 应用模板

基于 [Lychee PHP](https://lychee-php.watsonhaw.top/) 框架的应用骨架。

## 环境要求

- PHP >= 8.2
- Composer

## 安装

```bash
composer create-project watsonhaw/lychee-php my-app
cd my-app
cp .env.example .env
```

## 目录结构

```
app/
  controller/   # 控制器
  model/        # 模型
  middleware/   # 中间件
  view/         # Twig 模板
  lang/         # 语言包
config/         # 配置文件
database/
  migrations/   # 数据库迁移
  seeders/      # 数据填充
public/         # Web 根目录
  index.php     # 入口文件
runtime/        # 运行时缓存/日志/会话
lee             # 命令行入口
```

## 启动开发服务器

```bash
php lee run
# 默认 http://127.0.0.1:8000
```

## 常用命令

```bash
php lee run                  # 启动开发服务器
php lee list                 # 查看所有命令
php lee route:list           # 查看路由列表
php lee migrate:run          # 执行迁移
php lee migrate:rollback     # 回滚迁移
php lee cron:run             # 运行定时任务
php lee queue:work           # 启动队列消费者
```
