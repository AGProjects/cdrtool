<?
function check_telephone($tel,$country) {
        global $err;
        if ($country == "NL") {
        	if (preg_match("/^\+31\-\d{2}-\d{7}$/",$tel) ||
                	preg_match("/^\+31\-\d{3}-\d{6}$/",$tel)) {
                } else {
                        $err="NL numbers must be in format +31-DD-DDDDDDD or +31-DDD-DDDDDD";
                        return 0;
                }
        }
        return 1;
}
# subscriber form
$f = new form;

$f->add_element(array(	"name"=>"username",
			"type"=>"text",
			"size"=>"25",
			"length_e"=>"2",
			"minlength"=>"2",
			"maxlength"=>"25",
			"valid_regex"=>"^[-a-zA-Z0-9@_\.]{2,}$",
            "valid_e"=>"Username required: - mininum 2 chars (letters, digits, _, -, @, .)"
			));
$f->add_element(array(	"name"=>"password",
                "type"=>"text",
                "size"=>"25",
                "minlength"=>"5",
                "maxlength"=>"25",
                "pass"=>1,
                "valid_regex"=>"^[a-zA-Z0-9|_|-]*$",
                "valid_e"=>"Password: Letters, digits _ - only - minim 5 characters",
                "value"=>"$password"
                ));

$f->add_element(array(	"name"=>"name",
			"type"=>"text",
			"length_e"=>"3",
			"minlength"=>"3",
			"maxlength"=>"100",
			"size"=>"30",
			"valid_regex"=>"^[-a-zA-Z0-9|_|\.|\s ]*$",
			"valid_e"=>"Name required - min 1 chars (letters, digits _ - . spaces only)",
			"icase"=>1
			));
$f->add_element(array(	"name"=>"organization",
			"type"=>"text",
			"length_e"=>"6",
			"maxlength"=>"100",
			"size"=>"30",
			"valid_regex"=>"^[-a-zA-Z0-9|_|\.|\s ]*$",
			"valid_e"=>"Organization required - min 6 chars (letters, digits _ - . spaces only)",
			"icase"=>1
			));
$f->add_element(array(	"name"=>"email",
			"type"=>"text",
			"length_e"=>6,
			"minlength"=>"6",
			"maxlength"=>"100",
			"size"=>"30",
			"valid_e"=>"Syntax error in E-Mail address.",
			"valid_regex"=>"^([-a-zA-Z0-9._]+@[-a-zA-Z0-9_]+(\.[-a-zA-Z0-9_]+)+)*$"
			));
$f->add_element(array(	"name"=>"aNumberFilter",
			"type"=>"text",
			"maxlength"=>"100",
			"size"=>"60"
			));
$f->add_element(array(	"name"=>"domainFilter",
			"type"=>"text",
			"maxlength"=>"255",
			"size"=>"60"
			));
$f->add_element(array(	"name"=>"impersonate",
			"type"=>"text",
			"maxlength"=>"255",
			"size"=>"11"
			));
$f->add_element(array(	"name"=>"gatewayFilter",
			"type"=>"text",
			"maxlength"=>"255",
			"size"=>"60"
			));
$f->add_element(array(	"name"=>"compidFilter",
			"type"=>"text",
			"maxlength"=>"255",
			"size"=>"60"
			));
$f->add_element(array(	"name"=>"cscodeFilter",
			"type"=>"text",
			"maxlength"=>"255",
			"size"=>"60"
			));
$f->add_element(array(	"name"=>"serviceFilter",
			"type"=>"text",
			"maxlength"=>"255",
			"size"=>"60"
			));
$f->add_element(array(	"name"=>"afterDateFilter",
			"type"=>"text",
			"maxlength"=>"10",
			"size"=>"11"
			));

$f->add_element(array(	"name"=>"tel",
			"type"=>"text",
			"size"=>"30"
			));
$f->add_element(array(	"name"=>"expire",
			"type"=>"text",
			"size"=>"11"
			));

$f->add_element(array(  "name"=>"yubikey",
                "type"=>"text",
                "size"=>"12",
                "minlength"=>"12",
                "maxlength"=>"12",
                "valid_regex"=>"^[a-zA-Z0-9|_|-]*$"
                ));

$blocked_els=array(
                array("label"=>"","value"=>"0"),
                array("label"=>gettext("Blocked"),"value"=>"1")
            );
$f->add_element(array("type"=>"select",
                      "name"=>"blocked",
                      "options"=>$blocked_els,
                      "size"=>1,
                      "value"=>""
			));

$f->add_element(
            array("type"=>"submit",
		      "name"=>"submit",
		      "extrahtml"=>"class=btn",
               "value"=>"Submit")
		);

