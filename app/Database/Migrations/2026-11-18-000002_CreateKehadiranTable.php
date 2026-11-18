<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKehadiranTable extends Migration
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
            'student_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'attendance_date' => [
                'type' => 'DATE',
            ],
            'status' => [
                'type'    => "ENUM('H','S','I','A')",
                'null'    => false,
                'default' => 'H',
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
        $this->forge->addKey(['student_id', 'attendance_date'], false, true);
        $this->forge->addKey('attendance_date');
        $this->forge->addForeignKey('student_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kehadiran', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('kehadiran', true);
    }
}
