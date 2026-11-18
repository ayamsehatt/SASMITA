<?php

namespace App\Libraries;

/**
 * Olahan data kehadiran dan nilai siswa untuk halaman analitik.
 *
 * Kelas ini tidak menyentuh database maupun session sehingga perhitungannya
 * bisa diuji secara terpisah. Controller hanya meneruskan baris-baris hasil
 * query model ke sini.
 */
class KelasReport
{
    public const KKM = 75;

    /** @var array<string, string> */
    public const STATUS_LABELS = [
        'H' => 'Hadir',
        'S' => 'Sakit',
        'I' => 'Izin',
        'A' => 'Alpa',
    ];

    /** @var array<string, string> */
    public const TYPE_LABELS = [
        'tugas'    => 'Tugas',
        'formatif' => 'Formatif',
        'sumatif'  => 'Sumatif',
    ];

    /** @var list<array<string, mixed>> */
    private array $students;

    /** @var array<int|string, array<string, string>> */
    private array $attendance = [];

    /** @var array<int|string, array<string, float|null>> */
    private array $scores = [];

    /** @var list<string> */
    private array $dates = [];

    /**
     * @param list<array<string, mixed>> $students
     * @param list<array<string, mixed>> $attendanceRows
     * @param list<array<string, mixed>> $assessments
     * @param list<array<string, mixed>> $scoreRows
     */
    public function __construct(
        array $students,
        array $attendanceRows,
        array $assessments,
        array $scoreRows
    ) {
        $this->students = array_values($students);

        foreach ($attendanceRows as $row) {
            $studentId = (int) ($row['student_id'] ?? 0);
            $date      = (string) ($row['attendance_date'] ?? '');
            $status    = (string) ($row['status'] ?? '');

            if ($studentId === 0 || $date === '' || ! isset(self::STATUS_LABELS[$status])) {
                continue;
            }

            $this->attendance[$studentId][$date] = $status;
            $this->dates[$date]                  = $date;
        }

        $assessmentIds = [];

        foreach ($assessments as $assessment) {
            $assessmentIds[(int) ($assessment['id'] ?? 0)] = true;
        }

        foreach ($scoreRows as $row) {
            $assessmentId = (int) ($row['assessment_id'] ?? 0);
            $studentId    = (int) ($row['student_id'] ?? 0);

            if (! isset($assessmentIds[$assessmentId]) || $studentId === 0) {
                continue;
            }

            $raw   = $row['score'] ?? null;
            $score = ($raw === null || $raw === '') ? null : (float) $raw;

            if ($score !== null && ($score < 0 || $score > 100)) {
                $score = null;
            }

            $this->scores[$studentId][$assessmentId] = $score;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function kpi(): array
    {
        $attendanceRecords = 0;
        $present           = 0;
        $attention         = 0;
        $totalScore        = 0.0;
        $scoredValues      = 0;

        foreach ($this->students as $student) {
            $counts = $this->countStatus($student);
            $total  = array_sum($counts);

            $attendanceRecords += $total;
            $present           += $counts['H'];

            if ($total > 0 && $this->percentage($counts['H'], $total) < 75.0) {
                $attention++;
            }

            foreach ($this->scores[(int) ($student['id'] ?? 0)] ?? [] as $score) {
                if ($score !== null) {
                    $totalScore += $score;
                    $scoredValues++;
                }
            }
        }

        return [
            'students'           => count($this->students),
            'className'          => (string) ($this->students[0]['class_name'] ?? 'VII A'),
            'attendanceRecords'  => $attendanceRecords,
            'attendancePercent'  => $attendanceRecords > 0 ? $this->percentage($present, $attendanceRecords) : null,
            'attention'          => $attention,
            'avgScore'           => $scoredValues > 0 ? round($totalScore / $scoredValues, 1) : null,
            'scoredValues'       => $scoredValues,
            'gradeComposition'   => $this->attendanceTotals(),
        ];
    }

    /**
     * @return array{H: int, S: int, I: int, A: int, total: int}
     */
    public function attendanceTotals(): array
    {
        $totals = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];

        foreach ($this->attendance as $days) {
            foreach ($days as $status) {
                $totals[$status]++;
            }
        }

        $totals['total'] = array_sum($totals);

        return $totals;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function trend(int $limit = 8): array
    {
        $dates = array_values($this->dates);
        sort($dates);
        $dates = array_slice($dates, -max($limit, 1));

        $result = [];

        foreach ($dates as $date) {
            $counts = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];

            foreach ($this->attendance as $days) {
                if (isset($days[$date])) {
                    $counts[$days[$date]]++;
                }
            }

            $total = array_sum($counts);

            $result[] = [
                'date'    => $date,
                'label'   => $this->shortDate($date),
                'counts'  => $counts,
                'total'   => $total,
                'percent' => $total > 0 ? $this->percentage($counts['H'], $total) : 0.0,
            ];
        }

        return $result;
    }

    /**
     * Distribusi rata-rata nilai tiap siswa (bukan tiap butir nilai).
     *
     * @return list<array<string, mixed>>
     */
    public function gradeDistribution(): array
    {
        $buckets = [
            ['label' => '86 – 100', 'tone' => 'excellent', 'min' => 86, 'count' => 0],
            ['label' => '71 – 85',  'tone' => 'good',      'min' => 71, 'count' => 0],
            ['label' => '56 – 70',  'tone' => 'fair',      'min' => 56, 'count' => 0],
            ['label' => '< 56',     'tone' => 'low',       'min' => 0,  'count' => 0],
        ];

        $graded = 0;

        foreach ($this->students as $student) {
            $average = $this->averageScore($student);

            if ($average === null) {
                continue;
            }

            $graded++;

            foreach ($buckets as $index => $bucket) {
                if ($average >= $bucket['min']) {
                    $buckets[$index]['count']++;
                    break;
                }
            }
        }

        foreach ($buckets as $index => $bucket) {
            $buckets[$index]['percent'] = $graded > 0 ? $this->percentage($bucket['count'], $graded) : 0.0;
        }

        return $buckets;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function recap(): array
    {
        $rows = [];

        foreach ($this->students as $student) {
            $counts = $this->countStatus($student);
            $total  = array_sum($counts);
            $present = $total > 0 ? $this->percentage($counts['H'], $total) : null;
            $average = $this->averageScore($student);

            if ($present === null && $average === null) {
                $tone  = 'muted';
                $label = 'Belum ada data';
            } elseif (($present !== null && $present < 75.0) || ($average !== null && $average < self::KKM)) {
                $tone  = 'warn';
                $label = 'Perlu perhatian';
            } elseif ($present === null || $average === null) {
                $tone  = 'info';
                $label = 'Belum lengkap';
            } else {
                $tone  = 'ok';
                $label = 'Baik';
            }

            $rows[] = [
                'id'         => (int) ($student['id'] ?? 0),
                'nis'        => (string) ($student['nis'] ?? ''),
                'name'       => (string) ($student['name'] ?? ''),
                'counts'     => $counts,
                'total'      => $total,
                'present'    => $present,
                'average'    => $average,
                'tone'       => $tone,
                'label'      => $label,
            ];
        }

        return $rows;
    }

    /**
     * @param list<array<string, mixed>> $assessments
     *
     * @return list<array<string, mixed>>
     */
    public function assessmentSummaries(array $assessments): array
    {
        $rows = [];

        foreach ($assessments as $assessment) {
            $id     = (int) ($assessment['id'] ?? 0);
            $values = [];

            foreach ($this->students as $student) {
                $score = $this->scores[(int) ($student['id'] ?? 0)][$id] ?? null;

                if ($score !== null) {
                    $values[] = $score;
                }
            }

            $count = count($values);

            $rows[] = [
                'id'    => $id,
                'title' => (string) ($assessment['title'] ?? ''),
                'type'  => (string) ($assessment['type'] ?? 'formatif'),
                'label' => self::TYPE_LABELS[(string) ($assessment['type'] ?? '')] ?? 'Formatif',
                'date'  => (string) ($assessment['assessment_date'] ?? ''),
                'count' => $count,
                'avg'   => $count > 0 ? round(array_sum($values) / $count, 1) : null,
                'min'   => $count > 0 ? min($values) : null,
                'max'   => $count > 0 ? max($values) : null,
            ];
        }

        return $rows;
    }

    /**
     * @param list<array<string, mixed>> $assessments
     *
     * @return list<array<string, string>>
     */
    public function insights(array $assessments): array
    {
        if ($this->students === []) {
            return [[
                'tone' => 'info',
                'text' => 'Belum ada siswa. Tambahkan siswa lewat panel Kelola siswa untuk mulai merekam kehadiran dan nilai.',
            ]];
        }

        $recap    = $this->recap();
        $summaries = $this->assessmentSummaries($assessments);
        $insights = [];

        $lowAttendance = [];
        $lowScore      = [];
        $unscored      = [];

        foreach ($recap as $row) {
            if ($row['present'] !== null && $row['present'] < 75.0) {
                $lowAttendance[] = $row['name'];
            }

            if ($row['average'] !== null && $row['average'] < self::KKM) {
                $lowScore[] = $row['name'];
            }

            if ($row['average'] === null) {
                $unscored[] = $row['name'];
            }
        }

        if ($this->dates === []) {
            $insights[] = [
                'tone' => 'warn',
                'text' => 'Belum ada data kehadiran. Isi kehadiran minimal satu pertemuan agar grafik dapat ditampilkan.',
            ];
        }

        if ($lowAttendance !== []) {
            $insights[] = [
                'tone' => 'warn',
                'text' => 'Kehadiran di bawah 75%: ' . $this->joinNames($lowAttendance) . '. Pertimbangkan tindak lanjut.',
            ];
        }

        if ($lowScore !== []) {
            $insights[] = [
                'tone' => 'warn',
                'text' => 'Rata-rata nilai di bawah KKM ' . self::KKM . ': ' . $this->joinNames($lowScore) . '.',
            ];
        }

        if ($unscored !== []) {
            $insights[] = [
                'tone' => 'info',
                'text' => 'Belum memiliki nilai: ' . $this->joinNames($unscored) . '.',
            ];
        }

        if ($summaries === []) {
            $insights[] = [
                'tone' => 'info',
                'text' => 'Belum ada asesmen. Buat asesmen baru lalu isi nilainya pada panel input nilai.',
            ];
        }

        foreach ($summaries as $summary) {
            if ($summary['count'] === 0) {
                $insights[] = [
                    'tone' => 'info',
                    'text' => 'Asesmen "' . $summary['title'] . '" belum memiliki nilai sama sekali.',
                ];
            }
        }

        if ($insights === []) {
            $insights[] = [
                'tone' => 'ok',
                'text' => 'Semua siswa memiliki data kehadiran dan nilai di atas KKM ' . self::KKM . '. Pertahankan!',
            ];
        }

        return $insights;
    }

    /**
     * @param array<string, mixed> $student
     *
     * @return array{H: int, S: int, I: int, A: int}
     */
    private function countStatus(array $student): array
    {
        $counts = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
        $id     = (int) ($student['id'] ?? 0);

        foreach ($this->attendance[$id] ?? [] as $status) {
            $counts[$status]++;
        }

        return $counts;
    }

    /**
     * @param array<string, mixed> $student
     */
    private function averageScore(array $student): ?float
    {
        $values = [];

        foreach ($this->scores[(int) ($student['id'] ?? 0)] ?? [] as $score) {
            if ($score !== null) {
                $values[] = $score;
            }
        }

        if ($values === []) {
            return null;
        }

        return round(array_sum($values) / count($values), 1);
    }

    /**
     * @param list<string> $names
     */
    private function joinNames(array $names): string
    {
        $shown = array_slice($names, 0, 3);
        $text  = implode(', ', $shown);

        if (count($names) > 3) {
            $text .= ' dan ' . (count($names) - 3) . ' siswa lainnya';
        }

        return $text;
    }

    private function percentage(float $part, float $total): float
    {
        return round(($part / $total) * 100, 1);
    }

    private function shortDate(string $date): string
    {
        $timestamp = strtotime($date);

        if ($timestamp === false) {
            return $date;
        }

        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        return date('j', $timestamp) . ' ' . $months[(int) date('n', $timestamp)];
    }
}
