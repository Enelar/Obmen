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

  protected function Add($name, $text, $images)
  {
    $adid = db::Query("INSERT INTO public.adv(owner, name, descr, images) VALUES ($1, $2, $3, $4) RETURNING id",
      [$this('api', 'auth')->uid(), $name, $text, $images], true);

    return $adid->id;
  }

  protected function ShowAdv($id)
  {
    return
    [
      "design" => "cp/adv/show",
      "data" =>
      [
        "adv" => db::Query("SELECT * FROM public.adv WHERE id=$1", [$id], true),
      ],
    ];
  }
}