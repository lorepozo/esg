<?php
if (!@$_SERVER['SSL_CLIENT_S_DN_CN']) {
	header('Location: https://'.$_SERVER[HTTP_HOST].':444'.$_SERVER[REQUEST_URI]);
}

$esg = json_decode(file_get_contents('esg.json') , true);
$admin = explode("@", $_SERVER['SSL_CLIENT_S_DN_Email']) [0];
if (!in_array($admin, $esg["admins"])) {
  echo "You are not administrator and don't have these privileges.";
  exit;
}

try {
  $c = file_get_contents('records/'.$_GET["q"].'.json');
  echo $c;
}
catch (Exception $e) {
  echo "Could not open record.";
}
?>