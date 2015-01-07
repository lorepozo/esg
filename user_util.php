<?php
function users_print($esg, $users, $counter = false, $salt = false) {
  $img = '<td><img src="%s" style="max-height:60px;max-width:60px;" /></td>'
  ?><div class="table-responsive"><table id="users" class="table table-hover"><thead><tr><?php
  if ($counter === true) {
    echo "<th>#</th>";
  }
  if ($salt === false) {
    echo "<th></th>";
  } else {
    echo "<th>sum</th>";
  }
  echo "<th></th>";
  foreach ($esg["overview"] as $field) {
    echo "<th>".$field."</th>";
  }
  ?></tr></thead><tbody><?php
  if ($salt !== false) {
    $keys = [];
    $saltedusers = [];
    foreach ($users as $id => $user) {
      $sum = md5($salt.$id);
      array_push($keys, $sum);
      $saltedusers[$sum] = $user;
    }
    sort($keys);
    $newusers = [];
    foreach($keys as $sum) {
      array_push($newusers, $saltedusers[$sum]);
    }
    $users = $saltedusers;
    ksort($users);
  }
  $images = [];
  foreach ($esg["questions"] as $category) {
    foreach ($category[1]["fields"] as $question) {
      if ($question[0] == 'image') {
        array_push($images, $question[1]);
      }
    }
  }
  if ($counter === true) {$c = 1;}
  foreach ($users as $sum => $user) {
    $id = base64_encode($user["email"]);
    ?><tr><?php
    if ($counter === true) {
      echo '<th scope="row">'.$c++.'</th>';
    }
    if ($salt === false) { 
      ?><td><input id="<?php echo $id ?>" type="checkbox" checked onclick="console.log(this);checkupdate(this)"></td><?php
    } else {
      ?><td><?php echo $sum ?></td><?php
    }
    ?><td><a class="btn btn-default" href="user.php?id=<?php echo $id ?>">user</a></td><?php
    foreach ($esg["overview"] as $field) {
      if (in_array($field, $images)) {
        echo sprintf($img, $user[$field]);
      } elseif (gettype($user[$field]) == "integer") {
        echo "<td><script type='text/plain'>".$user[$field]."</script>".date("D M j Y", $user[$field])."</td>";
      } else {
        echo "<td>".$user[$field]."</td>";
      }
    }
    ?></tr><?php
  }
  ?></tbody></table></div><?php
  if ($salt === false) {
    $emails = [];
    foreach($users as $id => $user) {
      $emails[$id] = $user["email"];
    } ?>
    <script type="application/json" id="emailobject"><?php echo json_encode($emails) ?></script>
    <a id="toggle_selected" class="btn btn-default">Toggle selected</a>
    <a id="email_selected" class="btn btn-primary">Email selected</a>
    <script>
    (function(){
      var tbody = document.querySelector('#users tbody'),
          heads = document.querySelector('#users thead tr').children,
          users = [].slice.call(tbody.children);
      for (var i = 0; i < heads.length; i++) {(function(){
        var newusers = users.slice(0), head = heads[i];
        newusers.sort(function(a, b){
          if (a.children[i].innerHTML < b.children[i].innerHTML) return -1;
          if (a.children[i].innerHTML > b.children[i].innerHTML) return 1;
          return 0;
        });
        head.addEventListener("click", function(){
          tbody.innerHTML = "";
          for (var j = 0; j < newusers.length; j++) {
            tbody.appendChild(newusers[j])
          }
          for (var j = 0; j < heads.length; j++) {
            heads[j].style.color='';
          }
          head.style.color='#0080FF';
        });
      })()}

      function emailobject() {
        return JSON.parse(document.getElementById("emailobject").innerHTML)
      }
      function emails(o){
        return "<"+Object.keys(o).map(function(key){return o[key]}).join(">,<")+">"
      }
      var allemails = emailobject(),
          emailo = emailobject();
      window.checkupdate = function(check) {
        if (check) {
          if (check.checked) emailo[check.id]=allemails[check.id];
          else delete emailo[check.id];
        }
        document.getElementById('email_selected').href="mailto:<esglizards@mit.edu>?bcc="+emails(emailo)+"&Subject=Your%20MIT%20ESG%20Application"
      };
      checkupdate();
      document.getElementById('toggle_selected').addEventListener('click', function(){
        for (var key in allemails) {
          if (key in emailo) delete emailo[key];
          else emailo[key]=allemails[key];
        }
        [].forEach.call(document.querySelectorAll('#users input[type=checkbox]'),function(el){
          el.checked = !el.checked
        });
        checkupdate()
      });
    })()
    </script>
    <?php
  }
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
    i.height = 120;
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
    if (typeof json[key] == "number") {
      json[key] = (new Date(json[key]*1000)).toDateString()
    }
    v.innerHTML = String(json[key]).replace(/\r\n/g,"<br>");
    t.appendChild(k);
    t.appendChild(v);
    tbody.appendChild(t);
  }
  table.appendChild(tbody);
  document.getElementById("user").appendChild(table);
  })()</script><?php
} ?>