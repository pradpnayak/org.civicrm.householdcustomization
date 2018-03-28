CRM.$(function($) {
  var paymentLinks = CRM.vars.paymentLinks;
  var link = '';
  $.each(paymentLinks, function(key, value) {
    link = '<a href="' + value + '">Make Payment</a></br>';
    $('.crm-dashboard-civipledge tr#rowid' + key + ' td:last-child').prepend(link);
  });
});
