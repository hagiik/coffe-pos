<?php

namespace App\Filament\Resources\OrdersResource\Pages;

use App\Filament\Resources\OrdersResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\View\View;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrdersResource::class;

    // public function render(): View
    // {
    //     return view('filament.resources.orders-resource.view', [
    //         'record' => $this->record,
    //     ]);
    // }
}
