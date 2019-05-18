<?php
$app_secret = '8422cf406e7a977482ae97ad23714174';
$app_id = '474355809662556';

require_once __DIR__ . '\vendor\autoload.php';
session_start();

$fb = new Facebook\Facebook([
  'app_id' => $app_id,
  'app_secret' => $app_secret,
  'default_graph_version' => 'v3.0',
  ]);

$helper = $fb->getJavaScriptHelper();

  try {
    $accessToken = $helper->getAccessToken()->getValue();
  } catch(Facebook\Exceptions\FacebookResponseException $e) {
    if(strcmp($e->getErrorType(),"OAuthExceptionerror") ) {
      try {
        $accessToken =  $_SESSION['fb_access_token'];
      } catch (Exception $e) {
        echo 'Session error: ' . $e->getMessage();
        exit;
      }
    } else {
      echo 'Graph returned an error1: ' . $e->getMessage();
      exit;
    }
  } catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
  }

if (! isset($accessToken)) {
  echo 'No cookie set or no OAuth data could be obtained from cookie.';
  exit;
}

$_SESSION['fb_access_token'] = (string) $accessToken;

try {
  // Returns a `Facebook\FacebookResponse` object
  $response = $fb->get('/me?fields=id,name', $accessToken);
  $requestPicture = $fb->get('/me/picture?redirect=false', $accessToken);
  $requestGroups = $fb->get('/me/groups', $accessToken);
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  echo 'Graph returned an error2: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

$user = $response->getGraphUser();
$picture = $requestPicture->getGraphUser();
$groups_edge = $requestGroups->getGraphEdge();
$member = false;
// 178577692220627 is the group id for mtg group
foreach ($groups_edge as $group) {
    //var_dump($group->asArray());
    if($group["id"] == '178577692220627') {
      $member = true;
      break;
    }
}

/*if(!$member) {
  echo "Sorry, you are not a member of mtgUK & Ireland MTG Cards For Trade & Sale :(";
  exit();
}*/

$user_id = $user['id'];
$user_name = $user['name'];
$user_pic_url = $picture['url'];

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

  $check_user_stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
  $check_user_stmt->execute([$user_id]);

  if($check_user_stmt->rowCount() < 1) {
    $add_user_stmt = $pdo->prepare("INSERT INTO members (id, name, picture_url) VALUES (?, ?, ?);");
    $add_user_stmt->execute([$user_id, $user_name, $user_pic_url]);
  }

  $stmt = $pdo->prepare("SELECT * FROM members WHERE name LIKE :name ORDER BY score_total DESC LIMIT 10;");
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
          <tr id="user_row">
          <td><img src="' .$row["picture_url"]. '" alt="" border=3 height=50 width=50></img></td>
          <td id="td_name">'.$row["name"].'</td>
          <td id="score">'.$row["score_total"].'</td>
          <td>
            <table class="table" id="score_table">
              <tr><td id="score_buy">'.$row["score_buy"].'</td></tr>
              <tr><td id="score_sell">'.$row["score_sell"].'</td></tr>
            </table>
          </td>
          <td><img src="add_feedback_button.png" alt="Profile Picture" title="Vouch for '.$row["name"].'" onClick="addFeedback(\''.$row["id"].'\', \''.$row["name"].'\', \''.$user_id.'\', \''.$user_name.'\')" height=20 width=20></img></td>
          </tr>
          <tr id="trade_results">
          <div id="trades_div">
          </div>
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
