<?php

namespace App\Livewire\Pages\Admin\Generals\PermissionResources;

use Livewire\Component;

class PermissionList extends Component
{
  public function render()
  {
    return view('livewire.pages.admin.generals.permission-resources.permission-list')
      ->title($this->title);
  }
  
  public string $url = '/permissions';
  public string $title = 'Permissions';
  use \App\Helpers\Permission\Traits\WithPermission;

  #[\Livewire\Attributes\Locked]
  private string $basePageName = 'permission';
  
  public function amount()
  {
    $this->permission($this->basePageName.'-list');
  }

}
