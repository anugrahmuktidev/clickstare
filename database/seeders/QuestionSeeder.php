<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Option;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // PRETEST (10 soal)
        // =========================
        $pre = [
            [
                'nomor' => 1,
                'teks'  => 'Apa yang dimaksud dengan rokok?',
                'opsi'  => [
                    ['teks' => 'Alat musik tradisional', 'benar' => false],
                    ['teks' => 'Benda yang dibakar dan dihisap, mengandung nikotin', 'benar' => true],
                    ['teks' => 'Makanan ringan', 'benar' => false],
                    ['teks' => 'Obat flu', 'benar' => false],
                ],
            ],
            [
                'nomor' => 2,
                'teks'  => 'Zat apa pada rokok yang menyebabkan kecanduan?',
                'opsi'  => [
                    ['teks' => 'Vitamin C', 'benar' => false],
                    ['teks' => 'Nikotin', 'benar' => true],
                    ['teks' => 'Kalsium', 'benar' => false],
                    ['teks' => 'Serat', 'benar' => false],
                ],
            ],
            [
                'nomor' => 3,
                'teks'  => 'Asap rokok yang dihirup orang di sekitar perokok disebut…',
                'opsi'  => [
                    ['teks' => 'Udara segar', 'benar' => false],
                    ['teks' => 'Asap pabrik', 'benar' => false],
                    ['teks' => 'Asap rokok pasif (secondhand smoke)', 'benar' => true],
                    ['teks' => 'Aroma terapi', 'benar' => false],
                ],
            ],
            [
                'nomor' => 4,
                'teks'  => 'Bahaya jangka panjang merokok bagi paru-paru adalah…',
                'opsi'  => [
                    ['teks' => 'Asma langsung sembuh', 'benar' => false],
                    ['teks' => 'Kanker paru dan PPOK', 'benar' => true],
                    ['teks' => 'Paru-paru lebih bersih', 'benar' => false],
                    ['teks' => 'Daya tahan meningkat', 'benar' => false],
                ],
            ],
            [
                'nomor' => 5,
                'teks'  => 'Merokok meningkatkan risiko penyakit…',
                'opsi'  => [
                    ['teks' => 'Jantung koroner dan stroke', 'benar' => true],
                    ['teks' => 'Cacar air', 'benar' => false],
                    ['teks' => 'Radang tenggorokan ringan saja', 'benar' => false],
                    ['teks' => 'Pilek musiman', 'benar' => false],
                ],
            ],
            [
                'nomor' => 6,
                'teks'  => 'Yang bukan alasan orang mulai merokok adalah…',
                'opsi'  => [
                    ['teks' => 'Tekanan teman sebaya', 'benar' => false],
                    ['teks' => 'Rasa penasaran', 'benar' => false],
                    ['teks' => 'Ingin terlihat keren', 'benar' => false],
                    ['teks' => 'Anjuran dokter', 'benar' => true],
                ],
            ],
            [
                'nomor' => 7,
                'teks'  => 'Rokok elektronik (vape) aman untuk remaja. Pernyataan ini…',
                'opsi'  => [
                    ['teks' => 'Benar sepenuhnya', 'benar' => false],
                    ['teks' => 'Salah, tetap berisiko adiksi nikotin', 'benar' => true],
                    ['teks' => 'Benar jika rasanya buah', 'benar' => false],
                    ['teks' => 'Benar jika dipakai di luar ruangan', 'benar' => false],
                ],
            ],
            [
                'nomor' => 8,
                'teks'  => 'Istilah “thirdhand smoke” merujuk pada…',
                'opsi'  => [
                    ['teks' => 'Asap di udara terbuka', 'benar' => false],
                    ['teks' => 'Residu asap menempel di baju/permukaan', 'benar' => true],
                    ['teks' => 'Asap dari dapur', 'benar' => false],
                    ['teks' => 'Asap kendaraan bermotor', 'benar' => false],
                ],
            ],
            [
                'nomor' => 9,
                'teks'  => 'Kandungan kimia berbahaya lain pada rokok selain nikotin adalah…',
                'opsi'  => [
                    ['teks' => 'Air mineral', 'benar' => false],
                    ['teks' => 'Tar dan karbon monoksida', 'benar' => true],
                    ['teks' => 'Protein', 'benar' => false],
                    ['teks' => 'Serat pangan', 'benar' => false],
                ],
            ],
            [
                'nomor' => 10,
                'teks'  => 'Cara paling efektif mencegah dampak rokok bagi remaja adalah…',
                'opsi'  => [
                    ['teks' => 'Mencoba sedikit saja', 'benar' => false],
                    ['teks' => 'Hanya merokok di luar rumah', 'benar' => false],
                    ['teks' => 'Tidak memulai sama sekali (say no)', 'benar' => true],
                    ['teks' => 'Merokok saat liburan', 'benar' => false],
                ],
            ],
        ];

        foreach ($pre as $q) {
            $question = Question::create([
                'tipe'  => 'pre',
                'nomor' => $q['nomor'],
                'teks'  => $q['teks'],
            ]);
            foreach ($q['opsi'] as $opt) {
                $question->options()->create($opt);
            }
        }

        // =========================
        // POSTTEST (10 soal)
        // =========================
        $post = [
            [
                'nomor' => 1,
                'teks'  => 'Mengapa berhenti merokok bermanfaat bagi jantung?',
                'opsi'  => [
                    ['teks' => 'Karena menambah kolesterol', 'benar' => false],
                    ['teks' => 'Mengurangi risiko penyakit jantung dan stroke', 'benar' => true],
                    ['teks' => 'Menyempitkan pembuluh darah', 'benar' => false],
                    ['teks' => 'Meningkatkan tekanan darah', 'benar' => false],
                ],
            ],
            [
                'nomor' => 2,
                'teks'  => 'Strategi efektif berhenti merokok adalah…',
                'opsi'  => [
                    ['teks' => 'Menghindari pemicu dan minta dukungan', 'benar' => true],
                    ['teks' => 'Menyimpan rokok di saku', 'benar' => false],
                    ['teks' => 'Mengganti dengan permen setiap saat', 'benar' => false],
                    ['teks' => 'Membeli rokok dengan merk baru', 'benar' => false],
                ],
            ],
            [
                'nomor' => 3,
                'teks'  => 'Paparan asap rokok pada anak dapat menyebabkan…',
                'opsi'  => [
                    ['teks' => 'Pertumbuhan lebih cepat', 'benar' => false],
                    ['teks' => 'Infeksi saluran napas dan asma', 'benar' => true],
                    ['teks' => 'Daya tahan meningkat', 'benar' => false],
                    ['teks' => 'Mata lebih tajam', 'benar' => false],
                ],
            ],
            [
                'nomor' => 4,
                'teks'  => 'Perbedaan pretest dan posttest dalam satu program edukasi adalah…',
                'opsi'  => [
                    ['teks' => 'Pretest menilai setelah belajar; posttest sebelum', 'benar' => false],
                    ['teks' => 'Keduanya untuk hiburan', 'benar' => false],
                    ['teks' => 'Pretest sebelum edukasi, posttest sesudah edukasi', 'benar' => true],
                    ['teks' => 'Keduanya sama persis', 'benar' => false],
                ],
            ],
            [
                'nomor' => 5,
                'teks'  => 'Manfaat lingkungan “Zona Tanpa Rokok” adalah…',
                'opsi'  => [
                    ['teks' => 'Mendorong perokok aktif di dalam ruangan', 'benar' => false],
                    ['teks' => 'Melindungi non-perokok dari paparan asap', 'benar' => true],
                    ['teks' => 'Menaikkan kadar tar', 'benar' => false],
                    ['teks' => 'Membuat udara lebih berasap', 'benar' => false],
                ],
            ],
            [
                'nomor' => 6,
                'teks'  => 'Rokok “light” atau low tar…',
                'opsi'  => [
                    ['teks' => 'Terbukti aman', 'benar' => false],
                    ['teks' => 'Tetap berbahaya bagi kesehatan', 'benar' => true],
                    ['teks' => 'Tidak mengandung nikotin', 'benar' => false],
                    ['teks' => 'Bisa dikonsumsi anak-anak', 'benar' => false],
                ],
            ],
            [
                'nomor' => 7,
                'teks'  => 'Contoh dukungan sosial untuk berhenti merokok adalah…',
                'opsi'  => [
                    ['teks' => 'Teman menyediakan rokok gratis', 'benar' => false],
                    ['teks' => 'Keluarga mengingatkan dan menemani', 'benar' => true],
                    ['teks' => 'Guru memberi izin merokok di kelas', 'benar' => false],
                    ['teks' => 'Membuat tantangan merokok bersama', 'benar' => false],
                ],
            ],
            [
                'nomor' => 8,
                'teks'  => 'Thirdhand smoke paling baik dikurangi dengan…',
                'opsi'  => [
                    ['teks' => 'Membiarkan jendela tertutup', 'benar' => false],
                    ['teks' => 'Mencuci baju/permukaan yang terpapar asap', 'benar' => true],
                    ['teks' => 'Menyemprot pewangi ruangan saja', 'benar' => false],
                    ['teks' => 'Menghirup lebih dalam', 'benar' => false],
                ],
            ],
            [
                'nomor' => 9,
                'teks'  => 'Komponen asap rokok yang mengikat hemoglobin dan mengurangi oksigen adalah…',
                'opsi'  => [
                    ['teks' => 'Karbon monoksida', 'benar' => true],
                    ['teks' => 'Oksigen murni', 'benar' => false],
                    ['teks' => 'Karbon dioksida biasa', 'benar' => false],
                    ['teks' => 'Nitrogen', 'benar' => false],
                ],
            ],
            [
                'nomor' => 10,
                'teks'  => 'Langkah pertama yang baik saat bertekad berhenti merokok adalah…',
                'opsi'  => [
                    ['teks' => 'Menentukan tanggal berhenti & menyingkirkan rokok', 'benar' => true],
                    ['teks' => 'Membeli stok rokok lebih banyak', 'benar' => false],
                    ['teks' => 'Merokok hanya setengah batang', 'benar' => false],
                    ['teks' => 'Mengganti ke merk lebih mahal', 'benar' => false],
                ],
            ],
        ];

        foreach ($post as $q) {
            $question = Question::create([
                'tipe'  => 'post',
                'nomor' => $q['nomor'],
                'teks'  => $q['teks'],
            ]);
            foreach ($q['opsi'] as $opt) {
                $question->options()->create($opt);
            }
        }
    }
}
