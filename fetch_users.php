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
$search = '';
if(isset($_POST["query"])) {
  $search = $_POST["query"];
}
  $stmt = $pdo->prepare("SELECT * FROM members WHERE name LIKE :name");
  $stmt->execute(array(':name' => '%'.$search.'%'));
  if($stmt->rowCount() > 0) {
    $output .= '<div class="table-responsive">
        <table class="table bordered" id="users_table">
          <tr>
            <th>Picture</th>
            <th>Name</th>
            <th id="score_header" colspan="2">Score</th>
            <th></th>
          </tr>';
    while ($row = $stmt->fetch()) {
      $output .= '
      <tr>
        <table class="table bordered" id="user_table" fbid="'.$row["id"].'">
          <tr>
          <td><img src="placeholder_profile.png" alt="" border=3 height=50 width=50></img></td>
          <td id="td_name">'.$row["name"].'</td>
          <td id="score">'.$row["score_total"].'</td>
          <td>
            <table class="table" id="score_table">
              <tr><td id="score_buy">'.$row["score_buy"].'</td></tr>
              <tr><td id="score_sell">'.$row["score_sell"].'</td></tr>
            </table>
          </td>
          <td><img src="add_feedback_button.png" alt="Profile Picture" title="Vouch for '.$row["name"].'" height=20 width=20></img></td>
          </tr>
          <tr id="trade_results">
          </tr>
        </table>
      </tr>
      ';
    }
    $output .= ' </table>';
  	echo $output;
  } else {
    echo 'No results found';
  }
?>
