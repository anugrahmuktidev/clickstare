@php
    $issuedAt = $issuedAt ?? now();
    $issuedDate = $issuedAt->translatedFormat('d F Y');
    $upperName = strtoupper($user->name);
    $scoreText = number_format($attempt->score, 0);
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Helvetica Neue', Arial, sans-serif;
            color: #111827;
            background: #e5e7eb;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        * {
            box-sizing: border-box;
        }

        .page {
            width: 240mm;
            height: 160mm;
            padding: 8mm;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            width: 100%;
            height: 100%;
            background: #fff;
            border: 5px solid #fcd34d;
            border-radius: 28px;
            padding: 14mm 20mm;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }

        .header {
            background: #ffc821;
            border-radius: 16px;
            padding: 8mm;
            text-align: center;
            color: #fff;
            margin: 0 auto 8mm;
            width: 50%;
        }

        .header .title {
            margin: 0;
            font-size: 30px;
            font-weight: 700;
            letter-spacing: 1.5px;
        }

        .content {
            text-align: center;
            width: 100%;
        }

        .content .label {
            font-size: 18px;
            color: #4b5563;
            margin-bottom: 5mm;
        }

        .content .name {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 38px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 5mm 0;
        }

        .content .description {
            font-size: 16px;
            color: #374151;
            margin: 3mm 0;
        }

        .content .date {
            margin-top: 5mm;
            font-size: 14px;
            color: #1f2937;
        }

        .signatures {
            width: 100%;
            margin-top: 8mm;
            display: flex;
            justify-content: flex-end;
        }

        .signature-block {
            padding-top: 5mm;
            max-width: 200px;
            margin-left: auto;
            text-align: center;
        }

        .signatures .line {
            display: block;
            border-top: 2px solid #1f2937;
            width: 160px;
            margin: 6px auto 10px;
        }

        .signatures .role {
            font-size: 12px;
            color: #374151;
            margin-bottom: 2mm;
        }

        .signatures .name {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 14px;
            font-weight: 600;
        }

        .signature {
            width: 160px;
            margin-left: auto;
            margin-right: auto;
        }

        .signature svg {
            width: 100%;
            height: auto;
        }

        .signature path {
            stroke: #1f2937;
            stroke-width: 2.4;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .code-card {
            position: absolute;
            left: 12mm;
            bottom: 10mm;
            width: 28mm;
            height: 8mm;
            border-radius: 8px;
            background: #dc2626;
            color: #fff;
            padding: 3.5mm;
            text-align: center;
        }

        .code-card .code-label {
            font-size: 10px;
            margin-bottom: 1mm;
        }

        .code-card .code-value {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            letter-spacing: 1px;
        }

        .ribbon {
            position: absolute;
            top: 25px;
            right: -110px;
            transform: rotate(45deg);
            background: #ef4444;
            color: #fff;
            padding: 10px 110px;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35);
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="card">
            <div>
                <div class="header">
                    <p class="title">SERTIFIKAT KELULUSAN</p>
                </div>
  <div class="ribbon">Zona Tanpa Rokok</div>

                <div class="content">
                    <p class="label">Diberikan kepada</p>
                    <p class="name">{{ $upperName }}</p>
                    <p class="description">Telah menyelesaikan program edukasi bahaya rokok elektronik ClicSTARe.</p>
                    <p class="description">
                        Mencapai {{ $attempt->total_benar }} jawaban benar dari {{ $attempt->total_soal }} soal posttest (nilai
                        {{ $scoreText }}).
                    </p>
                    <p class="date">Diterbitkan pada {{ $issuedDate }}</p>
                </div>
                <br>
                <br>
                <br>
                <br>
                <br>
                <div class="signatures">
                    <div class="signature-block">
                        <div class="signature" aria-hidden="true">
                            <svg viewBox="0 0 160 60" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet">
                                <path d="M5 35 Q 25 5 46 26 T 86 32 Q 98 12 122 28 T 155 34" />
                            </svg>
                        </div>
                        <div class="line"></div>
                        <div class="role">Koordinator Program</div>
                        <div class="name">ClicSTARe</div>
                    </div>
                </div>
            </div>
                <br>
                <br>
            <br>
            <br>
            <div class="code-card">
                <div class="code-label">Kode Peserta</div>
                <div class="code-value">ID-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>
    </div>
</body>

</html>
