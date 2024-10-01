<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Component;
#[Title('Details -DEMO')]

class ProductdetailPage extends Component
{
    public $slug;

   
    public function render()
    {
        return view('livewire.productdetail-page', [
            'product'=>Product::where('slug', $this->slug)->firstOrFail(),
        ]);
    }

     public function mount($slug){
       
        $this->slug=$slug;
    }
}
