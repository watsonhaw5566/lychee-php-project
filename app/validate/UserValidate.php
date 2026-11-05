<?php

declare(strict_types=1);

namespace app\validate;

use Lychee\validate\Validate;

/**
 * 用户相关接口的数据校验。
 *
 * 用法：
 *   (new User())->scene('login')->failException()->check($data);
 *
 * 除 require 外的规则在字段缺失或为空时自动跳过，
 * 因此可选字段只需声明非 require 规则即可。
 */
class UserValidate extends Validate
{
    protected array $rule = [
        'username'         => 'require|max:50',
        'email'            => 'require|email',
        'password'         => 'require|min:6',
        'confirm_password' => 'require|confirm:password',
        'role'             => 'in:admin,user',
        'status'           => 'in:0,1',
    ];

    protected array $message = [
        'username.require'         => '用户名不能为空',
        'username.max'             => '用户名不能超过 50 个字符',
        'email.require'            => '邮箱不能为空',
        'email.email'              => '邮箱格式不正确',
        'password.require'         => '密码不能为空',
        'password.min'             => '密码长度不能少于 6 位',
        'confirm_password.require' => '请确认密码',
        'confirm_password.confirm' => '两次输入的密码不一致',
        'role.in'                  => '角色值非法',
        'status.in'                => '状态值非法',
    ];

    protected array $scene = [
        // 邮箱登录
        'login'           => ['email', 'password'],
        // 新建用户（管理后台）
        'register'        => ['username', 'email', 'password'],
        // 修改当前登录用户基础信息
        'change_info'     => ['username', 'email'],
        // 修改当前登录用户密码
        'change_password' => ['password', 'confirm_password'],
    ];
}
