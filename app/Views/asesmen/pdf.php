<?php
/**
 * Template PDF asesmen (Dompdf).
 *
 * Sengaja memakai table dan CSS sederhana karena Dompdf tidak mendukung
 * flexbox/grid. Semua teks di-escape agar aman dari HTML tak terduga.
 *
 * @var array<string, mixed> $asesmen
 */
$questions = $asesmen['questions'] ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($asesmen['title']) ?></title>
    <style>
        @page {
            margin: 86px 52px 72px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 9.6pt;
            line-height: 1.55;
            color: #241b3d;
        }

        .doc-accent {
            height: 4px;
            margin-bottom: 16px;
            background-color: #6d28d9;
        }

        .doc-kicker {
            margin: 0;
            font-size: 7.4pt;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #6d28d9;
        }

        h1.doc-title {
            margin: 5px 0 14px;
            font-size: 19pt;
            line-height: 1.25;
            color: #1c1535;
        }

        table.identity {
            width: 100%;
            margin-bottom: 12px;
            border-top: 1px solid #ddd3f3;
            border-bottom: 1px solid #ddd3f3;
            border-collapse: collapse;
        }

        table.identity th,
        table.identity td {
            padding: 6px 8px;
            font-size: 9pt;
            text-align: left;
            vertical-align: top;
        }

        table.identity th {
            width: 17%;
            font-weight: normal;
            color: #7b7491;
        }

        table.identity td {
            width: 33%;
            font-weight: bold;
            color: #241b3d;
        }

        .doc-summary {
            margin: 0 0 16px;
            font-size: 8.4pt;
            color: #7b7491;
        }

        .instructions {
            margin-bottom: 18px;
            padding: 11px 14px;
            border: 1px solid #e2d9f6;
            background-color: #f8f5ff;
        }

        .instructions h2 {
            margin: 0 0 5px;
            font-size: 8.6pt;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #4c1d95;
        }

        .instructions p {
            margin: 0;
            font-size: 9pt;
            color: #443c60;
        }

        .section-title {
            margin: 0 0 14px;
            padding-bottom: 6px;
            border-bottom: 1.5px solid #d9cdf5;
            font-size: 9.6pt;
            font-weight: bold;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: #4c1d95;
        }

        .question {
            margin-bottom: 16px;
            page-break-inside: avoid;
        }

        table.q-head {
            width: 100%;
            border-collapse: collapse;
        }

        table.q-head td {
            padding: 0;
            vertical-align: top;
        }

        .q-num {
            width: 24px;
            font-size: 9.8pt;
            font-weight: bold;
            color: #4c1d95;
        }

        .q-text {
            font-size: 9.6pt;
        }

        .q-points {
            width: 52px;
            padding-top: 2px;
            font-size: 7.6pt;
            text-align: right;
            color: #7b7491;
        }

        .options {
            margin: 5px 0 0 24px;
        }

        .option {
            margin: 2px 0;
            font-size: 9.4pt;
        }

        .option-letter {
            display: inline-block;
            width: 18px;
            font-weight: bold;
            color: #4c1d95;
        }

        .answer-lines {
            margin: 7px 0 0 24px;
        }

        .answer-line {
            height: 21px;
            border-bottom: 1px solid #bdb3d4;
        }

        .answer-single {
            margin: 7px 0 0 24px;
            font-size: 9.4pt;
            color: #443c60;
        }

        .answer-single .line {
            color: #9a93ad;
        }

        .bs-row {
            margin: 5px 0 0 24px;
            font-size: 9.4pt;
        }

        .bs-box {
            display: inline-block;
            width: 10px;
            height: 10px;
            margin: 0 5px 0 14px;
            border: 1px solid #7b7491;
        }

        .bs-box--first {
            margin-left: 0;
        }

        .key-page {
            page-break-before: always;
        }

        .key-intro {
            margin: 0 0 14px;
            font-size: 9pt;
            color: #443c60;
        }

        table.key-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.key-table th {
            padding: 7px 8px;
            border-bottom: 1px solid #d9cdf5;
            background-color: #f2ecff;
            font-size: 7.6pt;
            font-weight: bold;
            letter-spacing: 0.8px;
            text-align: left;
            text-transform: uppercase;
            color: #4c1d95;
        }

        table.key-table td {
            padding: 7px 8px;
            border-bottom: 1px solid #ede7fa;
            font-size: 9pt;
            vertical-align: top;
        }

        table.key-table td.num {
            width: 32px;
            font-weight: bold;
            color: #4c1d95;
        }

        table.key-table td.type {
            width: 96px;
            color: #7b7491;
        }

        table.key-table td.answer {
            font-weight: bold;
        }

        table.key-table td.points {
            width: 44px;
            text-align: right;
            color: #7b7491;
        }

        .rubric-block {
            margin-top: 16px;
            padding: 10px 12px;
            border: 1px solid #e2d9f6;
            background-color: #fbf9ff;
            page-break-inside: avoid;
        }

        .rubric-block h3 {
            margin: 0 0 4px;
            font-size: 9.2pt;
            color: #241b3d;
        }

        .rubric-block p {
            margin: 0;
            font-size: 9pt;
            color: #443c60;
        }

        .doc-foot-note {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #e2d9f6;
            font-size: 7.4pt;
            color: #9a93ad;
        }
    </style>
