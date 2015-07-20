<?php

class vkcom extends api
{
    private $integration;
    private $raw_integration;

    public function __construct()
    {
        $this->addons["stop_if_first"] = true;
        parent::__construct();
        $this->integration = $this('api/integration', 'vk', true);
        $this->raw_integration = $this('api/integration/vk', 'raw', true);
    }

    protected function Reserve()
    {
        return
        [
            "reset" => $this->make_url(),
        ];
    }

    protected function Auth()
    {
        global $_GET;

        $token = $this->get_access_token($_GET['code']);
        phoxy_protected_assert($token, ["error" => "Токен устарел"]);

        $res = $this->save_token($token['user_id'], $token['access_token'], $token['expires_in']);

        if (!$res->uid)
            return
            [
                "error" => "Что то пошло не так."
            ];

        $uid = $this('api', 'auth')->login($res->uid);

        $this->integration->UpdateMyInfo();

        return
        [
            "data" => ["uid" => $uid],
            "design" => "auth/vk/return",
        ];
    }

    private function make_url()
    {
        $url = 'https://oauth.vk.com/authorize';
        $redirect_uri = conf()->url.conf()->vk->redirect_urn;

        $params =
        [
            'client_id'     => conf()->vk->client_id,
            'redirect_uri'  => $redirect_uri,
            'response_type' => 'code',
            'scope' => conf()->vk->scope,
        ];

        return $url.'?'. http_build_query($params);
    }

    public function get_access_token( $code )
    {
        $redirect_uri = conf()->url.conf()->vk->redirect_urn;
        $params =
        [
            'client_id' => conf()->vk->client_id,
            'client_secret' => conf()->vk->client_secret,
            'redirect_uri'  => $redirect_uri,
            'code' => $code,
        ];

        $token = $this->integration->api_request('https://oauth.vk.com/access_token', $params);

        return $token;
    }

    public function save_token( $id, $token, $expire )
    {
        return db::ConditionalQuery
        (
          function() use ($id)
          {
            return db::Query("SELECT count(*) FROM users.\"auth.vk\" WHERE vkid=$1", [$id], true)['count'];
          },
          function () use ($id, $token, $expire)
          {
            return db::Query("UPDATE users.\"auth.vk\" SET token=$2, expires=now()+$3::interval WHERE vkid=$1 RETURNING uid",
                [$id, $token, $expire], true);
          },
          function () use ($id, $token, $expire)
          {
            return db::Query("INSERT INTO users.\"auth.vk\"(uid, vkid, token, expires) VALUES ($1, $2, $3, now()+$4::interval) RETURNING uid",
                [$this('api', 'auth')->do_oneclick_reg(), $id, $token, $expire], true);
          }
        );
    }
}
