<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MentionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'accountId' => $this->account_id,
            'platform' => $this->platform,
            'platformId' => $this->platform_id,
            "content" => $this->content,
            "type" => $this->type,
            "status" => $this->status,
            "url" => $this->url,
            "senderName" => $this->sender_name,
            "senderUsername" => $this->sender_username,
            "senderProfileUrl" => $this->sender_url,
            "senderAvatar" => $this->sender_avatar,
            "column" => $this->column,
            "order" => $this->order,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
