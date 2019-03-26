<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Resources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $url = preg_match('/^http/', $this->url) ? $this->url : 'http://'.$this->url;

        $modButton = app()->make('button.edit', [
            'data' => $this,
            'text' => 'modify',
            'title' => $this->name,
        ]);
        $modButton->setUrl(route('resources.edit', ['id' => $this->id], false));

        $delButton = app()->make('button.destroy', [
            'data' => $this,
            'text' => 'delete',
            'title' => $this->name,
        ]);
        $delUrl = route('resources.destroy', ['id' => $this->id], false);
        $delButton->pushAttributes([ 'onclick' => "SwalAlter.delete('".$delUrl."', '刪除', '刪除此筆資料： ".$this->name." - ".$this->route."', '確認刪除')"]);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'route' => $this->route,
            'buttons' => (string) $modButton. '&nbsp;' .(string) $delButton,
        ];
    }
}