while(list($k,$v) = each($DATASOURCES)) {
    if ($k!="unknown") {
        $cdrSourcesEls[]=array("label"=>$v[name],"value"=>$k);
    }
}

$f->add_element(array("type"=>"select",
                      "name"=>"sources",
                      "options"=>$cdrSourcesEls,
                      "size"=>8,
                      "multiple"=>"1",
                      "value"=>""
			        )
            );

$f->add_element(array("type"=>"select",
                      "name"=>"auth_method",
                      "options"=> array(
                                    array("label"=>"Username+Password+Yubikey","value"=>"7"),
                                    array("label"=>"Username+Yubikey","value"=>"5"),
                                    array("label"=>"Yubikey","value"=>"4"),
                                  ),
                      "multiple"=>"0",
                      "value"=>""
              )
            );


function showForm($id="") {
    global $CDRTool, $verbose, $perm, $auth, $sess, $cdr, $f,
    $perms, $source, $sources, $action;

    $sources=explode(",",$sources);

    global $afterDateFilter;

    if (preg_match("/^0000-00-00$/",$afterDateFilter)) {
        $afterDateFilter="";
    }

    $f->load_defaults();
    $f->start("","GET","","", "","form-horizontal");

    print "<input type=hidden name=check value=yes>";
    print "<input type=hidden name=action value=\"$action\">";

    if ($frzall) {
        $f->freeze();
    }

    if (!$perm->have_perm("admin")) {
        $ff=array("sources",
                  "gatewayFilter",
                  "domainFilter",
                  "aNumberFilter",
                  "serviceFilter",
                  "compidFilter",
                  "cscodeFilter",
                  "afterDateFilter",
                  "impersonate");
            $f->freeze($ff);
        }


    print "
    <div class=\"row-fluid\">
    <div class=\"span6\">
    <h3>Contact details</h3>
    <p>";
    print _("The fields marked with ");
    print " <font color=orange>*</font> ";
    print _("are mandatory");
    print ":</font>
    </p>
    ";

    $f->show_element("action","");

    if ($id) {
            $f->add_element(array("type"=>"hidden",
                                  "name"=>"id",
                                  "value"=>"$id"
                            ));
    }

    print "
      <div class=\"control-group\">
      <label class='control-label'>
    ";
    print _("Name");
    print "<font color=orange> *</font></label>
    ";
    print "
    <div class='controls'>
    <font color=$formelcolor>
    ";
    $f->show_element("name","");
    print "
    </font>
    </div>
    </div>
    ";

    print "
      <div class=\"control-group\">
      <label class='control-label'>
    ";
    print _("Organization");
    print "</label>
    <div class='controls'>
      <font color=$formelcolor>
    ";
    $f->show_element("organization","");
    print "
    </div>
    </div>
    ";

    print "
      <div class=\"control-group\">
      <label class='control-label'>
    ";
    print _("E-mail");
    print "<font color=orange> *</font></label>
      <div class='controls'>
      <font color=$formelcolor>
    ";
    $f->show_element("email","");
    print "
    </div>
    </div>
    ";


    print "
      <div class=\"control-group\">
      <label class='control-label'>
    ";
    print _("Telephone");
    print "</label>
      <div class='controls'>
      <font color=$labelcolor>
    ";
    $f->show_element("tel","");
    print "
    </div>
    </div>
    ";
    print "
      <div class=\"control-group\">
      <label class='control-label'>
    ";

    print _("Username");
    print " <font color=orange>*</font></label>
      <div class='controls'><font color=$labelcolor>
    ";

    $f->show_element("username","");

    print "
    </div>
    </div>
    ";

    print "
      <div class=\"control-group\">
      <label class='control-label'>
    ";
    print _("Password");
    print " <font color=orange>*</font></label>
      <div class='controls'><font color=$labelcolor>
    ";
    $f->show_element("password","");
    print "</div>
    </div>
    ";

    print "
      <div class=\"control-group\">
      <label class='control-label'>
    ";
    print _("Yubikey");
    print "</label>
    <div class='controls'>
      <font color=$formelcolor>
    ";
    $f->show_element("yubikey","");
    print "
    </div>
    </div>
    ";

    print "
      <div class=\"control-group\">
      <label class='control-label'>
    ";
    print _("Yubikey usage");
    print "</label>
    <div class='controls'>
      <font color=$formelcolor>
    ";
    $f->show_element("auth_method","");
    print "
    </div>
    </div>
    ";

    print "
          <div class=\"control-group\">
            <label class='control-label'>
              <font color=$labelcolor>
           E-mail settings</label>
            <div class='controls'><font color=$labelcolor>
           ";
           print "<input type=checkbox name=mailsettings value=1> ";
           print "
           </div>
           </div>
           ";

    if ($perm->have_perm("admin")) {
        print "<hr>
               <div class=\"control-group\">
               <label class='control-label'>
               <font color=$labelcolor>
               Expire date </label>
               <div class='controls'>
               <font color=$labelcolor>
        ";
        $f->show_element("expire","");
        print "
           </div>
           </div>
        ";

        print "
              <div class=\"control-group\">
              <label class='control-label'><font color=$labelcolor>
              Impersonate </label>
               <div class='controls'><font color=$labelcolor>
           ";
           $f->show_element("impersonate","");
           print "
           </font>
           </div>
           </div>
           ";
           print "
              <div class=\"control-group\">
              <label class='control-label'><font color=$labelcolor>
           Delete </label>
           <div class='controls'><font color=$labelcolor>
           ";
           print "<input type=checkbox name=delete value=1>";
           print "
           </font>
           </div>
           </div>
           ";

           /*
           print "
           <tr>
           <td valign=top><font color=$labelcolor>
           Lock </td>
           <td colspan=2 valign=top><font color=$labelcolor>
           ";
                   $f->show_element("blocked","");
           print "
           </font>
           </td>
           </tr>
           ";
           */
           print "
            <hr>
           ";
    }

    print "
    </div>";

    print "<div class=span6>
    ";

     print "
     <h3>Permissions</h3>
     <div class='row-fluid'>";
     if ($perm->have_perm("admin")) {
        print "<div class='span6'>
        <p><b>Functions</b></p>";
        print $perm->perm_sel("perms", $perms);
        print "</div>";
     }
     print "<div class='span6'><p><strong>Data sources</strong></p>";
     $f->show_element("sources","");
     print "
     </div></div>";

     print "
      <hr noshade size=1>
      <p>
      <strong>Filters</strong></p>
     ";
     print "<div class=\"control-group\">
            <label class='control-label'>Trusted peers</label><div class='controls'>";
     $f->show_element("gatewayFilter","");
     print "</div></div>";
     print "<div class=\"control-group\">
            <label class='control-label'>Domains</label><div class='controls'>";
     $f->show_element("domainFilter","");
     print "</div></div>";
     print "<div class=\"control-group\">
            <label class='control-label'>Subscribers</label><div class='controls'>";
     $f->show_element("aNumberFilter","");
     print "</div></div>";
     print "<div class=\"control-group\">
            <label class='control-label'>After date</label><div class='controls'>";
     $f->show_element("afterDateFilter","");
     print "</div></div></div></div>";

    if (!$frzall) {
        print "<div class='form-actions'>";
        $f->show_element("submit","","btn");
        print "</div>";
    }
    $f->finish();                     // Finish form
}

