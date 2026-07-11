<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'judul' => 'Rokok Elektrik Tetap Mengandung Zat Berbahaya',
                'konten' => 'Rokok elektrik bukan uap air biasa. Aerosol dari rokok elektrik dapat mengandung nikotin dan zat beracun lain yang berbahaya bagi pengguna maupun orang di sekitarnya. Karena itu, pilihan paling aman tetap tidak menggunakan rokok elektrik maupun rokok konvensional.' . "\n\n" .
                    'Sumber:' . "\n" .
                    'WHO - Tobacco: E-cigarettes' . "\n" .
                    'https://www.who.int/news-room/questions-and-answers/item/tobacco-e-cigarettes',
                'gambar_path' => null,
            ],
            [
                'judul' => 'Nikotin Bisa Mengganggu Perkembangan Otak Remaja',
                'konten' => 'Paparan nikotin pada anak dan remaja dapat berdampak negatif pada perkembangan otak. Dampaknya dapat berhubungan dengan proses belajar, kontrol emosi, dan risiko kecanduan di kemudian hari. Karena itu, penggunaan rokok elektrik pada usia sekolah bukan hal sepele.' . "\n\n" .
                    'Sumber:' . "\n" .
                    'WHO - Tobacco: E-cigarettes' . "\n" .
                    'https://www.who.int/news-room/questions-and-answers/item/tobacco-e-cigarettes',
                'gambar_path' => null,
            ],
            [
                'judul' => 'Rokok Elektrik Dapat Mendorong Remaja Mencoba Rokok Biasa',
                'konten' => 'Bukti yang dirangkum WHO menunjukkan penggunaan rokok elektrik meningkatkan kemungkinan remaja non-perokok untuk mulai menggunakan rokok konvensional. Artinya, rokok elektrik bukan pintu keluar yang aman, tetapi bisa menjadi pintu masuk ke kebiasaan merokok.' . "\n\n" .
                    'Sumber:' . "\n" .
                    'WHO - Tobacco: E-cigarettes' . "\n" .
                    'https://www.who.int/news-room/questions-and-answers/item/tobacco-e-cigarettes',
                'gambar_path' => null,
            ],
            [
                'judul' => 'Produk yang Dibilang Tanpa Nikotin Belum Tentu Benar-Benar Aman',
                'konten' => 'CDC menjelaskan bahwa isi rokok elektrik sering sulit dipastikan. Beberapa produk yang dipasarkan seolah tanpa nikotin ternyata ditemukan tetap mengandung nikotin. Ini membuat remaja bisa terpapar zat adiktif tanpa sadar.' . "\n\n" .
                    'Sumber:' . "\n" .
                    'CDC - About E-Cigarettes (Vapes)' . "\n" .
                    'https://www.cdc.gov/tobacco/e-cigarettes/about.html',
                'gambar_path' => null,
            ],
            [
                'judul' => 'Cairan dan Alat Rokok Elektrik Juga Punya Risiko Cedera',
                'konten' => 'WHO menyebut rokok elektrik tidak hanya menimbulkan risiko kecanduan dan gangguan kesehatan, tetapi juga dapat menyebabkan cedera fisik, misalnya luka bakar akibat ledakan atau kerusakan alat. Cairan rokok elektrik yang bocor atau tertelan anak juga berbahaya.' . "\n\n" .
                    'Sumber:' . "\n" .
                    'WHO - Tobacco: E-cigarettes' . "\n" .
                    'https://www.who.int/news-room/questions-and-answers/item/tobacco-e-cigarettes',
                'gambar_path' => null,
            ],
        ];

        foreach ($posts as $post) {
            Post::updateOrCreate(
                ['judul' => $post['judul']],
                $post
            );
        }
    }
}
