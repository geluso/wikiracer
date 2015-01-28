<?php
header("Content-type: application/json");

$debug = true;

if($debug) {
  header("Access-Control-Allow-Origin: http://localhost:8000");
  header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
  header("Access-Control-Allow-Credentials: true");
}

$DIR = "/home/moonmayor/5tephen.com/wikiracer/pages";
$WIKI_PREFIX = "http://en.wikipedia.org/wiki";
  
function requireGetParameter($parameter) {
  if(empty($_GET[$parameter])) {
    throw new Exception ("Parameter $parameter empty.");
  }
  return $_GET[$parameter];
}

/**
  Input Handler
*/

$response = array();

$article = requireGetParameter("article");
$url = "$WIKI_PREFIX/$article";
$filepath = "$DIR/$article.html";

system("wget $url -O $filepath");
echo system("python get_links.py $filepath" . " 2>&1");

echo json_encode($response);

?>
