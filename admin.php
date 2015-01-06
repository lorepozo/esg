<?php include("admin_util.php") ?>
<!DOCTYPE html>
<html>
<head>
  <title>ESG Administrator</title>
  <link rel="stylesheet" href="resources/bootstrap.min.css">
</head>
<body class="container">
<?php
if (!@$_SERVER['SSL_CLIENT_S_DN_CN']) {
  ?>Please <a href="<?php
    echo 'https://' . $_SERVER[HTTP_HOST] . ':444' . $_SERVER[REQUEST_URI];
  ?>">log in</a> (MIT Certificates Required)
  </body></html><?php
  exit;
}

$esg = json_decode(file_get_contents('esg.json') , true);
$admin = explode("@", $_SERVER['SSL_CLIENT_S_DN_Email']) [0];
if (!in_array($admin, $esg["administrators"])) {
  ?>You are not an administrator and don't have these privileges.
  </body></html><?php
  exit;
}

?>
<h3>Welcome, administrator <?php echo $_SERVER['SSL_CLIENT_S_DN_CN']?>.
</h3><br>
<?php
$file = file_get_contents('users.json');
$users = json_decode($file, true);
if ($_GET["name"]) {
  $names = explode('_', $_GET["name"]);
  $first = ucfirst($names[1]);
  $last = ucfirst($names[0]);
}
foreach($users as $tmpid => $tmpuser) {
  if ((isset($_GET["kerb"]) and $_GET["kerb"] == $tmpuser["kerb"])
   or (isset($_GET["email"]) and $_GET["email"] == $tmpuser["email"])
   or (isset($_GET["name"]) and $first == $tmpuser["first"]
                            and $last == $tmpuser["last"])) {
    $first = $tmpuser["first"];
    $last = $tmpuser["last"];
    $kerb = $tmpuser["kerb"];
    $email = $tmpuser["email"];
    break;
  }
}

if (isset($_GET["email"])) {
  $id = str_replace('=','',base64_encode($_GET["email"]));
  $user = [
    "first" => $first,
    "last" => $last,
    "year" => $esg["year"],
    "kerb" => $_GET["kerb"],
    "email" => $_GET["email"]
  ];

  $newuser = !isset($users[$id]);
  $kerbemailchange = !$newuser and ((isset($_GET["kerb"]) and $users[$id]["kerb"] != $user["kerb"])
                                 or (isset($_GET["email"]) and $users[$id]["email"] != $user["email"]));
  $write = $kerbchange or $newuser;
  $msg = "";
  if ($kerbemailchange) {
    $users[$id]["kerb"] = $user["kerb"];
    $users[$id]["email"] = $user["email"];
    $delta = '{"kerb":"'.$user["kerb"].'","email":"'.$user["email"].'"}';
    $msg = "Information updated for " . $user["last"] . ", " . $user["first"];
  }
  elseif ($newuser) {
    $users[$id] = $user;
    $msg = "Added user " . $last . ", " . $first;
    $delta = sprintf('{"id":"%s","first":"%s","last":"%s","kerb":"%s","email":"%s"}', $id, $first, $last, (string)$user["kerb"], (string)$user["email"]);
  }
  if ($write) {
    $jsonstring = json_encode($users);
    if (!$jsonstring or $jsonstring == "" or $first = "" or $last = "") {
      Print "There was an error and your change couldn't be processed";
    } else {
      db_write($delta, $admin, $first, $last, $id, $jsonstring);
    }
  }
  user_print($users[$id]);
} else {
  usage_print();
}
changelog_print();
?>
</body>
</html>