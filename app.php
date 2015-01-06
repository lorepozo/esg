<?php // secure connection if no id provided to use kerberos authentication
if (!isset($_GET["id"]) and !@$_SERVER['SSL_CLIENT_S_DN_CN']) {
	header('Location: https://'.$_SERVER[HTTP_HOST].':444'.$_SERVER[REQUEST_URI]);
}
include 'app_util.php';
?>
<!DOCTYPE html>
<html>
<?php handle_post($_POST)?>
<head><title>ESG Application</title>
	<link rel="stylesheet" href="resources/bootstrap.min.css">
  <script>
	window.URL = window.URL || window.webkitURL;
	function preview(o) {
		var i = new Image(),
			url = o.file?o.file:window.URL.createObjectURL(o.files[0]),
			p = document.createElement("img"),
			prev = document.getElementById(o.id);
		i.onload = function() {
			p.src = url,
			p.height = 120,
			p.style.border="1px solid black",
			prev.innerHTML='';
			prev.appendChild(p);
		}
		i.src = url;
	}
  function register_image(id, loc){
    document.getElementById(id+'_btn').addEventListener("click", function(e){
      e.preventDefault();
      form[id].click();
    }, false);
    loc && preview({
      id: id+"_prev",
      file: loc
    });
  }
  </script>
</head>
<?php 
init_user();
if (!isset($user)) {?>
  <body><h2>Problem with Application</h2>
  <p>We're sorry, but we encountered a problem when trying to serve your application.<p>
  <p><b>Before filling out an application you must do all of the following.</b><br>
  1. Go to an ESG information session.<br>
  2. After the session have a staff member enter your name in our database.<br>
  3. Use the link we emailed to you to access this application."
  <p></body>
<?php exit;
}?>
<script type="application/json" id="user">
<?php echo json_encode($user)?>
</script>
<body>
<div class="container">
	<div><img src="resources/lizardtessellation.png" width="600"></div>
		<h1>ESG Application Fall <?php echo $esg["year"]?>
			(due <?php echo date('M j, Y',$esg["due"])?>)</h1>
	<p>
	<?php echo $esg["toptext"]?>
	<table class="table" cellspacing="0" cellpadding="4" border="0">
	<tr><th>Student name:</th>
		<td><?php echo $user["last"]?>, <?php echo $user["first"]?></td>
	</tr>
	<tr><th>MIT email:</th>
    <?php if (isset($user["kerb"])) { ?>
      <td><?php echo $user["kerb"]?>@mit.edu</td>
    <?php } else { ?>
      <td>Please inform <a href="mailto:esglizards@mit.edu">esglizards@mit.edu</a>
        of your kerberos as soon as you get one.</td>
    <?php } ?>
	</tr>
	<tr><th>Application ID:</th>
		<td><?php echo $id?></td>
	</tr>
	</table>
	<p>
	<hr>
	<h2>Instructions</h2>
	Please answer all the questions in this form and then
	click the <b>submit</b> button at the bottom.<br>
	If you don't finish you can click the <b>save</b>  button and come back to
	finish the form using the same link you already used.
	<p>
	Please note that admission to ESG is lottery based. Your answers to these
	questions help us learn something about you, but they will not
	affect your chances in the lottery.
	<p>
	<b>This application is due no later than <?php echo date('l, F j Y',$esg["due"])?>.</b><br>
  If you have any questions or concerns, please contact us at
  <a href="mailto:esglizards@mit.edu">esglizards@mit.edu</a>.
	<p>
	<hr>
	<?php echo $esg["toptext"]?>
	<form role="form" name="form" id="form" method="post" enctype="multipart/form-data">
	  <input type="hidden" name="saveorsubmit">
	  <input type="hidden" name="id" value="<?php echo $id?>">
    <!-- TODO: Ask Jeremy about "cat" category thing -->
    
		<?php
    if (!isset($user["kerb"])) { ?>
      <div class="form-group">
        <label>Email: </label>
        <input type="text" name="email">
      </div>
      <hr>
    <?php }
		foreach ($esg["questions"] as $category){
			if (in_array($category[0], $esg["aftersubjects"])) {continue;}
			category_print($category, $user);
		}
    include 'app_subjects.php';
		foreach($esg["questions"] as $category){
			if (in_array($category[0], $esg["aftersubjects"])){
				category_print($category, $user);
				break;
			}
		}
		?>

		<h2>Save or Submit Application</h2>
	  <button type="button" class="btn btn-success" onclick="submitForm(1)">Save</button>
	  <button type="button" class="btn btn-primary" onclick="submitForm()">Submit</button>
	</form>
	<div style="height:80px"></div>
<h5 class="text-right"><small>Designed by <a href="mailto:lucasem@mit.edu">Lucas</a></small></h5>
</div>
<script>
(function(){
  var user = JSON.parse(document.getElementById('user').innerHTML);
  for (key in user) {
  	try {form.elements[key].value=user[key]}
  	catch (e) {/* gotta catch 'em all! */}
  }
	window.submitForm = function(dosave){
	  form.saveorsubmit.value = dosave ? "save" : "submit";
	  form.submit();
	};
})()
</script>
</body>
</html>