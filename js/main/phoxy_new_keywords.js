function PatchPhoxy(originalfunction, newfunction)
{
  var hooker = phoxy[originalfunction];
  phoxy[originalfunction] = function()
  {
    newfunction.apply(hooker, arguments);
  }
}

// style keyword
PatchPhoxy('ApiAnswer', function()
{
  if (typeof arguments[0]['style'] !== 'undefined')
  {
    var style = arguments[0]['style'];
    if (typeof style === 'string')
      style = [style];

    for (var k in style)
      if (!$('link[href="' + style[k] + '"]').size())
        $('head').append("<link rel='stylesheet' href='" + style[k] + "' />")
  }

  this.apply(phoxy, arguments);
})