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

$output = '';
$fbid = '';
if(isset($_POST["query"])) {
  $fbid = $_POST["query"];
}
  $stmt = $pdo->prepare("SELECT * FROM trades WHERE seller_id = ? OR buyer_id = ?");
  $stmt->execute(array($fbid, $fbid));
  if($stmt->rowCount() > 0) {
    $output .= '
            <td colspan="5">
              <table class="table" id="trades_table">
              <tr>
                <th>Buy or Sell</th>
                <th>Who Vouched</th>
                <th>Date</th>
              </tr>';
    while ($row = $stmt->fetch()) {

      // Work out if it was buy or sell
      $sell = false;
      if(strcmp($row['seller_id'], $fbid)) {
        $buyorsell = 'sell';
        $sell = true;
      } else {
        $buyorsell = 'buy';
      }

      // Find who vouched
      if($sell) {
          $vouchedby = $row['seller_name'];
      } else {
          $vouchedby = $row['buyer_name'];
      }

      $output .= '
      <tr>
        <td>'.$buyorsell.'</td>
        <td>'.$vouchedby.'</td>
        <td>'.$row["date"].'</td>
      </tr>
      ';
    }
    $output .= '</table> </td>';
  	echo $output;
  } else {
    echo '<td colspan="5"> No results found </td>';
  }
?>
