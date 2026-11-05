<?php

declare(strict_types=1);

namespace app\model;

use Lychee\orm\Model;

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
    /** 序列化时隐藏密码 */
    protected array $hidden = ['password'];

    /**
     * 密码写入时自动哈希。
     */
    public function setPasswordAttr(mixed $value): string
    {
        return password_hash((string) $value, PASSWORD_DEFAULT);
    }
}
