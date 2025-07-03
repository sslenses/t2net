<?php
namespace App\Filament\Resources;

use App\Filament\Resources\TugasResource\Pages;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\SlideOver;

class TugasResource extends Resource
{
    protected static ?string $model = Tugas::class;

    protected static ?string $navigationGroup = 'Teknis';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('jenis_order')
                    ->label('Jenis Order')
                    ->options([
                        'psb' => 'PSB',
                        'survey' => 'Survey',
                        'pengecekan error' => 'Pengecekan Error',
                        'request' => 'Order/Request',
                        'lain-lain' => 'Lain-lain',
                    ])
                    ->default('lain-lain')
                    ->required(),

                Forms\Components\TextInput::make('judul')
                    ->label('Keterangan Pelanggan')
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'review' => 'Review',
                        'proses' => 'Proses',
                        'segera' => 'Segera',
                        'terlambat' => 'Terlambat',
                        'selesai' => 'Selesai',
                        'dihentikan' => 'Dihentikan',
                        'dibatalkan' => 'Dibatalkan',
                    ])
                    ->default('-')
                    ->required(),

                Select::make('prioritas')
                    ->label('Prioritas')
                    ->options([
                        1 => '1 - BTS/Link',
                        2 => '2 - Dedicated/Corporate',
                        3 => '3 - Personal',
                        4 => '4 - Order',
                    ])
                    ->default(3)
                    ->required(),

                DateTimePicker::make('tenggat_waktu')
                    ->label('Tenggat Waktu')
                    ->default(now()->addDays(3)) // default 3 hari dari sekarang
                    ->timezone('Asia/Jakarta')
                    ->withoutSeconds()
                    ->displayFormat('d-m-Y H:i') // Ada dua displayFormat, yang kedua akan menimpa yang pertama
                    ->required(),

                Select::make('kategori')
                    ->label('Kategori')
                    ->options([
                        'mendadak' => 'Mendadak',
                        'terjadwal' => 'Terjadwal',
                    ])
                    ->default('terjadwal')
                    ->required(),

                Select::make('penanggung_jawab_id')
                    ->label('Penanggung Jawab')
                    ->options(User::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->nullable(),

                Forms\Components\TextInput::make('deskripsi')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->striped()
        ->query(
            Tugas::query()
                ->selectRaw('*, DATEDIFF(tenggat_waktu, CURRENT_DATE()) as sisa_hari') // <-- Perubahan di sini
        )
        ->columns([
            
                Tables\Columns\TextColumn::make('label_order')
                    ->label('Order')
                    // ->searchable()
                    // ->sortable()
                    ->hidden(),
                
                Tables\Columns\TextColumn::make('tugas_id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('judul')
                    ->label('Task')
                    ->label('Subjek') // Label sudah ada di form, mungkin ini duplikat
                    ->searchable(),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->color(fn ($record) => $record->warna_status),
                    
                Tables\Columns\BadgeColumn::make('prioritas')
                    ->label('Prio')
                    ->searchable()
                    ->colors([
                        1 => 'gray',
                        2 => 'blue',
                        3 => 'yellow',
                        4 => 'orange',
                        5 => 'red',
                    ]),

                Tables\Columns\BadgeColumn::make('kategori')
                    ->label('Kategori')
                    ->color(fn (string $state): string => match ($state) {
                        'mendadak' => 'danger',
                        'terjadwal' => 'success',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('penanggungJawab.name')
                    ->label('PIC'),

                Tables\Columns\TextColumn::make('tenggat_waktu')
                    ->label('Tanggal')
                    ->searchable()
                    ->dateTime('d-m-Y')
                    ->sortable()
                    ->color(fn ($record) => $record->warna_tenggat_waktu)
                    ->tooltip(fn ($record) => $record->tenggat_waktu->diffForHumans()),
                
                Tables\Columns\TextColumn::make('sisa_hari')
                    ->label('Sisa Hari')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->warna_sisa_hari),
                
                Tables\Columns\TextColumn::make('deskripsi')
                    ->searchable()
                        ->label('Deskripsi'),
                    ])
    
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'review' => 'Review',
                        'proses' => 'Proses',
                        'segera' => 'Segera',
                        'terlambat' => 'Terlambat',
                        'selesai' => 'Selesai',
                        'dihentikan' => 'Dihentikan',
                        'dibatalkan' => 'Dibatalkan',
                    ]),
                    
                Tables\Filters\SelectFilter::make('prioritas')
                    ->options([
                        1 => '1 - BTS/Link',
                        2 => '2 - Dedicated/Corporate',
                        3 => '3 - Personal',
                        4 => '4 - Order',
                    ]),

                Tables\Filters\SelectFilter::make('kategori')
                    ->options([
                        'mendadak' => 'Mendadak',
                        'terjadwal' => 'Terjadwal',
                    ]),

                Tables\Filters\SelectFilter::make('penanggung_jawab_id')
                    ->label('Penanggung Jawab')
                    ->options(User::all()->pluck('name', 'id')),

                // Tables\Filters\SelectFilter::make('label_order')
                //     ->label('Jenis Order')
                //     ->options([
                //         'psb' => 'PSB',
                //         'survey' => 'Survey',
                //         'pengecekan error' => 'Pengecekan Error',
                //         'request' => 'Order/Request',
                //         'lain-lain' => 'Lain-lain',
                //     ]),

    Tables\Filters\Filter::make('tugasHariIni')
    ->label('Tugas Hari Ini')
    ->query(fn ($query) => $query->whereDate('tenggat_waktu', \Carbon\Carbon::today()))
    ->name('tugasHariIni') // ✅ WAJIB di Filament 3

                                       
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTugas::route('/'),
            // 'create' => Pages\CreateTugas::route('/create'),
            // 'edit' => Pages\EditTugas::route('/{record}/edit'),
        ];
    }
}