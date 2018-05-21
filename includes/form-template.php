<?php 
  include_once 'woocommerce-4all-gateway.php';

  $gateway_4all = new woocommerce_4all_gateway($this->gatewaySettings);
  $paymentMethods = $gateway_4all->getPaymentMethods();
  $nonePaymentMethods = true; //variavel para o caso do merchant ainda nao ter nenhuma affiliation cadastrada

  if ($paymentMethods) {
    $brandsList = [];

    //a ordem das imagens esta de acordo com os id's retornados do gateway correspondendo a imagem
    $brands = [
      "https://4all.com/brands/visa.png", 
      "https://4all.com/brands/mastercard.png",
      "https://4all.com/brands/diners.png", 
      "https://4all.com/brands/elo.png", 
      "https://4all.com/brands/amex.png", 
      "https://4all.com/brands/discover.png", 
      "https://4all.com/brands/aura.png", 
      "https://4all.com/brands/jcb.png", 
      "https://4all.com/brands/hipercard.png"
    ];

    for ($i=0; $i < sizeof($paymentMethods["brands"]); $i++) { 
      //o -1 é necessario, pois o gateway retorna os id's de 1 para cima
      array_push($brandsList, $paymentMethods["brands"][$i]["brandId"] -1);
    }

    $brandsListString = implode(";", $brandsList);
  } else {
    $nonePaymentMethods = true;
  }

?>

<p class="form-row">
  <label>Name of the buyer (same as the card)</label>
  <input type="text" name="cardholderName" maxlength="200">
</p>
<p class="form-row">
  <label>Card number</label>
  <input type="text" name="cardNumber" maxlength="200" <?php if ($nonePaymentMethods) { echo 'class="disabled" disabled'; } ?>>
</p>
<input type="hidden" id="brandsList" value="<?php echo $brandsListString; ?>">
<div class='form-row-brands'>
  <?php 
    if (!$nonePaymentMethods) {
      for ($i=0; $i < sizeof($brandsList); $i++) { 
        echo '<img src="' . $brands[$brandsList[$i]] . '" id="brand-' . $brandsList[$i] . '" class="">';
      }
    } else {
      echo '<p>Não há formas de pagamento cadastrados</p>';
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