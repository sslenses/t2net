<?php

namespace App\Filament\Resources\TugasResource\Pages;

use App\Filament\Resources\TugasResource;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TugasResource\Widgets\TugasStats;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;

class ListTugas extends ListRecords
{
    protected static string $resource = TugasResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            TugasStats::class,
        ];
    }

    protected function getHeaderActions(): array
{
    return [
        CreateAction::make()
            ->label('Tambah Tugas')
            ->modalHeading('Tambah Tugas Baru')
            ->modalSubmitActionLabel('Simpan')
            ->icon('heroicon-o-plus-circle')
            ->color('primary')
    ];
}


}
