<?php

namespace App\Filament\Resources\ForumLikeResource\Pages;

use App\Filament\Resources\ForumLikeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditForumLike extends EditRecord
{
    protected static string $resource = ForumLikeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
