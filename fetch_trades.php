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
  $stmt = $pdo->prepare("SELECT * FROM vouches WHERE vouchee_id = ?;");
  $stmt->execute([$fbid]);
  if($stmt->rowCount() > 0) {
    $output .= '
            <td colspan="5">
            <div id="trades_div">
              <table class="table" id="trades_table">
              <tr>
                <th>Bought or Sold</th>
                <th>Who Vouched</th>
                <th>Date</th>
              </tr>';
    while ($row = $stmt->fetch()) {

      // Work out if it was buy or sell
      if($row['vouchee_sale']) {
        $buyorsell = 'Sold';
      } else {
        $buyorsell = 'Bought';
      }

      // Find who vouched
      $vouchedby = $row['voucher_name'];

      $output .= '
      <tr>
        <td>'.$buyorsell.'</td>
        <td>'.$vouchedby.'</td>
        <td>'.$newDate = date("d-m-Y", strtotime($row["date"])).'</td>
      </tr>
      ';
    }
    $output .= '</table></div></td>';
  	echo $output;
  } else {
    echo '<td colspan="5"> No results found </td>';
  }
?>
