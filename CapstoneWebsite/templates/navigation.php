<style>
html, body {
    height:100%;
} 
body{
  background-color: white;
  background-image: url(images/bxf6-hero.jpg);
  background-repeat: no-repeat;
	background-attachment: fixed;
  background-size: cover;
  font-family: Verdana, arial, sans-serif;

}
.navigation {
  background-color: #000000;
  border: 1px solid #A76358;
  text-align: center;
}
.navigation ul {
  list-style: none;
  display: inline-block;
}
.navigation ul li {
  float: left;
  margin: 0 20px;
}
.navigation ul li a {
  color: #6C4818;
  text-decoration: none;
  padding: 14px 16px;
}
.navigation li a:hover {
  background-color: #714307;
  color: white;
}
.content{
  max-width: 1200px;
  margin: auto;
  background: #BB8D50;
}
.link{
  
}
.collapsible {
  background-color: #BB8D50;
  color: Black;
  cursor: pointer;
  padding: 18px;
  width: 100%;
  border: none;
  text-align: left;
  outline: none;
  font-size: 20px;
}

.active, .collapsible:hover {
  background-color: #714307;
  color: white;
}

.collapsible_div {
  padding: 0 18px;
  display: none;
  overflow: hidden;
  background-color: #f1f1f1;
}
</style>

<nav class="navigation">
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="menu.php">Menu</a></li>
        <li><a href="faq.php">FAQ</a></li>
        <li><a href="contact.php">Contact Us</a></li>
    </ul>
</nav>