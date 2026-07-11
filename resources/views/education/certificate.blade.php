@php
    $issuedAt = $issuedAt ?? now();
    $upperName = strtoupper($user->name);
    $issuedDate = $issuedAt->translatedFormat('d F Y');
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
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
        }

        * {
            box-sizing: border-box;
        }

        .page {
            position: relative;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
        }

        .background {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        .background img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .overlay {
            position: absolute;
            inset: 0;
        }

        .name-mask {
            position: absolute;
            top: 64mm;
            left: 100mm;
            width: 160mm;
            height: 13mm;
            background: #FDFCFA;
        }

        .date-mask {
            position: absolute;
            right: 55mm;
            bottom: 30mm;
            width: 55mm;
            height: 8mm;
            background: #FDFCFA;
        }

        .participant-name {
            position: absolute;
            top: 67mm;
            left: 15mm;
            width: 100%;
            text-align: center;
            font-size: 33px;
            line-height: 1.05;
            font-weight: 800;
            letter-spacing: 1px;
            color: #0f6d81;
        }

        .issued-date {
            position: absolute;
            right: 43mm;
            bottom: 31mm;
            width: 66mm;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            color: #111827;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="background">
            <img src="{{ $backgroundDataUri }}" alt="Template Sertifikat">
        </div>

        <div class="overlay">
            <div class="name-mask"></div>
            <div class="date-mask"></div>
            <div class="participant-name">{{ $upperName }}</div>
            <div class="issued-date">{{ $issuedDate }}</div>
        </div>
    </div>
</body>

</html>
