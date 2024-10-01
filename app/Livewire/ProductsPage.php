<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Product DEMO APP')]
class ProductsPage extends Component
{
    use WithPagination;
    public function render()
    {
        $ProductQuery = Product::query()->where('is_active', 1);
        $brands=Brand::where('is_active', 1)->get('id', 'name', 'slug');
        $categories = Category::where('is_active', 1)->get('id', 'name', 'slug');
        return view('livewire.products-page',[
            'products'=>$ProductQuery->paginate(6),
            'brands'=>$brands,
            'categories'=>$categories
        ]);
    }
}
