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
    return
    [
      "design" => "talk/talk",
      "data" =>
      [
        "messages" => db::Query("SELECT * FROM public.messages WHERE tid=$1 ORDER BY tid ASC", [$id]),
      ]
    ];
  }

  protected function Send($id, $message)
  {
    return db::Query("INSERT INTO public.messages(tid, uid, \"text\") VALUES ($1, $2, $3)",
      [$id, $this('api', 'auth')->uid(), $message], true);
  }

  private function info($id)
  {
    return db::Query("SELECT * FROM public.talks WHERE tid=$1", [$id], true);
  }
}