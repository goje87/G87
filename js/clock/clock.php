var g = {};

g.clock = 
{
  date: null,
  
  init: function()
  {
    g.clock.date = new Date();
    g.clock.date.setUTCDate(<?php echo date('d', time()); ?>);
    g.clock.date.setUTCMonth(<?php echo date('n', time()); ?>);
    g.clock.date.setUTCFullYear(<?php echo date('Y', time()); ?>);
    g.clock.date.setUTCHours(<?php echo date('H', time()); ?>);
    g.clock.date.setUTCMinutes(<?php echo date('i', time()); ?>);
    g.clock.date.setUTCSeconds(<?php echo date('s', time()); ?>);
  }
}

$(function()
{
  g.clock.init();
});