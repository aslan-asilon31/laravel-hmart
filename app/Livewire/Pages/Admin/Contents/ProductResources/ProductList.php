<?php

namespace App\Livewire\Pages\Admin\Contents\ProductResources;

use Livewire\Component;
use Livewire\Attributes\Title;

class ProductList extends Component
{
  public $title = 'Product';
  public string $url = '/products';

  public function render()
  {
    return view('livewire.pages.admin.contents.product-resources.product-list')
      ->title($this->title);
  }
}
