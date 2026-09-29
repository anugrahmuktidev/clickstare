<?php

namespace App\Filament\Resources\Videos\Pages;

use App\Filament\Resources\Videos\VideoResource;
use App\Models\Video;
use Filament\Resources\Pages\CreateRecord;

class CreateVideo extends CreateRecord
{
    protected static string $resource = VideoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! empty($data['existing_video_path'])) {
            $data['path'] = $data['existing_video_path'];
        }

        unset($data['existing_video_path']);

        return $data;
    }
}
