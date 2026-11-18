<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengaturanTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kunci' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
            ],
            'nilai' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kunci');
        $this->forge->createTable('pengaturan', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('pengaturan', true);
    }
}
