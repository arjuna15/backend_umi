<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Rencana Studi (KRS) - {{ $user->name }}</title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.4; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 1.5rem; letter-spacing: 0.5px; }
        .header p { margin: 4px 0 0 0; font-size: 0.85rem; color: #555; }
        .title { text-align: center; font-size: 1.1rem; font-weight: bold; margin: 15px 0; text-transform: uppercase; letter-spacing: 1px; }
        .info-table { width: 100%; margin-bottom: 20px; font-size: 0.85rem; }
        .info-table td { padding: 4px 0; vertical-align: top; }
        .krs-table { width: 100%; border-collapse: collapse; font-size: 0.8rem; margin-top: 10px; }
        .krs-table th { background-color: #f5f5f5; border: 1px solid #bbb; padding: 10px 8px; text-align: left; font-weight: bold; }
        .krs-table td { border: 1px solid #ccc; padding: 10px 8px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #fafafa; }
        .footer-table { width: 100%; margin-top: 50px; font-size: 0.85rem; }
        .footer-table td { text-align: center; vertical-align: top; width: 50%; }
        .signature-space { height: 75px; position: relative; }
        .qrcode { position: absolute; top: 10px; left: 50%; transform: translateX(-50%); width: 60px; height: 60px; opacity: 0.15; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Universitas Mitra Bangsa (UMIBA)</h2>
        <p>Kampus UMIBA: Jl. Pemuda No. 123, Rawamangun, Jakarta Timur | Telp: (021) 12345678</p>
        <p>Email: akademik@umiba.ac.id | Website: www.umiba.ac.id</p>
    </div>

    <div class="title">Kartu Rencana Studi (KRS) Resmi</div>

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

    <table class="krs-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 15%;">Kode MK</th>
                <th style="width: 35%;">Mata Kuliah</th>
                <th style="width: 8%;" class="text-center">SKS</th>
                <th style="width: 17%;">Hari & Jam</th>
                <th style="width: 20%;">Ruangan & Dosen</th>
            </tr>
        </thead>
        <tbody>
            @foreach($krs as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->course->code }}</td>
                <td style="font-weight: bold;">{{ $item->course->name }}</td>
                <td class="text-center">{{ $item->course->sks }}</td>
                <td>{{ $item->course->hari }}, {{ $item->course->jam_mulai }} - {{ $item->course->jam_selesai }}</td>
                <td>
                    R. {{ $item->course->ruangan }}<br>
                    <span style="font-size: 0.75rem; color: #555;">{{ $item->course->dosen ? $item->course->dosen->name : '-' }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right" style="padding: 10px;">Total Kredit SKS diambil:</td>
                <td class="text-center">{{ $totalSks }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <table class="footer-table">
        <tr>
            <td>
                <p>Menyetujui,</p>
                <p style="font-weight: bold; margin-top: 5px;">Dosen Pembimbing Akademik</p>
                <div class="signature-space">
                    <!-- Placeholder cap digital UMIBA -->
                </div>
                <p style="text-decoration: underline; font-weight: bold;">{{ $user->dosenWali ? $user->dosenWali->name : '................................................' }}</p>
                <p style="font-size: 0.8rem; color: #666; margin-top: 2px;">NIDN: {{ $user->dosenWali ? $user->dosenWali->nim_nip : '................................' }}</p>
            </td>
            <td>
                <p>Jakarta, {{ date('d F Y') }}</p>
                <p style="font-weight: bold; margin-top: 5px;">Mahasiswa Bersangkutan</p>
                <div class="signature-space">
                    <!-- QR code verifikasi berkas -->
                </div>
                <p style="text-decoration: underline; font-weight: bold;">{{ $user->name }}</p>
                <p style="font-size: 0.8rem; color: #666; margin-top: 2px;">NIM: {{ $user->nim_nip }}</p>
            </td>
        </tr>
    </table>
</body>
</html>
