<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ</title>
<?php
    require "templates/navigation.php";
?>
<script>
var coll = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var content = this.nextElementSibling;
    if (content.style.display === "block") {
      content.style.display = "none";
    } else {
      content.style.display = "block";
    }
  });
}
</script>
</head>
<body>

    <div class="content">
    <h1 class="centerText">Frequently Asked Questions (FAQ)</h1>





<button type="button" class="collapsible">When are our operating hours?</button>
<div class="collapsible_div">
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

<button type="button" class="collapsible">Who's our coffee supplier</button>
<div class="collapsible_div">
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

<button type="button" class="collapsible">What are the origins of Love You A Latte store</button>
<div class="collapsible_div">
  <p>After a vacation visit to Columbia our founder Jim Bob, a Botanist, crossbreed the Colombian Arabica and Italian Robusta beans to give a rich smoother, sweeter earther taste with hits of honey and chocolate. After his retirement as a Botanist Jim and his wife Sarah Bob, decided to open a coffee shop named after their golden retriever love.</p>
</div>
<hr>

</div>

    
</body>
</html>
