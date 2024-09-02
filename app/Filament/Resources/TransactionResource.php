<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'bx-dollar-circle';
    protected static ?string $activeNavigationIcon = 'bxs-dollar-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Transaksi')
                    ->placeholder('contoh: beli makan siang')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('amount')
                    ->label('Total Pengeluaran')
                    ->prefix('Rp')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('date')
                    ->default(now())
                    ->label('Tanggal Transaksi (otomatis terisi tanggal sekarang)'),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->placeholder('contoh: terpaksa beli soalnya ngga masak nasi')
                    ->columnSpanFull(),
                Forms\Components\Radio::make('type')
                    ->label('Tipe Transaksi')
                    ->required()
                    ->options([
                        'income' => 'Pemasukan',
                        'expense' => 'Pengeluaran',
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->heading('Transaksi')
            ->description('Kelola Transaksimu disini.')
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Transaksi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Total Pengeluaran')
                    ->prefix('Rp ')
                    ->money('IDR')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe Transaksi')
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        return $state === 'income' ? 'Pemasukan' : 'Pengeluaran';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal transaksi')
                    ->default(now()->format('Y-m-d'))
                    ->date()
                    ->sortable()

            ])
            ->filters([
                SelectFilter::make('type')
                    ->searchable()
                    ->native(false)
                    ->label('Tipe Transaksi')
                    ->options([
                        'income' => 'Pemasukan',
                        'expense' => 'Pengeluaran',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            // 'create' => Pages\CreateTransaction::route('/create'),
            // 'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
