<?php
function check_telephone($tel, $country)
{
    global $err;
    if ($country == "NL") {
        if (preg_match("/^\+31\-\d{2}-\d{7}$/", $tel)
            || preg_match("/^\+31\-\d{3}-\d{6}$/", $tel)
        ) {
        } else {
            $err = "NL numbers must be in format +31-DD-DDDDDDD or +31-DDD-DDDDDD";
            return 0;
        }
    }
    return 1;
}
# subscriber form
$f = new form;

$f->add_element(
    array(
        "name"=>"username",
        "type"=>"text",
        "size"=>"25",
        "length_e"=>"2",
        "minlength"=>"2",
        "maxlength"=>"25",
        "valid_regex"=>"^[-a-zA-Z0-9@_\.]{2,}$",
        "valid_e"=>"Username required: - mininum 2 chars (letters, digits, _, -, @, .)"
    )
);

$f->add_element(
    array(
        "name"=>"password",
        "type"=>"text",
        "size"=>"25",
        "minlength"=>"5",
        "maxlength"=>"25",
        "pass"=>1,
        //"valid_regex"=>"^.{5,}",
        //"valid_e"=>"Password: Letters, digits _ - only - minim 5 characters",
        "value"=>"$password"
    )
);

$f->add_element(
    array(
        "name"=>"name",
        "type"=>"text",
        "length_e"=>"3",
        "minlength"=>"3",
        "maxlength"=>"100",
        "size"=>"30",
        "valid_regex"=>"^[-a-zA-Z0-9|_|\.|\s ]*$",
        "valid_e"=>"Name required - min 1 chars (letters, digits _ - . spaces only)",
        "icase"=>1
    )
);
$f->add_element(
    array(
        "name"=>"organization",
        "type"=>"text",
        "length_e"=>"6",
        "maxlength"=>"100",
        "size"=>"30",
        "valid_regex"=>"^[-a-zA-Z0-9|_|\.|\s ]*$",
        "valid_e"=>"Organization required - min 6 chars (letters, digits _ - . spaces only)",
        "icase"=>1
    )
);
$f->add_element(
    array(
        "name"=>"email",
        "type"=>"text",
        "length_e"=>6,
        "minlength"=>"6",
        "maxlength"=>"100",
        "size"=>"30",
        "valid_e"=>"Syntax error in E-Mail address.",
        "valid_regex"=>"^([-a-zA-Z0-9._]+@[-a-zA-Z0-9_]+(\.[-a-zA-Z0-9_]+)+)*$"
    )
);
$f->add_element(
    array(
        "name"=>"aNumberFilter",
        "type"=>"text",
        "maxlength"=>"100",
        "size"=>"60"
    )
);
$f->add_element(
    array(
        "name"=>"domainFilter",
        "type"=>"text",
        "maxlength"=>"255",
        "size"=>"60"
    )
);
$f->add_element(
    array(
        "name"=>"impersonate",
        "type"=>"text",
        "maxlength"=>"255",
        "size"=>"11"
    )
);
$f->add_element(
    array(
        "name"=>"gatewayFilter",
        "type"=>"text",
        "maxlength"=>"255",
        "size"=>"60"
    )
);
$f->add_element(
    array(
        "name"=>"compidFilter",
        "type"=>"text",
        "maxlength"=>"255",
        "size"=>"60"
    )
);
$f->add_element(
    array(
        "name"=>"cscodeFilter",
        "type"=>"text",
        "maxlength"=>"255",
        "size"=>"60"
    )
);
$f->add_element(
    array(
        "name"=>"serviceFilter",
        "type"=>"text",
        "maxlength"=>"255",
        "size"=>"60"
    )
);
$f->add_element(
    array(
        "name"=>"afterDateFilter",
        "type"=>"text",
        "maxlength"=>"10",
        "size"=>"11"
    )
);
$f->add_element(
    array(
        "name"=>"aclFilter",
        "type"=>"text",
        "maxlength"=>"100",
        "size"=>"20"
    )
);

$f->add_element(
    array(
        "name"=>"tel",
        "type"=>"text",
        "size"=>"30"
    )
);

