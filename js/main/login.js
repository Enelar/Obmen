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

function login_exception_hook(original_callstack, context)
{
  function login_exception_catch()
  {
    if (uid())
      phoxy.ApiRequest(context.data.origin, original_callstack);
    else
      original_callstack(); // do not recursive ask for resource, just report failure
  }

  var arr =
  {
    "design" : "auth/index",
    "data" :
    {
      "original_callstack" : login_exception_catch
    },
  };
  phoxy.ApiAnswer(arr);
}
