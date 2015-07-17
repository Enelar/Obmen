<?php

class main extends api
{
  protected function Reserve()
  {
    unset($this->addons['result']);
    return
    [
      'design' => 'main/body',

      'script' => 'main/login',
      'before' => 'set_uid',
      'data' =>
      [
        'get_login' => $this('api', 'auth')->get_uid(),
      ]
    ];
  }

  protected function Home()
  {
    return
    [
      'design' => 'main/home',
    ];
  }

  protected function Hat()
  {
    unset($this->addons['result']);
    return
    [
      'design' => 'main/hat',
      'result' => 'hat',
    ];
  }
}