<?php

class user extends api
{
  protected function Reserve($uid)
  {
    return
    [
      "design" => "user/profile",
      "data" =>
      [
        "user" => db::Query("SELECT * FROM users.info WHERE uid=$1", [$uid], true),
        "adv" => db::Query("SELECT * FROM public.adv WHERE owner=$1", [$uid]),
      ],
    ];
  }

  protected function link($uid)
  {
    return
    [
      "design" => "user/link",
      "data" => db::Query("SELECT * FROM users.info WHERE uid=$1", [$uid], true),
   ];
  }
}