var $ = jQuery;

(function($) {
  var $context = $('#order_review');

  function createMask(string) {
    return string.replace(/\D/g, '').replace(/(\d{2})(\d)/, '$1/$2').replace(/(\d{2})(\d)/, '$1/$2').replace(/(\d{2})(\d{2})$/, '$1$2');
  }

  function destroyMask(string) {
    return string.replace(/\D/g, '').substring(0, 3);
  }

  function checkCardType(number, $brands) {


    var id = 1;   

    $brands.find('#brand-' + id).addClass('active').siblings().removeClass('active');
  }

  var expirationSelector = '.payment_method_4all [name=expirationDate]';
  var cardNumberSelector = '.payment_method_4all [name=cardNumber]';

  $context.on('keypress', expirationSelector, function(event) {
    var v = destroyMask(event.target.value);
    event.target.value = createMask(v);
  });

  $context.on('keypress', cardNumberSelector, function (event) {
    var v = event.target.value;
    checkCardType(v, $('.form-row-brands'));
  });

}(jQuery));
