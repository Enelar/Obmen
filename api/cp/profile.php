<?php

class profile extends api
{
  protected function Reserve($uid = null)
  {
    $uid = $this('api', 'user')->IfNullMe($uid);
    return
    [
      "design" => "user/profile",
      "data" =>
      [
        "user" => db::Query("SELECT * FROM users.info WHERE uid=$1", [$uid], true),
        "adv" => db::Query("SELECT * FROM public.adv WHERE owner=$1 ORDER BY id DESC", [$uid]),
      ],
    ];
  }
}