<?php
include 'private.php';

header("Content-type: application/json");
session_start();

$debug = true;

if($debug) {
  header("Access-Control-Allow-Origin: http://localhost:8000");
  header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
  header("Access-Control-Allow-Credentials: true");
}

function exceptionHandler($e) {
  echo '{"error":{"msg":' . json_encode($e->getMessage()) . '}}';
  exit;
}

set_exception_handler('exceptionHandler');

if (empty($_ENV['DATABASE_SERVER'])) {
  $server = "mysql.5tephen.com";
} else {
  $server = $_ENV['DATABASE_SERVER'];
}

$dbname = 'mooncolonyorg_chaingang';

$db = new PDO('mysql:host=' . $server . ";dbname=$dbname", $dbusername, $dbpassword, array( PDO::ATTR_PERSISTENT => false));
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function readChain($count, $offset) {
  global $db;

  $query = <<<'EOD'
    SELECT *
    FROM chain
    ORDER BY id DESC
    LIMIT :offset, :count
EOD;

  $q = $db->prepare($query);
  $q->bindValue(':count', $count, PDO::PARAM_INT);
  $q->bindValue(':offset', $offset, PDO::PARAM_INT);
  $q->execute();

  return $q->fetchAll(PDO::FETCH_ASSOC);
}

function readChainSince($count, $since) {
  global $db;

  $query = <<<'EOD'
    SELECT *
    FROM chain
    ORDER BY id DESC
    WHERE id > :since
    LIMIT :count
EOD;

  $q = $db->prepare($query);
  $q->bindValue(':count', $count, PDO::PARAM_INT);
  $q->bindValue(':since', $offset, PDO::PARAM_INT);
  $q->execute();

  return $q->fetchAll(PDO::FETCH_ASSOC);
}

function addLink($newPhrase, $currentPhrase, $color) {
  global $db;

  $q = $db->prepare('INSERT INTO chain(phrase, prev_phrase, datetime, color) VALUES(:phrase, :prev_phrase, NOW(), :color)');
  $q->bindValue(':phrase', $newPhrase, PDO::PARAM_STR);
  $q->bindValue(':prev_phrase', $currentPhrase, PDO::PARAM_STR);
  $q->bindValue(':color', $color, PDO::PARAM_STR);
  $q->execute();

  return $db->lastInsertId();
}

/**
  Misc. Functions
*/

function resetdb() {
  exec("cat create_db.sql | sqlite3 panzoomtag.db");
  return "OK";
}

function requireGetParameter($parameter) {
  if(empty($_GET["newPhrase"])) {
    throw new Exception ("Parameter $parameter empty.");
  }
  return $_GET[$parameter];
}

/**
  Input Handler
*/

$response = array();

if(isset($_GET["action"])) {
  switch($_GET["action"]) {
    case "readchain":
      $offset = $_GET["offset"] ? intval($_GET["offset"]) : 0;
      $count = $_GET["count"] ? intval($_GET["count"]) : 100;

      $response["count"] = $count;
      $response["offset"] = $offset;
      $response["chain"] = readChain($count, $offset);
      break;
    case "readChainSince":
      $since = requireGetParameter("since");
      $count = $_GET["count"] ? intval($_GET["count"]) : 100;

      $response["since"] = $since;
      $response["count"] = $count;
      $response["chain"] = readChainSince($count, $since);
      break;
    case "addLink":
      $newPhrase = requireGetParameter("newPhrase");
      $currentPhrase = requireGetParameter("currentPhrase");
      $color = requireGetParameter("color");

      $response["newLink"] = addLink($newPhrase, $currentPhrase, $color);
      break;
    case "reset":
      $response = resetdb();
      break;
    case "info":
      phpinfo();
      break;
    case "session_info":
      print_r($_SESSION);
      exit();
      $response = $_SESSION['userId'];
      break;
  }
}

echo json_encode($response);
