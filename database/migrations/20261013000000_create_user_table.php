<?php

declare(strict_types=1);

use Lychee\migration\Migration;

class CreateUserTableMigration extends Migration
{
    public function up(): void
    {
        $this->table('user')
            ->addColumn('username', 'string', ['length' => 50])
            ->addColumn('email', 'string', ['length' => 255])
            ->addColumn('password', 'string', ['length' => 255])
            ->addColumn('avatar', 'string', ['length' => 500, 'null' => true])
            ->addColumn('role', 'string', ['length' => 20, 'default' => 'admin'])
            ->addColumn('role_id', 'integer', ['unsigned' => true, 'null' => true])
            ->addColumn('status', 'tinyinteger', ['default' => 1])
            ->addColumn('last_login_time', 'datetime', ['null' => true])
            ->addDatetimes()
            ->addIndex(['email'], ['type' => 'UNIQUE'])
            ->create();
    }

    public function down(): void
    {
        $this->table('user')->drop();
    }
}
