<?php

class user extends api
{
  protected function Reserve($a)
  {
    return $this('api/cp', 'profile', true)->Reserve();
  }

  protected function link($uid = null)
  {
    $uid = $this->IfNullMe($uid);
    return
    [
      "design" => "user/link",
      "data" => db::Query("SELECT * FROM users.info WHERE uid=$1", [$uid], true),
   ];
  }

  public function IfNullMe($uid = null)
  {
    if (!is_null($uid))
      return $uid;
    return $this('api', 'auth')->uid();
  }
}