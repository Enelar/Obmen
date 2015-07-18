<?php

class talks extends api
{
  protected function Reserve()
  { // Crap code here
    $uid = $this('api', 'auth')->uid();
    $talks = db::Query(
      'SELECT * FROM public.talks as b
          WHERE "from" = $1
            OR "to" = $1
          ORDER BY snap DESC', [$uid]);

    $ret = [];
    foreach ($talks as $row)
    {
      $count = db::Query(
        "SELECT count(*) as unread
          FROM public.messages
          WHERE tid = $1
            AND readed IS NULL
            AND uid != $2",
            [$row->tid, $uid], true);

      $last_message = db::Query(
        "SELECT *
          FROM public.messages
          WHERE tid = $1
            AND readed IS NULL
            AND uid != $2
          ORDER BY mid DESC
          LIMIT 1",
            [$row->tid, $uid], true);

      $ret[] = array_merge($row->__2array(), $count->__2array(), $last_message->__2array());
    }

    return
    [
      "design" => 'blocks/messaging/talk.list',
      "data" =>
      [
        "list" => $ret,
      ],
    ];
  }
}