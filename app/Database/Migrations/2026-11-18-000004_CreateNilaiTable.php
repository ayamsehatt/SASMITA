<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNilaiTable extends Migration
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
            'assessment_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'student_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'score' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->addKey(['assessment_id', 'student_id'], false, true);
        $this->forge->addForeignKey('assessment_id', 'asesmen', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('student_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('nilai', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('nilai', true);
    }
}
