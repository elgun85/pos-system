<?php

namespace App\Filament\Resources\Expenses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Daxili Xərclər Modulu')
                    ->schema([

                        Group::make()
                            ->schema([
                                Select::make('expense_category_id')
                                    ->relationship('expenseCategory', 'name')
                                    ->label('Kateqoriya')
                                    ->preload()
                                    ->searchable()
                                    ->native(false)
                                    ->required()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Yeni Kateqoriya')
                                            ->required()
                                            ->maxLength(300)
                                    ]),

                                TextInput::make('amount')
                                    ->label('Məbləğ')
                                    ->numeric()
                                    ->required()
                                    ->prefix('₼')
                                    ->minValue(0.01),

                                Select::make('payment_method_id')
                                    ->label('Ödəniş növü')
                                    ->required()
                                    ->relationship(
                                        'paymentMethod',
                                        'name',
                                        modifyQueryUsing: fn($query) => $query->where('status', true)
                                    )
                                    ->preload()
                                    ->searchable()
                                    ->native(false),

                            ])->columns(3),


                        Textarea::make('notes')
                            ->label('Açiqlama')
                            ->maxLength(300),
                    ])->secondary()->columnSpan(2)->columnStart(1),
            ]);
    }
}
