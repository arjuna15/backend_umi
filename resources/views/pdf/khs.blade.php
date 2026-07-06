<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Hasil Studi (KHS) - {{ $user->name }}</title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.4; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 1.5rem; letter-spacing: 0.5px; }
        .header p { margin: 4px 0 0 0; font-size: 0.85rem; color: #555; }
        .title { text-align: center; font-size: 1.1rem; font-weight: bold; margin: 15px 0; text-transform: uppercase; letter-spacing: 1px; }
        .info-table { width: 100%; margin-bottom: 20px; font-size: 0.85rem; }
        .info-table td { padding: 4px 0; vertical-align: top; }
        .khs-table { width: 100%; border-collapse: collapse; font-size: 0.75rem; margin-top: 10px; }
        .khs-table th { background-color: #f5f5f5; border: 1px solid #bbb; padding: 8px 4px; text-align: center; font-weight: bold; }
        .khs-table td { border: 1px solid #ccc; padding: 8px 4px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .total-row { font-weight: bold; background-color: #fafafa; }
        .footer-table { width: 100%; margin-top: 50px; font-size: 0.85rem; }
        .footer-table td { text-align: center; vertical-align: top; width: 50%; }
        .signature-space { height: 75px; position: relative; }
        .gpa-summary { border: 1px solid #ccc; padding: 12px; margin-top: 20px; font-size: 0.85rem; background-color: #fafafa; width: 40%; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Universitas Mitra Bangsa (UMIBA)</h2>
        <p>Kampus UMIBA: Jl. Pemuda No. 123, Rawamangun, Jakarta Timur | Telp: (021) 12345678</p>
        <p>Email: akademik@umiba.ac.id | Website: www.umiba.ac.id</p>
    </div>

    <div class="title">Kartu Hasil Studi (KHS) Resmi</div>

    <table class="info-table">
        <tr>
            <td style="width: 15%; font-weight: bold;">Nama Mahasiswa</td>
            <td style="width: 35%;">: {{ $user->name }}</td>
            <td style="width: 15%; font-weight: bold;">Semester</td>
            <td style="width: 35%;">: {{ $semester }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">NIM</td>
            <td>: {{ $user->nim_nip }}</td>
            <td style="font-weight: bold;">Tahun Akademik</td>
            <td>: 2026/2027 (Ganjil)</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Program Studi</td>
            <td>: {{ strtoupper($user->prodi ?? '-') }}</td>
            <td style="font-weight: bold;">Dosen Pembimbing Wali</td>
            <td>: {{ $user->dosenWali ? $user->dosenWali->name : '-' }}</td>
        </tr>
    </table>

    <table class="khs-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 10%;">Kode MK</th>
                <th style="width: 28%;" class="text-left">Mata Kuliah</th>
                <th style="width: 5%;">SKS</th>
                <th style="width: 9%;">Hadir (10%)</th>
                <th style="width: 9%;">Tugas (20%)</th>
                <th style="width: 9%;">UTS (30%)</th>
                <th style="width: 9%;">UAS (40%)</th>
                <th style="width: 9%;">Akhir</th>
                <th style="width: 8%;">Grade</th>
            </tr>
        </thead>
        <tbody>
            @foreach($krs as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $item->course->code }}</td>
                <td style="font-weight: bold;">{{ $item->course->name }}</td>
                <td class="text-center">{{ $item->course->sks }}</td>
                <td class="text-center">{{ $item->attendance_score !== null ? round($item->attendance_score) : '-' }}</td>
                <td class="text-center">{{ $item->assignment_score !== null ? round($item->assignment_score) : '-' }}</td>
                <td class="text-center">{{ $item->uts_score !== null ? round($item->uts_score) : '-' }}</td>
                <td class="text-center">{{ $item->uas_score !== null ? round($item->uas_score) : '-' }}</td>
                <td class="text-center" style="font-weight: bold;">{{ $item->score !== null ? number_format($item->score, 1) : '-' }}</td>
                <td class="text-center" style="font-weight: bold;">{{ $item->grade ?: '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 100%; margin-top: 20px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <table style="width: 90%; border-collapse: collapse; font-size: 0.85rem;">
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 6px 0; font-weight: bold;">Total SKS Diambil</td>
                        <td style="padding: 6px 0; text-align: right;">{{ $totalSks }} SKS</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 6px 0; font-weight: bold;">Total SKS Lulus</td>
                        <td style="padding: 6px 0; text-align: right;">{{ $totalSksLulus }} SKS</td>
                    </tr>
                    <tr style="background-color: #f5f5f5; font-weight: bold; border-top: 2px solid #333;">
                        <td style="padding: 8px 6px;">Indeks Prestasi Semester (IPS)</td>
                        <td style="padding: 8px 6px; text-align: right; font-size: 1.1rem; color: #C41E3A;">{{ number_format($ipSemester, 2) }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <!-- QR validation or extra academic notes -->
            </td>
        </tr>
    </table>

    <table class="footer-table">
        <tr>
            <td>
                <p>Menyetujui,</p>
                <p style="font-weight: bold; margin-top: 5px;">Dosen Pembimbing Akademik</p>
                <div class="signature-space">
                    <!-- Cap Digital UMIBA -->
                </div>
                <p style="text-decoration: underline; font-weight: bold;">{{ $user->dosenWali ? $user->dosenWali->name : '................................................' }}</p>
                <p style="font-size: 0.8rem; color: #666; margin-top: 2px;">NIDN: {{ $user->dosenWali ? $user->dosenWali->nim_nip : '................................' }}</p>
            </td>
            <td>
                <p>Jakarta, {{ date('d F Y') }}</p>
                <p style="font-weight: bold; margin-top: 5px;">Kepala Program Studi</p>
                <div class="signature-space">
                    <!-- QR code verifikasi berkas -->
                </div>
                <p style="text-decoration: underline; font-weight: bold;">{{ strtoupper($user->prodi ?? 'KAPRODI') }} UMIBA</p>
                <p style="font-size: 0.8rem; color: #666; margin-top: 2px;">Tanda Tangan & Cap Resmi</p>
            </td>
        </tr>
    </table>
</body>
</html>
