<?php

class main extends api
{
  protected function Reserve($a = null)
  {
    unset($this->addons['result']);
    return
    [
      'design' => 'main/body',
    ];
  }

  protected function Home()
  {
    return
    [
      'design' => 'snippets/empty',
    ];
  }

  protected function Head()
  {
    unset($this->addons['result']);
    return
    [
      'design' => 'main/header',
      'result' => 'header',
    ];
  }
}