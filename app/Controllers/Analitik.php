<?php

namespace App\Controllers;

use App\Libraries\KelasReport;
use App\Models\AssessmentModel;
use App\Models\AttendanceModel;
use App\Models\ScoreModel;
use App\Models\StudentModel;
use CodeIgniter\HTTP\RedirectResponse;

class Analitik extends BaseController
{
    /**
     * Helper yang dipakai di view (old(), site_url(), base_url()).
     *
     * @var list<string>
     */
    protected $helpers = ['form', 'url'];

    private StudentModel $students;
    private AttendanceModel $attendance;
    private AssessmentModel $assessments;
    private ScoreModel $scores;

    public function __construct()
    {
        $this->students    = new StudentModel();
        $this->attendance  = new AttendanceModel();
        $this->assessments = new AssessmentModel();
        $this->scores      = new ScoreModel();
    }

    /**
     * Rekap, grafik, sekaligus panel input kehadiran dan nilai.
     */
    public function index()
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        $studentRows    = $this->students
            ->orderBy('class_name', 'ASC')
            ->orderBy('nis', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
        $attendanceRows = $this->attendance->findAll();
        $assessmentRows = $this->assessments
            ->orderBy('assessment_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();
        $scoreRows = $this->scores->findAll();

        $selectedDate       = $this->resolveDate();
        $selectedAssessment = $this->resolveAssessment($assessmentRows);

        $attendanceMap = [];

        foreach ($attendanceRows as $row) {
            if ($row['attendance_date'] === $selectedDate) {
                $attendanceMap[(int) $row['student_id']] = (string) $row['status'];
            }
        }

        $scoreMap = [];

        foreach ($scoreRows as $row) {
            if ((int) $row['assessment_id'] === $selectedAssessment) {
                $scoreMap[(int) $row['student_id']] = $row['score'] === null ? '' : (string) $row['score'];
            }
        }

        $report = new KelasReport($studentRows, $attendanceRows, $assessmentRows, $scoreRows);
        $kpi    = $report->kpi();
        $user   = session('user') ?? [];

        return view('analitik/index', [
            'title'              => 'Analitik Kelas · SASMITA',
            'active'             => 'analitik',
            'crumb'              => 'Analitik',
            'teacherName'        => (string) ($user['name'] ?? ''),
            'className'          => (string) ($kpi['className'] ?? 'VII A'),
            'kpi'                => $kpi,
            'attendanceTotals'   => $report->attendanceTotals(),
            'trend'              => $report->trend(8),
            'distribution'       => $report->gradeDistribution(),
            'recap'              => $report->recap(),
            'assessmentSummaries' => $report->assessmentSummaries($assessmentRows),
            'insights'           => $report->insights($assessmentRows),
            'kkm'                => KelasReport::KKM,
            'statusLabels'       => KelasReport::STATUS_LABELS,
            'students'           => $studentRows,
            'assessments'        => $assessmentRows,
            'selectedDate'       => $selectedDate,
            'selectedAssessment' => $selectedAssessment,
            'attendanceMap'      => $attendanceMap,
            'scoreMap'           => $scoreMap,
        ]);
    }

    /**
     * Simpan/ubah kehadiran seluruh siswa untuk satu tanggal.
     */
    public function saveAttendance(): RedirectResponse
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        $date = trim((string) $this->request->getPost('tanggal'));

        if (! $this->isValidDate($date, true)) {
            return $this->fail('Tanggal kehadiran tidak valid atau bukan hari ini/masa lalu.', 'analitik');
        }

        $rawStatus = $this->request->getPost('kehadiran');

        if (! is_array($rawStatus) || $rawStatus === []) {
            return $this->fail('Pilih status kehadiran minimal satu siswa.', 'analitik?tanggal=' . $date);
        }

        $allowed = [];

        foreach ($this->students->findAll() as $student) {
            $allowed[(int) $student['id']] = true;
        }

        $entries = [];

        foreach ($rawStatus as $studentId => $status) {
            $id = filter_var($studentId, FILTER_VALIDATE_INT);

            if ($id === false || ! isset($allowed[(int) $id])) {
                continue;
            }

            $status = strtoupper(trim((string) $status));

            if (! isset(KelasReport::STATUS_LABELS[$status])) {
                return $this->fail('Status kehadiran tidak dikenali.', 'analitik?tanggal=' . $date);
            }

            $entries[(int) $id] = $status;
        }

        if ($entries === []) {
            return $this->fail('Tidak ada siswa yang dapat disimpan.', 'analitik?tanggal=' . $date);
        }

        $db = db_connect();
        $db->transStart();

        foreach ($entries as $studentId => $status) {
            $existing = $this->attendance
                ->where('student_id', $studentId)
                ->where('attendance_date', $date)
                ->first();

            if ($existing !== null) {
                $this->attendance->update((int) $existing['id'], ['status' => $status]);
                continue;
            }

            $this->attendance->insert([
                'student_id'      => $studentId,
                'attendance_date' => $date,
                'status'          => $status,
            ]);
        }

        $db->transComplete();

        return redirect()
            ->to(site_url('analitik?tanggal=' . $date))
            ->with('success', 'Kehadiran ' . count($entries) . ' siswa untuk tanggal ' . $this->longDate($date) . ' tersimpan.');
    }

