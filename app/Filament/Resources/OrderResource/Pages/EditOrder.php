<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use App\Models\Order;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Repeater;
use App\Models\Product;
use Filament\Forms\Components\Component;
use App\Enums\OrderStatus;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Forms\Components\ToggleButtons;

class EditOrder extends EditRecord
{
    // use CreateRecord\Concerns\HasWizard;
    use EditRecord\Concerns\HasWizard;

    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Order Updated Successfully';
    }

    public function getSteps(): array
    {
        return [
            Step::make('Order Details')
                ->schema([
                    Section::make('')
                        ->schema([
                            TextInput::make('total')
                                ->hidden(),
                            TextInput::make('order_number')
                                ->disabled()
                                ->required(),
                            Select::make('user_id')
                                ->relationship('user', 'name', function (Builder $query) {
                                    $query->where('is_admin', false);
                                })
                                ->disabled()
                                ->required(),

                            ToggleButtons::make('status')
                                ->inline()
                                ->options(OrderStatus::class)
                                ->required(),
                        ])->columns(3),

                    Section::make('Shipping Info')
                        ->schema([
                            TextInput::make('state')
                                ->required()
                                ->label('State/Province')
                                ->placeholder('State/Province'),
                            TextInput::make('city')
                                ->required()
                                ->placeholder('City'),
                            TextInput::make('zip_code')
                                ->required()
                                ->placeholder('Zip Code'),
                            TextInput::make('address')
                                ->required()
                                ->placeholder('Address')
                                ->columnSpanFull(),
                        ])->columns(3)->collapsible(),
                    Section::make()
                        ->schema([
                            RichEditor::make('comment'),
                        ])->columnSpanFull(),
                ]),

            Step::make('Oder Items')
                ->schema([
                    Repeater::make('Items')
                        ->relationship('oderItems')
                        ->schema([
                            Select::make('product_id')
                                ->options(Product::where([['is_visible', 1]])->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->required()
                                ->placeholder('Product')
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            TextInput::make('quantity')
                                ->numeric()
                                ->required()
                                ->minValue(1)
                                ->placeholder('Quantity')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (?string $state, $set, Component $component) {
                                    if ($component->getName() == 'quantity') {
                                        $container = $component->getContainer();
                                        $statePatch = explode('.', $container->getStatePath());
                                        $statePatch = $statePatch[2];
                                        $data = $container->getLivewire()->data;
                                        $currentRow = in_array($statePatch, array_keys($data['Items'])) ? $data['Items'][$statePatch] : [];
                                        $product = Product::find($currentRow['product_id']);
                                        $itemTotalPrice = ($product->sale_price ?? $product->price) * ($currentRow['quantity']);
                                        $set('item_price', ($product->sale_price ?? $product->price));
                                        $set('item_total', $itemTotalPrice);
                                    }
                                }),
                            TextInput::make('item_price')
                                ->readOnly()
                                ->required()
                                ->placeholder('Price'),
                            TextInput::make('item_total')
                                ->readOnly()
                                ->required()
                                ->placeholder('Total'),
                        ])->columns(4)
                        ->minItems(1)
                        ->collapsible(),
                ])
        ];
    }
}