$f->add_element(
    array(
        "name"=>"expire",
        "type"=>"text",
        "size"=>"11"
    )
);

$use_yubikey = 0;
if (stream_resolve_include_path('Auth/Yubico.php')) {
    require_once 'Auth/Yubico.php';
    $use_yubikey = 1;
}

if ($use_yubikey) {
    $f->add_element(
        array(
            "name"=>"yubikey",
            "type"=>"text",
            "size"=>"12",
            "minlength"=>"12",
            "maxlength"=>"12",
            "valid_regex"=>"^[a-zA-Z0-9|_|-]*$"
        )
    );
}

$blocked_els=array(
    array("label"=>"","value"=>"0"),
    array("label"=>gettext("Blocked"),"value"=>"1")
);

$f->add_element(
    array(
        "type"=>"select",
        "name"=>"blocked",
        "options"=>$blocked_els,
        "size"=>1,
        "value"=>""
    )
);

$f->add_element(
    array(
        "type"=>"submit",
        "name"=>"submit",
        "extrahtml"=>"class=btn",
        "value"=>"Submit"
    )
);

while (list($k,$v) = each($DATASOURCES)) {
    if ($k != "unknown") {
        $cdrSourcesEls[] = array(
            "label" => $v[name],
            "value" => $k
        );
    }
}

$f->add_element(
    array(
        "type"=>"select",
        "name"=>"sources",
        "options"=>$cdrSourcesEls,
        "size"=>8,
        "multiple"=>"1",
        "value"=>""
    )
);

