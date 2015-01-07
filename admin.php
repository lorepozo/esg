<?php include("admin_util.php") ?>
<!DOCTYPE html>
<html>
<head>
  <title>ESG Administrator</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
</head>
<body class="container">
<input type="checkbox" title="Applicant View"
  onclick="var c=this.checked;[].forEach.call(document.querySelectorAll('.notapplicant'),function(e){e.style.display=c?'none':''})">
  <span class="notapplicant"> Applicant View</span>
<div class="notapplicant">
<?php

$admin = admin_login($_SERVER);

$users = admin_post($_POST, $admin);

$esg = json_decode(file_get_contents('esg.json') , true);

?>
<h3>Welcome, administrator <?php echo $_SERVER['SSL_CLIENT_S_DN_CN']?>.
</h3><br>

<h2><a href="lottery.php">Lottery</a></h2>

<h2>Applicants</h2>
<?php users_print($esg, $users) ?>

</div>

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
    <div class="form-group notapplicant">
      <label class="sr-only" for="interviewer">Interviewer</label>
      <input type="text" class="form-control" name="interviewer" placeholder="Interviewer">
    </div>
  </div><br>
  <div class="form-group notapplicant">
    <label class="sr-only" for="comments">Comments</label>
    <textarea class="form-control" name="comments" placeholder="Comments" rows=2></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Add Applicant</button>
</form>

<div class="notapplicant">

<h2>Changelog</h2>
<?php changelog_print() ?>

</div>

</body>
</html>