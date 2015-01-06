<?php

function db_write($delta, $admin, $first, $last, $id, $jsonstring) {
  $delta = str_replace(',',';COMMA;',$delta);
  $timestamp = date("Y-m-d-G-i-s");
  $change = sprintf("\n%s,admin %s,%s %s (%s),%s", $timestamp, $admin, $first, $last, $id, $delta);
  file_put_contents('changelog.csv', $change, FILE_APPEND | LOCK_EX);
  file_put_contents('users.json', $jsonstring, LOCK_EX);
  file_put_contents('records/'.$timestamp.'.json', $jsonstring);
  Print $msg;
}

function user_print($user) {
  $userj = $user;
  $useri = [];
  $esg = json_decode(file_get_contents('esg.json'),true);
  foreach ($esg["questions"] as $category) {
    foreach ($category[1]["fields"] as $question) {
      if ($question[0] == 'image') {
        unset($userj[$question[1]]);
        if (isset($user[$question[1]])) {
          $useri[$question[1]]=$user[$question[1]];
        }
      }
    }
  } ?>
  <script id='userj' type="application/json">
  <?php echo json_encode($userj) ?>
  </script>
  <script id='useri' type="application/json">
  <?php echo json_encode($useri) ?>
  </script>
  <div id='user'></div>
  <script>(function(){
  var json = JSON.parse(document.getElementById('userj').innerHTML),
    images = JSON.parse(document.getElementById('useri').innerHTML),
    table = document.createElement("table"),
    tbody = document.createElement("tbody"),
    k,b,v,t;
  table.classList.add('table','table-striped');
  for (key in images) {
    t = document.createElement('tr'),
    v = document.createElement('td'),
    i = document.createElement('img'),
    k = document.createElement('td'),
    b = document.createElement('strong');
    b.innerHTML = key;
    i.src = images[key];
    k.appendChild(b);
    v.appendChild(i);
    t.appendChild(k);
    t.appendChild(v);
    tbody.appendChild(t);
  }
  for (key in json) {
    t = document.createElement('tr'),
    v = document.createElement('td'),
    k = document.createElement('td'),
    b = document.createElement('strong');
    b.innerHTML = key;
    k.appendChild(b);
    v.innerHTML = String(json[key]).replace(/\r\n/g,"<br>");
    t.appendChild(k);
    t.appendChild(v);
    tbody.appendChild(t);
  }
  table.appendChild(tbody);
  document.getElementById("user").appendChild(table);
  })()</script><?php
}

function usage_print() {
  ?>Please at least provide either a name parameter,
  such as <code>name=morales_lucas</code>,<br>
  or a kerberos (MIT email) parameter, <code>kerb=lucasem</code>, <br>
  or a non-MIT email address, <code>email=foo@gmail.com</code>, to view submissions.<br><br>
  If you're creating a new user, <code>name</code> is <em>required</em>,<br>
  and providing <code>kerb</code> will store that to the user.<br><br>
  Kerberii can be stored by providing <code>name</code> and <code>kerb</code>.<br><?php
}

function changelog_print() { 
  ?><script id='changelogd' type="application/json">
  <?php echo file_get_contents('changelog.csv')?>
  </script>
  <h1>Changelog</h1>
  <div id='changelog'></div>
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
      c.innerHTML = items[j].replace(/;COMMA;/g,",");
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