if ($use_yubikey) {
    $f->add_element(
        array(
            "type"=>"select",
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
}

function wrapFormElement($label, $element)
{
    if (is_array($label)) {
        $label = implode('', $label);
    }

    if (is_array($element)) {
        $element = implode('', $element);
    }

    printf('
        <div class="control-group">
            <label class="control-label">%s</label>
            <div class="controls">
            %s
            </div>
        </div>',
        $label,
        $element
    );
}

function showForm($id = "")
{
    global $CDRTool, $verbose, $perm, $auth, $sess, $cdr, $f,
    $perms, $source, $sources, $action;

    $sources = explode(",", $sources);

    $use_yubikey = 0;
    if (stream_resolve_include_path('Auth/Yubico.php')) {
        require_once 'Auth/Yubico.php';
        $use_yubikey = 1;
    }

    global $afterDateFilter;

    if (preg_match("/^0000-00-00$/", $afterDateFilter)) {
        $afterDateFilter = "";
    }

    $f->load_defaults();
    $f->start("", "GET", "", "", "", "form-horizontal");

    print "<input type=hidden name=check value=yes>";
    print "<input type=hidden name=action value=\"$action\">";

    if ($frzall) {
        $f->freeze();
    }

    if (!$perm->have_perm("admin")) {
        $ff = array(
            "sources",
            "gatewayFilter",
            "domainFilter",
            "aNumberFilter",
            "serviceFilter",
            "compidFilter",
            "cscodeFilter",
            "afterDateFilter",
            "aclFilter",
            "impersonate"
        );
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

    $f->show_element("action", "");

    if ($id) {
        $f->add_element(
            array(
                "type"=>"hidden",
                "name"=>"id",
                "value"=>"$id"
            )
        );
    }

    wrapFormElement(
        array(
            _("Name"),
            "<font color=orange> *</font>"
        ),
        array(
            "<font color=$formelcolor>",
            $f->get_element("name", ""),
            "</font>"
        )
    );

    wrapFormElement(
        _("Organization"),
        array(
            "<font color=$formelcolor>",
            $f->get_element("organization", ""),
            "</font>"
        )
    );

    wrapFormElement(
        array(
            _("E-mail"),
            "<font color=orange> *</font>"
        ),
        array(
            "<font color=$formelcolor>",
            $f->get_element("email", ""),
            "</font>"
        )
    );

    wrapFormElement(
        _("Telephone"),
        array(
            "<font color=$labelcolor>",
            $f->get_element("tel", ""),
            "</font>"
        )
    );

    wrapFormElement(
        array(
            _("Username"),
            "<font color=orange>*</font>"
        ),
        array(
            "<font color=$labelcolor>",
            $f->get_element("username", ""),
            "</font>"
        )
    );

    wrapFormElement(
        array(
            _("Password"),
            "<font color=orange>*</font>"
        ),
        array(
            "<font color=$labelcolor>",
            $f->get_element("password", ""),
            "</font>"
        )
    );

    if ($use_yubikey) {
        wrapFormElement(
            _("Yubikey"),
            array(
                "<font color=$formelcolor>",
                $f->get_element("yubikey", ""),
                "</font>"
            )
        );

        wrapFormElement(
            _("Yubikey usage"),
            array(
                "<font color=$formelcolor>",
                $f->get_element("auth_method", ""),
                "</font>"
            )
        );
    }

    wrapFormElement(
        array(
            "<font color=$labelcolor>",
            "E-mail settings",
            "</font>"
        ),
        array(
            "<font color=$labelcolor>",
            "<input type=checkbox name=mailsettings value=1>",
            "</font>"
        )
    );

    if ($perm->have_perm("admin")) {
        print "<hr>";
        wrapFormElement(
            array(
                "<font color=$labelcolor>",
                "Expire date",
                "</font>"
            ),
            array(
                "<font color=$labelcolor>",
                $f->get_element("expire", ""),
                "</font>"
            )
        );

        wrapFormElement(
            array(
                "<font color=$labelcolor>",
                "Impersonate",
                "</font>"
            ),
            array(
                "<font color=$labelcolor>",
                $f->get_element("impersonate", ""),
                "</font>"
            )
        );

        wrapFormElement(
            array(
                "<font color=$labelcolor>",
                "Delete",
                "</font>"
            ),
            array(
                "<font color=$labelcolor>",
                "<input type=checkbox name=delete value=1>",
                "</font>"
            )
        );

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
    $f->show_element("sources", "");
    print "
     </div></div>";

    print "
      <hr noshade size=1>
      <p>
      <strong>Filters</strong></p>
     ";

    wrapFormElement("IP ACL", $f->get_element("aclFilter", ""));
    wrapFormElement("Trusted peers", $f->get_element("gatewayFilter", ""));
    wrapFormElement("Domains", $f->get_element("domainFilter", ""));
    wrapFormElement("Subscribers", $f->get_element("aNumberFilter", ""));
    wrapFormElement("After date", $f->get_element("afterDateFilter", ""));

    print "</div></div>";

    if (!$frzall) {
        print "<div class='form-actions'>";
        $f->show_element("submit", "", "btn");
        print "</div>";
    }
    $f->finish();                     // Finish form
}

function accountList()
{
    global $auth, $perm, $verbose, $search_text, $PHP_SELF;
    $uid = $auth->auth["uid"];

    $db = new DB_CDRTool;

    $query = "select * from auth_user";
    if (!$perm->have_perm("admin")) {
        $query .= sprintf(" where user_id = '%s'", addslashes($uid));
    }
    $query .= " order by name asc";
    $db->query($query);
    dprint_sql($query);

    $rows = $db->num_rows();

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
    while ($db->next_record()) {
        $id_db          = $db->f('user_id');
        $name           = $db->f('name');
        $username       = $db->f('username');
        $email          = $db->f('email');
        $organization   = $db->f('organization');
        $password       = $db->f('password');
        $tel            = $db->f('tel');
        $domainFilter   = $db->f('domainFilter');
        $aNumberFilter  = $db->f('aNumberFilter');
        $expire         = $db->f('expire');
        $sources        = preg_replace("/,/", ", ", $db->f('sources'));

        $bgcolor = "";
        if (date('Y-m-d') > $expire) {
            $bgcolor = "error";
        }

        print "
            <tr class=$bgcolor>
                <td><a href=\"$PHP_SELF?id=$id_db&action=edit\">$name</a></td>
                <td>$organization</td>
                <td>$username</td>
                <td><nobr><a href=mailto:$email>$email</a></nobr></td>
                <td><nobr>$tel</nobr></td>
                <td>$sources</td>
                <td>$expire</td>
            </tr>
        ";
    }

    print "</table>
    </center>
    ";
}
?>
