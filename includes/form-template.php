<?php 
  include_once 'woocommerce-4all-gateway.php';

  $gateway_4all = new woocommerce_4all_gateway($this->gatewaySettings);
  $paymentMethods = $gateway_4all->getPaymentMethods();

  $brandsList = [];

  $brands = [
    "https://4all.com/brands/visa.png", 
    "https://4all.com/brands/mastercard.png",
    "https://4all.com/brands/diners.png", 
    "https://4all.com/brands/elo.png", 
    "https://4all.com/brands/amex.png", 
    "https://4all.com/brands/discover.png", 
    "https://4all.com/brands/aura.png", 
    "https://4all.com/brands/jcb.png", 
    "https://4all.com/brands/hipercard.png", 
    "https://4all.com/brands/maestro.png", 
    "https://4all.com/brands/4-all.png", 
    "https://4all.com/brands/ticket.png"
  ];

  for ($i=0; $i < sizeof($paymentMethods["brands"]); $i++) { 
    array_push($brandsList, $paymentMethods["brands"][$i]["brandId"]);
  }

  $brandsListString = implode(";", $brandsList);
?>

<p class="form-row">
  <label>Name of the buyer (same as the card)</label>
  <input type="text" name="cardholderName" maxlength="200">
</p>
<p class="form-row">
  <label>Card number</label>
  <input type="text" name="cardNumber" maxlength="200">
</p>
<input type="hidden" id="brandsList" value="<?php echo $brandsListString; ?>">
<div class='form-row-brands'>
  <?php 
    for ($i=0; $i < sizeof($brandsList); $i++) { 
      echo '<img src="' . $brands[$brandsList[$i]] . '" id="brand-' . $brandsList[$i] . '" class="">';
    }
  ?>
</div>
<p class="form-row">
  <label>Expiration date</label>
  <input type="text" placeholder="MM/YY" name="expirationDate" maxlength="200">
</p>
<p class="form-row">
  <label>Security code</label>
  <input type="text" name="securityCode" maxlength="200">
</p>

<p class="form-row form-row-installment">
  <label>Installment</label>
  <select name="installment">

  <?php
    $minInstallment = $paymentMethods['resume']['minInstallments'];
    $maxInstallments = $paymentMethods['resume']['maxInstallments'];
    
    for (;$minInstallment<=$maxInstallments;$minInstallment++) {
      echo '<option value="'.$minInstallment.'">'.$minInstallment.'</option>';
    }

  ?>
  </select>
</p>