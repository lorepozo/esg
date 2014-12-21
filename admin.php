<!DOCTYPE html>
<html>
<head>
  <title>Application Administrator</title>
  <link rel="stylesheet" href="resources/bootstrap.min.css">
</head>
<body class="container">
<?php
if (@$_SERVER['SSL_CLIENT_S_DN_CN']) {
  $esg = json_decode(file_get_contents('esg.json') , true);
  $kerb = explode("@", $_SERVER['SSL_CLIENT_S_DN_Email']) [0];
  if (in_array($kerb, $esg["administrators"])) {
   ?>
   <h3>Welcome, administrator
    <?php echo $_SERVER['SSL_CLIENT_S_DN_CN']?>.
   </h3><br>
   <?php
    if (!isset($_GET["name"]) and isset($_GET["kerb"])) {
      $file = file_get_contents('users.json');
      $users = json_decode($file, true);
      foreach($users as $tmpid => $tmpuser) {
        if ($_GET["kerb"] == $tmpuser["kerb"]) {
          $_GET["name"] = strtolower($tmpuser["last"] . '_' . $tmpuser["first"]);
          $kerbused = true;
          break;
        }
      }
    }

    if (isset($_GET["name"])) {
      $file = file_get_contents('users.json');
      $users = json_decode($file, true);
      $id = str_replace('=','',base64_encode($_GET["name"]));
      $names = explode('_', $_GET["name"]);
      $first = ucfirst($names[1]);
      $last = ucfirst($names[0]);
      $user = [
        "first" => $first,
        "last" => $last,
        "year" => $esg["year"],
      ];
      if (isset($_GET["kerb"])) {
        $user["kerb"] = $_GET["kerb"];
      }

      if ($users[$id]) {
        if (isset($_GET["kerb"]) and !$kerbused and ($users[$id]["kerb"] != $_GET["kerb"])) {
          $users[$id]["kerb"] = $_GET["kerb"];
          $jsonstring = json_encode($users);
          if ($jsonstring != "null" and $jsonstring != "") {
            $delta = '{"kerb":"'.$_GET["kerb"].'"}';
            $delta = str_replace(',',';COMMA;',$delta);
            $timestamp = date("Y-m-d-G-i-s");
            $change = sprintf("\n%s,admin %s,%s %s (%s),%s", $timestamp, $kerb, $first, $last, $id, $delta);
            file_put_contents('changelog.csv', $change, FILE_APPEND | LOCK_EX);
            file_put_contents('users.json', $jsonstring, LOCK_EX);
            file_put_contents('records/'.$timestamp.'.json', $jsonstring);
            Print "kerberos updated for " . $users[$id]["last"] . ", " . $users[$id]["first"];
          } else {
            Print "There was an error and the user's kerberos couldn't be updated.";
          }
        }
      } else {
        $users[$id] = $user;
        $jsonstring = json_encode($users);
        if ($jsonstring != "null" and $jsonstring and $user["first"] != "") {
          if ($user["kerb"]) {
            $delta = sprintf('{"id":"%s","first":"%s","last":"%s","kerb":"%s"}', $id, $first, $last, (string)$user["kerb"]);
          } else {
            $delta = sprintf('{"id":"%s","first":"%s","last":"%s"}', $id, $first, $last);
          }
          $delta = str_replace(',',';COMMA;',$delta);
          $timestamp = date("Y-m-d-G-i-s");
          $change = sprintf("\n%s,admin %s,%s %s (%s),%s", $timestamp, $kerb, $first, $last, $id, $delta);
          file_put_contents('changelog.csv', $change, FILE_APPEND | LOCK_EX);
          file_put_contents('users.json', $jsonstring, LOCK_EX);
          file_put_contents('records/'.$timestamp.'.json', $jsonstring);
          Print "Added user " . $user["last"] . ", " . $user["first"];
        } else {
          Print "There was an error and the user couldn't be added.";
        }
      }
      ?>
      <script id='userj' type="application/json">
      <?php echo json_encode($users[$id])?>
      </script>
      <div id='user'></div>
      <script>(function(){
      var json = JSON.parse(document.getElementById('userj').innerHTML),
        table = document.createElement("table"),
        tbody = document.createElement("tbody"),
        k,b,v,t;
      table.classList.add('table','table-striped');
      for (key in json) {
        t = document.createElement('tr');
        v = document.createElement('td');
        k = document.createElement('td');
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
      })()</script>
<?php
    } else {
?>
    Please at least provide either a name parameter,
    such as <code>name=morales_lucas</code>,<br>
    or a kerberos (MIT email) parameter, <code>kerb=lucasem</code>, to view submissions.<br><br>
    If you're creating a new user, <code>name</code> is <em>required</em>,<br>
    and providing <code>kerb</code> will store that to the user.<br><br>
    Kerberii can be stored by providing <code>name</code> and <code>kerb</code>.<br>
<?php
    }
    ?>
    <script id='changelogd' type="application/json">
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
      r = document.createElement('tr');
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
    })()</script>
<?php
  } else {
?>
You are not an administrator and don't have these privileges.
<?php
  }
} else {
?>
Please <a href="<?php
  echo 'https://' . $_SERVER[HTTP_HOST] . ':444' . $_SERVER[REQUEST_URI];
?>">log in</a> (MIT Certificates Required)
<?php
}
?>
</body>
</html>