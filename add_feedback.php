<?php
$host = '127.0.0.1';
$db   = 'test';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
  $pdo = new PDO($dsn, $user, $pass, $opt);
} catch(PDOException $ex) {
    die(json_encode(array('outcome' => false, 'message' => 'Unable to connect')));
}

try {
  $old_trade_date = $_POST["trade_date"];
  $trade_date = date("Y-m-d", strtotime(str_replace('/', '-', $old_trade_date)));
  $buy_or_sell = $_POST["buy_or_sell"];

  $voucher_name = $_POST["my_name"];
  $voucher_id = $_POST["my_id"];
  $vouchee_id = $_POST["id"];
  $vouchee_name = $_POST["name"];
  $vouchee_sale = strcmp($buy_or_sell, 'bought_from') == 0;
} catch(exception $e) {
  alert("Oops! Something went wrong. Error Code 18.");
  exit;
}

  $output = '';
  $stmt = $pdo->prepare("INSERT INTO vouches (date, voucher_id, voucher_name, vouchee_id, vouchee_name, vouchee_sale) VALUES (?, ?, ?, ?, ?, ?);");
  $stmt->execute([str_replace('/', '-', $trade_date), $voucher_id, $voucher_name, $vouchee_id, $vouchee_name, $vouchee_sale]);

  if($vouchee_sale) {
    $stmt = $pdo->prepare("UPDATE members SET score_total = score_total + 1, score_sell = score_sell + 1 WHERE id = ?");
    $stmt->execute([$vouchee_id]);
  } else {
    $stmt = $pdo->prepare("UPDATE members SET score_total = score_total + 1, score_buy = score_buy + 1 WHERE id = ?");
    $stmt->execute([$vouchee_id]);
  }
?>
