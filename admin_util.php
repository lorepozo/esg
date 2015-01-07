<?php

include("user_util.php");

function admin_login($server) {
  if (!$server['SSL_CLIENT_S_DN_CN']) {
    ?>Please <a href="<?php
      echo 'https://' . $server[HTTP_HOST] . ':444' . $server[REQUEST_URI];
    ?>">log in</a> (MIT Certificates Required)
    </body></html><?php
    exit;
  }

  $esg = json_decode(file_get_contents('esg.json') , true);
  $admin = explode("@", $server['SSL_CLIENT_S_DN_Email']) [0];
  if (!in_array($admin, $esg["admins"])) {
    ?>You are not an administrator and don't have these privileges.
    </body></html><?php
    exit;
  }
  return $admin;
}

function admin_post($user) {
  if ($user) {
    if ((!isset($user["email"]) or $user["email"] == "") and isset($user["kerb"]) and $user["kerb"] != "") {
      $user["email"] = $user["kerb"]."@mit.edu";
    }
    $id = str_replace('=','',base64_encode($user["email"]));
    $user["id"] = $id;
    $user["created"] = time();
    $file = file_get_contents('users.json');
    $users = json_decode($file, true);
    if (isset($users[$id])) { ?>
      <div class="alert alert-warning">
        A user with that email already exists: 
        <a href="user.php?id=<?php echo $id ?>"><?php echo $users[$id]["first"]." ".$users[$id]["last"] ?></a>
      </div>
      <?php
    } elseif (!isset($user["email"]) or !isset($user["first"]) or !isset($user["last"])) { ?>
      <div class="alert alert-danger">
        Please at least provide a first name, last name, and email address (or kerberos).
      </div>
      <?php
    } else {
      $users[$id] = $user;
      $jsonstring = json_encode($users);
      $user["id"] = $id;
      $delta = json_encode($user);
      db_write($delta, "admin ".$admin, $user["first"], $user["last"], $id, $jsonstring); ?>
      <div class="alert alert-success">
        The user 
        <a href="user.php?id=<?php echo $id ?>"><?php echo $user["first"]." ".$user["last"] ?></a>
        has been added!
      </div>
      <?php
    }
  } else {
    $file = file_get_contents('users.json');
    $users = json_decode($file, true);
  }
  return $users;
}

function db_write($delta, $admin, $first, $last, $id, $jsonstring) {
  $delta = str_replace(',',';COMMA;',$delta);
  $timestamp = date("Y-m-d-G-i-s");
  $change = sprintf("\n%s,%s,%s %s (%s),%s", $timestamp, $admin, $first, $last, $id, $delta);
  file_put_contents('changelog.csv', $change, FILE_APPEND | LOCK_EX);
  file_put_contents('users.json', $jsonstring, LOCK_EX);
  file_put_contents('records/'.$timestamp.'.json', $jsonstring);
  Print $msg;
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
    r.style.cursor="pointer";
    r.addEventListener('click',function(){
      window.location = 'records.php?q='+items[0]
    });
    tb.appendChild(r)
  }
  table.appendChild(tb);
  document.getElementById('changelog').appendChild(table);
  })()</script><?php
}

?>