var warmup_obj =
{
  wait: true,
  config: "/api/phoxy",
  skip_initiation: true,
  sync_cascade: true,
  OnWaiting: function()
  {
    phoxy._EarlyStage.sync_require[0] = "/enjs.js";

    phoxy._EarlyStage.sync_require.push("//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js");
    phoxy._EarlyStage.EntryPoint();
    phoxy.state.early.optional.lazy = 4;
  },
  OnBeforeCompile: function()
  {
    phoxy.config['api_dir'] = '/' + phoxy.config['api_dir'];
    phoxy.config['ejs_dir'] = '/' + phoxy.config['ejs_dir'];
    phoxy.config['js_dir'] = '/' + phoxy.config['js_dir'];

    requirejs.config(
    {
      baseUrl: phoxy.config['js_dir'],
    });

    $('head').append
    (
      '<link rel="subresource" href="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.0.0/semantic.min.js">'
      + '<link rel="subresource" href="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js">'
      + '<link rel="prefetch" href="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.0.0/semantic.min.js">'
      + '<link rel="prefetch" href="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js">'
    );

  },
  OnAfterCompile: function()
  {
    phoxy.ChangeHash = function(url)
    {
      if (typeof url !== 'string')
      {
        arr = url = url.slice(0)
        url = arr.shift(1);
        if (arr.length > 0)
          url += "(" + arr.join() + ")";
      }

      history.pushState({}, document.title, '/' + url);
      return false;
    }

    var not_found = phoxy.ApiAnswer;
    phoxy.ApiAnswer = function(data)
    {
      if (data["error"] === 'Module not found'
          || data["error"] === "Unexpected RPC call (Module handler not found)")
      {
        $('.removeafterload').remove();
        return phoxy.ApiRequest("utils/page404");
      }
      return not_found.apply(this, arguments);
    }

    // Allow force non-caching requests on form post and any information update
    var direct_request = phoxy.ApiRequest;
    phoxy.ApiRequest = function(origin, callback, direct)
    {
      if (typeof direct === 'undefined' || direct !== true)
        return direct_request.apply(this, arguments);
      if (typeof origin == 'string')
        origin += "?direct";
      else
        origin[0] += "?direct";

      return direct_request.call(this, origin, callback);
    }

    phoxy.Log(3, "Phoxy ready. Starting");
  },
  OnBeforeFirstApiCall: function()
  {
    requirejs.config({baseUrl: phoxy.Config()['js_dir']});

    // Enable jquery in EJS context
    var origin_hook = EJS.Canvas.prototype.hook_first;
    EJS.Canvas.prototype.hook_first = function()
    {
      return $(origin_hook.apply(this, arguments));
    }
  },
  OnInitialClientCodeComplete: function()
  {
    phoxy.Log(3, "Initial handlers complete");

    $('.removeafterload').remove();
    $('body').trigger('initialrender');

    phoxy.AJAX(location.pathname.substr(1) + location.search, function(r)
    {
      phoxy.ApiAnswer(r);
      phoxy.Log(3, "First page rendered");

      if (r.stop_if_first)
        return;
      phoxy.ApiRequest('main/Hat');
    })
  }
};

if (typeof phoxy.prestart === 'undefined')
  phoxy = warmup_obj;
else
{
  phoxy.prestart = warmup_obj;
  phoxy.prestart.OnWaiting();
}

if (typeof YOUR_ANALYTICS_KEY !== 'undefined')
  if(window.analytics=window.analytics||[],window.analytics.included)window.console&&console.error&&console.error("analytics.js included twice");else{window.analytics.included=!0,window.analytics.methods=["identify","group","track","page","pageview","alias","ready","on","once","off","trackLink","trackForm","trackClick","trackSubmit"],window.analytics.factory=function(a){return function(){var n=Array.prototype.slice.call(arguments);return n.unshift(a),window.analytics.push(n),window.analytics}};for(var i=0;i<window.analytics.methods.length;i++){var key=window.analytics.methods[i];window.analytics[key]=window.analytics.factory(key)}window.analytics.load=function(a){var n=document.createElement("script");n.type="text/javascript",n.async=!0,n.src=("https:"===document.location.protocol?"https://":"http://")+"cdn.segment.com/analytics.js/v1/"+a+"/analytics.min.js";var t=document.getElementsByTagName("script")[0];t.parentNode.insertBefore(n,t)},window.analytics.SNIPPET_VERSION="2.0.9",
    window.analytics.load(YOUR_ANALYTICS_KEY)}

(function()
{
  if (typeof require === 'undefined')
    return setTimeout(arguments.callee, 50);
  clearTimeout(require_not_loading);

  require(['/phoxy/phoxy.js'], function(){});
})();

var require_not_loading = setTimeout(function()
{
  var d = document;
  var js = d.createElement("script");
  js.type = "text/javascript";
  js.src = "//cdnjs.cloudflare.com/ajax/libs/require.js/2.1.15/require.min.js";
  d.head.appendChild(js);
}, 1000);

// Loading animation
(function()
{
  if (typeof phoxy._EarlyStage == 'undefined')
    return setTimeout(arguments.callee, 10);

  var percents = phoxy._EarlyStage.LoadingPercentage();
  var element = document.getElementById('percent');

  if (element == null)
    return;
  element.style.width = percents + "px";
  element.style.opacity = percents / 100;
  setTimeout(arguments.callee, 50);

  if (percents == 100)
    $('.removeafterload').css('opacity', 0);
})();