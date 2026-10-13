<?php

declare(strict_types=1);

namespace app\command;

use Lychee\console\Command;
use Lychee\console\Input;
use Lychee\console\Output;
use Lychee\console\input\Argument;
use Lychee\console\input\Option;

/**
 * 生成宝塔面板 Nginx 站点配置命令。
 *
 * 用法：php lee make:nginx example.com [--dir=项目目录名] [--force]
 * 在项目根目录生成 nginx.conf，域名相关路径（站点根目录、
 * 证书校验 include、日志文件）均按输入域名自动填充。
 * 站点目录与域名不同名时，用 --dir 指定 /www/wwwroot/{域名}/ 下的项目目录名。
 */
class NginxConfCommand extends Command
{
    protected string $name = 'make:nginx';

    protected string $description = 'Generate a BaoTa nginx.conf in the project root';

    protected function configure(): void
    {
        $this->setName($this->name);
        $this->setDescription($this->description);
        $this->addArgument('domain', Argument::REQUIRED, '站点域名，例如 example.com');
        $this->addOption('dir', null, Option::VALUE_OPTIONAL, '站点目录下的项目目录名（默认与域名同级）');
        $this->addOption('force', 'f', Option::VALUE_NONE, '已存在 nginx.conf 时强制覆盖');
    }

    protected function execute(Input $input, Output $output): int
    {
        $domain = strtolower(trim((string) $input->getArgument('domain')));

        if (!preg_match('/^(?=.{1,253}$)([a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/', $domain)) {
            $output->writeln('<error>域名格式不正确，请输入合法域名，例如 example.com</error>');

            return 1;
        }

        $dir = trim((string) ($input->getOption('dir') ?? ''));

        if ($dir !== '' && !preg_match('/^[a-zA-Z0-9._-]+$/', $dir)) {
            $output->writeln('<error>--dir 只能包含字母、数字、点、下划线和连字符。</error>');

            return 1;
        }

        $publicRoot = '/www/wwwroot/' . $domain . ($dir !== '' ? '/' . $dir : '') . '/public';

        $target = $this->app->rootPath . 'nginx.conf';

        if (is_file($target) && !$input->getOption('force')) {
            $output->writeln('<error>nginx.conf 已存在，使用 --force（-f）可覆盖。</error>');

            return 1;
        }

        $result = file_put_contents($target, $this->buildConfig($domain, $publicRoot));

        if ($result === false) {
            $output->writeln('<error>nginx.conf 写入失败，请检查根目录写权限。</error>');

            return 1;
        }

        $output->writeln("<info>Created:</info> nginx.conf（域名：{$domain}）");
        $output->writeln("<comment>站点根目录：{$publicRoot}</comment>");

        return 0;
    }

    /**
     * 按域名与站点根目录渲染宝塔风格的站点配置。
     */
    private function buildConfig(string $domain, string $publicRoot): string
    {
        return <<<NGINX
server
{
    listen 80;
    listen [::]:80;
    server_name {$domain};
    # Web 根目录必须指向框架入口目录 public，禁止指向项目根目录（否则 app/config/.env/vendor 等会被外网访问）
    root {$publicRoot};
    # 必须包含 index.php：访问 / 时 try_files 的 \$uri/ 命中目录后由 index 模块找入口，
    # 找不到且 autoindex 关闭会直接 403，无法回退到 /index.php
    index index.php index.html index.htm;
    #CERT-APPLY-CHECK--START
    # 用于SSL证书申请时的文件验证相关配置 -- 请勿删除
    include /www/server/panel/vhost/nginx/well-known/{$domain}.conf;
    #CERT-APPLY-CHECK--END
    include /www/server/panel/vhost/nginx/extension/{$domain}/*.conf;

    #SSL-START SSL相关配置，请勿删除或修改下一行带注释的404规则
    #error_page 404/404.html;
    #SSL-END

    #ERROR-PAGE-START  错误页配置，可以注释、删除或修改
    # 404/异常页由 Lychee 框架统一渲染，不配置静态错误页，避免拦截框架响应
    #error_page 502 /502.html;
    #ERROR-PAGE-END

    #PHP-INFO-START  PHP引用配置，可以注释或修改
    include enable-php-83.conf;
    #PHP-INFO-END

    #REWRITE-START URL重写规则引用,修改后将导致面板设置的伪静态规则失效
    include /www/server/panel/vhost/rewrite/{$domain}.conf;
    #REWRITE-END

    # 后台前端 SPA：构建产物在 public/admin（rsbuild admin-build），Vue Router history 模式 base=/admin/
    location = /admin {
        return 301 /admin/;
    }
    location /admin/ {
        # 已构建的静态资源（js/css/图片）直接返回，前端路由路径统一回退到 index.html
        # 后台接口实际为 /api/admin/*，不会进入此 location
        try_files \$uri \$uri/ /admin/index.html;
    }

    # 前台页面与 /api 接口：存在的静态文件直接返回，其余全部交给 Lychee 单入口
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # 禁止访问的敏感文件
    location ~* (\\.user.ini|\\.htaccess|\\.htpasswd|\\.env.*|\\.project|\\.bashrc|\\.bash_profile|\\.bash_logout|\\.DS_Store|\\.gitignore|\\.gitattributes|LICENSE|README\\.md|CLAUDE\\.md|CHANGELOG\\.md|CHANGELOG|CONTRIBUTING\\.md|TODO\\.md|FAQ\\.md|composer\\.json|composer\\.lock|package(-lock)?\\.json|yarn\\.lock|pnpm-lock\\.yaml|\\.\\w+~|\\.swp|\\.swo|\\.bak(up)?|\\.old|\\.tmp|\\.temp|\\.log|\\.sql(\\.gz)?|docker-compose\\.yml|docker\\.env|Dockerfile|\\.csproj|\\.sln|Cargo\\.toml|Cargo\\.lock|go\\.mod|go\\.sum|phpunit\\.xml|phpunit\\.xml|pom\\.xml|build\\.gradl|pyproject\\.toml|requirements\\.txt|application(-\\w+)?\\.(ya?ml|properties))$
    {
        return 404;
    }

    # 禁止访问的敏感目录
    location ~* /(\\.git|\\.svn|\\.bzr|\\.vscode|\\.claude|\\.idea|\\.ssh|\\.github|\\.npm|\\.yarn|\\.pnpm|\\.cache|\\.husky|\\.turbo|\\.next|\\.nuxt|node_modules|runtime)/ {
        return 404;
    }

    #一键申请SSL证书验证目录相关设置
    location ~ \\.well-known{
        allow all;
    }

    #禁止在证书验证目录放入敏感文件
    if ( \$uri ~ "^/\\.well-known/.*\\.(php|jsp|py|js|css|lua|ts|go|zip|tar\\.gz|rar|7z|sql|bak)\$" ) {
        return 403;
    }

    location ~* \\.(gif|jpg|jpeg|png|bmp|swf|webp|ico|svg)\$
    {
        expires      30d;
        error_log /dev/null;
        access_log /dev/null;
    }

    location ~* \\.(js|css|woff2?|ttf|eot)\$
    {
        expires      12h;
        error_log /dev/null;
        access_log /dev/null;
    }
    access_log  /www/wwwlogs/{$domain}.log;
    error_log  /www/wwwlogs/{$domain}.error.log;
}
NGINX;
    }
}
