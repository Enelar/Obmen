<?php

class search extends api
{
  protected function Reserve($query)
  {
    return $this->DummySeach($query);
  }

  const max_subqueries = 5;
  private function DummySeach($query)
  {
    $array = explode(" ", $query);
    $reduce_complexivity = array_slice($array, 0, search::max_subqueries);

    $subqueries = implode("|", $reduce_complexivity);
 
    $results = db::Query("SELECT * FROM public.adv WHERE name ~* $1", [$subqueries]);

    unset($this->addons['result']);
    return
    [
      "design" => "blocks/search/results",
      "result" => "search",
      "data" =>
      [
        "rows" => $results,
      ],
    ];
  }

  protected function Prepare()
  {
    return
    [
      "script" => "adv/search",
      "routeline" => "search.Prepare",
    ];
  }
}