function set_uid(cb, rpcd)
{
  window.uid_value = rpcd.data.get_login;
  return cb(rpcd);
}

function uid()
{
  return window.uid_value;
}

function call_for_login()
{
  if (uid() > 0)
    return false;
  phoxy.MenuCall('auth/uid');
  return true;
}