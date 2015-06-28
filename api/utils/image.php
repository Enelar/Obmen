<?php

class image extends api
{
  protected function Reserve()
  {
    return
    [
      "design" => "image/index",
    ];
  }

  public function LocationByName( $name )
  {
    $res = $this->info($name);
    return $this->base_prefix.$res['name'].".".$res['ext'];
  }

  public function info( $name )
  {
    return db::Query("SELECT * FROM utils.images WHERE name=$1", [$name], true);
  }

  public function ShelterOrphan( $id, $name )
  {
    db::Query("UPDATE utils.images SET owner=$3 WHERE iid=$1 AND name=$2",
      [$id, $name, $this('api','auth')->uid()]);
  }
}