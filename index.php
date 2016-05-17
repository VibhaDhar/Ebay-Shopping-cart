<!DOCTYPE html>
<!-- 
  Student Name: Vibha Dhar
  UTA Student ID: 1001095020
  
  -->
<?php
session_start();
?>
<html>
<head><title>Buy Products</title></head>
<body>
<?php
$totalVal = 0;
// Below:To empty cart by deleting values stored in session.
if ($_GET['clear'] == "1") {
    echo "<center>Your cart is now empty!</center>";
    session_unset();
}
// Below:To delete specific item from the cart.
if (isset($_GET['delete'])) {
    $key = array_search($_GET['delete'], $_SESSION['names']);
    if ($key !== false)
        unset($_SESSION['names'][$key]);
}

$buyItemId = $_GET['buy'];

//var_dump($_SESSION['names']);
// To show cart items only if items are purchased
if ((!empty($_SESSION['names']) || !(empty($buyItemId)))) {
    $list              = array();
    $list              = $_SESSION['names'];
    //$_SESSION['names'] = array();
    $list[]            = $buyItemId;
    $_SESSION['names'] = $list;
    echo "<center><h2>Your cart:</h2></center>";
    echo "<table align=center border=1>";
    foreach ($_SESSION['xmllist'] as $xmlstr) {
        
        
        //$xmlstr   = $_SESSION['xml'];
        $xml      = new SimpleXMLElement($xmlstr);
        $list     = array();
        $list     = $_SESSION['names'];
        $category = $xml->categories->category;
        
        foreach ($category->items->children() as $product) {
            foreach ($list as $sessionBuyId) {
                //echo $sessionBuyId . "   ";
                if ($product['id'] == $sessionBuyId) {
                    
                    echo "<tr>";
                    echo "<td>" . "<img src=\"" . $product->images->image->sourceURL . "\"/></a></td>";
                    echo "<td>" . $product->name . "</td>";
                    echo "<td>" . $product->minPrice . "</td>";
                    echo "<td><a href=\"index.php?delete=" . $product['id'] . "\">Delete</a>";
                    echo "</tr>";
                    $totalVal = $totalVal + (int) $product->minPrice;
                    break;
                }
            }
        }
    }
    echo "</table><br/>";
    echo "<center>Total value: " . $totalVal . "$";
    echo "<br/>";
    
    echo "<center>\n";
    echo "<form action=\"index.php\" method=\"GET\">\n";
    echo "<input type=\"hidden\" name=\"clear\" value=\"1\"/>\n";
    echo "<input type=\"submit\" value=\"Empty Basket\"/>\n";
    echo "</form>\n";	
    echo "</center>\n";
	
	echo "<br/><br/><center>\n";
    echo "<form action=\"http://vibha12345-env.elasticbeanstalk.com\" method=\"GET\">\n";		
    echo "<input type=\"hidden\" name=\"totalVal\" value=\"".$totalVal."\"/>\n";
    echo "<input type=\"submit\" value=\"Checkout and Email\"/>\n";
    echo "</form>\n";	
    echo "</center>\n";
}

?>
<?php
?>
<!-- To display the list of category -->
<form action="<?php
$_SERVER["PHP_SELF"];
?>" method="GET">
<?php
$xmlstr        = file_get_contents('http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/CategoryTree?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&visitorUserAgent&visitorIPAddress&trackingId=7000610&categoryId=72&showAllDescendants=true');
$xml           = new SimpleXMLElement($xmlstr);
$root_category = $xml->category;
echo "<fieldset><legend>Find products:</legend>
<label>Category: <select name=\"category\">";
echo "<option value=\"" . $root_category['id'] . "\">" . $root_category->name . "</option>";
echo "<optgroup label=\"" . $root_category->name . ":\">";
foreach ($root_category->categories->category as $subcategory) {
    echo "<option value=\"" . $subcategory['id'] . "\">" . $subcategory->name . "</option>";
    echo "<optgroup label=\"" . $subcategory->name . ":\">";
    foreach ($subcategory->categories->category as $subsubcategory) {
        echo "<option value=\"" . $subsubcategory['id'] . "\">" . $subsubcategory->name . "</option>";
    }
    echo "</optgroup>";
}
echo "</optgroup></select></label>";
echo "<label>   Search keywords: <input type=\"text\" name=\"search\"/><label>
<input type=\"submit\" value=\"Search\"/></fieldset>";
?>

</form>

<?php
error_reporting(E_All);
ini_set('display_errors', 'On');

// To retrieve the result list when a keyword is searched.
global $search;
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search     = $_GET["search"];
    $category   = $_GET["category"];
    $website    = 'http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/GeneralSearch?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&trackingId=7000610&visitorUserAgent=&visitorIPAddress=&keyword=' . $search . '&category=' . $category . '&numItems=20';
    $xmlstr     = file_get_contents($website);
    $xml        = new SimpleXMLElement($xmlstr);
    $searchList = array();
    $searchList = $_SESSION['search'];
    
    //var_dump($_SESSION['search']);
    global $test;
    $test = true;
    foreach ($_SESSION['search'] as $search1) {
        //echo $search1;
        if ((string) $search == (string) $search1) {            
            $test = false;
        }
    }
    $searchList[]       = $search;
    $searchList         = array_unique($searchList);
    $_SESSION['search'] = $searchList;
    
    if ($test) {
        $xmllist             = array();
        $xmllist             = $_SESSION['xmllist'];
        $xmllist[]           = $xml->asXML();
        $_SESSION['xmllist'] = $xmllist;
    }
    
    echo "<table align=center border=\"1\"";
    $category = $xml->categories->category;
    foreach ($category->items->product as $product) {
        echo "<tr>";
        echo "<td><a href=\"index.php?buy=" . $product['id'] . "\">" . "<img src=\"" . $product->images->image->sourceURL . "\"/></a></td>";
        echo "<td>" . $product->name . "</td>";
        echo "<td align=right>" . $product->minPrice . "</td>";
        echo "<td>" . $product->fullDescription . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>


</body>
</html>
