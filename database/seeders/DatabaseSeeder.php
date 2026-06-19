<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Admin UMIBA',
            'email' => 'admin@umiba.ac.id',
            'password' => bcrypt('password123'),
        ]);

        \App\Models\Content::insert([
            ['key' => 'home_hero_title', 'type' => 'text', 'value' => 'Raih Masa Depan Gemilang Bersama UMIBA'],
            ['key' => 'home_hero_subtitle', 'type' => 'text', 'value' => 'Pendaftaran Mahasiswa Baru Tahun Akademik 2026/2027 Telah Dibuka. Bergabunglah menjadi bagian dari generasi cerdas dan inovatif.'],
            ['key' => 'home_hero_bg', 'type' => 'image', 'value' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_2.png'],
            ['key' => 'profil_hero_title', 'type' => 'text', 'value' => 'Profil Universitas'],
            ['key' => 'profil_hero_bg', 'type' => 'image', 'value' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_1.png'],
            ['key' => 'akademik_hero_title', 'type' => 'text', 'value' => 'Fakultas & Akademik'],
            ['key' => 'akademik_hero_bg', 'type' => 'image', 'value' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_2.png'],
            ['key' => 'akademik_html', 'type' => 'html', 'value' => '<!-- ░░░ HERO SUBPAGE ░░░ -->


<!-- ░░░ NAVIGATION TABS ░░░ -->
<div style="position: sticky; top: 100px; z-index: 900; margin-top: 24px; margin-bottom: 24px;">
  <div class="container">
    <div class="glass" style="padding: 12px; border-radius: var(--radius-full); display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
      <a href="#manajemen" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Manajemen & Bisnis</a>
      <a href="#hukum" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Ilmu Hukum</a>
      <a href="#komputer" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">TI & Aktuaria</a>
    </div>
  </div>
</div>

<!-- ░░░ MANAJEMEN & BISNIS ░░░ -->
<section id="manajemen" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="glass glass-card fade-up">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Fakultas Utama</span>
      <h2>Fakultas Manajemen dan Bisnis</h2>
      <p>Fakultas Manajemen dan Bisnis UMIBA bertujuan mencetak wirausahawan dan profesional di bidang manajemen yang mampu menghadapi dinamika bisnis global.</p>
      
      <div class="grid grid-2" style="margin-top: var(--space-5);">
        <div class="glass glass-card">
          <h3 class="text-red">S1 Manajemen</h3>
          <p>Membekali mahasiswa dengan kemampuan analisis bisnis, pemasaran, keuangan, dan sumber daya manusia dengan pendekatan kurikulum berbasis industri.</p>
          
          <div style="margin: 16px 0; padding: 12px; background: rgba(0,0,0,0.05); border-radius: var(--radius-sm); border: 1px solid rgba(255,255,255,0.1);">
            <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--color-muted); display: block; margin-bottom: 4px;">Ketua Program Studi</span>
            <div style="display: flex; align-items: center; gap: 8px;">
              <i class="ph-fill ph-user-circle" style="font-size: 1.5rem; color: var(--umiba-red);"></i>
              <span style="font-size: 0.9rem; font-weight: 600;">Indri Astuti, S.Pd., M.M., M.Pd.</span>
            </div>
          </div>

          <a href="/prodi-manajemen" class="btn btn-glass" style="width: 100%; margin-top: auto;">Detail Program Studi</a>
        </div>
        <div class="glass glass-card" id="magister" style="display: flex; flex-direction: column;">
          <h3 class="text-red">S2 Magister Manajemen</h3>
          <p>Program pascasarjana untuk mendalami strategi kepemimpinan dan manajemen korporasi tingkat lanjut bagi para profesional.</p>
          <a href="/prodi-magister" class="btn btn-glass" style="width: 100%; margin-top: auto;">Detail Program Studi</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ░░░ FAKULTAS HUKUM ░░░ -->
<section id="hukum" style="padding: var(--space-8) 0; background: rgba(255, 255, 255, 0.4);">
  <div class="container">
    <div class="glass glass-card fade-up">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Pilihan Profesi</span>
      <h2>Fakultas Hukum</h2>
      <p>Menjadikan mahasiswa lulusan ilmu hukum yang kritis, berintegritas, dan menjunjung tinggi nilai-nilai keadilan sosial di Indonesia maupun taraf internasional.</p>
      
      <div class="glass glass-card" style="margin-top: var(--space-5); max-width: 600px; display: flex; flex-direction: column;">
        <h3 class="text-red">S1 Ilmu Hukum</h3>
        <p>Program studi dengan konsentrasi Hukum Perdata, Pidana, dan Hukum Tata Negara. Mahasiswa difasilitasi dengan praktik peradilan semu (Moot Court).</p>
        
        <div style="margin: 16px 0; padding: 12px; background: rgba(0,0,0,0.05); border-radius: var(--radius-sm); border: 1px solid rgba(255,255,255,0.1);">
          <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--color-muted); display: block; margin-bottom: 4px;">Ketua Program Studi</span>
          <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
            <i class="ph-fill ph-user-circle" style="font-size: 1.5rem; color: var(--umiba-red);"></i>
            <span style="font-size: 0.9rem; font-weight: 600;">Kamilov Sagala, S.H., M.H.</span>
          </div>
          <div style="display: flex; align-items: center; gap: 8px;">
            <i class="ph-fill ph-user" style="font-size: 1.2rem; color: var(--color-muted);"></i>
            <span style="font-size: 0.85rem; color: var(--color-muted);">Darwin S. Siagian, S.H., M.H. (Dosen)</span>
          </div>
        </div>

        <a href="/prodi-hukum" class="btn btn-glass" style="width: 100%; margin-top: auto;">Detail Program Studi</a>
      </div>
    </div>
  </div>
</section>

<!-- ░░░ FAKULTAS TI & AKTUARIA ░░░ -->
<section id="komputer" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="glass glass-card fade-up">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Masa Depan Digital</span>
      <h2>Fakultas Teknologi Informasi dan Aktuaria</h2>
      <p>Mencetak talenta unggul di bidang komputasi cerdas, keamanan siber, dan analisis aktuaria untuk memenuhi kebutuhan industri 4.0.</p>
      
      <div class="grid grid-3" style="margin-top: var(--space-5);">
        <div class="glass glass-card" style="display: flex; flex-direction: column;">
          <h3 class="text-red">S1 Ilmu Komputer</h3>
          <p>Fokus pada kecerdasan buatan, rekayasa perangkat lunak, dan sains data.</p>
          
          <div style="margin: 16px 0; padding: 12px; background: rgba(0,0,0,0.05); border-radius: var(--radius-sm); border: 1px solid rgba(255,255,255,0.1);">
            <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--color-muted); display: block; margin-bottom: 4px;">Ketua Program Studi</span>
            <div style="display: flex; align-items: center; gap: 8px;">
              <i class="ph-fill ph-user-circle" style="font-size: 1.5rem; color: var(--umiba-red);"></i>
              <span style="font-size: 0.9rem; font-weight: 600;">Ahmad Fajar Sidiq, S.Kom., M.Kom.</span>
            </div>
          </div>

          <a href="/prodi-komputer" class="btn btn-glass" style="width: 100%; margin-top: auto;">Detail Program Studi</a>
        </div>
        <div class="glass glass-card" id="sistem" style="display: flex; flex-direction: column;">
          <h3 class="text-red">S1 Sistem &amp; Teknologi Informasi</h3>
          <p>Memadukan teknologi informasi dengan proses bisnis untuk mengelola sistem enterprise.</p>
          <a href="/prodi-sistem" class="btn btn-glass" style="width: 100%; margin-top: auto;">Detail Program Studi</a>
        </div>
        <div class="glass glass-card" id="aktuaria" style="display: flex; flex-direction: column;">
          <h3 class="text-red">S1 Ilmu Aktuaria</h3>
          <p>Mempelajari statistika, matematika, dan manajemen risiko asuransi dan keuangan.</p>
          <a href="/prodi-aktuaria" class="btn btn-glass" style="width: 100%; margin-top: auto;">Detail Program Studi</a>
        </div>
      </div>
    </div>
  </div>
</section>'],
            ['key' => 'dokumen_hero_title', 'type' => 'text', 'value' => 'Dokumen Resmi'],
            ['key' => 'dokumen_hero_bg', 'type' => 'image', 'value' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_3.png'],
            ['key' => 'dokumen_html', 'type' => 'html', 'value' => '<!-- ░░░ HERO SUBPAGE ░░░ -->


<!-- ░░░ NAVIGATION TABS ░░░ -->
<div style="position: sticky; top: 100px; z-index: 900; margin-top: 24px; margin-bottom: 24px;">
  <div class="container">
    <div class="glass" style="padding: 12px; border-radius: var(--radius-full); display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
      <a href="#sk" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">SK & Legalitas</a>
      <a href="#mahasiswa" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Pedoman Mahasiswa</a>
      <a href="#brosur" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Brosur PMB</a>
    </div>
  </div>
</div>

<!-- ░░░ DOKUMEN SECTION ░░░ -->
<section id="sk" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="fade-up" style="margin-bottom: 40px;">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Dokumen Universitas</span>
      <h2>SK & Legalitas Kampus</h2>
    </div>
    <div class="grid grid-2" style="gap: 20px;">
      <div class="glass glass-card fade-up" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h4 style="margin-bottom: 4px;">SK Pendirian UMIBA</h4>
          <p style="font-size: 0.85rem; margin: 0;">No. 486/E/O/2023 tertanggal 13 Juni 2023</p>
        </div>
        <a href="#" class="btn btn-primary" style="padding: 10px; border-radius: 50%;"><i class="ph-bold ph-download-simple"></i></a>
      </div>
      <div class="glass glass-card fade-up">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <div>
            <h4 style="margin-bottom: 4px;">Sertifikat Akreditasi Institusi</h4>
            <p style="font-size: 0.85rem; margin: 0;">BAN-PT Tahun 2024</p>
          </div>
          <a href="#" class="btn btn-primary" style="padding: 10px; border-radius: 50%;"><i class="ph-bold ph-download-simple"></i></a>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="mahasiswa" style="padding: var(--space-8) 0; background: rgba(185, 28, 28, 0.03);">
  <div class="container">
    <div class="fade-up" style="margin-bottom: 40px;">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Kemahasiswaan</span>
      <h2>Pedoman & ORMAWA</h2>
    </div>
    <div class="grid grid-3" style="gap: 20px;">
      <div class="glass glass-card fade-up" style="text-align: center;">
        <i class="ph-duotone ph-file-pdf" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h4 style="margin-bottom: 8px;">AD/ART BEM UMIBA</h4>
        <a href="#" class="btn btn-glass" style="width: 100%; margin-top: 16px;">Unduh Dokumen</a>
      </div>
      <div class="glass glass-card fade-up">
        <div style="text-align: center;">
          <i class="ph-duotone ph-file-pdf" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
          <h4 style="margin-bottom: 8px;">Pedoman PKM 2025</h4>
          <a href="#" class="btn btn-glass" style="width: 100%; margin-top: 16px;">Unduh Dokumen</a>
        </div>
      </div>
      <div class="glass glass-card fade-up">
        <div style="text-align: center;">
          <i class="ph-duotone ph-file-pdf" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
          <h4 style="margin-bottom: 8px;">Buku Panduan Akademik</h4>
          <a href="#" class="btn btn-glass" style="width: 100%; margin-top: 16px;">Unduh Dokumen</a>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="brosur" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="glass glass-card fade-up" style="background: linear-gradient(135deg, rgba(185, 28, 28, 0.05), rgba(185, 28, 28, 0.02)); padding: 60px; text-align: center; border: 1px solid rgba(185, 28, 28, 0.1);">
      <h2 style="font-size: 2.5rem; margin-bottom: 16px;">Unduh Brosur PMB 2026</h2>
      <p style="max-width: 600px; margin: 0 auto 32px;">Dapatkan informasi lengkap mengenai biaya pendidikan, syarat pendaftaran, dan beasiswa di Universitas Mitra Bangsa.</p>
      <div class="flex-center" style="gap: 16px;">
        <a href="#" class="btn btn-primary" style="padding: 16px 32px;"><i class="ph-bold ph-download-simple"></i> Download Brosur (PDF)</a>
      </div>
    </div>
  </div>
</section>'],
            ['key' => 'fasilitas_hero_title', 'type' => 'text', 'value' => 'Fasilitas UMIBA'],
            ['key' => 'fasilitas_hero_bg', 'type' => 'image', 'value' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_1.png'],
            ['key' => 'fasilitas_html', 'type' => 'html', 'value' => '<!-- ░░░ HERO SUBPAGE ░░░ -->


<!-- ░░░ NAVIGATION TABS ░░░ -->
<div style="position: sticky; top: 100px; z-index: 900; margin-top: 24px; margin-bottom: 24px;">
  <div class="container">
    <div class="glass" style="padding: 12px; border-radius: var(--radius-full); display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
      <a href="#akademik" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Fasilitas Akademik</a>
      <a href="#non-akademik" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Fasilitas Umum</a>
    </div>
  </div>
</div>

<!-- ░░░ CONTENT ░░░ -->
<section id="akademik" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="fade-up" style="text-align: center; margin-bottom: 48px;">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Penunjang Belajar</span>
      <h2>Fasilitas Akademik</h2>
    </div>
    <div class="grid grid-3">
      <div class="glass glass-card fade-up">
        <i class="ph-duotone ph-books" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h3>Perpustakaan Digital</h3>
        <p>Akses ribuan jurnal internasional, e-book, dan ruang baca yang nyaman dilengkapi dengan akses internet berkecepatan tinggi.</p>
      </div>
      <div class="glass glass-card fade-up" style="transition-delay: 0.1s;">
        <i class="ph-duotone ph-desktop" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h3>Laboratorium Komputer</h3>
        <p>Dilengkapi dengan PC spesifikasi tinggi standar industri (i7/Ryzen 7) untuk mendukung praktikum prodi IT dan Sistem Informasi.</p>
      </div>
      <div class="glass glass-card fade-up" style="transition-delay: 0.2s;">
        <i class="ph-duotone ph-scales" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h3>Ruang Peradilan Semu</h3>
        <p>Moot Court khusus untuk mahasiswa Fakultas Hukum untuk simulasi praktik peradilan yang didesain persis seperti pengadilan nyata.</p>
      </div>
    </div>
  </div>
</section>

<section id="non-akademik" style="padding: var(--space-8) 0; background: rgba(185, 28, 28, 0.03);">
  <div class="container">
    <div class="fade-up" style="text-align: center; margin-bottom: 48px;">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Penunjang Mahasiswa</span>
      <h2>Fasilitas Umum</h2>
    </div>
    <div class="grid grid-2">
      <div class="glass glass-card fade-up" style="display: flex; gap: 20px; align-items: center;">
        <i class="ph-duotone ph-coffee" style="font-size: 4rem; color: var(--umiba-red);"></i>
        <div>
          <h3>Student Lounge & Cafe</h3>
          <p style="margin:0;">Ruang komunal yang didesain modern untuk diskusi kelompok atau sekadar bersantai, lengkap dengan colokan listrik dan Wi-Fi.</p>
        </div>
      </div>
      <div class="glass glass-card fade-up" style="transition-delay: 0.1s; display: flex; gap: 20px; align-items: center;">
        <i class="ph-duotone ph-mosque" style="font-size: 4rem; color: var(--umiba-red);"></i>
        <div>
          <h3>Masjid Kampus</h3>
          <p style="margin:0;">Fasilitas ibadah yang luas dan bersih, mendukung kegiatan kerohanian mahasiswa dan dosen.</p>
        </div>
      </div>
    </div>
  </div>
</section>'],
            ['key' => 'informasi_hero_title', 'type' => 'text', 'value' => 'Pusat Informasi'],
            ['key' => 'informasi_hero_bg', 'type' => 'image', 'value' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_3.png'],
            ['key' => 'informasi_html', 'type' => 'html', 'value' => '<!-- ░░░ HERO SUBPAGE ░░░ -->


<!-- ░░░ NAVIGATION TABS ░░░ -->
<div style="position: sticky; top: 100px; z-index: 900; margin-top: 24px; margin-bottom: 24px;">
  <div class="container">
    <div class="glass" style="padding: 12px; border-radius: var(--radius-full); display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
      <a href="#biaya" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Biaya Pendidikan</a>
      <a href="#infografis" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Info Grafis</a>
    </div>
  </div>
</div>

<!-- ░░░ BIAYA PENDIDIKAN ░░░ -->
<section id="biaya" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="glass glass-card fade-up">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Informasi Terkini</span>
      <h2>Biaya Pendidikan Tahun Ajaran 2025/2026</h2>
      <p>Universitas Mitra Bangsa berkomitmen memberikan pendidikan berkualitas dengan biaya yang terjangkau. Kami juga menyediakan berbagai program beasiswa bagi mahasiswa berprestasi dan mahasiswa kurang mampu.</p>
      
      <table style="width: 100%; border-collapse: collapse; margin-top: var(--space-4); text-align: left;">
        <thead>
          <tr style="border-bottom: 2px solid var(--umiba-red);">
            <th style="padding: 12px; color: var(--color-text);">Program Studi</th>
            <th style="padding: 12px; color: var(--color-text);">Uang Pangkal</th>
            <th style="padding: 12px; color: var(--color-text);">SPP / Semester</th>
          </tr>
        </thead>
        <tbody>
          <tr style="border-bottom: 1px solid rgba(0,0,0,0.1);">
            <td style="padding: 12px;">S1 Manajemen / Hukum</td>
            <td style="padding: 12px;">Rp 5.500.000</td>
            <td style="padding: 12px;">Rp 4.000.000</td>
          </tr>
          <tr style="border-bottom: 1px solid rgba(0,0,0,0.1);">
            <td style="padding: 12px;">S1 Ilmu Komputer / Sistem TI</td>
            <td style="padding: 12px;">Rp 6.000.000</td>
            <td style="padding: 12px;">Rp 4.500.000</td>
          </tr>
          <tr style="border-bottom: 1px solid rgba(0,0,0,0.1);">
            <td style="padding: 12px;">S1 Ilmu Aktuaria</td>
            <td style="padding: 12px;">Rp 6.500.000</td>
            <td style="padding: 12px;">Rp 4.800.000</td>
          </tr>
          <tr>
            <td style="padding: 12px;">S2 Magister Manajemen</td>
            <td style="padding: 12px;">Rp 8.000.000</td>
            <td style="padding: 12px;">Rp 7.000.000</td>
          </tr>
        </tbody>
      </table>
      
      <p style="margin-top: var(--space-4); font-size: 0.9rem; color: var(--color-muted);">*Biaya di atas adalah estimasi dan dapat berubah sewaktu-waktu. Untuk informasi rincian biaya lengkap, silakan unduh brosur atau hubungi bagian pendaftaran.</p>
    </div>
  </div>
</section>

<!-- ░░░ BROSUR & INFOGRAFIS ░░░ -->
<section id="infografis" style="padding: var(--space-8) 0; background: rgba(255, 255, 255, 0.4);">
  <div class="container grid grid-2">
    <div class="glass glass-card fade-up">
      <h2 class="text-red">Unduh Brosur</h2>
      <p>Dapatkan informasi lengkap tentang visi misi, fakultas, program studi, kegiatan kemahasiswaan, dan panduan lengkap tata cara pendaftaran mahasiswa baru.</p>
      <a href="https://umiba.ac.id/doc/brosur/BrosurUMIBA2025.pdf" target="_blank" class="btn btn-primary" style="margin-top: var(--space-3);">Download Brosur (PDF)</a>
    </div>
    <div class="glass glass-card fade-up" style="transition-delay: 0.1s;">
      <h2 class="text-red">Info Grafis</h2>
      <p>Melihat visualisasi data tentang pencapaian Universitas Mitra Bangsa, statistik kelulusan, penyerapan kerja alumni, dan persebaran mahasiswa di seluruh Indonesia.</p>
      <a href="#" class="btn btn-glass" style="margin-top: var(--space-3);">Lihat Info Grafis</a>
    </div>
  </div>
</section>'],
            ['key' => 'kegiatan-dosen_hero_title', 'type' => 'text', 'value' => 'Kegiatan Dosen'],
            ['key' => 'kegiatan-dosen_hero_bg', 'type' => 'image', 'value' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_2.png'],
            ['key' => 'kegiatan-dosen_html', 'type' => 'html', 'value' => '<!-- ░░░ HERO SUBPAGE ░░░ -->


<!-- ░░░ NAVIGATION TABS ░░░ -->
<div style="position: sticky; top: 100px; z-index: 900; margin-top: 24px; margin-bottom: 24px;">
  <div class="container">
    <div class="glass" style="padding: 12px; border-radius: var(--radius-full); display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
      <a href="#tridharma" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Tridharma Perguruan Tinggi</a>
      <a href="#publikasi" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Publikasi & Riset</a>
    </div>
  </div>
</div>

<!-- ░░░ TRIDHARMA ░░░ -->
<section id="tridharma" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="fade-up" style="text-align: center; margin-bottom: 48px;">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Aktivitas Inti</span>
      <h2>Tridharma Perguruan Tinggi</h2>
      <p style="max-width: 600px; margin: 0 auto;">Dosen UMIBA tidak hanya aktif mengajar, tetapi juga terus berinovasi melalui penelitian dan terjun langsung membantu masyarakat.</p>
    </div>
    <div class="grid grid-3">
      <div class="glass glass-card fade-up">
        <i class="ph-duotone ph-chalkboard-teacher" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h3>Pengajaran</h3>
        <p>Proses transfer ilmu pengetahuan menggunakan metode interaktif, studi kasus, dan e-learning terintegrasi.</p>
      </div>
      <div class="glass glass-card fade-up" style="transition-delay: 0.1s;">
        <i class="ph-duotone ph-flask" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h3>Penelitian</h3>
        <p>Eksplorasi ilmu baru yang inovatif, berkolaborasi dengan mahasiswa dan mitra industri untuk menjawab tantangan zaman.</p>
      </div>
      <div class="glass glass-card fade-up" style="transition-delay: 0.2s;">
        <i class="ph-duotone ph-users-three" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h3>Pengabdian</h3>
        <p>Penerapan hasil riset secara langsung untuk memberdayakan dan meningkatkan taraf hidup masyarakat (UMKM & Desa Binaan).</p>
      </div>
    </div>
  </div>
</section>

<!-- ░░░ PUBLIKASI ░░░ -->
<section id="publikasi" style="padding: var(--space-8) 0; background: rgba(185, 28, 28, 0.03);">
  <div class="container grid grid-2" style="align-items: center; gap: 40px;">
    <div class="fade-up">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Jejak Akademik</span>
      <h2>Publikasi & Rekam Jejak</h2>
      <p>Karya-karya ilmiah dosen UMIBA secara rutin diterbitkan di jurnal nasional terakreditasi SINTA maupun jurnal internasional bereputasi (Scopus).</p>
      <a href="https://ejurnal.umiba.ac.id/" target="_blank" class="btn btn-primary" style="margin-top: 16px;">Akses E-Jurnal UMIBA</a>
    </div>
    <div class="glass glass-card fade-up" style="transition-delay: 0.1s;">
      <h3 style="margin-bottom: 16px;">Pangkalan Data Dosen</h3>
      <p style="font-size: 0.95rem; margin-bottom: 24px;">Informasi detail mengenai riwayat pendidikan, kepangkatan, dan portofolio penelitian dosen dapat diakses secara transparan melalui portal PDDikti.</p>
      <a href="https://pddikti.kemdikbud.go.id/" target="_blank" class="btn btn-glass" style="width: 100%;"><i class="ph-bold ph-link" style="margin-right: 8px;"></i> Cari Dosen di PDDikti</a>
    </div>
  </div>
</section>'],
            ['key' => 'kurikulum_hero_title', 'type' => 'text', 'value' => 'Kurikulum & Akademik'],
            ['key' => 'kurikulum_hero_bg', 'type' => 'image', 'value' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_2.png'],
            ['key' => 'kurikulum_html', 'type' => 'html', 'value' => '<!-- ░░░ HERO SUBPAGE ░░░ -->


<!-- ░░░ NAVIGATION TABS ░░░ -->
<div style="position: sticky; top: 100px; z-index: 900; margin-top: 24px; margin-bottom: 24px;">
  <div class="container">
    <div class="glass" style="padding: 12px; border-radius: var(--radius-full); display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
      <a href="#struktur" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Struktur Kurikulum</a>
      <a href="#kalender" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Kalender Akademik</a>
    </div>
  </div>
</div>

<!-- ░░░ STRUKTUR KURIKULUM ░░░ -->
<section id="struktur" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="grid grid-2" style="align-items: center; gap: 40px;">
      <div class="fade-up">
        <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Berbasis KKNI</span>
        <h2>Struktur Kurikulum UMIBA</h2>
        <p>Kurikulum Universitas Mitra Bangsa dirancang secara komprehensif mengacu pada Kerangka Kualifikasi Nasional Indonesia (KKNI) dan Merdeka Belajar Kampus Merdeka (MBKM). Total Satuan Kredit Semester (SKS) yang harus ditempuh untuk jenjang Sarjana (S1) minimal adalah 144 SKS.</p>
        <ul style="list-style: none; padding: 0; margin-top: 24px;">
          <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 12px;">
            <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
            Mata Kuliah Wajib Umum (MKWU)
          </li>
          <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 12px;">
            <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
            Mata Kuliah Wajib Fakultas (MKWF)
          </li>
          <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 12px;">
            <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
            Mata Kuliah Keahlian Program Studi
          </li>
          <li style="display: flex; align-items: center; gap: 12px;">
            <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
            Mata Kuliah Pilihan / MBKM
          </li>
        </ul>
      </div>
      <div class="glass glass-card fade-up">
        <h3 style="margin-bottom: 16px;">Download Kurikulum per Prodi</h3>
        <div style="display: flex; flex-direction: column; gap: 12px;">
          <a href="#" class="btn btn-glass" style="justify-content: flex-start;"><i class="ph-bold ph-download-simple"></i> Kurikulum S1 Manajemen</a>
          <a href="#" class="btn btn-glass" style="justify-content: flex-start;"><i class="ph-bold ph-download-simple"></i> Kurikulum S1 Hukum</a>
          <a href="#" class="btn btn-glass" style="justify-content: flex-start;"><i class="ph-bold ph-download-simple"></i> Kurikulum S1 Ilmu Komputer</a>
          <a href="#" class="btn btn-glass" style="justify-content: flex-start;"><i class="ph-bold ph-download-simple"></i> Kurikulum S1 Sistem Informasi</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ░░░ KALENDER AKADEMIK ░░░ -->
<section id="kalender" style="padding: var(--space-8) 0; background: rgba(185, 28, 28, 0.03);">
  <div class="container">
    <div class="fade-up" style="text-align: center; margin-bottom: 48px;">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Jadwal</span>
      <h2>Kalender Akademik 2025/2026</h2>
    </div>
    <div class="glass glass-card fade-up" style="max-width: 800px; margin: 0 auto;">
      <div style="display: flex; justify-content: space-between; padding: 16px; border-bottom: 1px solid rgba(0,0,0,0.1);">
        <strong>Awal Perkuliahan Gasal</strong> <span>Agustus 2025</span>
      </div>
      <div style="display: flex; justify-content: space-between; padding: 16px; border-bottom: 1px solid rgba(0,0,0,0.1);">
        <strong>Ujian Tengah Semester (UTS)</strong> <span>Oktober 2025</span>
      </div>
      <div style="display: flex; justify-content: space-between; padding: 16px; border-bottom: 1px solid rgba(0,0,0,0.1);">
        <strong>Ujian Akhir Semester (UAS)</strong> <span>Januari 2026</span>
      </div>
      <div style="display: flex; justify-content: space-between; padding: 16px;">
        <strong>Awal Perkuliahan Genap</strong> <span>Februari 2026</span>
      </div>
      <div style="margin-top: 24px; text-align: center;">
        <a href="#" class="btn btn-primary">Unduh PDF Kalender Lengkap</a>
      </div>
    </div>
  </div>
</section>'],
            ['key' => 'lppm_hero_title', 'type' => 'text', 'value' => 'LPPM UMIBA'],
            ['key' => 'lppm_hero_bg', 'type' => 'image', 'value' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_2.png'],
            ['key' => 'lppm_html', 'type' => 'html', 'value' => '<!-- ░░░ HERO SUBPAGE ░░░ -->


<!-- ░░░ NAVIGATION TABS ░░░ -->
<div style="position: sticky; top: 100px; z-index: 900; margin-top: 24px; margin-bottom: 24px;">
  <div class="container">
    <div class="glass" style="padding: 12px; border-radius: var(--radius-full); display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
      <a href="#tentang" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Tentang</a>
      <a href="#penelitian" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Penelitian</a>
      <a href="#pengabdian" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Pengabdian</a>
      <a href="#berita" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Berita & Info</a>
    </div>
  </div>
</div>

<!-- ░░░ TENTANG SECTION ░░░ -->
<section id="tentang" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="grid grid-2" style="align-items: center;">
      <div class="fade-up">
        <span class="text-red" style="font-weight: 700; text-transform: uppercase; font-size: 0.9rem;">Profil Lembaga</span>
        <h2 style="margin-top: 12px;">Membangun Budaya Riset yang Berdampak</h2>
        <p>LPPM UMIBA merupakan jantung dari kegiatan akademik yang menghubungkan teori ilmiah dengan implementasi praktis di masyarakat. Kami berkomitmen untuk meningkatkan reputasi universitas melalui publikasi internasional dan hilirisasi produk penelitian.</p>
        
        <div class="grid grid-2" style="margin-top: 32px; gap: 20px;">
          <div class="glass glass-card" style="padding: 24px;">
            <i class="ph-duotone ph-eye" style="font-size: 2.5rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
            <h3 style="font-size: 1.25rem;">Visi</h3>
            <p style="font-size: 0.9rem; margin: 0;">Menjadi lembaga unggulan dalam pengembangan IPTEK berbasis kearifan lokal yang diakui secara nasional.</p>
          </div>
          <div class="glass glass-card" style="padding: 24px;">
            <i class="ph-duotone ph-target" style="font-size: 2.5rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
            <h3 style="font-size: 1.25rem;">Misi</h3>
            <p style="font-size: 0.9rem; margin: 0;">Memfasilitasi riset inovatif dan program pengabdian yang solutif bagi permasalahan bangsa.</p>
          </div>
        </div>
      </div>
      <div class="glass glass-card fade-up" style="padding: 0; overflow: hidden;">
        <img src="https://umiba.ac.id/wp-content/uploads/2026/05/rektor-UMIBA-2026.jpeg" alt="Struktur LPPM" style="width: 100%; height: 500px; object-fit: cover;">
        <div class="glass" style="position: absolute; bottom: 20px; left: 20px; right: 20px; padding: 20px;">
          <h4 style="margin:0;">Kepala LPPM UMIBA</h4>
          <p style="margin:0; font-size: 0.85rem;">Mengoordinasikan riset unggulan dosen & mahasiswa.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ░░░ PENELITIAN SECTION ░░░ -->
<section id="penelitian" style="padding: var(--space-8) 0; background: rgba(248, 250, 252, 0.5);">
  <div class="container">
    <div style="text-align: center; max-width: 800px; margin: 0 auto 60px;" class="fade-up">
      <span class="text-red" style="font-weight: 700; text-transform: uppercase;">Excellence in Research</span>
      <h2 style="margin-top: 12px;">Program Penelitian</h2>
      <p>Kami menyediakan berbagai skema pendanaan dan dukungan bagi peneliti untuk mengeksplorasi batas-batas pengetahuan baru.</p>
    </div>

    <div class="grid grid-3">
      <div class="glass glass-card fade-up">
        <div style="background: rgba(185, 28, 28, 0.1); width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
          <i class="ph-duotone ph-currency-circle-dollar" style="font-size: 2rem; color: var(--umiba-red);"></i>
        </div>
        <h3>Hibah Internal</h3>
        <p style="font-size: 0.95rem;">Pendanaan tahunan untuk dosen tetap UMIBA guna meningkatkan produktivitas publikasi SINTA.</p>
        <a href="#" class="btn btn-glass" style="margin-top: auto; font-size: 0.85rem;">Download Panduan</a>
      </div>
      <div class="glass glass-card fade-up" style="transition-delay: 0.1s;">
        <div style="background: rgba(185, 28, 28, 0.1); width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
          <i class="ph-duotone ph-globe" style="font-size: 2rem; color: var(--umiba-red);"></i>
        </div>
        <h3>Hibah Kompetitif</h3>
        <p style="font-size: 0.95rem;">Dukungan pengajuan proposal hibah Kemendikbudristek dan pendanaan eksternal internasional.</p>
        <a href="#" class="btn btn-glass" style="margin-top: auto; font-size: 0.85rem;">Lihat Skema</a>
      </div>
      <div class="glass glass-card fade-up" style="transition-delay: 0.2s;">
        <div style="background: rgba(185, 28, 28, 0.1); width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
          <i class="ph-duotone ph-student" style="font-size: 2rem; color: var(--umiba-red);"></i>
        </div>
        <h3>Riset Mahasiswa</h3>
        <p style="font-size: 0.95rem;">Program kolaborasi riset dosen-mahasiswa untuk tugas akhir dan kompetisi PKM.</p>
        <a href="#" class="btn btn-glass" style="margin-top: auto; font-size: 0.85rem;">Daftar Program</a>
      </div>
    </div>
  </div>
</section>

<!-- ░░░ PENGABDIAN SECTION ░░░ -->
<section id="pengabdian" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="grid grid-2" style="align-items: center; gap: 60px;">
      <div class="fade-up" style="order: 2;">
        <span class="text-red" style="font-weight: 700; text-transform: uppercase;">Community Impact</span>
        <h2 style="margin-top: 12px;">Pengabdian Masyarakat</h2>
        <p>Membawa solusi inovatif dari ruang kelas langsung ke tengah masyarakat untuk perubahan yang nyata dan berkelanjutan.</p>
        
        <ul style="list-style: none; padding: 0; margin: 32px 0;">
          <li style="display: flex; gap: 16px; margin-bottom: 24px;">
            <div style="color: var(--umiba-red); font-size: 1.5rem;"><i class="ph-fill ph-check-circle"></i></div>
            <div>
              <h4 style="margin:0;">Pemberdayaan UMKM</h4>
              <p style="margin:0; font-size: 0.9rem;">Digitalisasi pemasaran dan pengelolaan keuangan untuk pengusaha lokal.</p>
            </div>
          </li>
          <li style="display: flex; gap: 16px; margin-bottom: 24px;">
            <div style="color: var(--umiba-red); font-size: 1.5rem;"><i class="ph-fill ph-check-circle"></i></div>
            <div>
              <h4 style="margin:0;">Desa Binaan</h4>
              <p style="margin:0; font-size: 0.9rem;">Pengembangan potensi ekonomi desa melalui inovasi teknologi tepat guna.</p>
            </div>
          </li>
          <li style="display: flex; gap: 16px;">
            <div style="color: var(--umiba-red); font-size: 1.5rem;"><i class="ph-fill ph-check-circle"></i></div>
            <div>
              <h4 style="margin:0;">Edukasi Masyarakat</h4>
              <p style="margin:0; font-size: 0.9rem;">Penyuluhan hukum, kesehatan digital, dan literasi teknologi informasi.</p>
            </div>
          </li>
        </ul>
      </div>
      <div class="grid grid-2 fade-up" style="gap: 20px; order: 1;">
        <img src="https://umiba.ac.id/wp-content/uploads/2025/12/umiba-4pilar-1536x938-1.jpeg" style="width: 100%; height: 250px; object-fit: cover; border-radius: var(--radius-lg); margin-top: 40px;" alt="Pengabdian 1">
        <img src="https://umiba.ac.id/wp-content/uploads/2025/08/umiba-upacara.jpg" style="width: 100%; height: 250px; object-fit: cover; border-radius: var(--radius-lg);" alt="Pengabdian 2">
      </div>
    </div>
  </div>
</section>

<!-- ░░░ BERITA & PENGUMUMAN SECTION ░░░ -->
<section id="berita" style="padding: var(--space-8) 0; background: rgba(185, 28, 28, 0.03);">
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 48px;" class="fade-up">
      <div>
        <span class="text-red" style="font-weight: 700; text-transform: uppercase;">Update LPPM</span>
        <h2 style="margin: 8px 0 0;">Berita & Pengumuman</h2>
      </div>
      <a href="https://lppm.umiba.ac.id/" target="_blank" class="btn btn-primary">Lihat Web LPPM Original</a>
    </div>

    <div class="grid grid-3">
      <!-- Announcement Card -->
      <div class="glass glass-card fade-up" style="border-top: 4px solid var(--umiba-red);">
        <span style="font-size: 0.8rem; font-weight: 700; color: var(--umiba-red); text-transform: uppercase;">Pengumuman</span>
        <h3 style="font-size: 1.2rem; margin-top: 12px;">Penerimaan Proposal Hibah Penelitian & Pengabdian 2026</h3>
        <p style="font-size: 0.9rem;">Batas akhir pengumpulan proposal tahap 1 adalah 30 Juni 2026. Segera unduh panduan terbaru.</p>
        <div style="margin-top: auto; padding-top: 20px; border-top: 1px solid rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 0.8rem; color: var(--color-muted);">10 Juni 2026</span>
          <a href="#" class="text-red" style="font-weight: 700; font-size: 0.85rem; text-decoration: none;">Unduh PDF →</a>
        </div>
      </div>

      <!-- News Card 1 -->
      <div class="glass glass-card fade-up" style="padding:0; overflow:hidden;">
        <img src="https://umiba.ac.id/wp-content/uploads/2025/08/umiba-upacara.jpg" style="width:100%; height:180px; object-fit:cover;" alt="Berita">
        <div style="padding:24px;">
          <span style="font-size: 0.8rem; font-weight: 700; color: var(--umiba-red); text-transform: uppercase;">Berita Riset</span>
          <h3 style="font-size: 1.15rem; margin-top: 12px;">LPPM UMIBA Gelar Sosialisasi Mitigasi Bencana di Pasar Minggu</h3>
          <p style="font-size: 0.9rem;">Implementasi teknologi sensor dini berbasis IoT untuk masyarakat bantaran sungai.</p>
        </div>
      </div>

      <!-- News Card 2 -->
      <div class="glass glass-card fade-up" style="padding:0; overflow:hidden;">
        <img src="https://umiba.ac.id/wp-content/uploads/2025/12/umiba-4pilar-1536x938-1.jpeg" style="width:100%; height:180px; object-fit:cover;" alt="Berita">
        <div style="padding:24px;">
          <span style="font-size: 0.8rem; font-weight: 700; color: var(--umiba-red); text-transform: uppercase;">Kerjasama</span>
          <h3 style="font-size: 1.15rem; margin-top: 12px;">MoU LPPM UMIBA & Dinas Koperasi Jakarta Selatan</h3>
          <p style="font-size: 0.9rem;">Kolaborasi strategis untuk pendampingan legalitas dan sertifikasi halal UMKM binaan.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ░░░ QUICK LINKS CTA ░░░ -->
<section style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="glass glass-card fade-up" style="background: linear-gradient(135deg, rgba(185, 28, 28, 0.05), rgba(185, 28, 28, 0.02)); padding: 60px; text-align: center; border: 1px solid rgba(185, 28, 28, 0.1);">
      <h2 style="font-size: 2.5rem; margin-bottom: 16px;">Siap Berinovasi Bersama Kami?</h2>
      <p style="max-width: 600px; margin: 0 auto 32px;">Akses seluruh layanan LPPM mulai dari pendaftaran HKI, pengajuan proposal, hingga publikasi jurnal dalam satu pintu.</p>
      <div class="flex-center" style="gap: 16px; flex-wrap: wrap;">
        <a href="https://ejurnal.umiba.ac.id/" target="_blank" class="btn btn-primary" style="padding: 16px 32px;">Portal E-Jurnal</a>
        <a href="https://lppm.umiba.ac.id/" target="_blank" class="btn btn-glass" style="padding: 16px 32px;">Panduan Peneliti</a>
      </div>
    </div>
  </div>
</section>'],
            ['key' => 'mutu_hero_title', 'type' => 'text', 'value' => 'LPM UMIBA'],
            ['key' => 'mutu_hero_bg', 'type' => 'image', 'value' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_1.png'],
            ['key' => 'mutu_html', 'type' => 'html', 'value' => '<!-- ░░░ HERO SUBPAGE ░░░ -->


<!-- ░░░ NAVIGATION TABS ░░░ -->
<div style="position: sticky; top: 100px; z-index: 900; margin-top: 24px; margin-bottom: 24px;">
  <div class="container">
    <div class="glass" style="padding: 12px; border-radius: var(--radius-full); display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
      <a href="#tentang-lpm" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Tentang LPM</a>
    </div>
  </div>
</div>

<!-- ░░░ CONTENT ░░░ -->
<section id="tentang-lpm" style="padding: var(--space-8) 0;">
  <div class="container grid grid-2" style="align-items: start;">
    <div class="glass glass-card fade-up">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Akreditasi &amp; Evaluasi</span>
      <h2>Lembaga Penjaminan Mutu</h2>
      <p>LPM UMIBA bertanggung jawab untuk merencanakan, melaksanakan, mengevaluasi, dan menindaklanjuti program penjaminan mutu internal di lingkungan Universitas Mitra Bangsa.</p>
      <p>Kami memastikan bahwa seluruh kegiatan tridharma perguruan tinggi sesuai dengan Standar Nasional Pendidikan Tinggi (SN Dikti) dan standar mutu yang telah ditetapkan universitas.</p>
      <a href="https://lpm.umiba.ac.id/" target="_blank" class="btn btn-primary" style="margin-top: var(--space-3);">Kunjungi Website LPM</a>
    </div>
    
    <div class="glass glass-card fade-up" style="transition-delay: 0.1s;">
      <h3 class="text-red">Sistem Penjaminan Mutu Internal (SPMI)</h3>
      <p>Tahapan SPMI yang diterapkan di UMIBA mencakup siklus PPEPP:</p>
      <ul style="padding-left: var(--space-4); margin-top: var(--space-3);">
        <li style="margin-bottom: 8px;"><strong>P</strong>enetapan Standar Mutu</li>
        <li style="margin-bottom: 8px;"><strong>P</strong>elaksanaan Standar Mutu</li>
        <li style="margin-bottom: 8px;"><strong>E</strong>valuasi (Pelaksanaan Standar Mutu)</li>
        <li style="margin-bottom: 8px;"><strong>P</strong>engendalian (Pelaksanaan Standar Mutu)</li>
        <li style="margin-bottom: 8px;"><strong>P</strong>eningkatan Standar Mutu</li>
      </ul>
    </div>
  </div>
</section>'],
            ['key' => 'prodi-aktuaria_hero_title', 'type' => 'text', 'value' => 'S1 Ilmu Aktuaria'],
            ['key' => 'prodi-aktuaria_hero_bg', 'type' => 'image', 'value' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_3.png'],
            ['key' => 'prodi-aktuaria_html', 'type' => 'html', 'value' => '<!-- ░░░ HERO SUBPAGE ░░░ -->


<!-- ░░░ TABS ░░░ -->
<div style="position: sticky; top: 100px; z-index: 900; margin-top: 24px; margin-bottom: 24px;">
  <div class="container">
    <div class="glass" style="padding: 12px; border-radius: var(--radius-full); display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
      <a href="#sambutan" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Sambutan Kaprodi</a>
      <a href="#visimisi" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Visi & Misi</a>
      <a href="#profil" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Profil & Karir</a>
      <a href="#dokumen" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Kurikulum & SK</a>
    </div>
  </div>
</div>

<!-- ░░░ SAMBUTAN KAPRODI ░░░ -->
<section id="sambutan" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="grid grid-2" style="align-items: center; gap: 60px;">
      <div class="fade-up">
        <span class="text-red" style="font-weight: 700; text-transform: uppercase; font-size: 0.9rem;">Greetings</span>
        <h2 style="margin-top: 12px;">Sambutan Ketua Program Studi</h2>
        <p style="font-style: italic; font-size: 1.1rem; line-height: 1.8; color: var(--color-text); margin: 24px 0;">"Mengelola risiko adalah seni dalam matematika. Program Aktuaria UMIBA mempersiapkan Anda menjadi ahli finansial yang krusial bagi industri asuransi dan keuangan."</p>
        <div style="display: flex; flex-direction: column; gap: 4px;">
          <h4 style="margin: 0; color: var(--umiba-red);">Dosen Ahli UMIBA</h4>
          <p style="margin: 0; font-size: 0.9rem; font-weight: 600;">Ketua Program Studi S1 Ilmu Aktuaria</p>
        </div>
        <div style="margin-top: 32px;">
          <a href="https://pddikti.kemdikbud.go.id/" target="_blank" class="btn btn-glass"><i class="ph-bold ph-users-three"></i> Lihat Daftar Lengkap Dosen (PDDikti)</a>
        </div>
      </div>
      <div class="fade-up" style="position: relative;">
        <div class="glass" style="padding: 15px; border-radius: var(--radius-lg); transform: rotate(2deg);">
          <img src="https://via.placeholder.com/400x500/B91C1C/fff?text=Kaprodi+Aktuaria" alt="Dosen Ahli UMIBA" style="width: 100%; border-radius: var(--radius-md); box-shadow: 0 15px 30px rgba(0,0,0,0.1); background: #eee;">
        </div>
        <div style="position: absolute; -z-index: 1; top: -20px; right: -20px; width: 100px; height: 100px; background: var(--umiba-red-alpha); border-radius: 50%; filter: blur(30px);"></div>
      </div>
    </div>
  </div>
</section>

<!-- ░░░ VISI & MISI ░░░ -->
<section id="visimisi" style="padding: var(--space-8) 0;">
  <div class="container grid grid-2" style="align-items: start; gap: 40px;">
    <div class="glass glass-card fade-up">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Arah Gerak Prodi</span>
      <h2>Visi S1 Ilmu Aktuaria</h2>
      <p style="font-size: 1.1rem; line-height: 1.8; font-weight: 600; color: var(--color-text);">"Mencetak aktuaris profesional bersertifikasi standar internasional yang tanggap terhadap dinamika industri keuangan dan asuransi."</p>
    </div>
    <div class="glass glass-card fade-up" style="transition-delay: 0.1s;">
      <h3 class="text-red">Misi S1 Ilmu Aktuaria</h3>
      <ul style="padding-left: var(--space-4);">
        <li style="margin-bottom: 8px;">Memberikan pendidikan matematika finansial dan aktuaria terbaik di tingkat nasional.</li>
        <li style="margin-bottom: 8px;">Bekerjasama aktif dengan PAI (Persatuan Aktuaris Indonesia) untuk sertifikasi lulusan.</li>
        <li style="margin-bottom: 8px;">Menerapkan sains data dan big data analytics pada manajemen risiko asuransi.</li>
        <li style="margin-bottom: 8px;">Mengembangkan riset inovatif dalam pemodelan risiko dan aktuaria.</li>
      </ul>
    </div>
  </div>
</section>

<!-- ░░░ PROFIL LULUSAN & PEMINATAN ░░░ -->
<section id="profil" style="padding: var(--space-8) 0; background: rgba(185, 28, 28, 0.03);">
  <div class="container grid grid-2" style="gap: 40px;">
    
    <!-- Peminatan -->
    <div class="fade-up">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Fokus Keilmuan</span>
      <h2>Peminatan / Konsentrasi</h2>
      <p>Mahasiswa dapat memilih peminatan khusus di semester atas untuk memfokuskan kompetensi dan karir profesional mereka.</p>
      <div class="grid grid-2" style="gap: 16px; margin-top: 24px;">
        <div class="glass glass-card" style="padding: 16px; text-align: center;">
          <h4 style="margin: 0; color: var(--umiba-red); font-size: 1rem;">Asuransi Jiwa & Kesehatan</h4>
        </div>
        <div class="glass glass-card" style="padding: 16px; text-align: center;">
          <h4 style="margin: 0; color: var(--umiba-red); font-size: 1rem;">Asuransi Umum (General Insurance)</h4>
        </div>
        <div class="glass glass-card" style="padding: 16px; text-align: center;">
          <h4 style="margin: 0; color: var(--umiba-red); font-size: 1rem;">Manajemen Risiko Keuangan</h4>
        </div>
      </div>
    </div>

    <!-- Profil Lulusan -->
    <div class="glass glass-card fade-up" style="transition-delay: 0.1s;">
      <h3 class="text-red">Prospek Karir & Profil Lulusan</h3>
      <p>Lulusan program studi ini dipersiapkan untuk menempati berbagai posisi strategis di industri, pemerintahan, dan korporasi:</p>
      <ul style="list-style: none; padding: 0; margin-top: 24px;">
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Aktuaris Bersertifikat (FSAI/ASAI)</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Risk Manager</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Data Analyst Keuangan</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Konsultan Asuransi & Dana Pensiun</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Underwriter</strong>
        </li>
      </ul>
    </div>
  </div>
</section>

<!-- ░░░ DOKUMEN ░░░ -->
<section id="dokumen" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="fade-up" style="text-align: center; margin-bottom: 48px;">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Unduhan Resmi</span>
      <h2>Dokumen {data[\'title\']}</h2>
    </div>
    <div class="grid grid-3" style="gap: 24px;">
      
      <!-- Kurikulum -->
      <div class="glass glass-card fade-up" style="display: flex; flex-direction: column; text-align: center;">
        <i class="ph-duotone ph-book-open" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h4 style="margin-bottom: 8px;">Buku Kurikulum</h4>
        <p style="font-size: 0.85rem; margin-bottom: 24px;">Pedoman sebaran mata kuliah (SKS) dari semester 1 hingga akhir kelulusan.</p>
        <a href="#" class="btn btn-glass" style="margin-top: auto; width: 100%;"><i class="ph-bold ph-download-simple"></i> Unduh PDF</a>
      </div>

      <!-- SK Pendirian -->
      <div class="glass glass-card fade-up" style="transition-delay: 0.1s; display: flex; flex-direction: column; text-align: center;">
        <i class="ph-duotone ph-certificate" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h4 style="margin-bottom: 8px;">SK Pendirian Prodi</h4>
        <p style="font-size: 0.85rem; margin-bottom: 24px;">Legalitas resmi pembukaan program studi dari Kemendikbudristek RI.</p>
        <a href="#" class="btn btn-glass" style="margin-top: auto; width: 100%;"><i class="ph-bold ph-download-simple"></i> Unduh SK</a>
      </div>

      <!-- Akreditasi -->
      <div class="glass glass-card fade-up" style="transition-delay: 0.2s; display: flex; flex-direction: column; text-align: center;">
        <i class="ph-duotone ph-medal" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h4 style="margin-bottom: 8px;">Sertifikat Akreditasi</h4>
        <p style="font-size: 0.85rem; margin-bottom: 24px;">Sertifikat Akreditasi resmi dari BAN-PT / LAM untuk program studi ini.</p>
        <a href="#" class="btn btn-glass" style="margin-top: auto; width: 100%;"><i class="ph-bold ph-download-simple"></i> Unduh Sertifikat</a>
      </div>

    </div>
  </div>
</section>'],
            ['key' => 'prodi-hukum_hero_title', 'type' => 'text', 'value' => 'S1 Ilmu Hukum'],
            ['key' => 'prodi-hukum_hero_bg', 'type' => 'image', 'value' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_1.png'],
            ['key' => 'prodi-hukum_html', 'type' => 'html', 'value' => '<!-- ░░░ HERO SUBPAGE ░░░ -->


<!-- ░░░ TABS ░░░ -->
<div style="position: sticky; top: 100px; z-index: 900; margin-top: 24px; margin-bottom: 24px;">
  <div class="container">
    <div class="glass" style="padding: 12px; border-radius: var(--radius-full); display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
      <a href="#sambutan" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Sambutan Kaprodi</a>
      <a href="#visimisi" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Visi & Misi</a>
      <a href="#profil" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Profil & Karir</a>
      <a href="#dokumen" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Kurikulum & SK</a>
    </div>
  </div>
</div>

<!-- ░░░ SAMBUTAN KAPRODI ░░░ -->
<section id="sambutan" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="grid grid-2" style="align-items: center; gap: 60px;">
      <div class="fade-up">
        <span class="text-red" style="font-weight: 700; text-transform: uppercase; font-size: 0.9rem;">Greetings</span>
        <h2 style="margin-top: 12px;">Sambutan Ketua Program Studi</h2>
        <p style="font-style: italic; font-size: 1.1rem; line-height: 1.8; color: var(--color-text); margin: 24px 0;">"Selamat datang di kawah candradimuka para penegak keadilan. Kami mencetak lulusan hukum yang kritis, berintegritas, dan siap menjawab tantangan hukum nasional maupun global."</p>
        <div style="display: flex; flex-direction: column; gap: 4px;">
          <h4 style="margin: 0; color: var(--umiba-red);">Kamilov Sagala, S.H., M.H.</h4>
          <p style="margin: 0; font-size: 0.9rem; font-weight: 600;">Ketua Program Studi S1 Ilmu Hukum</p>
        </div>
        <div style="margin-top: 32px;">
          <a href="https://pddikti.kemdikbud.go.id/" target="_blank" class="btn btn-glass"><i class="ph-bold ph-users-three"></i> Lihat Daftar Lengkap Dosen (PDDikti)</a>
        </div>
      </div>
      <div class="fade-up" style="position: relative;">
        <div class="glass" style="padding: 15px; border-radius: var(--radius-lg); transform: rotate(2deg);">
          <img src="https://umiba.ac.id/wp-content/uploads/2025/09/WhatsApp-Image-2025-09-25-at-14.37.30.jpeg" alt="Kamilov Sagala, S.H., M.H." style="width: 100%; border-radius: var(--radius-md); box-shadow: 0 15px 30px rgba(0,0,0,0.1); background: #eee;">
        </div>
        <div style="position: absolute; -z-index: 1; top: -20px; right: -20px; width: 100px; height: 100px; background: var(--umiba-red-alpha); border-radius: 50%; filter: blur(30px);"></div>
      </div>
    </div>
  </div>
</section>

<!-- ░░░ VISI & MISI ░░░ -->
<section id="visimisi" style="padding: var(--space-8) 0;">
  <div class="container grid grid-2" style="align-items: start; gap: 40px;">
    <div class="glass glass-card fade-up">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Arah Gerak Prodi</span>
      <h2>Visi S1 Ilmu Hukum</h2>
      <p style="font-size: 1.1rem; line-height: 1.8; font-weight: 600; color: var(--color-text);">"Menjadi pusat unggulan dalam pendidikan dan penelitian di bidang hukum yang berorientasi pada keunggulan akademik, integritas, dan kontribusi kepada bangsa."</p>
    </div>
    <div class="glass glass-card fade-up" style="transition-delay: 0.1s;">
      <h3 class="text-red">Misi S1 Ilmu Hukum</h3>
      <ul style="padding-left: var(--space-4);">
        <li style="margin-bottom: 8px;">Memberikan pendidikan hukum berkualitas tinggi secara teori dan praktik.</li>
        <li style="margin-bottom: 8px;">Mendorong pemikiran kritis dan analitis terhadap isu hukum kompleks.</li>
        <li style="margin-bottom: 8px;">Memupuk etika profesional dan integritas tinggi bagi calon yuris.</li>
        <li style="margin-bottom: 8px;">Mendorong penelitian berkualitas yang berdampak positif bagi masyarakat.</li>
        <li style="margin-bottom: 8px;">Mengembangkan keterampilan praktis melalui magang dan klinik hukum.</li>
        <li style="margin-bottom: 8px;">Menginspirasi kepemimpinan dan pelayanan masyarakat (pro bono).</li>
      </ul>
    </div>
  </div>
</section>

<!-- ░░░ PROFIL LULUSAN & PEMINATAN ░░░ -->
<section id="profil" style="padding: var(--space-8) 0; background: rgba(185, 28, 28, 0.03);">
  <div class="container grid grid-2" style="gap: 40px;">
    
    <!-- Peminatan -->
    <div class="fade-up">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Fokus Keilmuan</span>
      <h2>Peminatan / Konsentrasi</h2>
      <p>Mahasiswa dapat memilih peminatan khusus di semester atas untuk memfokuskan kompetensi dan karir profesional mereka.</p>
      <div class="grid grid-2" style="gap: 16px; margin-top: 24px;">
        <div class="glass glass-card" style="padding: 16px; text-align: center;">
          <h4 style="margin: 0; color: var(--umiba-red); font-size: 1rem;">Hukum Bisnis dan Perdagangan</h4>
        </div>
        <div class="glass glass-card" style="padding: 16px; text-align: center;">
          <h4 style="margin: 0; color: var(--umiba-red); font-size: 1rem;">Hukum Internasional & HAM</h4>
        </div>
        <div class="glass glass-card" style="padding: 16px; text-align: center;">
          <h4 style="margin: 0; color: var(--umiba-red); font-size: 1rem;">Hukum Pidana dan Kriminologi</h4>
        </div>
        <div class="glass glass-card" style="padding: 16px; text-align: center;">
          <h4 style="margin: 0; color: var(--umiba-red); font-size: 1rem;">Hukum Teknologi & Kekayaan Intelektual</h4>
        </div>
      </div>
    </div>

    <!-- Profil Lulusan -->
    <div class="glass glass-card fade-up" style="transition-delay: 0.1s;">
      <h3 class="text-red">Prospek Karir & Profil Lulusan</h3>
      <p>Lulusan program studi ini dipersiapkan untuk menempati berbagai posisi strategis di industri, pemerintahan, dan korporasi:</p>
      <ul style="list-style: none; padding: 0; margin-top: 24px;">
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Advokat / Pengacara Profesional</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Pegawai Negeri / Hakim / Jaksa</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Konsultan Hukum (Legal Consultant)</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>In-house Counsel Perusahaan</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Peneliti & Dosen Hukum</strong>
        </li>
      </ul>
    </div>
  </div>
</section>

<!-- ░░░ DOKUMEN ░░░ -->
<section id="dokumen" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="fade-up" style="text-align: center; margin-bottom: 48px;">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Unduhan Resmi</span>
      <h2>Dokumen {data[\'title\']}</h2>
    </div>
    <div class="grid grid-3" style="gap: 24px;">
      
      <!-- Kurikulum -->
      <div class="glass glass-card fade-up" style="display: flex; flex-direction: column; text-align: center;">
        <i class="ph-duotone ph-book-open" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h4 style="margin-bottom: 8px;">Buku Kurikulum</h4>
        <p style="font-size: 0.85rem; margin-bottom: 24px;">Pedoman sebaran mata kuliah (SKS) dari semester 1 hingga akhir kelulusan.</p>
        <a href="#" class="btn btn-glass" style="margin-top: auto; width: 100%;"><i class="ph-bold ph-download-simple"></i> Unduh PDF</a>
      </div>

      <!-- SK Pendirian -->
      <div class="glass glass-card fade-up" style="transition-delay: 0.1s; display: flex; flex-direction: column; text-align: center;">
        <i class="ph-duotone ph-certificate" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h4 style="margin-bottom: 8px;">SK Pendirian Prodi</h4>
        <p style="font-size: 0.85rem; margin-bottom: 24px;">Legalitas resmi pembukaan program studi dari Kemendikbudristek RI.</p>
        <a href="#" class="btn btn-glass" style="margin-top: auto; width: 100%;"><i class="ph-bold ph-download-simple"></i> Unduh SK</a>
      </div>

      <!-- Akreditasi -->
      <div class="glass glass-card fade-up" style="transition-delay: 0.2s; display: flex; flex-direction: column; text-align: center;">
        <i class="ph-duotone ph-medal" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h4 style="margin-bottom: 8px;">Sertifikat Akreditasi</h4>
        <p style="font-size: 0.85rem; margin-bottom: 24px;">Sertifikat Akreditasi resmi dari BAN-PT / LAM untuk program studi ini.</p>
        <a href="#" class="btn btn-glass" style="margin-top: auto; width: 100%;"><i class="ph-bold ph-download-simple"></i> Unduh Sertifikat</a>
      </div>

    </div>
  </div>
</section>'],
            ['key' => 'prodi-komputer_hero_title', 'type' => 'text', 'value' => 'S1 Ilmu Komputer'],
            ['key' => 'prodi-komputer_hero_bg', 'type' => 'image', 'value' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_3.png'],
            ['key' => 'prodi-komputer_html', 'type' => 'html', 'value' => '<!-- ░░░ HERO SUBPAGE ░░░ -->


<!-- ░░░ TABS ░░░ -->
<div style="position: sticky; top: 100px; z-index: 900; margin-top: 24px; margin-bottom: 24px;">
  <div class="container">
    <div class="glass" style="padding: 12px; border-radius: var(--radius-full); display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
      <a href="#sambutan" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Sambutan Kaprodi</a>
      <a href="#visimisi" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Visi & Misi</a>
      <a href="#profil" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Profil & Karir</a>
      <a href="#dokumen" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Kurikulum & SK</a>
    </div>
  </div>
</div>

<!-- ░░░ SAMBUTAN KAPRODI ░░░ -->
<section id="sambutan" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="grid grid-2" style="align-items: center; gap: 60px;">
      <div class="fade-up">
        <span class="text-red" style="font-weight: 700; text-transform: uppercase; font-size: 0.9rem;">Greetings</span>
        <h2 style="margin-top: 12px;">Sambutan Ketua Program Studi</h2>
        <p style="font-style: italic; font-size: 1.1rem; line-height: 1.8; color: var(--color-text); margin: 24px 0;">"Era AI menuntut kita untuk adaptif. Di sini, kami menempa talenta digital yang tidak hanya mahir koding, tapi juga inovatif dalam menciptakan solusi cerdas bagi masyarakat."</p>
        <div style="display: flex; flex-direction: column; gap: 4px;">
          <h4 style="margin: 0; color: var(--umiba-red);">Ahmad Fajar Sidiq, S.Kom., M.Kom.</h4>
          <p style="margin: 0; font-size: 0.9rem; font-weight: 600;">Ketua Program Studi S1 Ilmu Komputer</p>
        </div>
        <div style="margin-top: 32px;">
          <a href="https://pddikti.kemdikbud.go.id/" target="_blank" class="btn btn-glass"><i class="ph-bold ph-users-three"></i> Lihat Daftar Lengkap Dosen (PDDikti)</a>
        </div>
      </div>
      <div class="fade-up" style="position: relative;">
        <div class="glass" style="padding: 15px; border-radius: var(--radius-lg); transform: rotate(2deg);">
          <img src="https://umiba.ac.id/wp-content/uploads/2024/05/fajar_130.jpg" alt="Ahmad Fajar Sidiq, S.Kom., M.Kom." style="width: 100%; border-radius: var(--radius-md); box-shadow: 0 15px 30px rgba(0,0,0,0.1); background: #eee;">
        </div>
        <div style="position: absolute; -z-index: 1; top: -20px; right: -20px; width: 100px; height: 100px; background: var(--umiba-red-alpha); border-radius: 50%; filter: blur(30px);"></div>
      </div>
    </div>
  </div>
</section>

<!-- ░░░ VISI & MISI ░░░ -->
<section id="visimisi" style="padding: var(--space-8) 0;">
  <div class="container grid grid-2" style="align-items: start; gap: 40px;">
    <div class="glass glass-card fade-up">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Arah Gerak Prodi</span>
      <h2>Visi S1 Ilmu Komputer</h2>
      <p style="font-size: 1.1rem; line-height: 1.8; font-weight: 600; color: var(--color-text);">"Mencetak sumber daya manusia yang ahli dalam bidang pemrograman komputer berbasis kecerdasan artifisial dan keamanan jaringan."</p>
    </div>
    <div class="glass glass-card fade-up" style="transition-delay: 0.1s;">
      <h3 class="text-red">Misi S1 Ilmu Komputer</h3>
      <ul style="padding-left: var(--space-4);">
        <li style="margin-bottom: 8px;">Menyelenggarakan pendidikan pemrograman dan keamanan jaringan terkini.</li>
        <li style="margin-bottom: 8px;">Menyelenggarakan penelitian mendalam di bidang kecerdasan artifisial.</li>
        <li style="margin-bottom: 8px;">Berperan aktif menyejahterakan masyarakat melalui implementasi riset teknologi.</li>
        <li style="margin-bottom: 8px;">Menyelenggarakan pendidikan pakar pemrograman yang mengedepankan kewirausahaan.</li>
        <li style="margin-bottom: 8px;">Menjalin kemitraan global di sektor pengembangan teknologi digital.</li>
      </ul>
    </div>
  </div>
</section>

<!-- ░░░ PROFIL LULUSAN & PEMINATAN ░░░ -->
<section id="profil" style="padding: var(--space-8) 0; background: rgba(185, 28, 28, 0.03);">
  <div class="container grid grid-2" style="gap: 40px;">
    
    <!-- Peminatan -->
    <div class="fade-up">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Fokus Keilmuan</span>
      <h2>Peminatan / Konsentrasi</h2>
      <p>Mahasiswa dapat memilih peminatan khusus di semester atas untuk memfokuskan kompetensi dan karir profesional mereka.</p>
      <div class="grid grid-2" style="gap: 16px; margin-top: 24px;">
        <div class="glass glass-card" style="padding: 16px; text-align: center;">
          <h4 style="margin: 0; color: var(--umiba-red); font-size: 1rem;">Programming & Software Development</h4>
        </div>
        <div class="glass glass-card" style="padding: 16px; text-align: center;">
          <h4 style="margin: 0; color: var(--umiba-red); font-size: 1rem;">Networking & Cyber Security</h4>
        </div>
        <div class="glass glass-card" style="padding: 16px; text-align: center;">
          <h4 style="margin: 0; color: var(--umiba-red); font-size: 1rem;">Embedded System & IoT</h4>
        </div>
      </div>
    </div>

    <!-- Profil Lulusan -->
    <div class="glass glass-card fade-up" style="transition-delay: 0.1s;">
      <h3 class="text-red">Prospek Karir & Profil Lulusan</h3>
      <p>Lulusan program studi ini dipersiapkan untuk menempati berbagai posisi strategis di industri, pemerintahan, dan korporasi:</p>
      <ul style="list-style: none; padding: 0; margin-top: 24px;">
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Computer Scientist</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Artificial Intelligence Engineer</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Software Engineer</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Network Engineer</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Cyber Security Engineer</strong>
        </li>
      </ul>
    </div>
  </div>
</section>

<!-- ░░░ DOKUMEN ░░░ -->
<section id="dokumen" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="fade-up" style="text-align: center; margin-bottom: 48px;">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Unduhan Resmi</span>
      <h2>Dokumen {data[\'title\']}</h2>
    </div>
    <div class="grid grid-3" style="gap: 24px;">
      
      <!-- Kurikulum -->
      <div class="glass glass-card fade-up" style="display: flex; flex-direction: column; text-align: center;">
        <i class="ph-duotone ph-book-open" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h4 style="margin-bottom: 8px;">Buku Kurikulum</h4>
        <p style="font-size: 0.85rem; margin-bottom: 24px;">Pedoman sebaran mata kuliah (SKS) dari semester 1 hingga akhir kelulusan.</p>
        <a href="#" class="btn btn-glass" style="margin-top: auto; width: 100%;"><i class="ph-bold ph-download-simple"></i> Unduh PDF</a>
      </div>

      <!-- SK Pendirian -->
      <div class="glass glass-card fade-up" style="transition-delay: 0.1s; display: flex; flex-direction: column; text-align: center;">
        <i class="ph-duotone ph-certificate" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h4 style="margin-bottom: 8px;">SK Pendirian Prodi</h4>
        <p style="font-size: 0.85rem; margin-bottom: 24px;">Legalitas resmi pembukaan program studi dari Kemendikbudristek RI.</p>
        <a href="#" class="btn btn-glass" style="margin-top: auto; width: 100%;"><i class="ph-bold ph-download-simple"></i> Unduh SK</a>
      </div>

      <!-- Akreditasi -->
      <div class="glass glass-card fade-up" style="transition-delay: 0.2s; display: flex; flex-direction: column; text-align: center;">
        <i class="ph-duotone ph-medal" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h4 style="margin-bottom: 8px;">Sertifikat Akreditasi</h4>
        <p style="font-size: 0.85rem; margin-bottom: 24px;">Sertifikat Akreditasi resmi dari BAN-PT / LAM untuk program studi ini.</p>
        <a href="#" class="btn btn-glass" style="margin-top: auto; width: 100%;"><i class="ph-bold ph-download-simple"></i> Unduh Sertifikat</a>
      </div>

    </div>
  </div>
</section>'],
            ['key' => 'prodi-magister_hero_title', 'type' => 'text', 'value' => 'S2 Magister Manajemen'],
            ['key' => 'prodi-magister_hero_bg', 'type' => 'image', 'value' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_2.png'],
            ['key' => 'prodi-magister_html', 'type' => 'html', 'value' => '<!-- ░░░ HERO SUBPAGE ░░░ -->


<!-- ░░░ TABS ░░░ -->
<div style="position: sticky; top: 100px; z-index: 900; margin-top: 24px; margin-bottom: 24px;">
  <div class="container">
    <div class="glass" style="padding: 12px; border-radius: var(--radius-full); display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
      <a href="#sambutan" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Sambutan Kaprodi</a>
      <a href="#visimisi" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Visi & Misi</a>
      <a href="#profil" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Profil & Karir</a>
      <a href="#dokumen" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Kurikulum & SK</a>
    </div>
  </div>
</div>

<!-- ░░░ SAMBUTAN KAPRODI ░░░ -->
<section id="sambutan" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="grid grid-2" style="align-items: center; gap: 60px;">
      <div class="fade-up">
        <span class="text-red" style="font-weight: 700; text-transform: uppercase; font-size: 0.9rem;">Greetings</span>
        <h2 style="margin-top: 12px;">Sambutan Ketua Program Studi</h2>
        <p style="font-style: italic; font-size: 1.1rem; line-height: 1.8; color: var(--color-text); margin: 24px 0;">"Membangun kepemimpinan strategis adalah kunci kesuksesan organisasi. Program Magister kami dirancang untuk mengasah visi manajerial Anda di level tertinggi."</p>
        <div style="display: flex; flex-direction: column; gap: 4px;">
          <h4 style="margin: 0; color: var(--umiba-red);">Dr. Nurmansyah, MMSI</h4>
          <p style="margin: 0; font-size: 0.9rem; font-weight: 600;">Ketua Program Pascasarjana Manajemen</p>
        </div>
        <div style="margin-top: 32px;">
          <a href="https://pddikti.kemdikbud.go.id/" target="_blank" class="btn btn-glass"><i class="ph-bold ph-users-three"></i> Lihat Daftar Lengkap Dosen (PDDikti)</a>
        </div>
      </div>
      <div class="fade-up" style="position: relative;">
        <div class="glass" style="padding: 15px; border-radius: var(--radius-lg); transform: rotate(2deg);">
          <img src="https://umiba.ac.id/wp-content/uploads/2024/06/WhatsApp-Image-2024-05-30-at-120111-239x300.jpeg" alt="Dr. Nurmansyah, MMSI" style="width: 100%; border-radius: var(--radius-md); box-shadow: 0 15px 30px rgba(0,0,0,0.1); background: #eee;">
        </div>
        <div style="position: absolute; -z-index: 1; top: -20px; right: -20px; width: 100px; height: 100px; background: var(--umiba-red-alpha); border-radius: 50%; filter: blur(30px);"></div>
      </div>
    </div>
  </div>
</section>

<!-- ░░░ VISI & MISI ░░░ -->
<section id="visimisi" style="padding: var(--space-8) 0;">
  <div class="container grid grid-2" style="align-items: start; gap: 40px;">
    <div class="glass glass-card fade-up">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Arah Gerak Prodi</span>
      <h2>Visi S2 Magister Manajemen</h2>
      <p style="font-size: 1.1rem; line-height: 1.8; font-weight: 600; color: var(--color-text);">"Menjadi pusat pengembangan kepemimpinan bisnis tingkat tinggi dan riset strategis di Asia Tenggara pada tahun 2030."</p>
    </div>
    <div class="glass glass-card fade-up" style="transition-delay: 0.1s;">
      <h3 class="text-red">Misi S2 Magister Manajemen</h3>
      <ul style="padding-left: var(--space-4);">
        <li style="margin-bottom: 8px;">Mengembangkan kompetensi kepemimpinan strategis bagi para profesional.</li>
        <li style="margin-bottom: 8px;">Mendorong riset bisnis berdampak global yang aplikatif bagi industri.</li>
        <li style="margin-bottom: 8px;">Membangun jejaring profesional (networking) yang solid antar mahasiswa dan alumni.</li>
        <li style="margin-bottom: 8px;">Mengintegrasikan prinsip tata kelola perusahaan yang baik (Good Corporate Governance).</li>
      </ul>
    </div>
  </div>
</section>

<!-- ░░░ PROFIL LULUSAN & PEMINATAN ░░░ -->
<section id="profil" style="padding: var(--space-8) 0; background: rgba(185, 28, 28, 0.03);">
  <div class="container grid grid-2" style="gap: 40px;">
    
    <!-- Peminatan -->
    <div class="fade-up">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Fokus Keilmuan</span>
      <h2>Peminatan / Konsentrasi</h2>
      <p>Mahasiswa dapat memilih peminatan khusus di semester atas untuk memfokuskan kompetensi dan karir profesional mereka.</p>
      <div class="grid grid-2" style="gap: 16px; margin-top: 24px;">
        <div class="glass glass-card" style="padding: 16px; text-align: center;">
          <h4 style="margin: 0; color: var(--umiba-red); font-size: 1rem;">Strategic Management</h4>
        </div>
        <div class="glass glass-card" style="padding: 16px; text-align: center;">
          <h4 style="margin: 0; color: var(--umiba-red); font-size: 1rem;">Corporate Finance</h4>
        </div>
        <div class="glass glass-card" style="padding: 16px; text-align: center;">
          <h4 style="margin: 0; color: var(--umiba-red); font-size: 1rem;">Human Capital Development</h4>
        </div>
        <div class="glass glass-card" style="padding: 16px; text-align: center;">
          <h4 style="margin: 0; color: var(--umiba-red); font-size: 1rem;">Global Marketing</h4>
        </div>
      </div>
    </div>

    <!-- Profil Lulusan -->
    <div class="glass glass-card fade-up" style="transition-delay: 0.1s;">
      <h3 class="text-red">Prospek Karir & Profil Lulusan</h3>
      <p>Lulusan program studi ini dipersiapkan untuk menempati berbagai posisi strategis di industri, pemerintahan, dan korporasi:</p>
      <ul style="list-style: none; padding: 0; margin-top: 24px;">
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>C-Level Executive (CEO, CFO, CMO)</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Senior Business Consultant</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Corporate Strategist</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Academic Researcher / Dosen Ahli</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Entrepreneur Skala Besar</strong>
        </li>
      </ul>
    </div>
  </div>
</section>

<!-- ░░░ DOKUMEN ░░░ -->
<section id="dokumen" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="fade-up" style="text-align: center; margin-bottom: 48px;">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Unduhan Resmi</span>
      <h2>Dokumen {data[\'title\']}</h2>
    </div>
    <div class="grid grid-3" style="gap: 24px;">
      
      <!-- Kurikulum -->
      <div class="glass glass-card fade-up" style="display: flex; flex-direction: column; text-align: center;">
        <i class="ph-duotone ph-book-open" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h4 style="margin-bottom: 8px;">Buku Kurikulum</h4>
        <p style="font-size: 0.85rem; margin-bottom: 24px;">Pedoman sebaran mata kuliah (SKS) dari semester 1 hingga akhir kelulusan.</p>
        <a href="#" class="btn btn-glass" style="margin-top: auto; width: 100%;"><i class="ph-bold ph-download-simple"></i> Unduh PDF</a>
      </div>

      <!-- SK Pendirian -->
      <div class="glass glass-card fade-up" style="transition-delay: 0.1s; display: flex; flex-direction: column; text-align: center;">
        <i class="ph-duotone ph-certificate" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h4 style="margin-bottom: 8px;">SK Pendirian Prodi</h4>
        <p style="font-size: 0.85rem; margin-bottom: 24px;">Legalitas resmi pembukaan program studi dari Kemendikbudristek RI.</p>
        <a href="#" class="btn btn-glass" style="margin-top: auto; width: 100%;"><i class="ph-bold ph-download-simple"></i> Unduh SK</a>
      </div>

      <!-- Akreditasi -->
      <div class="glass glass-card fade-up" style="transition-delay: 0.2s; display: flex; flex-direction: column; text-align: center;">
        <i class="ph-duotone ph-medal" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h4 style="margin-bottom: 8px;">Sertifikat Akreditasi</h4>
        <p style="font-size: 0.85rem; margin-bottom: 24px;">Sertifikat Akreditasi resmi dari BAN-PT / LAM untuk program studi ini.</p>
        <a href="#" class="btn btn-glass" style="margin-top: auto; width: 100%;"><i class="ph-bold ph-download-simple"></i> Unduh Sertifikat</a>
      </div>

    </div>
  </div>
</section>'],
            ['key' => 'prodi-manajemen_hero_title', 'type' => 'text', 'value' => 'S1 Manajemen'],
            ['key' => 'prodi-manajemen_hero_bg', 'type' => 'image', 'value' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_2.png'],
            ['key' => 'prodi-manajemen_html', 'type' => 'html', 'value' => '<!-- ░░░ HERO SUBPAGE ░░░ -->


<!-- ░░░ TABS ░░░ -->
<div style="position: sticky; top: 100px; z-index: 900; margin-top: 24px; margin-bottom: 24px;">
  <div class="container">
    <div class="glass" style="padding: 12px; border-radius: var(--radius-full); display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
      <a href="#sambutan" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Sambutan Kaprodi</a>
      <a href="#visimisi" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Visi & Misi</a>
      <a href="#profil" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Profil & Karir</a>
      <a href="#dokumen" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Kurikulum & SK</a>
    </div>
  </div>
</div>

<!-- ░░░ SAMBUTAN KAPRODI ░░░ -->
<section id="sambutan" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="grid grid-2" style="align-items: center; gap: 60px;">
      <div class="fade-up">
        <span class="text-red" style="font-weight: 700; text-transform: uppercase; font-size: 0.9rem;">Greetings</span>
        <h2 style="margin-top: 12px;">Sambutan Ketua Program Studi</h2>
        <p style="font-style: italic; font-size: 1.1rem; line-height: 1.8; color: var(--color-text); margin: 24px 0;">"Selamat Datang di Program Studi S1 Manajemen Universitas Mitra Bangsa. Kami berkomitmen untuk menyelenggarakan pendidikan manajemen yang inovatif dan berbasis IT untuk mencetak pemimpin bisnis masa depan yang profesional dan berintegritas tinggi."</p>
        <div style="display: flex; flex-direction: column; gap: 4px;">
          <h4 style="margin: 0; color: var(--umiba-red);">Indri Astuti, S.Pd., M.M., M.Pd.</h4>
          <p style="margin: 0; font-size: 0.9rem; font-weight: 600;">Ketua Program Studi S1 Manajemen</p>
        </div>
        <div style="margin-top: 32px;">
          <a href="https://pddikti.kemdikbud.go.id/detail-prodi/9fZK8_NXkdvm0r2V2yqdiIE1dLA3eEmsaU0r77u4lGSuMvxpBdtQAM66xGCACDWbLIP35g==" target="_blank" class="btn btn-glass"><i class="ph-bold ph-users-three"></i> Lihat Daftar Lengkap Dosen (PDDikti)</a>
        </div>
      </div>
      <div class="fade-up" style="position: relative;">
        <div class="glass" style="padding: 15px; border-radius: var(--radius-lg); transform: rotate(2deg);">
          <img src="https://umiba.ac.id/wp-content/uploads/2024/05/indri_130.jpg" alt="Indri Astuti" style="width: 100%; border-radius: var(--radius-md); box-shadow: 0 15px 30px rgba(0,0,0,0.1);">
        </div>
        <!-- Decorative blob -->
        <div style="position: absolute; -z-index: 1; top: -20px; right: -20px; width: 100px; height: 100px; background: var(--umiba-red-alpha); border-radius: 50%; filter: blur(30px);"></div>
      </div>
    </div>
  </div>
</section>

<!-- ░░░ VISI & MISI ░░░ -->
<section id="visimisi" style="padding: var(--space-8) 0;">
  <div class="container grid grid-2" style="align-items: start; gap: 40px;">
    <div class="glass glass-card fade-up">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Arah Gerak Prodi</span>
      <h2>Visi S1 Manajemen</h2>
      <p style="font-size: 1.1rem; line-height: 1.8; font-weight: 600; color: var(--color-text);">"Mencetak Sumber Daya Manusia yang Berkompeten Berbasis Teknologi Informasi dan Bertaraf Internasional Tahun 2035"</p>
    </div>
    <div class="glass glass-card fade-up" style="transition-delay: 0.1s;">
      <h3 class="text-red">Misi S1 Manajemen</h3>
      <ul style="padding-left: var(--space-4);">
        <li style="margin-bottom: 8px;">Menyelenggarakan program pendidikan berbasis IT secara professional dan ilmiah.</li>
        <li style="margin-bottom: 8px;">Melaksanakan penelitian terapan yang menunjang pengembangan keahlian bisnis.</li>
        <li style="margin-bottom: 8px;">Menyebarkan hasil penelitian terapan secara nyata dan tepat guna.</li>
        <li style="margin-bottom: 8px;">Mengadakan kegiatan pengabdian pada masyarakat secara edukatif dan konsisten.</li>
        <li style="margin-bottom: 8px;">Mencetak SDM kompeten yang mampu menjalankan kegiatan bisnis professional.</li>
        <li style="margin-bottom: 8px;">Meningkatkan sarana dan prasarana untuk mendukung proses pembelajaran berkualitas.</li>
        <li style="margin-bottom: 8px;">Menjalin kerjasama dengan Institusi dan Dunia Industri (DUDI) skala nasional & internasional.</li>
      </ul>
    </div>
  </div>
</section>

<!-- ░░░ PROFIL LULUSAN & PEMINATAN ░░░ -->
<section id="profil" style="padding: var(--space-8) 0; background: rgba(185, 28, 28, 0.03);">
  <div class="container grid grid-2" style="gap: 40px;">
    
    <!-- Peminatan -->
    <div class="fade-up">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Fokus Keilmuan</span>
      <h2>Peminatan / Konsentrasi</h2>
      <p>Mahasiswa dapat memilih peminatan khusus di semester atas untuk memfokuskan kompetensi dan karir profesional mereka.</p>
      <div class="grid grid-2" style="gap: 16px; margin-top: 24px;">
        <div class="glass glass-card" style="padding: 16px; text-align: center;">
          <h4 style="margin: 0; color: var(--umiba-red); font-size: 1rem;">Manajemen SDM</h4>
        </div>
        <div class="glass glass-card" style="padding: 16px; text-align: center;">
          <h4 style="margin: 0; color: var(--umiba-red); font-size: 1rem;">Manajemen Keuangan</h4>
        </div>
        <div class="glass glass-card" style="padding: 16px; text-align: center;">
          <h4 style="margin: 0; color: var(--umiba-red); font-size: 1rem;">Manajemen Pemasaran</h4>
        </div>
        <div class="glass glass-card" style="padding: 16px; text-align: center;">
          <h4 style="margin: 0; color: var(--umiba-red); font-size: 1rem;">Manajemen Produksi</h4>
        </div>
      </div>
    </div>

    <!-- Profil Lulusan -->
    <div class="glass glass-card fade-up" style="transition-delay: 0.1s;">
      <h3 class="text-red">Prospek Karir & Profil Lulusan</h3>
      <p>Lulusan program studi ini dipersiapkan untuk menempati berbagai posisi strategis di industri, pemerintahan, dan korporasi:</p>
      <ul style="list-style: none; padding: 0; margin-top: 24px;">
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Manajer Bidang Bisnis (Keuangan, Pemasaran, SDM)</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Wirausaha Bidang Jasa & Manufaktur</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Praktisi Perbankan</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Aparatur Sipil Negara (ASN)</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Ilmuwan Bidang Manajemen Bisnis</strong>
        </li>
      </ul>
    </div>
  </div>
</section>

<!-- ░░░ DOKUMEN ░░░ -->
<section id="dokumen" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="fade-up" style="text-align: center; margin-bottom: 48px;">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Unduhan Resmi</span>
      <h2>Dokumen {data[\'title\']}</h2>
    </div>
    <div class="grid grid-3" style="gap: 24px;">
      
      <!-- Kurikulum -->
      <div class="glass glass-card fade-up" style="display: flex; flex-direction: column; text-align: center;">
        <i class="ph-duotone ph-book-open" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h4 style="margin-bottom: 8px;">Buku Kurikulum</h4>
        <p style="font-size: 0.85rem; margin-bottom: 24px;">Pedoman sebaran mata kuliah (SKS) dari semester 1 hingga akhir kelulusan.</p>
        <a href="#" class="btn btn-glass" style="margin-top: auto; width: 100%;"><i class="ph-bold ph-download-simple"></i> Unduh PDF</a>
      </div>

      <!-- SK Pendirian -->
      <div class="glass glass-card fade-up" style="transition-delay: 0.1s; display: flex; flex-direction: column; text-align: center;">
        <i class="ph-duotone ph-certificate" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h4 style="margin-bottom: 8px;">SK Pendirian Prodi</h4>
        <p style="font-size: 0.85rem; margin-bottom: 24px;">Legalitas resmi pembukaan program studi dari Kemendikbudristek RI.</p>
        <a href="#" class="btn btn-glass" style="margin-top: auto; width: 100%;"><i class="ph-bold ph-download-simple"></i> Unduh SK</a>
      </div>

      <!-- Akreditasi -->
      <div class="glass glass-card fade-up" style="transition-delay: 0.2s; display: flex; flex-direction: column; text-align: center;">
        <i class="ph-duotone ph-medal" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h4 style="margin-bottom: 8px;">Sertifikat Akreditasi</h4>
        <p style="font-size: 0.85rem; margin-bottom: 24px;">Sertifikat Akreditasi resmi dari BAN-PT / LAM untuk program studi ini.</p>
        <a href="#" class="btn btn-glass" style="margin-top: auto; width: 100%;"><i class="ph-bold ph-download-simple"></i> Unduh Sertifikat</a>
      </div>

    </div>
  </div>
</section>'],
            ['key' => 'prodi-sistem_hero_title', 'type' => 'text', 'value' => 'S1 Sistem Informasi'],
            ['key' => 'prodi-sistem_hero_bg', 'type' => 'image', 'value' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_3.png'],
            ['key' => 'prodi-sistem_html', 'type' => 'html', 'value' => '<!-- ░░░ HERO SUBPAGE ░░░ -->


<!-- ░░░ TABS ░░░ -->
<div style="position: sticky; top: 100px; z-index: 900; margin-top: 24px; margin-bottom: 24px;">
  <div class="container">
    <div class="glass" style="padding: 12px; border-radius: var(--radius-full); display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
      <a href="#sambutan" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Sambutan Kaprodi</a>
      <a href="#visimisi" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Visi & Misi</a>
      <a href="#profil" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Profil & Karir</a>
      <a href="#dokumen" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Kurikulum & SK</a>
    </div>
  </div>
</div>

<!-- ░░░ SAMBUTAN KAPRODI ░░░ -->
<section id="sambutan" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="grid grid-2" style="align-items: center; gap: 60px;">
      <div class="fade-up">
        <span class="text-red" style="font-weight: 700; text-transform: uppercase; font-size: 0.9rem;">Greetings</span>
        <h2 style="margin-top: 12px;">Sambutan Ketua Program Studi</h2>
        <p style="font-style: italic; font-size: 1.1rem; line-height: 1.8; color: var(--color-text); margin: 24px 0;">"Sistem Informasi adalah jembatan antara bisnis dan teknologi. Mari bersama kami membangun ekosistem digital yang efisien dan berdampak positif."</p>
        <div style="display: flex; flex-direction: column; gap: 4px;">
          <h4 style="margin: 0; color: var(--umiba-red);">Dosen Ahli UMIBA</h4>
          <p style="margin: 0; font-size: 0.9rem; font-weight: 600;">Ketua Program Studi S1 Sistem Informasi</p>
        </div>
        <div style="margin-top: 32px;">
          <a href="https://pddikti.kemdikbud.go.id/" target="_blank" class="btn btn-glass"><i class="ph-bold ph-users-three"></i> Lihat Daftar Lengkap Dosen (PDDikti)</a>
        </div>
      </div>
      <div class="fade-up" style="position: relative;">
        <div class="glass" style="padding: 15px; border-radius: var(--radius-lg); transform: rotate(2deg);">
          <img src="https://via.placeholder.com/400x500/B91C1C/fff?text=Kaprodi+Sistem+Informasi" alt="Dosen Ahli UMIBA" style="width: 100%; border-radius: var(--radius-md); box-shadow: 0 15px 30px rgba(0,0,0,0.1); background: #eee;">
        </div>
        <div style="position: absolute; -z-index: 1; top: -20px; right: -20px; width: 100px; height: 100px; background: var(--umiba-red-alpha); border-radius: 50%; filter: blur(30px);"></div>
      </div>
    </div>
  </div>
</section>

<!-- ░░░ VISI & MISI ░░░ -->
<section id="visimisi" style="padding: var(--space-8) 0;">
  <div class="container grid grid-2" style="align-items: start; gap: 40px;">
    <div class="glass glass-card fade-up">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Arah Gerak Prodi</span>
      <h2>Visi S1 Sistem Informasi</h2>
      <p style="font-size: 1.1rem; line-height: 1.8; font-weight: 600; color: var(--color-text);">"Menjadi program studi unggulan dalam pengembangan sistem informasi korporasi dan tata kelola IT berbasis kebangsaan."</p>
    </div>
    <div class="glass glass-card fade-up" style="transition-delay: 0.1s;">
      <h3 class="text-red">Misi S1 Sistem Informasi</h3>
      <ul style="padding-left: var(--space-4);">
        <li style="margin-bottom: 8px;">Mendidik mahasiswa menguasai arsitektur enterprise dan analisis sistem.</li>
        <li style="margin-bottom: 8px;">Membangun solusi digital yang tepat guna untuk UMKM dan perusahaan multinasional.</li>
        <li style="margin-bottom: 8px;">Menciptakan technopreneur handal yang inovatif dan berkarakter.</li>
        <li style="margin-bottom: 8px;">Melaksanakan riset tata kelola teknologi informasi (IT Governance).</li>
      </ul>
    </div>
  </div>
</section>

<!-- ░░░ PROFIL LULUSAN & PEMINATAN ░░░ -->
<section id="profil" style="padding: var(--space-8) 0; background: rgba(185, 28, 28, 0.03);">
  <div class="container grid grid-2" style="gap: 40px;">
    
    <!-- Peminatan -->
    <div class="fade-up">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Fokus Keilmuan</span>
      <h2>Peminatan / Konsentrasi</h2>
      <p>Mahasiswa dapat memilih peminatan khusus di semester atas untuk memfokuskan kompetensi dan karir profesional mereka.</p>
      <div class="grid grid-2" style="gap: 16px; margin-top: 24px;">
        <div class="glass glass-card" style="padding: 16px; text-align: center;">
          <h4 style="margin: 0; color: var(--umiba-red); font-size: 1rem;">Enterprise Resource Planning (ERP)</h4>
        </div>
        <div class="glass glass-card" style="padding: 16px; text-align: center;">
          <h4 style="margin: 0; color: var(--umiba-red); font-size: 1rem;">Data Science & Analytics</h4>
        </div>
        <div class="glass glass-card" style="padding: 16px; text-align: center;">
          <h4 style="margin: 0; color: var(--umiba-red); font-size: 1rem;">IT Governance & Audit</h4>
        </div>
      </div>
    </div>

    <!-- Profil Lulusan -->
    <div class="glass glass-card fade-up" style="transition-delay: 0.1s;">
      <h3 class="text-red">Prospek Karir & Profil Lulusan</h3>
      <p>Lulusan program studi ini dipersiapkan untuk menempati berbagai posisi strategis di industri, pemerintahan, dan korporasi:</p>
      <ul style="list-style: none; padding: 0; margin-top: 24px;">
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>System Analyst</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>IT Consultant</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Database Administrator</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>IT Project Manager</strong>
        </li>
        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
          <div style="color: var(--umiba-red);"><i class="ph-fill ph-check-circle"></i></div>
          <strong>Technopreneur</strong>
        </li>
      </ul>
    </div>
  </div>
</section>

<!-- ░░░ DOKUMEN ░░░ -->
<section id="dokumen" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="fade-up" style="text-align: center; margin-bottom: 48px;">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Unduhan Resmi</span>
      <h2>Dokumen {data[\'title\']}</h2>
    </div>
    <div class="grid grid-3" style="gap: 24px;">
      
      <!-- Kurikulum -->
      <div class="glass glass-card fade-up" style="display: flex; flex-direction: column; text-align: center;">
        <i class="ph-duotone ph-book-open" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h4 style="margin-bottom: 8px;">Buku Kurikulum</h4>
        <p style="font-size: 0.85rem; margin-bottom: 24px;">Pedoman sebaran mata kuliah (SKS) dari semester 1 hingga akhir kelulusan.</p>
        <a href="#" class="btn btn-glass" style="margin-top: auto; width: 100%;"><i class="ph-bold ph-download-simple"></i> Unduh PDF</a>
      </div>

      <!-- SK Pendirian -->
      <div class="glass glass-card fade-up" style="transition-delay: 0.1s; display: flex; flex-direction: column; text-align: center;">
        <i class="ph-duotone ph-certificate" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h4 style="margin-bottom: 8px;">SK Pendirian Prodi</h4>
        <p style="font-size: 0.85rem; margin-bottom: 24px;">Legalitas resmi pembukaan program studi dari Kemendikbudristek RI.</p>
        <a href="#" class="btn btn-glass" style="margin-top: auto; width: 100%;"><i class="ph-bold ph-download-simple"></i> Unduh SK</a>
      </div>

      <!-- Akreditasi -->
      <div class="glass glass-card fade-up" style="transition-delay: 0.2s; display: flex; flex-direction: column; text-align: center;">
        <i class="ph-duotone ph-medal" style="font-size: 3rem; color: var(--umiba-red); margin-bottom: 16px;"></i>
        <h4 style="margin-bottom: 8px;">Sertifikat Akreditasi</h4>
        <p style="font-size: 0.85rem; margin-bottom: 24px;">Sertifikat Akreditasi resmi dari BAN-PT / LAM untuk program studi ini.</p>
        <a href="#" class="btn btn-glass" style="margin-top: auto; width: 100%;"><i class="ph-bold ph-download-simple"></i> Unduh Sertifikat</a>
      </div>

    </div>
  </div>
</section>'],
            ['key' => 'profil_hero_title', 'type' => 'text', 'value' => '${heroTitle}'],
            ['key' => 'profil_hero_bg', 'type' => 'image', 'value' => '${heroBg}'],
            ['key' => 'profil_html', 'type' => 'html', 'value' => '<!-- ░░░ HERO SUBPAGE ░░░ -->


<!-- ░░░ NAVIGATION TABS ░░░ -->
<div style="position: sticky; top: 100px; z-index: 900; margin-top: 24px; margin-bottom: 24px;">
  <div class="container">
    <div class="glass" style="padding: 12px; border-radius: var(--radius-full); display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
      <a href="#sejarah" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Sejarah</a>
      <a href="#visi-misi" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Visi & Misi</a>
      <a href="#tujuan" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Tujuan</a>
      <a href="#sasaran" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Sasaran & Strategi</a>
      <a href="#struktur" class="btn btn-glass" style="padding: 10px 20px; font-size: 0.9rem;">Struktur Organisasi</a>
    </div>
  </div>
</div>

<!-- ░░░ SEJARAH ░░░ -->
<section id="sejarah" style="padding: var(--space-8) 0;">
  <div class="container">
    <div class="glass glass-card fade-up">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Perjalanan Kami</span>
      <h2>Sejarah UMIBA</h2>
      <p>Universitas Mitra Bangsa (UMIBA) didirikan dengan semangat untuk memberikan pendidikan berkualitas tinggi kepada generasi muda Indonesia. Sejak awal berdirinya, UMIBA terus berkembang dan bertransformasi menjadi salah satu perguruan tinggi unggulan di Jakarta yang fokus pada pengembangan ilmu pengetahuan, teknologi, dan karakter mahasiswa.</p>
      <p>Dengan fasilitas modern dan tenaga pengajar profesional, UMIBA berkomitmen untuk menghasilkan lulusan yang siap bersaing di dunia global.</p>
    </div>
  </div>
</section>

<!-- ░░░ VISI DAN MISI ░░░ -->
<section id="visi-misi" style="padding: var(--space-8) 0; background: rgba(255, 255, 255, 0.4);">
  <div class="container grid grid-2">
    <div class="glass glass-card fade-up">
      <h2 class="text-red">Visi</h2>
      <p>Menjadi Universitas yang unggul, berdaya saing global, dan berkarakter dalam pengembangan ilmu pengetahuan dan teknologi pada tahun 2030.</p>
    </div>
    <div class="glass glass-card fade-up" style="transition-delay: 0.1s;">
      <h2 class="text-red">Misi</h2>
      <ul style="padding-left: var(--space-4);">
        <li style="margin-bottom: 8px;">Menyelenggarakan pendidikan tinggi yang bermutu dan relevan dengan kebutuhan masyarakat dan industri.</li>
        <li style="margin-bottom: 8px;">Melaksanakan penelitian inovatif yang berkontribusi pada perkembangan IPTEK.</li>
        <li style="margin-bottom: 8px;">Melakukan pengabdian kepada masyarakat untuk meningkatkan kesejahteraan.</li>
        <li style="margin-bottom: 8px;">Membangun tata kelola universitas yang baik (Good University Governance).</li>
      </ul>
    </div>
  </div>
</section>

<!-- ░░░ TUJUAN ░░░ -->
<section id="tujuan" style="padding: var(--space-8) 0;">
  <div class="container grid grid-2" style="align-items: center;">
    <div class="fade-up">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Capaian Akademik</span>
      <h2>Tujuan Universitas</h2>
      <p>Tujuan Universitas Mitra Bangsa dirumuskan untuk menghasilkan lulusan yang unggul dan berdaya saing global:</p>
      <ul style="padding-left: var(--space-4); margin-top: var(--space-3);">
        <li style="margin-bottom: 8px;">Menghasilkan lulusan yang memiliki kompetensi akademik dan profesional yang tinggi.</li>
        <li style="margin-bottom: 8px;">Menghasilkan lulusan yang mampu beradaptasi dengan cepat terhadap perubahan teknologi dan tuntutan industri global.</li>
        <li style="margin-bottom: 8px;">Membentuk lulusan yang memiliki jiwa kewirausahaan (entrepreneurship) dan kepemimpinan yang berkarakter kebangsaan.</li>
        <li style="margin-bottom: 8px;">Menghasilkan karya riset dan publikasi yang bermanfaat bagi masyarakat.</li>
      </ul>
    </div>
    <div class="glass glass-card fade-up" style="transition-delay: 0.2s;" id="sasaran">
      <h3 class="text-red">Sasaran &amp; Strategi Pencapaian</h3>
      <p>Untuk mencapai tujuan tersebut, UMIBA menerapkan berbagai strategi yang terukur:</p>
      <ul style="padding-left: var(--space-4); margin-top: var(--space-3);">
        <li style="margin-bottom: 8px;">Pengembangan dan pemutakhiran kurikulum berbasis Kerangka Kualifikasi Nasional Indonesia (KKNI) secara berkala.</li>
        <li style="margin-bottom: 8px;">Peningkatan kualifikasi akademik dan sertifikasi kompetensi seluruh dosen pengajar.</li>
        <li style="margin-bottom: 8px;">Perluasan jaringan kerjasama institusional dan kemitraan dengan industri (DUDI).</li>
        <li style="margin-bottom: 8px;">Modernisasi sarana dan prasarana pembelajaran berbasis digital untuk mendukung perkuliahan hybrid.</li>
      </ul>
    </div>
  </div>
</section>

<!-- ░░░ STRUKTUR ORGANISASI ░░░ -->
<section id="struktur" style="padding: var(--space-8) 0; background: rgba(185, 28, 28, 0.03);">
  <div class="container">
    <div class="fade-up" style="text-align: center; margin-bottom: 60px;">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Manajemen Kampus</span>
      <h2>Struktur Organisasi UMIBA</h2>
      <p style="max-width: 700px; margin: 0 auto;">Dipimpin oleh jajaran akademisi dan profesional berpengalaman untuk mewujudkan visi universitas unggul.</p>
    </div>

    <!-- Pimpinan Universitas -->
    <div class="fade-up" style="margin-bottom: 48px;">
      <h3 style="text-align: center; margin-bottom: 32px; color: var(--umiba-red);">Pimpinan Universitas</h3>
      <div class="grid grid-2" style="gap: 24px; max-width: 1000px; margin: 0 auto; align-items: stretch;">
        <!-- Rektor -->
        <div class="glass glass-card" style="text-align: center; border-top: 4px solid var(--umiba-red); display: flex; flex-direction: column; justify-content: center; padding: 24px;">
          <h4 style="font-size: 1.2rem; margin-bottom: 4px;">Sri Wahyuningsih, SE., MM</h4>
          <p style="font-weight: 700; text-transform: uppercase; font-size: 0.8rem; color: var(--color-muted); margin: 0;">Rektor</p>
        </div>
        <!-- WR 1 -->
        <div class="glass glass-card" style="text-align: center; display: flex; flex-direction: column; justify-content: center; padding: 24px;">
          <h4 style="font-size: 1.1rem; margin-bottom: 4px;">Indi Nervilia, BIBM, MBA</h4>
          <p style="font-weight: 700; text-transform: uppercase; font-size: 0.8rem; color: var(--umiba-red); margin: 0;">Wakil Rektor I (Akademik)</p>
        </div>
        <!-- WR 2 -->
        <div class="glass glass-card" style="text-align: center; display: flex; flex-direction: column; justify-content: center; padding: 24px;">
          <h4 style="font-size: 1.1rem; margin-bottom: 4px;">Hadi Mulyo Wibowo, SH, MM</h4>
          <p style="font-weight: 700; text-transform: uppercase; font-size: 0.8rem; color: var(--umiba-red); margin: 0;">Wakil Rektor II (Keuangan & SDM)</p>
        </div>
        <!-- WR 3 -->
        <div class="glass glass-card" style="text-align: center; display: flex; flex-direction: column; justify-content: center; padding: 24px;">
          <h4 style="font-size: 1.1rem; margin-bottom: 4px;">Dr. Drs. Yuni Pratikno, SE, MM, MH</h4>
          <p style="font-weight: 700; text-transform: uppercase; font-size: 0.8rem; color: var(--umiba-red); margin: 0;">Wakil Rektor III (Kemahasiswaan & Kerjasama)</p>
        </div>
      </div>
    </div>

    <!-- Dekan & Lembaga -->
    <div class="grid grid-2 fade-up" style="gap: 40px; margin-top: 60px;">
      <div>
        <h3 style="margin-bottom: 24px; font-size: 1.4rem;"><i class="ph-bold ph-graduation-cap"></i> Jajaran Dekanat</h3>
        <ul style="list-style: none; padding: 0;">
          <li class="glass glass-card" style="margin-bottom: 12px; padding: 16px;">
            <strong>Prof. Dr. Harries Madiistriyatno, S. Hum, M.Si</strong>
            <p style="margin: 0; font-size: 0.85rem; color: var(--umiba-red);">Dekan Fak. Manajemen dan Bisnis</p>
          </li>
          <li class="glass glass-card" style="margin-bottom: 12px; padding: 16px;">
            <strong>Drs. Nurmansyah, MMSI</strong>
            <p style="margin: 0; font-size: 0.85rem; color: var(--umiba-red);">Dekan Fak. Teknologi Informasi & Aktuaria</p>
          </li>
          <li class="glass glass-card" style="padding: 16px;">
            <strong>Kamilov Sagala, S.H., M.H</strong>
            <p style="margin: 0; font-size: 0.85rem; color: var(--umiba-red);">Dekan Fakultas Hukum</p>
          </li>
        </ul>
      </div>
      <div>
        <h3 style="margin-bottom: 24px; font-size: 1.4rem;"><i class="ph-bold ph-briefcase"></i> Kepala Lembaga</h3>
        <ul style="list-style: none; padding: 0;">
          <li class="glass glass-card" style="margin-bottom: 12px; padding: 16px;">
            <strong>Ir. Aswin Naldi Sahim, MM, Ph.D</strong>
            <p style="margin: 0; font-size: 0.85rem; color: var(--umiba-red);">Kepala LPMI (Penjaminan Mutu)</p>
          </li>
          <li class="glass glass-card" style="padding: 16px;">
            <strong>Dr. Nurwulan Kusuma Devi, MM</strong>
            <p style="margin: 0; font-size: 0.85rem; color: var(--umiba-red);">Kepala LPPM (Riset & Pengabdian)</p>
          </li>
        </ul>
        <div style="margin-top: 32px; text-align: right;">
          <a href="#" class="btn btn-primary">Lihat Bagan Lengkap (PDF)</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ░░░ FASILITAS ░░░ -->
<section id="fasilitas" style="padding: var(--space-8) 0; background: rgba(255, 255, 255, 0.4);">
  <div class="container">
    <div style="text-align: center; margin-bottom: var(--space-6);" class="fade-up">
      <span class="text-red" style="font-weight: 600; text-transform: uppercase;">Lingkungan Kampus</span>
      <h2>Fasilitas UMIBA</h2>
    </div>
    <div class="grid grid-3">
      <div class="glass glass-card fade-up">
        <h3>Ruang Kelas Modern</h3>
        <p>Dilengkapi dengan AC, proyektor LCD, dan Wi-Fi berkecepatan tinggi untuk mendukung proses belajar mengajar yang nyaman.</p>
      </div>
      <div class="glass glass-card fade-up" style="transition-delay: 0.1s;">
        <h3>Laboratorium</h3>
        <p>Laboratorium komputer mutakhir untuk Fakultas TI dan fasilitas praktik peradilan semu untuk Fakultas Hukum.</p>
      </div>
      <div class="glass glass-card fade-up" style="transition-delay: 0.2s;">
        <h3>Perpustakaan &amp; E-Library</h3>
        <p>Koleksi buku lengkap, jurnal internasional, dan area baca yang representatif.</p>
      </div>
    </div>
  </div>
</section>'],
        ]);

        \App\Models\News::insert([
            [
                'title' => 'Penerimaan Mahasiswa Baru Semester Gasal 2025/2026 Resmi Dibuka',
                'date' => '8 Juni 2026',
                'image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_1.png',
                'source' => 'kompaskampus.id'
            ],
            [
                'title' => 'Seminar Nasional Teknologi Informasi & Aktuaria 2025',
                'date' => '5 Juni 2026',
                'image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_2.png',
                'source' => 'wartaekonomi.co.id'
            ],
            [
                'title' => 'Mahasiswa UMIBA Raih Juara 1 Kompetisi Nasional 2025',
                'date' => '1 Juni 2026',
                'image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_3.png',
                'source' => 'teropongsenayan.com'
            ],
            [
                'title' => 'Universitas Mitra Bangsa Selenggarakan Gebyar Kemerdekaan HUT-RI Ke-80',
                'date' => '17 Agustus 2025',
                'image_url' => 'https://umiba.ac.id/wp-content/uploads/2025/08/umiba-upacara.jpg',
                'source' => 'newsdetik.co'
            ],
            [
                'title' => 'BEM UMIBA Desak Pemkot Jakarta Selatan Atasi Penumpukan Sampah',
                'date' => '20 Juli 2025',
                'image_url' => 'https://umiba.ac.id/wp-content/uploads/2025/07/pilarparlemen.jpg',
                'source' => 'pilarparlemen.id'
            ],
            [
                'title' => 'Kampus UMIBA Terima Kunjungan Kehormatan DPR RI',
                'date' => '15 Mei 2026',
                'image_url' => 'https://umiba.ac.id/wp-content/uploads/2026/05/audensiUMIBA-300x158.webp',
                'source' => 'kompaskampus.id'
            ],
            [
                'title' => 'Rapat Kerja Tahunan Pimpinan Universitas',
                'date' => '25 Mei 2026',
                'image_url' => 'https://umiba.ac.id/wp-content/uploads/2026/05/rektor-UMIBA-2026.jpeg',
                'source' => 'internal'
            ],
            [
                'title' => 'Peresmian Fasilitas Laboratorium Baru',
                'date' => '10 Mei 2026',
                'image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/1-4.png',
                'source' => 'internal'
            ]
        ]);

        \App\Models\Testimonial::insert([
            ['image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/1-1.png'],
            ['image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/2-1.png'],
            ['image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/3-1.png'],
            ['image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/1-2.png'],
            ['image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/2-2.png'],
            ['image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/3-2.png'],
        ]);
    }
}
