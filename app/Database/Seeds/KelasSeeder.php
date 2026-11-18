<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Data contoh untuk modul analitik kelas.
 *
 * Seeder ini sengaja dijalankan sekali: bila tabel students sudah berisi
 * data, proses dihentikan agar input guru tidak ditimpa.
 */
class KelasSeeder extends Seeder
{
    public function run(): void
    {
        if ($this->db->table('students')->countAll() > 0) {
            return;
        }

        $now = date('Y-m-d H:i:s');

        $names = [
            'Ahmad Fauzi', 'Aisyah Putri', 'Bima Saputra', 'Citra Maharani',
            'Dhika Pratama', 'Elena Kusuma', 'Farhan Maulana', 'Gita Anggraini',
            'Hafiz Ramadhan', 'Intan Permata', 'Joko Susilo', 'Kirana Dewi',
        ];

        $students = [];

        foreach ($names as $index => $name) {
            $students[] = [
                'nis'        => '2024' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'name'       => $name,
                'class_name' => 'VII A',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $this->db->table('students')->insertBatch($students);

        // Ambil id siswa sesuai urutan insert agar data kehadiran dan nilai
        // bisa dipetakan dengan benar.
        $rows   = $this->db->table('students')->orderBy('id', 'ASC')->get()->getResultArray();
        $ids    = array_column($rows, 'id');
        $dates  = $this->recentWeekdays(3);
        $status = [
            ['H', 'H', 'S', 'H', 'H', 'I', 'H', 'H', 'A', 'H', 'H', 'H'],
            ['H', 'H', 'H', 'H', 'I', 'H', 'S', 'H', 'H', 'H', 'A', 'H'],
            ['H', 'S', 'H', 'H', 'H', 'H', 'H', 'I', 'H', 'H', 'H', 'H'],
        ];

        $attendance = [];

        foreach ($dates as $day => $date) {
            foreach ($ids as $index => $studentId) {
                $attendance[] = [
                    'student_id'      => $studentId,
                    'attendance_date' => $date,
                    'status'          => $status[$day][$index],
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            }
        }

        $this->db->table('attendance')->insertBatch($attendance);

        $assessments = [
            ['title' => 'Ulangan Harian Bab 1', 'type' => 'formatif', 'assessment_date' => $dates[0]],
            ['title' => 'Tugas Proyek Sederhana', 'type' => 'tugas', 'assessment_date' => $dates[1]],
        ];

        $assessmentIds = [];

        foreach ($assessments as $assessment) {
            $this->db->table('assessments')->insert([
                'title'           => $assessment['title'],
                'type'            => $assessment['type'],
                'assessment_date' => $assessment['assessment_date'],
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);

            $assessmentIds[] = $this->db->insertID();
        }

        $values = [
            [85, 92, 76, 88, 64, 90, 82, 79, 95, 71, 68, 87],
            [90, 88, null, 84, 72, 94, 80, 86, 91, 78, 65, 89],
        ];

        $scores = [];

        foreach ($assessmentIds as $index => $assessmentId) {
            foreach ($values[$index] as $position => $value) {
                if ($value === null) {
                    continue;
                }

                $scores[] = [
                    'assessment_id' => $assessmentId,
                    'student_id'    => $ids[$position],
                    'score'         => $value,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];
            }
        }

        $this->db->table('scores')->insertBatch($scores);
    }

    /**
     * @return list<string>
     */
    private function recentWeekdays(int $count): array
    {
        $dates  = [];
        $cursor = strtotime('today');

        while (count($dates) < $count) {
            if ((int) date('N', $cursor) <= 5) {
                $dates[] = date('Y-m-d', $cursor);
            }

            $cursor = strtotime('-1 day', $cursor);
        }

        return array_reverse($dates);
    }
}
