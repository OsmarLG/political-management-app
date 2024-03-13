<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Panel;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Models\Contracts\FilamentUser;
use App\Filament\Resources\RoleResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RoleResource\RelationManagers;


class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $label = 'Rol';
    protected static ?string $pluralLabel = 'Roles';
    protected static ?string $navigationLabel = 'Roles';
    protected static ?string $navigationGroup = 'Users Settings';
    protected static ?string $navigationIcon = 'heroicon-o-finger-print';  
    protected static ?int    $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Rol')
                    ->schema([
                    TextInput::make('name')
                        ->label('Rol')
                        ->placeholder('Rol')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->minLength('3')
                        ->maxLength('30'),
                    Select::make('permissions')
                        ->label('Permisos')
                        ->placeholder('Selecciona uno o multiples permisos')
                        ->multiple()
                        ->relationship('permissions', 'name', fn (Builder $query) => 
                            auth()->user()->hasRole('MASTER') ? $query : $query->where('name', '!=', 'All')->where('name', '!=', 'Users')
                        )
                        ->preload()
                        ->live(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
                TextColumn::make('id')->sortable()->searchable(),
                TextColumn::make('name')->sortable()->searchable()->label('Rol'),
                TextColumn::make('created_at')->sortable()->dateTime('d-m-Y')->label('Fecha Creación'),
                TextColumn::make('updated_at')->sortable()->dateTime('d-m-Y')->label('Fecha Modificación'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()->where('name', '!=', 'MASTER');
    // }
}
