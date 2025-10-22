<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'pertanyaan' => 'Apa saja bahaya utama merokok bagi kesehatan?',
                'jawaban'    => 'Merokok meningkatkan risiko penyakit jantung, stroke, kanker (paru, mulut, tenggorokan), PPOK, dan gangguan kesuburan. Asap rokok juga merusak pembuluh darah dan paru-paru.'
            ],
            [
                'pertanyaan' => 'Apakah rokok elektrik (vape) lebih aman?',
                'jawaban'    => 'Tidak ada rokok yang aman. Vape tetap mengandung nikotin dan bahan kimia yang dapat merusak paru-paru serta menimbulkan kecanduan. Vape bukan metode berhenti merokok yang direkomendasikan kecuali dalam pengawasan medis.'
            ],
            [
                'pertanyaan' => 'Berapa lama sampai tubuh pulih setelah berhenti merokok?',
                'jawaban'    => 'Dalam 20 menit tekanan darah dan detak jantung membaik, 2–12 minggu sirkulasi darah meningkat, 1 tahun risiko penyakit jantung turun ~50%, dan 10 tahun risiko kanker paru turun signifikan dibanding perokok aktif.'
            ],
            [
                'pertanyaan' => 'Apa itu perokok pasif dan mengapa berbahaya?',
                'jawaban'    => 'Perokok pasif adalah orang yang menghirup asap rokok orang lain. Mereka berisiko penyakit jantung, asma, infeksi saluran napas, hingga SIDS pada bayi. Tidak ada batas aman paparan asap rokok.'
            ],
            [
                'pertanyaan' => 'Apakah merokok memengaruhi belajar dan prestasi sekolah?',
                'jawaban'    => 'Nikotin mengganggu konsentrasi, kualitas tidur, dan memicu kecemasan. Hal ini dapat menurunkan fokus, daya ingat, dan prestasi belajar.'
            ],
            [
                'pertanyaan' => 'Benarkah merokok membantu mengurangi stres?',
                'jawaban'    => 'Rasa “tenang” setelah merokok sebenarnya meredakan gejala putus nikotin sementara. Dalam jangka panjang, nikotin justru meningkatkan stres dan kecemasan.'
            ],
            [
                'pertanyaan' => 'Apa tanda-tanda kecanduan nikotin?',
                'jawaban'    => 'Sulit berhenti, gelisah saat tidak merokok, butuh rokok segera setelah bangun, dan meningkatnya frekuensi merokok. Gejala putus nikotin meliputi mudah marah, sulit konsentrasi, dan nafsu makan meningkat.'
            ],
            [
                'pertanyaan' => 'Bagaimana cara efektif berhenti merokok?',
                'jawaban'    => 'Tentukan tanggal berhenti, kenali pemicu, gunakan dukungan sosial/komunitas, konsultasi tenaga kesehatan, pertimbangkan terapi pengganti nikotin (permen karet/patch) sesuai anjuran, dan kelola stres dengan olahraga/relaksasi.'
            ],
            [
                'pertanyaan' => 'Apakah 1–2 batang per hari masih berbahaya?',
                'jawaban'    => 'Ya. Bahkan jumlah kecil tetap meningkatkan risiko penyakit jantung dan stroke. Risiko tidak linier—sedikit tetap berbahaya.'
            ],
            [
                'pertanyaan' => 'Apa dampak merokok pada penampilan?',
                'jawaban'    => 'Mempercepat penuaan kulit, menimbulkan bau mulut, gigi menguning, dan memperlambat penyembuhan luka.'
            ],
            [
                'pertanyaan' => 'Bagaimana melindungi keluarga dari asap rokok?',
                'jawaban'    => 'Terapkan rumah dan kendaraan bebas asap rokok, hindari merokok di dekat anak dan ibu hamil, serta dukung anggota keluarga untuk berhenti.'
            ],
            [
                'pertanyaan' => 'Apakah merokok memengaruhi keuangan?',
                'jawaban'    => 'Biaya rokok harian menumpuk menjadi beban bulanan/tahunan yang besar, ditambah biaya kesehatan akibat penyakit terkait rokok.'
            ],
        ];

        foreach ($items as $i) {
            Faq::firstOrCreate(
                ['pertanyaan' => $i['pertanyaan']],
                ['jawaban' => $i['jawaban']]
            );
        }
    }
}
