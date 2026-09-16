<?php

declare(strict_types=1);

namespace App\model;

use think\Model;

/**
 * 用户模型。
 *
 * @property int    $id
 * @property string $name
 * @property string $email
 * @property int    $age
 * @property string $created_at
 * @property string $updated_at
 */
class User extends Model
{
    protected $name = 'users';
}