function accountList() {
    global $auth, $perm, $verbose, $search_text;
    $uid=$auth->auth["uid"];

    $db        = new DB_CDRTool;

    $query="select * from auth_user";
    if (!$perm->have_perm("admin")) {
        $query.= sprintf(" where user_id = '%s'", addslashes($uid));
    }
    $query .= " order by name asc";
    $db->query($query);
    dprint($query);
    $rows=$db->num_rows();
    print "
    <p>
    <center>
    <table class='table table-hover table-condensed table-striped' width=100%>
    <thead>
    <tr>
     ";
     print "<th class=h>";
     print _("Name");
     print "</th><th class=h>";
     print _("Organization");
     print "</th><th class=h>";
     print _("Username");
     print "</th><th class=h>";
     print _("E-mail");
     print "</th><th class=h>";
     print _("Tel");
     print "</th><th class=h>";
     print _("Sources");
     print "</th><th class=h>";
     print _("Expire");
     print "</th>
     </tr></thead>
     ";
    while($db->next_record()) {
        $id_db          =$db->f('user_id');
        $name		    =$db->f('name');
        $username	    =$db->f('username');
        $email		    =$db->f('email');
        $organization 	=$db->f('organization');
        $password	    =$db->f('password');
        $tel		    =$db->f('tel');
        $domainFilter 	=$db->f('domainFilter');
        $aNumberFilter 	=$db->f('aNumberFilter');
        $expire     	=$db->f('expire');
        $sources	    =preg_replace ("/,/",", ",$db->f('sources'));

        if (date('Y-m-d') > $expire) {
        	$bgcolor="error";
        } else {
        	$bgcolor="";
        }

        print "
        <tr class=$bgcolor>
        <td> <a href=$PHPSELF?id=$id_db&action=edit>$name</a>
        <td> $organization
        <td> $username
        <td> <nobr><a href=mailto:$email>$email</a></nobr>
        <td> <nobr>$tel</nobr>
        <td> $sources
        <td> $expire
        </tr>
        ";

        $j++;
    }

    print "</table>
    </center>
    ";
}
?>
