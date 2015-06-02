<?php
define("SOFT_REG", 0);

class auth extends api
{
  protected function uid()
  {
    $this->addons['cache'] = ['no'];
    if (!SOFT_REG)
      phoxy_protected_assert
      (
        $this->is_user_authorized(),
        [
          "design" => "auth/index",
          "cache" => "no",
        ]
      );

    if (!SOFT_REG)
      if (!$this->is_user_authorized())
        $this->do_oneclick_reg();

    return $this->get_login();
  }

  public function get_uid($id = null)
  {
    if (session_status() !== PHP_SESSION_ACTIVE)
      session_start();
    global $_SESSION;

    if (!is_null($id))
      $_SESSION['uid'] = $id;
    if (isset($_SESSION['uid']))
      return $_SESSION['uid'];
    return 0;
  }

  public function is_user_authorized()
  {
    return !!$this->get_uid();
  }

  public function login($id)
  {
    return $this->get_uid($id);
  }

  protected function get_login()
  {
    return
    [
      "data" => ["get_login" => $this->get_uid()],
      "script" => "main/login",
      "before" => "set_uid",
    ];
  }

  protected function logout()
  {
    $this->login(0);
    return
    [
      'reset' => true,
    ];
  }

  private function get_forced_uid()
  {
    if ($this->get_uid())
      return $this->get_uid();
    $res = db::Query("INSERT INTO users.info DEFAULT VALUES RETURNING uid", [], true);
    return $this->login($res->uid);
  }

  public function do_oneclick_reg()
  {
    // If you want this method public:
    return $this->get_forced_uid();


    if ($this->get_uid())
      return
      [
        "reset" => '/'
      ];

    return
    [
      "design" => "auth/store_account",
      "data" => $this->get_forced_uid(),
    ];
  }

  protected function yea_im_pretty_sure_continue_without_registration()
  {
    return $this->do_oneclick_reg();
  }
}
