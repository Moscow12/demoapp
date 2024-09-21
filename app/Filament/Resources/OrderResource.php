<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                            ->relationship('users','name')
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
                                'bodaboda'=>'BodaBoda',
                                'bus'=>'Bus',
                                'ndege'=>'Ndege',
                                'dhl'=>'DHL'
                        ])
                        ->required(),
                ])->columns(2),
                    
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
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
