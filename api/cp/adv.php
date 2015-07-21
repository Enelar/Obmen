<?php

class adv extends api
{
  private $adv;

  public function __construct($adv)
  {
    $this->adv = $adv;
  }

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

  protected function Add($category, $name, $text, $images)
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

    $adid = db::Query("INSERT INTO public.adv(owner, category, name, descr, images, iid) VALUES ($1, $2, $3, $4, $5, $6) RETURNING id",
      [$this('api', 'auth')->uid(), $category, $name, $text, $names, $iis], true);

    return $adid->id;
  }

  protected function ShowAdv($id)
  {
    return $this->Show($id);
  }

  protected function Show($id)
  {
    return
    [
      "design" => "cp/adv/show",
      "data" =>
      [
        "adv" => $this->info($id),
      ],
    ];
  }

  public function Owner($id)
  {
    $res = $this->info($id);
    return $res->owner;
  }

  protected function Name($id)
  {
    $res = $this->info($id);

    return
    [
      "design" => "snippets/echo",
      "data" =>
      [
        "name" => $res->name,
      ],
    ];
  }

  protected function imagelink($id)
  {
    return
    [
      "design" => "adv/imagelink",
      "data" => $this->info($id),
    ];
  }

  public function info($id)
  {
    return db::Query("SELECT * FROM public.adv WHERE id=$1", [$id], true);;
  }

  protected function edit($id = null, $array = null)
  {
    if ($id == null)
    return
    [
      "design" => "cp/adv/edit",
      "data" =>
      [
        "adv" => $this->info($this->adv),
      ]
    ];

    $me = $this('api', 'auth')->uid();
    phoxy_protected_assert($this->Owner($id), "Нужно быть автором обьявления!");

    $imageobj = $this('api/utils', 'image');


    $urls = [];
    $iids = [];
    foreach ($array->images as $image)
    {
      $iid = (int)$image->id;
      if (!$iid)
        continue;
      $urls[] = $imageobj->LocationById($image->id);
      $iids[] = $image->id;
    }

    $res = db::Query(
      "UPDATE public.adv
        SET name = $2
          , descr = $3
          , category = $4
          , images = $5
          , iid = $6
        WHERE id = $1
        RETURNING id
      ",
      [
        (int)$id,
        $array->name,
        $array->descr,
        $array->category,
        $urls,
        $iids,
      ], true);

    phoxy_protected_assert($res->id == $id, "Не удалось сохранить изменения :(");
    return $res->id;
  }
}