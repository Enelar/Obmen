<?php

class talk extends api
{
  protected function Start($id)
  {
    unset($this->addons['result']);
    return
    [
      "design" => "talk/modal",
      "data" => $this->info($id),
    ];
  }

  protected function OnAdv($id)
  {
    
  }

  private function info($id)
  {
    return db::Query("SELECT * FROM public.talks WHERE tid=$1", [$id], true);
  }
}