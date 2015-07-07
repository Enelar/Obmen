var search = function(query)
{
  phoxy.Log(9, "Search " + query);

  search.Toggle(query.length !== 0);
  phoxy.ApiRequest(["adv/search", query], function(r)
  {
    phoxy.Log(9, "Results", r);
  });
}

search.prepared = false;

search.Prepare = function()
{
  if (search.prepared)
    return; // already done
  search.prepared = "in progress";

  $('#canvas').before("<div id='search'></div>");

  search.prepared = true;
  phoxy.Log(3, "Search subsytem prepared");
}

search.Toggle = function(state)
{
  if (state && !search.Active())
    return search.Activate();
  if (!state && search.Active())
    return search.DeActivate();
}

search.Active = function()
{
  return $('body').hasClass('searching');
}

search.Activate = function()
{
  $('body').addClass('searching');
}

search.DeActivate = function()
{
  $('body').removeClass('searching');
}