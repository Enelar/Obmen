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
    $imageobj = $this('api/utils', 'image');
    $uploadobj = $this('api/utils/image', 'upload');

    $names = [];
    $iis = [];
    foreach ($images as $image)
    {
      $names[] = $uploadobj->LocationByName($image->name);
      $iis[] = (int)$image->id;
      $imageobj->ShelterOrphan($image->id, $image->name);
    }

    $adid = db::Query("INSERT INTO public.adv(owner, name, descr, images, iid) VALUES ($1, $2, $3, $4, $5) RETURNING id",
      [$this('api', 'auth')->uid(), $name, $text, $names, $iis], true);

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