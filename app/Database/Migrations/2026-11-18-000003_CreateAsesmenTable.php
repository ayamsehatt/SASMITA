<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAsesmenTable extends Migration
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
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
            ],
            'type' => [
                'type'    => "ENUM('tugas','formatif','sumatif')",
                'null'    => false,
                'default' => 'formatif',
            ],
            'assessment_date' => [
                'type' => 'DATE',
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
        $this->forge->addKey('assessment_date');
        $this->forge->createTable('asesmen', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('asesmen', true);
    }
}