    /**
     * Buat asesmen baru lalu arahkan ke panel input nilai.
     */
    public function storeAssessment(): RedirectResponse
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        $title = trim((string) $this->request->getPost('title'));
        $type  = trim((string) $this->request->getPost('type'));
        $date  = trim((string) $this->request->getPost('assessment_date'));

        $errors = [];

        if (mb_strlen($title) < 3 || mb_strlen($title) > 120) {
            $errors[] = 'Judul asesmen harus 3–120 karakter.';
        }

        if (! in_array($type, ['tugas', 'formatif', 'sumatif'], true)) {
            $errors[] = 'Jenis asesmen tidak dikenali.';
        }

        if (! $this->isValidDate($date, true)) {
            $errors[] = 'Tanggal asesmen tidak valid.';
        }

        if ($errors !== []) {
            return $this->fail(implode(' ', $errors), 'analitik');
        }

        $id = $this->assessments->insert([
            'title'           => $title,
            'type'            => $type,
            'assessment_date' => $date,
        ]);

        return redirect()
            ->to(site_url('analitik?asesmen=' . (int) $id))
            ->with('success', 'Asesmen "' . $title . '" dibuat. Silakan isi nilainya.');
    }

    /**
     * Hapus asesmen beserta seluruh nilainya.
     */
    public function deleteAssessment($id = null): RedirectResponse
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        $id    = filter_var($id, FILTER_VALIDATE_INT);
        $row   = $id === false ? null : $this->assessments->find((int) $id);

        if ($row === null) {
            return $this->fail('Asesmen tidak ditemukan.', 'analitik');
        }

        $this->assessments->delete((int) $id);

        return redirect()
            ->to(site_url('analitik'))
            ->with('success', 'Asesmen berhasil dihapus beserta nilainya.');
    }

    /**
     * Simpan/ubah nilai siswa untuk satu asesmen.
     */
    public function saveScores(): RedirectResponse
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        $assessmentId = filter_var($this->request->getPost('asesmen_id'), FILTER_VALIDATE_INT);
        $target       = 'analitik' . ($assessmentId !== false ? '?asesmen=' . (int) $assessmentId : '');

        if ($assessmentId === false || $this->assessments->find((int) $assessmentId) === null) {
            return $this->fail('Asesmen tidak ditemukan.', 'analitik');
        }

        $rawScores = $this->request->getPost('nilai');
        $target    = 'analitik?asesmen=' . (int) $assessmentId;

        if (! is_array($rawScores)) {
            return $this->fail('Tidak ada nilai yang dikirim.', $target);
        }

        $allowed = [];

        foreach ($this->students->findAll() as $student) {
            $allowed[(int) $student['id']] = true;
        }

        $entries  = [];
        $cleared  = [];

        foreach ($rawScores as $studentId => $value) {
            $id = filter_var($studentId, FILTER_VALIDATE_INT);

            if ($id === false || ! isset($allowed[(int) $id])) {
                continue;
            }

            $value = trim((string) $value);

            if ($value === '') {
                $cleared[] = (int) $id;
                continue;
            }

            $score = filter_var($value, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 0, 'max_range' => 100],
            ]);

            if ($score === false) {
                return $this->fail('Nilai harus berupa angka bulat 0–100.', $target);
            }

            $entries[(int) $id] = $score;
        }

        $db = db_connect();
        $db->transStart();

        foreach ($entries as $studentId => $score) {
            $existing = $this->scores
                ->where('assessment_id', (int) $assessmentId)
                ->where('student_id', $studentId)
                ->first();

            if ($existing !== null) {
                $this->scores->update((int) $existing['id'], ['score' => $score]);
                continue;
            }

            $this->scores->insert([
                'assessment_id' => (int) $assessmentId,
                'student_id'    => $studentId,
                'score'         => $score,
            ]);
        }

        foreach ($cleared as $studentId) {
            $this->scores
                ->where('assessment_id', (int) $assessmentId)
                ->where('student_id', $studentId)
                ->delete();
        }

        $db->transComplete();

        return redirect()
            ->to($target)
            ->with('success', 'Nilai asesmen tersimpan (' . count($entries) . ' siswa dinilai).');
    }

    /**
     * Tambah siswa baru ke kelas.
     */
    public function storeStudent(): RedirectResponse
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        if ($this->students->countAll() >= 100) {
            return $this->fail('Batas maksimal 100 siswa per kelas sudah tercapai.', 'analitik');
        }

        $data = [
            'name'       => trim((string) $this->request->getPost('name')),
            'nis'        => trim((string) $this->request->getPost('nis')),
            'class_name' => trim((string) $this->request->getPost('class_name')) ?: 'VII A',
        ];

        if (! $this->students->validate($data)) {
            return $this->fail(implode(' ', $this->students->errors()), 'analitik');
        }

        $this->students->insert([
            'name'       => $data['name'],
            'nis'        => $data['nis'] === '' ? null : $data['nis'],
            'class_name' => $data['class_name'],
        ]);

        return redirect()
            ->to(site_url('analitik'))
            ->with('success', 'Siswa ' . $data['name'] . ' ditambahkan.');
    }

    /**
     * Hapus siswa; baris kehadiran dan nilai ikut terhapus lewat foreign key.
     */
    public function deleteStudent($id = null): RedirectResponse
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        $id  = filter_var($id, FILTER_VALIDATE_INT);
        $row = $id === false ? null : $this->students->find((int) $id);

        if ($row === null) {
            return $this->fail('Siswa tidak ditemukan.', 'analitik');
        }

        $this->students->delete((int) $id);

        return redirect()
            ->to(site_url('analitik'))
            ->with('success', 'Siswa ' . $row['name'] . ' beserta data kehadiran dan nilainya dihapus.');
    }

    /**
     * @param list<array<string, mixed>> $assessments
     */
    private function resolveAssessment(array $assessments): int
    {
        $raw = $this->request->getGet('asesmen');

        if ($raw !== null && ctype_digit((string) $raw)) {
            $id = (int) $raw;

            foreach ($assessments as $assessment) {
                if ((int) $assessment['id'] === $id) {
                    return $id;
                }
            }
        }

        return isset($assessments[0]) ? (int) $assessments[0]['id'] : 0;
    }

    private function resolveDate(): string
    {
        $raw = trim((string) ($this->request->getGet('tanggal') ?? ''));

        return $this->isValidDate($raw, true) ? $raw : date('Y-m-d');
    }

    private function isValidDate(string $date, bool $notFuture = false): bool
    {
        if ($date === '' || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return false;
        }

        $timestamp = strtotime($date);

        if ($timestamp === false || date('Y-m-d', $timestamp) !== $date) {
            return false;
        }

        return ! $notFuture || $timestamp <= strtotime('today');
    }

    private function fail(string $message, string $path): RedirectResponse
    {
        return redirect()
            ->to(site_url($path))
            ->with('error', $message);
    }

    private function longDate(string $date): string
    {
        $timestamp = strtotime($date);

        if ($timestamp === false) {
            return $date;
        }

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return date('j', $timestamp) . ' ' . $months[(int) date('n', $timestamp)] . ' ' . date('Y', $timestamp);
    }

    private function guard(): ?RedirectResponse
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()
                ->to(site_url('login'))
                ->with('error', 'Silakan masuk terlebih dahulu.');
        }

        return null;
    }
}
