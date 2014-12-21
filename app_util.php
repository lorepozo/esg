<?php

function handle_post($p) {

	$s = $p["saveorsubmit"];
	if ($s=="submit") {
		$sd = "submitted";
	} elseif ($s)  {
		$sd = "saved";
	}
	
	if ($s) {
    global $users, $id;
		$users = json_decode(file_get_contents('users.json'),true);
		$id = $p["id"];
    $d = [];
		foreach ($p as $key => $value) {
			if (($key == 'id') or ($key == 'saveorsubmit')) {
				continue;
			}
      if ($users[$id][$key] != $value) {
        $d[$key] = $value;
  			$users[$id][$key] = $value;
      }
		}
    $jsonstring = json_encode($users);
    $timestamp = date("Y-m-d-G-i-s");
    $delta = str_replace(",", ";COMMA;", json_encode($d));
    $change = sprintf("\n%s,%s,%s %s (%s),%s", $timestamp, $s, $users[$id]["first"], $users[$id]["last"], $id, $delta);
    file_put_contents('changelog.csv', $change, FILE_APPEND | LOCK_EX);
		file_put_contents('users.json', $jsonstring, LOCK_EX);
    file_put_contents('records/'.$timestamp.'.json', $jsonstring); ?>
	  <div class="alert alert-success" role="alert">Your response has been <?php echo $sd?>!</div>
	<?php }
  
  return $users[$id];

}

// GET ARGS: id=1234
function init_user() {
  global $esg, $user, $users, $id;
  $esg = json_decode(file_get_contents('esg.json'),true);
  if (isset($users)) {
    $user = $users[$id];
  } else {
    $users = json_decode(file_get_contents('users.json'),true);
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

function category_print($category) {?>
	<h2><?php echo $category[0]?></h2><p>
	<?php if ($category[1]["text"]) { ?>
		<p><?php echo $category[1]["text"]?></p>
	<?php }
  foreach ($category[1]["fields"] as $question) {?>
		<div class="form-group">
		<label><?php echo $question[2]?></label><br>
    <?php switch ($question[0]) {
  		case "text":?>
				<input class="form-control" type="text" name="<?php echo $question[1]?>"><br>
				<?php	break;
	  	case "textarea":?>
			  <textarea class="form-control" name="<?php echo $question[1]?>" <?php echo $question[3]?>></textarea><br>
				<?php echo $question[4];
				break;
		 	case "radio":
				foreach($question[3] as $radio) {?>
					<label class="radio-inline"><input type="radio" name="<?php echo $question[1]?>" value="<?php echo $radio[0]?>"><?php echo $radio[1]?></label><br>
				<?php }
				break;
  	}?>
		</label>
	  </div>
	<?php }?>
	<hr>
<?php } ?>