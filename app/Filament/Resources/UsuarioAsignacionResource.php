<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\UsuarioAsignacion;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Models\{User, Zona, Seccion, Manzana};
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UsuarioAsignacionResource\Pages;
use App\Filament\Resources\UsuarioAsignacionResource\RelationManagers;

class UsuarioAsignacionResource extends Resource
{
    protected static ?string $model = UsuarioAsignacion::class;

    protected static ?string $label = 'Asignacion';
    protected static ?string $pluralLabel = 'Asignaciones';
    protected static ?string $navigationLabel = 'Asignaciones';
    protected static ?string $navigationGroup = 'Users Settings';
    protected static ?string $navigationIcon = 'heroicon-o-user';    
    protected static ?string $slug = 'usuarios_asignaciones';    
    protected static ?int    $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Select::make('user_id')
                ->label('Usuario')
                ->options(User::query()
                    ->active()
                    ->whereDoesntHave('roles', function ($query) {
                        $query->whereIn('name', ['MASTER', 'ADMIN']);
                    })
                    ->whereDoesntHave('Asignacion')
                    ->with('roles') // Asegúrate de que los roles se cargan para evitar consultas N+1
                    ->get()
                    ->pluck('full_name', 'id'))
                ->reactive()
                ->afterStateUpdated(function ($state, $component, $set) {
                    $user = User::find($state);
                    $roleName = $user->roles->first()?->name; // Obtén el nombre del primer rol

                    // Aquí asumimos que los nombres de los roles y los modelos están en minúsculas y son idénticos
                    $set('modelo', $roleName === 'ZONAL' ? 'Zona' : ($roleName === 'SECCIONAL' ? 'Seccion' : 'Manzana'));
                })
                ->searchable()                  
                ->preload()
                ->live(),

            // El campo 'modelo' se rellenará automáticamente basándose en el rol del usuario seleccionado
            Forms\Components\TextInput::make('modelo')
                ->label('Tipo de Asignación')
                ->reactive()
                ->disabled()
                ->afterStateUpdated(function ($state, $component, $set) {
                    // No es necesario el tipo Closure aquí
                    $set('id_modelo', null);
                }),

            Forms\Components\Select::make('id_modelo')
                ->label('Asignación')
                ->options(function (callable $get) {
                    $modelo = $get('modelo');
                    switch ($modelo) {
                        case 'Zona':
                            return Zona::all()->pluck('nombre', 'id');
                        case 'Seccion':
                            return Seccion::all()->pluck('nombre', 'id');
                        case 'Manzana':
                            return Manzana::all()->pluck('nombre', 'id');
                        default:
                            return [];
                    }
                })
                ->reactive()
                ->searchable()                  
                ->hidden(fn (callable $get) => $get('modelo') === null)
                ->preload()
                ->live(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
                TextColumn::make('user.name')
                ->label('Nombre del Usuario')
                ->sortable()
                ->searchable(),
                
                TextColumn::make('user.roles.name')
                    ->label('Rol')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('asignable.nombre')
                    ->label('Asignación')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($state, $record) {
                        return optional($record->asignable)->nombre;
                    }),
                
                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d-m-Y')
                    ->sortable(),
            ])
            ->filters([
                //
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsuarioAsignacions::route('/'),
            'create' => Pages\CreateUsuarioAsignacion::route('/create'),
            'edit' => Pages\EditUsuarioAsignacion::route('/{record}/edit'),
        ];
    }
}
