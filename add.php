<?php
include("admin_util.php");
?>
<!DOCTYPE html>
<html>
<head>
  <title>ESG Administrator</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
</head>
<body class="container">
<?php
admin_login($_SERVER);
admin_post($_POST, $admin);
?>
<h2>Add Applicant</h2>
<form method="post">
  <div class="form-inline">
    <div class="form-group">
      <label class="sr-only" for="first">First Name</label>
      <input type="text" class="form-control" name="first" placeholder="First Name">
    </div>
    <div class="form-group">
      <label class="sr-only" for="last">Last Name</label>
      <input type="text" class="form-control" name="last" placeholder="Last Name">
    </div>
    <div class="form-group">
      <label class="sr-only" for="email">Email Address</label>
      <input type="email" class="form-control" name="email" placeholder="Email Address">
    </div>
    <div class="form-group">
      <label class="sr-only" for="kerb">Kerberos</label>
      <input type="text" class="form-control" name="kerb" placeholder="Kerberos (if applicable)">
    </div>
  <button type="submit" class="btn btn-primary">Add Applicant</button>
</form>
</body>
</html>