<?php

class SIPonline
{
    public $domain;
    public $allowedDomains;

    public function __construct($datasource = '', $database = 'db', $table = 'location')
    {
        global $CDRTool;

        $expandAll  = $_REQUEST['expandAll'];
        $domain     = $_REQUEST['domain'];

        $this->expandAll  = $expandAll;
        $this->domain     = $domain;
        $this->datasource = $datasource;

        if (strlen($CDRTool['filter']['domain'])) {
            $this->allowedDomains = explode(" ", $CDRTool['filter']['domain']);
            $allowed_domains_sql="";
            $j=0;
            foreach ($this->allowedDomains as $_domain) {
                if ($j>0) $allowed_domains_sql.=",";
                $allowed_domains_sql.="'".addslashes($_domain)."'";
                $j++;
            }
        }

        $this->locationDB = new $database;
        $this->locationTable = $table;

        $this->Registered=array();
        $this->countUA=array();

        $where = " where (1=1) " ;

        if ($allowed_domains_sql) {
            $where.= sprintf("and domain in (%s)",addslashes($allowed_domains_sql)) ;
        }

        $query = sprintf(
            "select count(*) as c, domain from %s %s group by domain order by domain ASC",
            addslashes($this->locationTable),
            $where
        );

        $this->locationDB->query($query);
        $this->domains=$this->locationDB->num_rows();
        while ($this->locationDB->next_record()) {
            $this->Registered[$this->locationDB->f('domain')]=$this->locationDB->f('c');
            $this->total=$this->total+$this->locationDB->f('c');
        }

        $query = sprintf("select count(*) as c, user_agent from %s %s", addslashes($this->locationTable), $where);
        if ($this->domain) {
            $query.=sprintf(" and domain = '%s' ", addslashes($this->domain));
        }

        $query.="
        group by user_agent
        order by c DESC";

        $this->locationDB->query($query);
        while ($this->locationDB->next_record()) {
            $this->countUA[$this->locationDB->f('user_agent')]=$this->locationDB->f('c');
        }
    }

    function showHeader()
    {
        print "<table id='opensips_registrar' class='table table-striped table-condensed'>";
        print "<thead><tr>
        ";

        if ($this->domain) {
            print "
            <th></th>
            <th width=120 align=right>User@Domain</th>
            <th></th>
            <th>SIP UA contact</th>
            <th>NAT address</th>
            <th>User Agent</th>
            <th>Expires</th>
            <th>Remain</th>
            ";
        } else {
            print "
            <th></td>
            <th align=right>Users@</th>
            <th>Domain</th>
            ";
        }
        print "
        </tr></thead>
        ";
    }

    function showFooter()
    {
        print "
        <tr>
        <th></th>
        <th align=right>$this->total users@</td>
        <th align=left>$this->domains domains</td>
        </tr>
        </table>
        ";
    }

    function showAll()
    {
        global $found;

        $this->showHeader();
        foreach (array_keys($this->Registered) as $ld) {

            $onlines=$this->Registered[$ld];

            if ($this->expandAll || ($this->domain && $this->domain==$ld)) {
                   $this->show($ld);
            } else {
                $found++;

                $url = sprintf("%s?datasource=%s&domain=%s",
                $_SERVER['PHP_SELF'],
                urlencode($this->datasource),
                urlencode($ld)
                );

                print "
                <tr>
                <td valign=top align=right>$found</td>
                <td valign=top align=right>$onlines users@</td>
                <td valign=top><a href=$url>$ld</a></td>
                ";

                if ($this->domain) {
                     print "
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     ";
                }
                print "
                </tr>
                ";
            }
        }

        $this->showfooter();
        /*
        print "<p>";
        $this->showUAdistribution();
        */
    }

    function show()
    {
        global $found;

        $query="SELECT *, SEC_TO_TIME(UNIX_TIMESTAMP(expires)-UNIX_TIMESTAMP(NOW())) AS remain
                FROM location
        ";
        if ($this->domain) $query.=sprintf(" where domain = '%s'", addslashes($this->domain));

        $query.= " ORDER BY domain ASC, username ASC ";

        $this->locationDB->query($query);

        while ($this->locationDB->next_record()) {
            $found++;

            $username   = $this->locationDB->f('username');
            $domain     = $this->locationDB->f('domain');
            $contact    = $this->locationDB->f('contact');
            $received   = $this->locationDB->f('received');
            $user_agent = $this->locationDB->f('user_agent');
            $expires    = $this->locationDB->f('expires');
            $remain     = $this->locationDB->f('remain');

            $contact_print=substr($contact,4);

            $c_els=explode(";", $contact);
            $r_els=explode(";", $received);

            $transport="UDP";

            if ($c_els[1] && preg_match("/transport=(tcp|tls)/i", $c_els[1], $m)) {
                $transport=strtoupper($m[1]);
            }

            $sip_account=$username."@".$domain;

            print "
            <tr>
            <td valign=top align=right>$found</td>
            <td valign=top>$sip_account</td>
            <td valign=top>$transport</td>
            <td valign=top align=right>$c_els[0]</td>
            <td valign=top align=right>$r_els[0]</td>
            <td valign=top>$user_agent</td>
            <td valign=top>$expires</td>
            <td valign=top align=right>$remain</td>
            </tr>
            ";

            $seen[$username]++;
            $seen[$domain]++;
       }
    }

    function showUAdistribution()
    {
        print "<table border=0 cellspacing=1 class=border>";
        print "<tr bgcolor=lightgrey> ";
        print "<td></td>";
        print "<th>User agent</th>";
        print "<th>Users</th>";
        print "</tr> ";

        foreach ($this->countUA as $k => $v) {
            $users=$users+$v;
            $count++;
            print "<tr> ";
            print "<td>$count</td>";
            print "<td>$k</td>";
            print "<td>$v</td>";
            print "</tr>";
        }

        print "<tr bgcolor=lightgrey> ";
        print "<td></td>";
        print "<td><b>$this->domain</b></td>";
        print "<td><b>$users</b></td>";
        print "</tr> ";

        print "</table>";
    }
}
