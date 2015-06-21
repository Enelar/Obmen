<?php

class adv extends api
{
  protected function Reserve($id = null)
  {
    if (is_null($id))
      return $this->AddForm();
    return $this->ShowAdv($id);
  }

  protected function AddForm()
  {
    return
    [
      "design" => "cp/adv/index",
    ];
  }

  protected function Add($name, $text)
  {
    $adid = db::Query("INSERT INTO public.adv(owner, name, descr) VALUES ($1, $2, $3) RETURNING id",
      [$this('api', 'auth')->uid(), $name, $text], true);

    return $adid->id;
  }

  protected function ShowAdv($id)
  {
    return
    [
      "design" => "blocks/adv",
      "data" =>
      [
        "adv" => db::Query("SELECT * FROM public.adv WHERE id=$1", [$id], true),
      ],
    ];
  }
}