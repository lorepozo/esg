<?php
include("admin_util.php");
if(!function_exists("db_write")){
  include("db.php"); 
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>ESG Administrator</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
</head>
<body class="container">
<?php

$admin = admin_login($_SERVER);

$users = admin_post($_POST, $admin);

$esg_globals = db_getglobals();

?>
<h3>Welcome, administrator <?php echo $_SERVER['SSL_CLIENT_S_DN_CN']?>.
</h3><br>

<h2><a href="lottery.php">Lottery</a></h2>

<h2>Applicants</h2>
<?php users_print($users) ?>

<h2>Add Applicant <a href="add.php">Solo link</a></h2>
<form method="post">
  <div class="form-inline">
    <div class="form-group">
      <label class="sr-only" for="apptype">Application Type</label>
      <select name="apptype" class="form-control">
        <?php foreach($esg_globals["apptypes"] as $apptype) { ?>
          <option value="<?php echo $apptype ?>"><?php echo $apptype ?></option>
        <?php } ?>
      </select>
    </div>
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
    <div class="form-group">
      <label class="sr-only" for="interviewer">Interviewer</label>
      <input type="text" class="form-control" name="interviewer" placeholder="Interviewer">
    </div>
  </div><br>
  <div class="form-group">
    <label class="sr-only" for="comments">Comments</label>
    <textarea class="form-control" name="comments" placeholder="Comments" rows=2></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Add Applicant</button>
</form>

<h2>Changelog</h2>
<?php changelog_print() ?>

</body>
</html>