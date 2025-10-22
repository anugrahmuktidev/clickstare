<?php
// app/Console/Commands/RenumberQuestions.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Question;

class RenumberQuestions extends Command
{
    protected $signature = 'questions:renumber';
    protected $description = 'Renomori ulang kolom nomor pada questions';

    public function handle(): int
    {
        $i = 1;
        Question::orderBy('nomor')->get()->each(function ($q) use (&$i) {
            $q->update(['nomor' => $i++]);
        });
        $this->info('Berhasil menomori ulang.');
        return self::SUCCESS;
    }
}
