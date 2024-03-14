<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $label = 'Usuario';
    protected static ?string $pluralLabel = 'Usuarios';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $navigationGroup = 'Users Settings';
    protected static ?string $navigationIcon = 'heroicon-o-user';    
    protected static ?int    $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Personal')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombres')
                            ->placeholder('Nombres')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('apellido_paterno')
                            ->label('Apellido Paterno')
                            ->placeholder('Apellido Paterno')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('apellido_materno')
                            ->label('Apellido Materno')
                            ->placeholder('Apellido Materno')
                            ->required()
                            ->maxLength(255),
                    ]),
                Section::make('Información de Inicio de Sesión')
                    ->schema([
                        TextInput::make('username')
                            ->label('Usuario')
                            ->placeholder('Usuario')
                            ->required()
                            ->unique(ignorable: fn ($record) => $record)
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->placeholder('Correo Electrónico')
                            ->email()
                            ->unique(ignorable: fn ($record) => $record)
                            ->required()
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('Contraseña')
                            ->placeholder('Contraseña')
                            ->password()
                            ->required(fn ($livewire) => $livewire instanceof CreateRecord)
                            ->dehydrateStateUsing(fn ($state) => !empty($state) ? bcrypt($state) : null)
                            ->dehydrated(fn ($state, $record) => !empty($state) || is_null($record))
                            ->maxLength(255),
                        TextInput::make('password_confirmation')
                            ->label('Confirmar Contraseña')
                            ->placeholder('Confirmar Contraseña')
                            ->password()
                            ->required(fn ($livewire) => $livewire instanceof CreateRecord)
                            ->dehydrated(fn ($state, $record) => !empty($state) || is_null($record))
                            ->maxLength(255),
                    ]),
                Section::make('Configuración de Usuario')
                    ->schema([
                        Select::make('role')
                            ->label('Rol')
                            ->placeholder('Selecciona un rol')
                            ->relationship('roles', 'name', function (Builder $query, $record) {
                                $currentUserId = optional($record)->id; // Utiliza optional para evitar errores si $record es null.
                                $currentUserRoles = $currentUserId ? User::find($currentUserId)->roles->pluck('name') : collect();
                        
                                if (!auth()->user()->hasRole('MASTER')) {
                                    $query->whereNotIn('name', ['MASTER']);
                                }
                        
                                if (auth()->user()->hasRole('ADMIN') && !auth()->user()->hasRole('MASTER')) {
                                    $query->where(function ($query) use ($currentUserRoles) {
                                        $query->whereNotIn('name', ['MASTER', 'ADMIN'])
                                              ->orWhereIn('name', $currentUserRoles);
                                    });
                                }
                        
                                return $query;
                            })                         
                            ->preload()
                            ->searchable()
                            ->live(),
                        Select::make('permissions')
                            ->label('Permisos')
                            ->placeholder('Selecciona uno o multiples permisos')
                            ->multiple()
                            ->searchable()
                            ->relationship('permissions', 'name', function (Builder $query, $livewire) {
                                $currentUserId = optional($livewire->record)->id;
                                
                                $currentUserPermissions = $currentUserId ? User::find($currentUserId)->permissions->pluck('name') : collect();
                        
                                if (!auth()->user()->hasRole('MASTER')) {
                                    // Filtra los permisos si el usuario autenticado no es MASTER.
                                    // Ajusta 'All' y 'Users' según tus necesidades o lógica de negocio.
                                    $query->where(function ($query) use ($currentUserPermissions) {
                                        $query->whereNotIn('name', ['All', 'Users'])
                                              ->orWhereIn('name', $currentUserPermissions);
                                    });
                                }
                        
                                return $query;
                            })
                            ->preload()
                            ->live(),
                        Select::make('status')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->options([
                                'ACTIVO' => 'ACTIVO',
                                'INACTIVO' => 'INACTIVO'
                            ])
                            ->placeholder('Selecciona una Opción'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($state, $record) {
                        return $record->name . ' ' . $record->apellido_paterno . ' ' . $record->apellido_materno;
                    }),
                Tables\Columns\TextColumn::make('username')
                    ->label('Usuario')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->roles->first()?->name ?? 'Sin rol';
                    }),                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')->sortable()->dateTime('d-m-Y')->label('Fecha Creación')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->sortable()->dateTime('d-m-Y')->label('Fecha Modificación')->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
