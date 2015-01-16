<?php

if(!function_exists("db_write")){
  include("db.php"); 
}

function handle_post($p, $f, $server) {

  $esg = db_getesg();
  if (@$server['SSL_CLIENT_S_DN_CN']) {
    if (in_array(explode("@", $server['SSL_CLIENT_S_DN_Email'])[0], $esg["admins"])) {
      $admin = explode("@", $server['SSL_CLIENT_S_DN_Email'])[0];
      ?><div class="alert alert-info" role="alert" style="margin:0">Any changes you make will be marked as administrator <code><?php echo $admin ?></code></div><?php
    }
  }

  $s = $p["saveorsubmit"];
  if ($s=="submit") {
    $sd = "submitted";
  } elseif ($s)  {
    $sd = "saved";
  }
  
  if ($s) {
    global $users, $id;
    $users = db_getusers();
    $id = $p["id"];
    $d = [];
    if (!isset($admin)) {
      $d[$sd] = time();
      $users[$id][$sd] = time();
    }
    if ($f) {
      foreach ($f as $key => $image) {
        if (!($image["error"] > 0) and ($image["size"] > 0) and exif_imagetype($image["tmp_name"])) {
          $imageloc = "images/".$key."_".$id.".".pathinfo($image["name"],PATHINFO_EXTENSION);
          move_uploaded_file($image["tmp_name"], $imageloc);
          $d[$key] = $imageloc;
          $users[$id][$key] = $imageloc;
        }
      }
    }
    $exclude = ['saveorsubmit'];
    $esg = db_getesg();
    foreach ($esg["questions"] as $name => $category) {
      foreach ($category["fields"] as $question) {
        if ($question[0] == 'image') {
          array_push($exclude, $question[1]);
        }
      }
    }
    foreach ($p as $key => $value) {
      if (in_array($key, $exclude)) {
        continue;
      }
      if ($users[$id][$key] != $value) {
        $d[$key] = $value;
        $users[$id][$key] = $value;
      }
    }
    $delta = json_encode($d);
    include("admin_util.php");
    if (isset($admin)) {
      $s = "admin ".$admin;
    }
    db_write($delta, $s, $id, $esg["year"], $users[$id]); ?>
    <div class="alert alert-success" role="alert">Your response has been <?php echo $sd?>!</div>
  <?php }
  
  return $users[$id];

}

// GET ARGS: id=1234
function init_user() {
  global $esg, $user, $users, $id;
  $esg = db_getesg();
  if ($users and $id) {
    $user = $users[$id];
  } else {
    $users = db_getusers();
    $id = $_GET["id"];
    if (!isset($id) and @$_SERVER['SSL_CLIENT_S_DN_CN']) {
      $kerb = explode("@", $_SERVER['SSL_CLIENT_S_DN_Email'])[0];
      foreach ($users as $tmpid => $tmpuser) {
        if ($tmpuser["kerb"]==$kerb) {
          $id = $tmpid;
          break;
        }
      }
    }
    if (isset($id)) {
      $user = $users[$id];
    }
  }
}

function category_print($name, $category, $user) {?>
  <h2><?php echo $name?></h2><p>
  <?php if ($category["text"]) { ?>
    <p><?php echo $category["text"]?></p>
  <?php }
  foreach ($category["fields"] as $question) { ?>
    <div class="form-group">
    <label><?php echo $question[2]?></label><br>
    <?php switch ($question[0]) {
      case "text": ?>
        <input class="form-control" type="text" name="<?php echo $question[1]?>"><br>
        <?php  break;
      case "textarea":?>
        <textarea class="form-control" name="<?php echo $question[1]?>" rows="<?php echo $question[4]?>"></textarea><br>
        <?php break;
       case "radio":
        foreach($question[4] as $radio) {?>
          <label class="radio-inline"><input type="radio" name="<?php echo $question[1]?>" value="<?php echo $radio[0]?>"><?php echo $radio[1]?></label><br>
        <?php }
        break;
      case "image": ?>
        <button class="btn btn-primary" id='<?php echo $question[1] ?>_btn'>Upload Image</button>
        <div id='<?php echo $question[1] ?>_prev' style='margin:10px 5px'></div>
        <input size=30 type="file" name='<?php echo $question[1] ?>' style="display:none"
          onchange="preview({id:'<?php echo $question[1] ?>_prev',files:this.files})" accept="image/*">
        <?php
        $param2 = ($user[$question[1]]) ? "'".$user[$question[1]]."'" : "undefined";
        ?><script>register_image('<?php echo $question[1] ?>', <?php echo $param2 ?>)</script>
        <?php
        break;
    }?>
    <?php if(isset($question[3]) && $question[3] != ""){ ?><p class="help-block"><?php echo $question[3] ?></p><?php } ?>
    </div>
  <?php }?>
  <hr>
<?php } ?>