<?php

declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 后台用户。
 *
 * @property int    $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $avatar
 * @property string $role
 * @property int    $role_id
 * @property int    $status
 * @property string $last_login_time
 * @property string $create_time
 * @property string $update_time
 */
class User extends Model
{
    protected $hidden = ['password'];

    /**
     * 密码写入时自动哈希。
     */
    public function setPasswordAttr(string $value): string
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }
}
