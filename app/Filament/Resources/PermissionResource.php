<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;
   
    protected static ?string $label = 'Permiso';
    protected static ?string $pluralLabel = 'Permisos';
    protected static ?string $navigationLabel = 'Permisos';
    protected static ?string $navigationGroup = 'Users Settings';
    protected static ?string $navigationIcon = 'heroicon-o-key';    
    protected static ?int    $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                ->label('Permiso')
                ->placeholder('Permiso')
                ->required()
                ->unique(ignoreRecord: true)
                ->minLength('3')
                ->maxLength('30'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->searchable(),
                TextColumn::make('name')->sortable()->searchable()->label('Permiso'),
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
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
