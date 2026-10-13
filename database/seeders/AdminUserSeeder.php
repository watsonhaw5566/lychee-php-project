<?php

declare(strict_types=1);

use Lychee\migration\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'admin@qq.com';

        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM `' . $this->prefix . 'user` WHERE `email` = ?'
        );
        $stmt->execute([$email]);

        if ((int) $stmt->fetchColumn() > 0) {
            return;
        }

        $this->insert('user', [
            'username' => 'admin',
            'email'    => $email,
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'avatar'   => '',
            'role'     => 'admin',
            'role_id'  => 1,
            'status'   => 1,
        ]);
    }
}
