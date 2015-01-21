<?php
include("user_util.php");
if(!function_exists("db_write")){
  include("db.php"); 
}
if(!function_exists("comm_update_user")){
  include("comm.php");
}

function admin_login($server) {
  if (!$server['SSL_CLIENT_S_DN_CN']) {
    ?>Please <a href="<?php
      echo 'https://' . $server[HTTP_HOST] . ':444' . $server[REQUEST_URI];
    ?>">log in</a> (MIT Certificates Required)
    </body></html><?php
    exit;
  }

  $esg = db_getesg();
  $admin = explode("@", $server['SSL_CLIENT_S_DN_Email']) [0];
  if (!in_array($admin, $esg["admins"])) {
    ?>You are not an administrator and don't have these privileges.
    </body></html><?php
    exit;
  }
  return $admin;
}

function admin_post($user, $admin) {
  $esg = db_getesg();
  $users = db_getusers();
  if ($user) {
    if ((!isset($user["email"]) or $user["email"] == "") and isset($user["kerb"]) and $user["kerb"] != "") {
      $user["email"] = $user["kerb"]."@mit.edu";
    }
    if ((!isset($user["email"]) or $user["email"] == "") or (!isset($user["first"]) or $user["first"] == "") or (!isset($user["last"]) or $user["last"] == "")) { ?>
      <div class="alert alert-danger">
        Please at least provide a first name, last name, and email address (or kerberos).
      </div><?php
      return $users;
    }
    $id = str_replace(['=','+','/'],['','-','_'],base64_encode($user["email"]));
    $user["id"] = $id;
    $user["created"] = time();
    $user["section"] = (time() < $esg["due"]["summer"]["part_a"]) ? "summer" : "fall";
    if (isset($users[$id])) { ?>
      <div class="alert alert-warning">
        A user with that email already exists: 
        <a href="user.php?id=<?php echo $id ?>"><?php echo $users[$id]["first"]." ".$users[$id]["last"] ?></a>
      </div>
      <?php
    } else {
      $user["id"] = $id;
      $users[$id] = $user;
      $delta = json_encode($user);
      db_write($delta, "admin ".$admin, $id, $esg["year"], $user);
      comm_update_user($user, "add"); ?>
      <div class="alert alert-success">
        The user 
        <a href="user.php?id=<?php echo $id ?>"><?php echo $user["first"]." ".$user["last"] ?></a>
        has been added<!-- and was sent an email -->!
      </div>
      <?php
    }
  }
  return $users;
}

function changelog_print() {
  ?><script id='changelogd' type="application/json">
  <?php echo file_get_contents('changelog.csv')?>
  </script>
  <div id='changelog' class="table-responsive"></div>
  <script>(function(){
  var csv = document.getElementById('changelogd').innerHTML,
      rows = csv.split('\n'),c,items,i,j,
      r=document.createElement('tr'),
      table = document.createElement('table'),
      tb = document.createElement('tbody'),
      th = document.createElement('thead');
  table.classList.add('table','table-hover');
  items = rows[1].split(',');
  for (j=0;j<items.length;j++){
    c = document.createElement('th');
    c.innerHTML = items[j];
    r.appendChild(c)
  }
  th.appendChild(r);
  table.appendChild(th);
  for (i=2;i<rows.length;i++){
    r = document.createElement('tr'),
    items = rows[i].split(',');
    for (j=0;j<items.length;j++){
      c = document.createElement('td');
      c.innerHTML = items[j].replace(/;COMMA;/g,", ").replace(/\-/g, "&#8209;");
      r.appendChild(c)
    }
    tb.appendChild(r)
  }
  table.appendChild(tb);
  document.getElementById('changelog').appendChild(table);
  })()</script><?php
}
?>