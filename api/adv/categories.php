<?php

class categories extends api
{
  protected function Reserve()
  {
    return $this->GetAll();
  }

  protected function GetAll()
  {
  	$res = db::Query("SELECT *, nlevel(tree) FROM public.categories WHERE nlevel(tree) < 4 ORDER BY tree ASC LIMIT 200");
    $ret = [];

    $hidden = false;
    foreach ($res as $row)
    {
      if ($row->nlevel == 2)
        $hidden = $row->hidden == 't';
      if ($hidden)
        continue;
      $ret[] = $row;
    }
        
  	return
  	[
  	  "design" => "utils/categories",
  	  "data" =>
  	  [
	  	  "categories" => $ret,
  	  ],
  	];
   }
}