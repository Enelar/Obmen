<?php

class vk extends \api
{
    public function api_request( $method, $params )
    {   // Todo exception on fault
        $api = $method.'?'.http_build_query($params);
        $res = @file_get_contents($api);
        return json_decode($res, true);
    }

    public function GetUserInfo($uid = null)
    {
      if ($uid == null)
        $uid = $this('api', 'auth')->uid();
      $vkid = $this->vkid_by_uid($uid);
      return $this->get_user_info($vkid);
    }

    /*  get_user_info(vk_id): trying to extract his token and call
        get_user_info(vk_id, token): regular call
        get_user_info(vk_id, vk_id): trying to extract second token and call
     */
    private function get_user_info( $user_id, $access_token = NULL )
    {
        if (!$this->detect_vk_id($user_id))
            throw new exception('First param should be vkid');

        if (is_null($access_token))
            $token = $this->token_by_vkid($user_id);
        else if ($this->detect_vk_id($access_token))
            $token = $this->token_by_vkid($access_token);
        else
            $token = $access_token;

        $params =
        [
            'uids'         => $user_id,
            'fields'       => 'uid,first_name,last_name,screen_name,sex,bdate,photo_50,photo_100,photo_200,photo_big',
            'access_token' => $token
        ];

        $user_info = $this->api_request('https://api.vk.com/method/users.get', $params);

        return array_shift($user_info["response"]);
    }

    public function vkid_by_uid( $uid )
    {
        $res = \db::Query("SELECT vkid FROM users.\"auth.vk\" WHERE uid=$1", [$uid], true);
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
        $res = \db::Query("SELECT token, (now() < expires) as fresh FROM users.\"auth.vk\" WHERE vkid=$1", [$vkid], true);
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

    public function UpdateMyInfo()
    {
      $uid = $this('api', 'auth')->uid();
        $user_info = $this->GetUserInfo($uid);

        \db::Query("UPDATE users.info SET avatar=$2 WHERE uid=$1",
            [
                $uid,
                [
                    $user_info['photo_50'],
                    $user_info['photo_100'],
                    $user_info['photo_200'],
                ]
            ]);
        \db::Query("UPDATE users.info SET name=$2 WHERE uid=$1",
            [
                $uid,
                [
                    $user_info['first_name'],
                    $user_info['last_name'],
                ]
            ]);
    }
}