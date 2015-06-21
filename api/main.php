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
  
  }
}