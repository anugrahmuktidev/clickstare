<?php

namespace App\Filament\Resources\ParticipantValidations\Pages;

use App\Filament\Resources\ParticipantValidations\ParticipantValidationResource;
use App\Models\SchoolClass;
use App\Models\Sekolah;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class ListParticipantValidations extends ListRecords
{
    protected static string $resource = ParticipantValidationResource::class;

    #[Url(as: 'sekolah')]
    public ?int $selectedSekolahId = null;

    #[Url(as: 'kelas')]
    public ?int $selectedClassId = null;

    public function mount(): void
    {
        parent::mount();

        if (! $this->selectedClassId) {
            $this->mountAction('pilihKelas');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pilihKelas')
                ->label($this->selectedClassId ? 'Ganti Sekolah / Kelas' : 'Pilih Sekolah / Kelas')
                ->icon('heroicon-o-funnel')
                ->modalHeading('Pilih Sekolah dan Kelas')
                ->modalSubmitActionLabel('Tampilkan Peserta')
                ->modalCancelAction(false)
                ->closeModalByClickingAway(false)
                ->fillForm(fn (): array => [
                    'sekolah_id' => $this->selectedSekolahId,
                    'school_class_id' => $this->selectedClassId,
                ])
                ->schema([
                    Select::make('sekolah_id')
                        ->label('Sekolah')
                        ->options(fn () => Sekolah::query()->orderBy('nama')->pluck('nama', 'id')->all())
                        ->searchable()
                        ->preload()
                        ->live()
                        ->required()
                        ->afterStateUpdated(fn (callable $set) => $set('school_class_id', null)),
                    Select::make('school_class_id')
                        ->label('Kelas')
                        ->options(fn (callable $get) => filled($get('sekolah_id'))
                            ? SchoolClass::query()
                                ->where('sekolah_id', $get('sekolah_id'))
                                ->orderBy('nama')
                                ->pluck('nama', 'id')
                                ->all()
                            : [])
                        ->searchable()
                        ->preload()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $class = SchoolClass::query()
                        ->whereKey($data['school_class_id'])
                        ->where('sekolah_id', $data['sekolah_id'])
                        ->firstOrFail();

                    $this->selectedSekolahId = (int) $data['sekolah_id'];
                    $this->selectedClassId = $class->id;
                    $this->resetTable();
                }),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        if (! $this->selectedClassId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('user', function (Builder $query): void {
            $query
                ->where('role', 'siswa')
                ->where('sekolah_id', $this->selectedSekolahId)
                ->where('school_class_id', $this->selectedClassId);
        });
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }
}
