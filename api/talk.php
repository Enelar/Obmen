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

  protected function Notifications()
  {
    $res = db::Query('WITH talks AS
      (
        SELECT tid FROM "public"."talks" WHERE "from" = $1 OR "to" = $1
      ) SELECT tid, count(*) FROM public.messages as a WHERE readed IS NULL AND uid != $1 GROUP BY tid',
      [$this('api', 'auth')->uid()]);

    return
    [
      'design' => 'blocks/messaging/notifications/envelope',
      'data' =>
      [
        'summary' => $res,
        'count' => count($res),
      ],
    ];
  }
}