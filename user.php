<?php include("admin_util.php"); ?>
<!DOCTYPE html>
<html>
<head>
  <title>User info</title>
  <link rel="stylesheet" href="resources/bootstrap.min.css">
  <script>
  </script>
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
if (!in_array($admin, $esg["staff"])) {
  ?>You are not staff and don't have these privileges.
  </body></html><?php
  exit;
}

?>
<h3>Welcome, staff member <?php echo $_SERVER['SSL_CLIENT_S_DN_CN']?>.
</h3><br>

<?php
if (!isset($_GET) or !isset($_GET["id"])) {
  echo "An id must be provided to get user information</body></html>";
  exit;
}
$file = file_get_contents('users.json');
$users = json_decode($file, true);
$id = $_GET["id"];
if (!isset($users[$id])) {
  echo "The user does not exist</body></html>";
  exit;
}
user_print($users[$id]);
?>

</body>
</html>