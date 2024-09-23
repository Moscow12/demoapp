<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\AddressRelationManager;
use App\Models\Order;
use App\Models\Product;
use Faker\Core\Number;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Relationship;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number as SupportNumber;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make()->schema([
                        Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user','name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('payment_method')
                            ->options([
                                'Strip'=>'Stripe',
                                'LIPA' =>'Lipa Hapa',
                                'COD' => 'Cash On Delivery'
                            ])->searchable()
                            ->required(),
                        Select::make('payment_status')
                            ->options([
                                'Pending'=>'Pending',
                                'Paid'=>'Paid',
                                'Failed'=>'Failed'
                            ])->default('Pending')
                            ->required(),
                        ToggleButtons::make('status')
                            ->inline()
                            ->default('new') 
                            ->required()
                            ->options([
                                'New' =>'New',
                                'Processing' => 'Processing',
                                'Shipped' => 'Shipped',
                                'Delivered' => 'Delivered',
                                'Cancelled' =>'Cancelled'
                            ])
                            ->colors([
                                'New' =>'info',
                                'Processing' => 'info',
                                'Shipped' => 'warning',
                                'Delivered' => 'success',
                                'Cancelled' =>'danger'
                            ])
                            ->icons([
                                'New' =>'heroicon-m-sparkles',
                                'Processing' => 'heroicon-m-arrow-path',
                                'Shipped' => 'heroicon-m-truck',
                                'Delivered' => 'heroicon-m-check-badge',
                                'Cancelled' =>'heroicon-m-x-circle'
                            ]),
                    
                        Select::make('currency')
                            ->options([
                                'tsh'=>'TSH',
                                'usd'=>'USD',
                                'eur'=>'EUR',
                                'gdp'=>'GDP'
                            ])
                            ->default('tsh')
                            ->required(),

                        Select::make('shipping_method')
                            ->options([
                                'bus'=>'Bus',
                                'ndege'=>'Ndege',
                                'dhl'=>'DHL'
                        ])
                        ->required(),
                ])->columns(2),
                    Section::make('Order Item')->schema([
                        Repeater::make('items')
                            ->Relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->columnspan(4)
                                    ->afterStateUpdated(fn ($state, Set $set) => $set('unit_amount', Product::find($state)?->price ?? 0))
                                    ->afterStateUpdated(fn ($state, Set $set) => $set('total_amount', Product::find($state)?->price ?? 0)),
                                TextInput::make('quantity')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1)
                                    ->columnspan(2)
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, Set $set, Get $get) =>$set('total_amount', $state*$get('unit_amount'))),
                                TextInput::make('unit_amount')
                                    ->numeric()
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnspan(3),
                                TextInput::make('total_amount')
                                    ->numeric()
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnspan(3),
                            ])->columns(12),
                        Placeholder::make('placeholder_grand_total')
                            ->label('Grand Total')
                            ->content(function (Get $get, Set $set){
                                $total=0;
                                if(!$repeaters = $get('items')){
                                    return $total;
                                }
                                foreach($repeaters as $key =>$repeater){
                                    $total +=$get("items.{$key}.total_amount");
                                }
                                $set('$grandtotal', $total);
                                return SupportNumber::currency($total, 'TSH.');
                            }),
                            Hidden::make('grandtotal')
                                ->default(0)
                    ]),
                    
                    Textarea::make('notes')
                        ->columnSpanFull()
                        ->required()
                ])->columnSpanFull()
            ]);
    } 

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('grandtotal')
                    ->numeric()
                    ->sortable()
                    ->money('Tsh'),
                TextColumn::make('payment_method')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('payment_status')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('shipping_method')
                    ->sortable()
                    ->searchable(),
                SelectColumn::make('status')
                    ->options([
                        'New' =>'New',
                        'Processing' => 'Processing',
                        'Shipped' => 'Shipped',
                        'Delivered' => 'Delivered',
                        'Cancelled' =>'Cancelled'
                    ])
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault:true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault:true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault:true)
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])

            ])
            
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 5 ? 'success' : 'danger';
    }
    public static function getRelations(): array
    {
        return [
           AddressRelationManager::class
            
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
