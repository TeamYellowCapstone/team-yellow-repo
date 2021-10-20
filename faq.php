<?php
    require "scripts/php/menuPageLoad.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        require "templates/head.php";
    ?>
    <script type="text/javascript" src="scripts/faq.js" defer></script>
    <title>FAQ</title>
</head>

  <body>

    <?php
      require "templates/navigation.php";
    ?>
    
    <div class="content">
    <h1 class="centerText">Frequently Asked Questions (FAQ)</h1>


      <button type="button" class="collapsible_faq">When are our operating hours?</button>
        <div class="collapsible__faq_div">
          <ul>
            <li>Mon:7:00 AM - 2:00 PM</li>
            <li>Tue:7:00 AM - 2:00 PM</li>
            <li>Wed:7:00 AM - 2:00 PM</li>
            <li>Thu:7:00 AM - 2:00 PM</li>
            <li>Fri:7:00 AM - 2:00 PM</li>
            <li>Sat:7:00 AM - 2:00 PM</li>
            <li>Sun: CLOSED</li>
          </ul>
            <p>OPEN FOR DINE-IN </p>
            <p>*CLOSED CHRISTMAS DAY, THANKSGIVING DAY, LABOR DAY, 
            <br>INDEPENDANCE DAY, MEMORIAL DAY, NEW YEAR'S DAY</p>
        </div>
      <hr>

      <button type="button" class="collapsible_faq">Who's our coffee supplier?</button>
        <div class="collapsible__faq_div">
          <p>We work with Ohio based suppliers in order to provide the best quality coffee beans and products to our customers.</p>
          <p>Here are a list of our suppliers:</p>
            <ul>
              <li>Phoenix Coffee Company	<br>http://www.phoenixcoffee.com	<br>3000 Bridge Ave, Cleveland, OH 44113 <br>(216) 400-7901</li>
              <br>
              <li>Village Coffee Company		<br>http://www.villagecoffeeco.com	<br>132 E Broadway, Granville, OH 43023 <br>(740) 587-4940</li>
              <br>
              <li>Short North Coffee House		<br>http://www.shortnorthcoffee.com	<br>1203 N High St, Columbus, OH 43201 <br>(614) 947-1077</li>
            </ul>
        </div>
      <hr>

      <button type="button" class="collapsible_faq">What are the origins of Love You A Latte store?</button>
        <div class="collapsible__faq_div">
          <p>After a vacation visit to Columbia our founder Jim Bob, a Botanist, crossbreed the Colombian Arabica and Italian Robusta beans to give a rich smoother, sweeter earther taste with hits of honey and chocolate. After his retirement as a Botanist Jim and his wife Sarah Bob, decided to open a coffee shop named after their golden retriever Love.</p>
        </div>
      <hr>

    </div>
    <div class="background-wrap">
            
    </div>
    
  </body>
</html>
