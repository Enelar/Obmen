<?php

class profile extends api
{
  protected function Reserve($uid = null)
  {
    if (is_null($uid))
      $uid = $this('api', 'auth')->uid();

    return
    [
      "design" => "user/profile",
      "data" =>
      [
        "user" => db::Query("SELECT * FROM users.info WHERE uid=$1", [$uid], true),
        "adv" => $this->Advertisments($uid),
      ],
    ];
  }

  protected function MyAdvertisments()
  {
    return
    [
      "data" =>
      [
        "adv" => $this->Advertisments($this('api', 'auth')->uid()),
      ],
    ];
  }

  public function Advertisments($uid)
  {
    return db::Query("SELECT * FROM public.adv WHERE owner=$1 ORDER BY id DESC", [$uid]);
  }

  protected function info($uid = null)
  {
    if (is_null($uid))
      $uid = $this('api', 'auth')->uid();

    return
    [
      "data" => db::Query("SELECT * FROM users.info WHERE uid=$1", [$uid], true),
    ]; 
  }
}