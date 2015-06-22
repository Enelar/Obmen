<?php

class profile extends api
{
  protected function Reserve( $uid = null )
  {
    return
    [
      "design" => "cp/profile/index",
    ];
  }
}