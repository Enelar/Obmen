<?php

class search extends api
{
  protected function Reserve($query)
  {
  }

  private function DummySeach($query)
  {
    $array = explode(" ", $query);
    $search_string = "%".implode("% %", $array)."%";

    $subqueries = explode(" ", $search_string);
    var_dump($subqueries);
    db::Query("SELECT * FROM public.adv WHERE title IN ")    
  }
}