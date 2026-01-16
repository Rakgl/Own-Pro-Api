<?php

namespace App\Http\Resources\Api\V1\Admin;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'event' => $this->event,
      'old_values' => $this->old_values,
      'new_values' => $this->new_values,
      'user' => $this->user ? $this->user->name : null,
      'created_at' => date('d/m/Y H:i:s', strtotime($this->created_at)),
      'ip_address' => $this->ip_address,
    ];
  }
}
