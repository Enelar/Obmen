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
      if ($row->hidden != 't')
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

  protected function Show($id)
  {
    $res = db::Query("WITH cats AS
(
    SELECT id
      FROM public.categories
      WHERE
        tree <@
          (SELECT tree FROM public.categories WHERE id=$1)
) SELECT adv.* FROM public.adv, cats WHERE category=cats.id ORDER BY category DESC, adv.id DESC",
        [$id], true);

    return
    [
      "design" => "blocks/adv/list",
      "data" =>
      [
        "adv" => $res,
      ],
    ];
  }
}