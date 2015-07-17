<?php

class talks extends api
{
  protected function Reserve()
  {
    return
    [
      "design" => 'blocks/messaging/talk.list',
      "data" =>
      [
        "list" => db::Query('SELECT * FROM public.talks WHERE "from"=$1 OR "to"=$1 ORDER BY snap DESC',
          [$this('api', 'auth')->uid()]),
      ],
    ];
  }
}