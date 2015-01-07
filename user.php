<?php include("admin_util.php"); ?>
<!DOCTYPE html>
<html>
<head>
  <title>User info</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
  <script>
  </script>
</head>
<body class="container">
<?php admin_login($_SERVER) ?>
<div style="height:20px;width:100%;"></div>
<?php
if (!isset($_GET) or !isset($_GET["id"])) {
  echo "An id must be provided to get user information</body></html>";
  exit;
}
$esg = json_decode(file_get_contents('esg.json'), true);
$users = json_decode(file_get_contents('users.json'), true)[$esg["year"]];
$id = $_GET["id"];
if (!isset($users[$id])) {
  echo "The user does not exist</body></html>";
  exit;
}

$path = preg_replace('/\/[^\/]*$/', '/', $_SERVER["PHP_SELF"]);
$url = "http://".$_SERVER["SERVER_NAME"].$path."app.php?id=".$id;
?>
<h2>Application ID: <?php echo $id ?></h2>

<h3><a href="app.php?id=<?php echo $id ?>"><em>Edit this user</em></a></h3><br>

<h4>Link for applicant: <a href="<?php echo $url ?>"><?php echo $url ?></a></h4>

<?php user_print($users[$id]) ?>

</body>
</html>