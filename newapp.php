<?php include("admin_util.php"); ?>
<!DOCTYPE html>
<html>
<head>
  <title>ESG Staff: Add Application</title>
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

if ($_POST) {
  $user = $_POST;
  $id = str_replace('=','',base64_encode($user["email"]));
  $file = file_get_contents('users.json');
  $users = json_decode($file, true);
  if (isset($users[$id])) { ?>
    <div class="alert alert-warning">
      A user with that email already exists: 
      <a href="user.php?id=<?php echo $id ?>"><?php echo $users[$id]["first"]." ".$users[$id]["last"] ?></a>
    </div>
    <?php
  }
  if (!isset($user["email"]) and isset($user["kerb"])) {
    $user["email"] = $user["kerb"]."@mit.edu";
  }
  if (!isset($user["email"]) or !isset($user["first"]) or !isset($user["last"])) { ?>
    <div class="alert alert-danger">
      Please at least provide a first name, last name, and email address (or kerberos).
    </div>
    <?php
  }
  $users[$id] = $user;
  $jsonstring = json_encode($users);
  $user["id"] = $id;
  $delta = json_encode($user);
  db_write($delta, $admin, $user["first"], $user["last"], $id, $jsonstring); ?>
  <div class="alert alert-success">
    The user 
    <a href="user.php?id=<?php echo $id ?>"><?php echo $user["first"]." ".$user["last"] ?></a>
    has been added!
  </div>
  <?php
}

?>
<h3>Welcome, staff member <?php echo $_SERVER['SSL_CLIENT_S_DN_CN']?>.
</h3><br>
<form role="form" name="form" id="form" method="post">
  <label for="first">First Name</label><input type="text" name="first">
  <label for="last">Last Name</label><input type="text" name="last">
  <label for="email">Email</label><input type="text" name="email">
  <label for="kerb">Kerberos</label><input type="text" name="kerb" placeholder="(if applicable)">
  <button type="button" class="btn btn-primary" onclick="form.submit()">Submit</button>
</form>
</body>
</html>