</head>
<body>

<div class="doc-accent"></div>

<p class="doc-kicker">Asesmen Pembelajaran</p>
<h1 class="doc-title"><?= esc($asesmen['title']) ?></h1>

<table class="identity">
    <tr>
        <th>Mata pelajaran</th>
        <td><?= esc($asesmen['subject']) ?></td>
        <th>Kelas</th>
        <td><?= esc($asesmen['class']) ?></td>
    </tr>
    <tr>
        <th>Guru</th>
        <td><?= esc($asesmen['teacher']) ?></td>
        <th>Waktu</th>
        <td><?= esc($asesmen['time'] !== '' ? $asesmen['time'] : '-') ?></td>
    </tr>
</table>

<p class="doc-summary">
    <?= esc($asesmen['total_items']) ?> soal |
    Total <?= esc($asesmen['total_points']) ?> poin |
    Diunduh <?= esc($asesmen['generated_at']) ?>
</p>

<?php if ($asesmen['instructions'] !== '') : ?>
    <div class="instructions">
        <h2>Petunjuk pengerjaan</h2>
        <p><?= nl2br(esc($asesmen['instructions'])) ?></p>
    </div>
<?php endif; ?>

<h2 class="section-title">Soal</h2>

<?php foreach ($questions as $question) : ?>
    <div class="question">
        <table class="q-head">
            <tr>
                <td class="q-num"><?= esc($question['number']) ?>.</td>
                <td class="q-text"><?= nl2br(esc($question['text'])) ?></td>
                <td class="q-points"><?= esc($question['points']) ?> poin</td>
            </tr>
        </table>

        <?php if ($question['type'] === 'pg') : ?>
            <div class="options">
                <?php foreach ($question['options'] as $option) : ?>
                    <div class="option">
                        <span class="option-letter"><?= esc($option['letter']) ?>.</span>
                        <span><?= esc($option['text']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($question['type'] === 'bs') : ?>
            <div class="bs-row">
                <span class="bs-box bs-box--first"></span>Benar
                <span class="bs-box"></span>Salah
            </div>
        <?php elseif ($question['type'] === 'isian') : ?>
            <div class="answer-single">
                Jawaban: <span class="line">______________________________________________</span>
            </div>
        <?php else : ?>
            <div class="answer-lines">
                <?php for ($line = 0; $line < 6; $line++) : ?>
                    <div class="answer-line"></div>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<?php
$essayRubrics = array_values(array_filter(
    $questions,
    static fn (array $question): bool => $question['type'] === 'esai' && trim((string) $question['rubric']) !== ''
));
?>

<div class="key-page">
    <div class="doc-accent"></div>
    <p class="doc-kicker">Asesmen Pembelajaran</p>
    <h1 class="doc-title">Kunci jawaban &amp; pedoman penilaian</h1>
    <p class="key-intro">
        <?= esc($asesmen['title']) ?> | <?= esc($asesmen['subject']) ?> |
        Kelas <?= esc($asesmen['class']) ?> | Guru <?= esc($asesmen['teacher']) ?>
    </p>

    <table class="key-table">
        <thead>
            <tr>
                <th scope="col">No</th>
                <th scope="col">Jenis</th>
                <th scope="col">Kunci / jawaban</th>
                <th scope="col">Poin</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($questions as $question) : ?>
                <tr>
                    <td class="num"><?= esc($question['number']) ?></td>
                    <td class="type"><?= esc($question['label']) ?></td>
                    <td class="answer">
                        <?php if ($question['type'] === 'esai') : ?>
                            <?= trim((string) $question['rubric']) !== '' ? 'Lihat rubrik di bawah' : 'Jawaban terbuka' ?>
                        <?php else : ?>
                            <?= esc($question['answer']) ?>
                        <?php endif; ?>
                    </td>
                    <td class="points"><?= esc($question['points']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php foreach ($essayRubrics as $question) : ?>
        <div class="rubric-block">
            <h3>Soal <?= esc($question['number']) ?> &middot; pedoman penskoran esai</h3>
            <p><?= nl2br(esc($question['rubric'])) ?></p>
        </div>
    <?php endforeach; ?>

    <p class="doc-foot-note">
        Total <?= esc($asesmen['total_items']) ?> soal | <?= esc($asesmen['total_points']) ?> poin |
        Dokumen dibuat dengan SASMITA.
    </p>
</div>

</body>
</html>
