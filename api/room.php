<?php

class room extends api
{
  protected function Reserve($adv)
  {
    $uid = $this('api', 'auth')->uid();
    if ($room = $this->FindRoom($uid, $adv))
      $this('api', 'talk', true)->OnAdv($room);

    unset($this->addons['result']);

    return
    [
      "design" => "talk/offer/modal",
      "data" =>
      [
        "adv" => (int)$adv,
      ],
    ];
  }

  protected function Create($aid, $offers)
  {
    unset($this->addons['result']);
    $advobj = $this('api/cp', 'adv');

    $from = $this('api', 'auth')->uid();

    $to = $advobj->Owner($aid);
    phoxy_protected_assert($to, "Нельзя меняться с пустотой, она этого не любит");
    phoxy_protected_assert(count($offers), "Предложите хоть что нибудь");

    $this->RequireAdvOwnership($from, $offers);

    $res = db::Query("INSERT INTO 
      public.talks
      (adv, \"from\", \"to\", offer)
      VALUES
      ($1, $2, $3, $4::int4[])
      RETURNING tid",
      [$aid, $from, $to, $offers], true);

    $room = $res->tid;
    if (!$room)
      phoxy_protected_assert($room = $this->FindRoom($from, $aid), "Не удалось начать обмен.");

    return $this('api', 'talk', true)->Start($room);      
  }

  private function RequireAdvOwnership($me, $offers)
  {
    $advobj = $this('api/cp', 'adv');
    foreach ($offers as $offer)
    {
      if ($advobj->Owner($offer) !== $me)
        phoxy_protected_assert(false, 
        [
          "error" => "Вещь должна принадлежать вам, что бы ее можно было предложить на обмен",
          "data" =>
          [
            "adv" => (int)$offer,
          ],
        ]);
    }
  }

  private function FindRoom($from, $about)
  {
    $res = db::Query("SELECT aid FROM public.talks WHERE \"from\"=$1 AND adv=$2", [$from, $about], true);
    if (!$res())
      return false;
    return $res->aid;
  }
}