<?php

use App\Libraries\KelasReport;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class KelasReportTest extends CIUnitTestCase
{
    /**
     * @return array{list<array<string, mixed>>, list<array<string, mixed>>, list<array<string, mixed>>, list<array<string, mixed>>}
     */
    private function fixtures(): array
    {
        $students = [
            ['id' => 1, 'nis' => '001', 'name' => 'Ahmad', 'class_name' => 'VII A'],
            ['id' => 2, 'nis' => '002', 'name' => 'Bima', 'class_name' => 'VII A'],
        ];

        $attendance = [
            ['student_id' => 1, 'attendance_date' => '2026-11-16', 'status' => 'H'],
            ['student_id' => 2, 'attendance_date' => '2026-11-16', 'status' => 'S'],
            ['student_id' => 1, 'attendance_date' => '2026-11-17', 'status' => 'H'],
            ['student_id' => 2, 'attendance_date' => '2026-11-17', 'status' => 'A'],
        ];

        $assessments = [
            ['id' => 10, 'title' => 'Ulangan Bab 1', 'type' => 'formatif', 'assessment_date' => '2026-11-17'],
        ];

        $scores = [
            ['assessment_id' => 10, 'student_id' => 1, 'score' => 90],
            ['assessment_id' => 10, 'student_id' => 2, 'score' => 60],
        ];

        return [$students, $attendance, $assessments, $scores];
    }

    public function testKpiMenghitungKehadiranDanNilai(): void
    {
        $report = new KelasReport(...$this->fixtures());
        $kpi    = $report->kpi();

        $this->assertSame(2, $kpi['students']);
        $this->assertSame(4, $kpi['attendanceRecords']);
        $this->assertSame(50.0, $kpi['attendancePercent']);
        $this->assertSame(1, $kpi['attention']);
        $this->assertSame(75.0, $kpi['avgScore']);
        $this->assertSame('VII A', $kpi['className']);

        $totals = $report->attendanceTotals();

        $this->assertSame(2, $totals['H']);
        $this->assertSame(1, $totals['S']);
        $this->assertSame(0, $totals['I']);
        $this->assertSame(1, $totals['A']);
        $this->assertSame(4, $totals['total']);
    }

    public function testTrendHanyaMengambilPertemuanTerakhir(): void
    {
        $report = new KelasReport(...$this->fixtures());
        $trend  = $report->trend(1);

        $this->assertCount(1, $trend);
        $this->assertSame('2026-11-17', $trend[0]['date']);
        $this->assertSame(1, $trend[0]['counts']['H']);
        $this->assertSame(1, $trend[0]['counts']['A']);
        $this->assertSame(50.0, $trend[0]['percent']);
    }

    public function testDistribusiDanRekapPerSiswa(): void
    {
        [$students, $attendance, $assessments, $scores] = $this->fixtures();
        $report = new KelasReport($students, $attendance, $assessments, $scores);

        $distribution = $report->gradeDistribution();

        $this->assertSame(1, $distribution[0]['count']); // 90 masuk 86–100
        $this->assertSame(0, $distribution[1]['count']);
        $this->assertSame(1, $distribution[2]['count']); // 60 masuk 56–70

        $recap = $report->recap();

        $this->assertSame('ok', $recap[0]['tone']);
        $this->assertSame(100.0, $recap[0]['present']);
        $this->assertSame(90.0, $recap[0]['average']);
        $this->assertSame('warn', $recap[1]['tone']);
        $this->assertSame(0.0, $recap[1]['present']);
        $this->assertSame(60.0, $recap[1]['average']);

        $summaries = $report->assessmentSummaries($assessments);

        $this->assertSame(2, $summaries[0]['count']);
        $this->assertSame(75.0, $summaries[0]['avg']);
        $this->assertSame(60.0, $summaries[0]['min']);
        $this->assertSame(90.0, $summaries[0]['max']);
    }

    public function testInsightMencatatSiswaYangPerluDitindaklanjuti(): void
    {
        [$students, $attendance, $assessments, $scores] = $this->fixtures();
        $report  = new KelasReport($students, $attendance, $assessments, $scores);
        $insights = $report->insights($assessments);

        $tones = array_column($insights, 'tone');

        $this->assertContains('warn', $tones);
        $this->assertNotContains('ok', $tones);
    }

    public function testKelasKosongMenghasilkanInsightTambahSiswa(): void
    {
        $report = new KelasReport([], [], [], []);
        $kpi    = $report->kpi();

        $this->assertSame(0, $kpi['students']);
        $this->assertNull($kpi['attendancePercent']);
        $this->assertNull($kpi['avgScore']);
        $this->assertSame([], $report->trend());
        $this->assertSame([], $report->recap());

        $insights = $report->insights([]);

        $this->assertCount(1, $insights);
        $this->assertSame('info', $insights[0]['tone']);
    }
}
