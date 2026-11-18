<?php

use App\Libraries\MateriGenerator;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class MateriGeneratorTest extends CIUnitTestCase
{
    /**
     * @return array<string, mixed>
     */
    private function sampleRps(): array
    {
        return [
            'id'         => 'rps-test-001',
            'title'      => 'Berpikir Kritis dalam Pembelajaran IPS',
            'objective'  => [
                'Peserta didik mampu menganalisis permasalahan sosial di lingkungan sekitar.',
                'Peserta didik mampu menyusun argumen yang didukung bukti.',
            ],
            'indicators' => [
                'Ketepatan analisis masalah',
                'Kualitas argumen dan bukti',
            ],
            'method'     => 'Problem Based Learning',
            'references' => [
                'Sumber terlampir: Modul_Berpikir_Kritis.pdf',
                'Buku ajar dan modul pendukung materi Berpikir Kritis dalam Pembelajaran IPS.',
            ],
            'meetings'   => [],
        ];
    }

    /**
     * @param array<string, mixed> $overrides
     *
     * @return array<string, mixed>
     */
    private function sampleMeeting(array $overrides = []): array
    {
        return array_merge([
            'week'       => 3,
            'topic'      => 'Ruang lingkup dan prinsip Berpikir Kritis dalam Pembelajaran IPS',
            'activities' => [
                'Ceramah interaktif',
                'Analisis contoh nyata',
                'Latihan identifikasi prinsip',
            ],
            'method'     => 'Diskusi kelompok',
            'assessment' => 'Lembar kerja identifikasi prinsip',
            'materials'  => 'Prosedur kerja dan contoh penerapan Berpikir Kritis dalam Pembelajaran IPS.',
            'indicator'  => 'Kualitas argumen dan bukti',
        ], $overrides);
    }

    public function testGenerateReturnsRequiredStructureForNormalWeek(): void
    {
        $generator = new MateriGenerator();
        $result    = $generator->generate($this->sampleRps(), $this->sampleMeeting());

        $this->assertSame(3, $result['week']);
        $this->assertSame('Ruang lingkup dan prinsip Berpikir Kritis dalam Pembelajaran IPS', $result['topic']);
        $this->assertSame('Diskusi kelompok', $result['method']);
        $this->assertSame('Lembar kerja identifikasi prinsip', $result['assessment']);
        $this->assertIsInt($result['estimated_minutes']);
        $this->assertGreaterThan(0, $result['estimated_minutes']);
        $this->assertIsString($result['summary']);
        $this->assertNotSame('', $result['summary']);
        $this->assertIsArray($result['objectives']);
        $this->assertNotEmpty($result['objectives']);
        $this->assertIsArray($result['sections']);
        $this->assertNotEmpty($result['sections']);
        $this->assertIsArray($result['key_terms']);
        $this->assertNotEmpty($result['key_terms']);
        $this->assertIsArray($result['activities']);
        $this->assertCount(3, $result['activities']);
        $this->assertIsArray($result['quiz']);
        $this->assertCount(3, $result['quiz']);
        $this->assertIsArray($result['references']);
        $this->assertNotEmpty($result['references']);
        $this->assertIsString($result['generated_at']);
        $this->assertNotSame('', $result['generated_at']);
        $this->assertFalse($result['is_exam']);

        foreach ($result['sections'] as $section) {
            $this->assertArrayHasKey('heading', $section);
            $this->assertArrayHasKey('paragraphs', $section);
            $this->assertArrayHasKey('points', $section);
            $this->assertIsArray($section['paragraphs']);
            $this->assertIsArray($section['points']);
        }

        foreach ($result['key_terms'] as $term) {
            $this->assertArrayHasKey('term', $term);
            $this->assertArrayHasKey('definition', $term);
        }

        foreach ($result['activities'] as $activity) {
            $this->assertArrayHasKey('step', $activity);
            $this->assertArrayHasKey('minutes', $activity);
            $this->assertIsInt($activity['minutes']);
        }

        foreach ($result['quiz'] as $item) {
            $this->assertArrayHasKey('question', $item);
            $this->assertArrayHasKey('answer', $item);
            $this->assertNotSame('', $item['question']);
            $this->assertNotSame('', $item['answer']);
        }
    }

    public function testNormalWeekContentIsBasedOnMeetingData(): void
    {
        $generator = new MateriGenerator();
        $result    = $generator->generate($this->sampleRps(), $this->sampleMeeting());

        $this->assertStringContainsString('Berpikir Kritis', $result['summary']);
        $this->assertStringContainsString('Kualitas argumen dan bukti', $result['summary']);

        $steps = array_column($result['activities'], 'step');
        $this->assertSame(['Ceramah interaktif', 'Analisis contoh nyata', 'Latihan identifikasi prinsip'], $steps);

        $allRefs = implode(' ', $result['references']);
        $this->assertStringContainsString('Modul_Berpikir_Kritis.pdf', $allRefs);
    }

    public function testWeek8GeneratesUtsOrientedContent(): void
    {
        $generator = new MateriGenerator();
        $meeting   = $this->sampleMeeting([
            'week'       => 8,
            'topic'      => 'Asesmen Tengah Semester',
            'activities' => ['Asesmen sumatif tengah semester', 'Umpan balik hasil asesmen'],
            'method'     => 'Asesmen tertulis',
            'assessment' => 'Soal uraian & studi kasus',
            'materials'  => 'Evaluasi capaian pertemuan 1-7.',
            'indicator'  => 'Ketercapaian tujuan pembelajaran',
        ]);

        $result = $generator->generate($this->sampleRps(), $meeting);

        $this->assertSame(8, $result['week']);
        $this->assertTrue($result['is_exam']);
        $this->assertStringContainsString('Tengah Semester', $result['summary']);
        $this->assertStringContainsString('UTS', $result['summary']);
        $this->assertSame(90, $result['estimated_minutes']);
        $this->assertNotEmpty($result['sections']);
        $this->assertNotEmpty($result['quiz']);

        $sectionText = '';
        foreach ($result['sections'] as $section) {
            $sectionText .= $section['heading'] . ' ' . implode(' ', $section['paragraphs']);
        }
        $this->assertStringContainsString('asesmen', strtolower($sectionText));
    }

    public function testWeek16GeneratesUasOrientedContent(): void
    {
        $generator = new MateriGenerator();
        $meeting   = $this->sampleMeeting([
            'week'       => 16,
            'topic'      => 'Asesmen Akhir Semester',
            'activities' => ['Asesmen sumatif akhir semester', 'Umpan balik dan rencana tindak lanjut'],
            'method'     => 'Asesmen tertulis',
            'assessment' => 'Soal uraian, studi kasus, & refleksi akhir',
            'materials'  => 'Evaluasi capaian keseluruhan semester.',
            'indicator'  => 'Ketercapaian tujuan pembelajaran',
        ]);

        $result = $generator->generate($this->sampleRps(), $meeting);

        $this->assertSame(16, $result['week']);
        $this->assertTrue($result['is_exam']);
        $this->assertStringContainsString('Akhir Semester', $result['summary']);
        $this->assertStringContainsString('UAS', $result['summary']);
        $this->assertSame(100, $result['estimated_minutes']);
        $this->assertNotEmpty($result['objectives']);

        $objectivesText = implode(' ', $result['objectives']);
        $this->assertStringContainsString('umpan balik', strtolower($objectivesText));
    }

    public function testActivitiesAreDistributedAcrossEstimatedTime(): void
    {
        $generator = new MateriGenerator();
        $meeting   = $this->sampleMeeting([
            'activities' => ['Pembukaan', 'Inti', 'Penutup'],
        ]);

        $result = $generator->generate($this->sampleRps(), $meeting);
        $total  = 0;

        foreach ($result['activities'] as $activity) {
            $total += (int) $activity['minutes'];
        }

        $this->assertSame($result['estimated_minutes'], $total);
    }
}
