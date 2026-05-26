<?php

namespace Database\Seeders;

use App\Models\KnowledgeQuestion;
use Illuminate\Database\Seeder;

class KnowledgeQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            'Merokok aktif meningkatkan risiko penyakit jantung dan stroke.',
            'Nikotin pada rokok dapat menyebabkan ketergantungan.',
            'Paparan asap rokok pasif berbahaya bagi anak-anak.',
            'Rokok elektronik (vape) tetap berisiko bagi kesehatan remaja.',
            'Thirdhand smoke dapat menempel pada pakaian dan permukaan benda.',
            'Menghindari lingkungan perokok dapat menurunkan paparan asap rokok.',
            'Dukungan keluarga membantu seseorang berhenti merokok.',
            'Lingkungan bebas rokok melindungi non-perokok dari paparan asap.',
            'Berhenti merokok memberikan manfaat kesehatan sejak dini.',
            'Edukasi tentang bahaya rokok penting untuk pencegahan pada remaja.',
        ];

        foreach ($questions as $index => $text) {
            KnowledgeQuestion::firstOrCreate(
                ['teks' => $text],
                [
                    'sort_order' => $index + 1,
                    'is_active'  => true,
                ]
            );
        }
    }
}
