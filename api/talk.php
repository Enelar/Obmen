<?php

class talk extends api
{
  protected function Start($id)
  {
    unset($this->addons['result']);
    return
    [
      "design" => "talk/modal",
      "data" =>
      [
        "id" => (int)$id,
      ],
    ];
  }
}