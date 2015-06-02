<?php

class raw extends api
{
  public function Request( $method, $params, $token = null )
  { // Todo exception on fault
    if ($token != null)
      $params['token'] = $token;

    $api = $method.'?'.http_build_query($params);
    $res = @file_get_contents($api);        
    return json_decode($res, true);
  }

  public function ApiRequest( $method, $params, $token = null )
  {
    return $this->Request("https://api.vk.com/method/{$method}", $params, $token);
  }

  public function vkid_by_uid( $uid )
  {
    $res = \db::Query('SELECT vkid FROM users."auth.vk" WHERE uid=$1', [$uid], true);
    return $res['vkid'];
  }

  public function force_vkid( $uid )
  {
    $ret = $this->vkid_by_uid($uid);
    if ($ret != null)
      return $ret;
    return $uid;
  }

  private function token_by_vkid( $vkid )
  {
    $res = \db::Query('SELECT token, (now() < expires) as fresh FROM users."auth.vk" WHERE vkid=$1', [$vkid], true);
    if (!$res)
      return false;
    if ($res['fresh'])
      return $res['token'];
    return NULL;
  }

  private function detect_vk_id( $data )
  {
    $try = (int)$data;
    return "$try" == $data;
  }

  public function token()
  {
    if ($this->my_token)
      return $this->my_token;

    $vkid = $this->vkid_by_uid($this('api', 'auth')->uid());
    if (!$vkid)
      throw new exception("Not registered with vk"); // Todo: redirect and autoauth vk
    return $this->my_token = $this->token_by_vkid($vkid);
  }

}