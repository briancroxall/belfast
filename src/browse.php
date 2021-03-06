<?php

include_once("config.php");
include_once("lib/xmlDbConnection.class.php");
include("common_functions.php");


$exist_args{"debug"} = true;
$xmldb = new xmlDbConnection($exist_args);

$xsl    = "xslt/browse.xsl";
$header_xsl = "xslt/teiheader-dc.xsl";
$header_xsl2 = "xslt/dc-htmldc.xsl";
$header_xsl3 = "xslt/poem-dc.xsl";

$id = $_REQUEST["id"];
$doctitle = $_REQUEST["doctitle"];


// construct xquery
if ($id) {
if (empty($doctitle)) {
$for = 'declare namespace tei="http://www.tei-c.org/ns/1.0";
for $a in /tei:TEI/tei:text/tei:group/tei:group[@xml:id="' . "$id" . '"]';
$let = 'let $hdr := root($a)/tei:TEI/tei:teiHeader';
$return = 'return 
<TEI>
{$hdr}
<group>
{$a}
</group>
</TEI>';
    }

elseif ($doctitle) {
$for = 'for $b in /tei:TEI/tei:text/tei:group/tei:group[@xml:id="' . "$id" . '"]';
$let = 'let $a := $b/tei:text[@xml:id &= "' . "$doctitle" . '"]
let $hdr := root($a)/tei:TEI/tei:teiHeader
let $date := $a/ancestor::tei:group/tei:docDate';
$return = 'return 
<TEI>
{$hdr}
{$date}
<group>
{$b/@id}
{$a}
</group>
</TEI>';    }
}



$xquery = "$for $let $return";

// run the query
$xmldb->xquery($xquery); 

//$xmldb->xslTransform($header_xsl);
//$xmldb->xslTransformResult($header_xsl2);
if ($id) {
if (empty($doctitle)) {
$xmldb->xslBind($header_xsl);
$xmldb->xslBind($header_xsl2);
$xmldb->transform();
    }
elseif ($doctitle) {
$xmldb->xslBind($header_xsl3);
$xmldb->xslBind($header_xsl2);
$xmldb->transform();
    }
}    
html_head("Workshop Poems");
$xmldb->printResult();
print "</head>";
include("web/xml/searchhead.xml");
include("web/xml/uppernav.xml");

print '<div class="poemcontent"> 
          <h2>Workshop Poems</h2>';



 $xsl_params = array ('doctitle' => $doctitle);
  $xmldb->xslTransform($xsl, $xsl_params);
  $xmldb->printResult();
  
  
print "<hr>";  
print "</div>";  
include("web/xml/lowernav.xml"); 
include_once("web/xml/google-trackbelfast.xml");
?>


</body>
</html>

