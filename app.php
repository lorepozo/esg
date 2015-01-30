<?php 
function app_print($esgfile=null, $title="ESG Application"){
  global $ESG_FILE;
  if($esgfile != null){
    $ESG_FILE = $esgfile; 
  }
  // secure connection if no id provided to use kerberos authentication
  if (!isset($_GET["id"]) and !@$_SERVER['SSL_CLIENT_S_DN_CN']) {
    header('Location: https://'.$_SERVER['HTTP_HOST'].':444'.$_SERVER['REQUEST_URI']);
  }
  include 'app_util.php';
  if(!function_exists("db_write")){
    include("db.php");
  }
  ?>
  <!DOCTYPE html>
  <html>
  <head><title><?php echo $title ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <style>
    input.valid, textarea.valid { border: 1px solid green; color: green }
    input.invalid, button.invalid, textarea.invalid { border: 1px solid red }
    </style>
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
      };
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
  <body>
  <?php
  
  $post_response = handle_post($_POST, $_FILES, $_SERVER);
  $user = init_user();
  $esg = db_getesg();
  $esg_globals = db_getglobals();

  if($_GET["tmp"] === "true" && @$_SERVER['SSL_CLIENT_S_DN_CN']) {
    if (in_array(explode("@", $_SERVER['SSL_CLIENT_S_DN_Email'])[0], $esg_globals["admins"])) {
      $user = ["id" => "example", "first" => "example", "last" => "user", "email" => "example@gmail.com"];
      $esg = db_getesg(file_get_contents("tmp.esg"));
      unlink("tmp.esg");
    }
  }
  
  if (!isset($user)) {
    echo "<div class='container'>".$esg["apperr"]."</div></body></html>";
    exit;
  }
  if ($user["apptype"] != $esg["apptype"]) {
    echo "<div class='container'>".$esg["apptypeerr"]."</div></body></html>";
    exit;
  }
  
  ?>
  <script type="application/json" id="user">
  <?php echo json_encode($user)?>
  </script>
  <?php echo $post_response ?>
  <div class="container">
    <div><img src="resources/lizardtessellation.png" width="600"></div>
      <h1>ESG Application for Fall <?php echo $esg_globals["year"]?>
        (due <?php echo date('M j, Y', $esg["due"])?>)</h1>
    <p>
    <?php echo $esg["toptext"]?>
    <table class="table" cellspacing="0" cellpadding="4" border="0">
    <tr><th>Student name:</th>
      <td><?php echo $user["last"]?>, <?php echo $user["first"]?></td>
    </tr>
    <tr><th>MIT email:</th>
      <?php if (isset($user["kerb"]) and $user["kerb"] != "") { ?>
        <td><?php echo $user["kerb"]?>@mit.edu</td>
      <?php } else { ?>
        <td>Please inform <a href="mailto:esglizards@mit.edu">esglizards@mit.edu</a>
          of your kerberos as soon as you get one.</td>
        </tr><tr><th>Other email</th><td><?php echo $user["email"]?></td>
      <?php } ?>
    </tr>
    <tr><th>Application ID:</th>
      <td><?php echo $user["id"] ?></td>
    </tr>
    </table>
    <p>
    <hr>
    <h2>Instructions</h2>
    <?php echo $esg["instructions"] ?>
    <p>
    <b>This application is due no later than <?php echo date('l, F j Y', $esg["due"]) ?>.</b><br>
    If you have any questions or concerns, please contact us at
    <a href="mailto:esglizards@mit.edu">esglizards@mit.edu</a>.
    </p>
    <hr>
    <?php echo $esg["toptext"]?>
    <form role="form" name="form" id="form" method="post" enctype="multipart/form-data">
      <input type="hidden" name="saveorsubmit">
      <input type="hidden" name="id" value="<?php echo $user["id"]?>">
      
      <?php
      if (!isset($user["kerb"])) { ?>
        <div class="form-group">
          <label for="email">Email: </label>
          <input type="text" name="email">
        </div>
        <hr>
      <?php }
      foreach ($esg["questions"] as $name => $category){
        if (@in_array($name, $esg["aftersubjects"])) {continue;}
        category_print($name, $category, $user);
      }
      if (isset($esg["subjects"])){
        include 'app_subjects.php';
      }
      foreach($esg["questions"] as $name => $category){
        if (@in_array($name, $esg["aftersubjects"])){
          category_print($name, $category, $user);
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
    var userel = document.getElementById('user'),
        user = JSON.parse(document.getElementById('user').innerHTML);
    for (var key in user) {
      try {form.elements[key].value=user[key]}
      catch (e) {/* gotta catch 'em all! */}
    }
    userel.parentElement.removeChild(userel);
    window.submitForm = function(dosave){
      form.saveorsubmit.value = dosave ? "save" : "submit";
      form.submit();
    };
  })()
  </script>
  </body>
  </html>
<?php
}
?>