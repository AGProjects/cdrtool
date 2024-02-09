<?php

class SipAliases extends Records
{
    var $selectionActiveExceptions=array('alias_domain');

    public function __construct($SoapEngine)
    {
        dprint("init SipAliases");

        $target_filters_els=explode("@",trim($_REQUEST['target_username_filter']));
        $target_username=$target_filters_els[0];

        if (count($target_filters_els) > 1) {
            $target_domain=$target_filters_els[1];
        }

        $this->filters   = array(
            'alias_username'    => strtolower(trim($_REQUEST['alias_username_filter'])),
            'alias_domain'      => strtolower(trim($_REQUEST['alias_domain_filter'])),
            'target_username'   => strtolower($target_username),
            'target_domain'      => strtolower($target_domain)
        );

        parent::__construct($SoapEngine);

        $this->sortElements=array(
            'changeDate'     => 'Change date',
            'aliasUsername'  => 'Alias user',
            'aliasDomain'    => 'Alias domain',
            'targetUsername' => 'Target user',
            'targetDomain'   => 'Target domain',
        );
    }

    function getRecordKeys()
    {
        // Filter
        $filter=array('aliasUsername'  => $this->filters['alias_username'],
                      'aliasDomain'    => $this->filters['alias_domain'],
                      'targetUsername' => $this->filters['target_username'],
                      'targetDomain'   => $this->filters['target_domain'],
                      'owner'          => intval($this->filters['owner']),
                      'customer'       => intval($this->filters['customer']),
                      'reseller'       => intval($this->filters['reseller'])
                      );

        // Range
        $range=array('start' => 0,
                     'count' => 500
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'aliasUsername';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        //dprint_r($Query);
        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getAliases');
        // Call function
        $result     = $this->SoapEngine->soapclient->getAliases($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl, $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
            return false;
        } else {
            foreach ($result->aliases as $alias) {
                $this->selectionKeys[]=array('username' => $alias->id->username,
                                             'domain'   => $alias->id->domain);
            }

            return true;
        }
    }


    function listRecords()
    {
        $this->getAllowedDomains();

        // Make sure we apply the domain filter from the login credetials

        $this->showSeachForm();

        // Filter
        $filter=array('aliasUsername'  => $this->filters['alias_username'],
                      'aliasDomain'    => $this->filters['alias_domain'],
                      'targetUsername' => $this->filters['target_username'],
                      'targetDomain'   => $this->filters['target_domain'],
                      'owner'          => intval($this->filters['owner']),
                      'customer'       => intval($this->filters['customer']),
                      'reseller'       => intval($this->filters['reseller'])

                      );

        // Range
        $range=array('start' => intval($this->next),
                     'count' => intval($this->maxrowsperpage)
                     );

        // Order

        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';
        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getAliases');

        // Call function
        $result     = $this->SoapEngine->soapclient->getAliases($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl, $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
            return false;
        } else {
            $this->rows = $result->total;

            if ($this->rows && $_REQUEST['action'] != 'PerformActions' && $_REQUEST['action'] != 'Delete') {
                $this->showActionsForm();
            }

            print "
            <p>
            <div class=\"alert alert-success\"><center>$this->rows records found</center></div>
            <p>
            <table class='table table-condensed table-striped' width=100%>
            <thead>
            <tr>
                <th>Id</th>
                <th>SIP alias</th>
                <th>Redirect target</th>
                <th>Owner</th>
                <th>Change date</th>
                <th>Actions</th>
            </tr>
            </thead>
            ";

            if (!$this->next)  $this->next=0;

            if ($this->rows > $this->maxrowsperpage)  {
                $maxrows = $this->maxrowsperpage + $this->next;
                if ($maxrows > $this->rows) $maxrows = $this->maxrowsperpage;
            } else {
                $maxrows=$this->rows;
            }

            $i=0;

            if ($this->rows) {
                while ($i < $maxrows)  {

                    if (!$result->aliases[$i]) break;

                    $alias = $result->aliases[$i];

                    $index=$this->next+$i+1;

                    $_url = $this->url.sprintf("&service=%s&action=Delete&alias_username_filter=%s&alias_domain_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($alias->id->username),
                    urlencode($alias->id->domain)
                    );

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['alias_username_filter'] == $alias->id->username &&
                        $_REQUEST['alias_domain_filter'] == $alias->id->domain) {
                        $_url .= "&confirm=1";
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    /*
                    $_customer_url = $this->url.sprintf("&service=customers@%s&customer_filter=%s",
                    urlencode($this->SoapEngine->customer_engine),
                    urlencode($alias->customer)
                    );
                    */

                    $_sip_accounts_url = $this->url.sprintf("&service=sip_accounts@%s&username_filter=%s&domain_filter=%s",
                    urlencode($this->SoapEngine->soapEngine),
                    urlencode($alias->target->username),
                    urlencode($alias->target->domain)
                    );

                    if ($alias->owner) {
                        $_owner_url = sprintf
                        ("<a href=%s&service=customers@%s&customer_filter=%s>%s</a>",
                        $this->url,
                        urlencode($this->SoapEngine->soapEngine),
                        urlencode($alias->owner),
                        $alias->owner
                        );
                    } else {
                        $_owner_url='';
                    }

                    printf("
                    <tr>
                    <td>%s</td>
                    <td>%s@%s</td>
                    <td><a href=%s>%s@%s</a></td>
                    <td>%s</td>
                    <td>%s</td>
                    <td><a class='btn-small btn-danger' href=%s>%s</a></td>
                    </tr>
                    ",
                    $index,
                    $alias->id->username,
                    $alias->id->domain,
                    $_sip_accounts_url,
                    $alias->target->username,
                    $alias->target->domain,
                    $_owner_url,
                    $alias->changeDate,
                    $_url,
                    $actionText
                    );
                    $i++;
                }
            }

            print "</table>";

            $this->showPagination($maxrows);

            /*
            $_properties=array(
                               array('name'       => $this->SoapEngine->port.'_sortBy',
                                     'value'      => $this->sorting['sortBy'],
                                     'permission' => 'customer',
                                     'category'   => 'web'
                                     ),
                               array('name'       => $this->SoapEngine->port.'_sortOrder',
                                     'value'      => $this->sorting['sortOrder'],
                                     'permission' => 'customer',
                                     'category'   => 'web'
                                     )
                               );

            print_r($_properties);
            $this->setCustomerProperties($_properties);
            */

            return true;
        }
    }

    function deleteRecord($dictionary=array())
    {
        if (!$dictionary['confirm'] && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Confirm to confirm the delete. </font>";
            return true;
        }

        if ($dictionary['alias_username']) {
            $alias_username=$dictionary['alias_username'];
        } else {
            $alias_username=$this->filters['alias_username'];
        }

        if ($dictionary['alias_domain']) {
            $alias_domain=$dictionary['alias_domain'];
        } else {
            $alias_domain=$this->filters['alias_domain'];
        }

        if (!strlen($alias_username) || !strlen($alias_domain)) {
            print "<p><font color=red>Error: missing SIP alias username or domain. </font>";
            return false;
        }

        $alias=array('username' => $alias_username,
                     'domain'   => $alias_domain
                    );

        $function=array('commit'   => array('name'       => 'deleteAlias',
                                            'parameters' => array($alias),
                                            'logs'       => array('success' => sprintf('SIP alias %s@%s has been deleted',$this->filters['alias_username'], $this->filters['alias_domain'])
                                                                  )
                                           )

                        );

        unset($this->filters);
        return $this->SoapEngine->execute($function, $this->html);
    }

    function showSeachFormCustom()
    {
        printf (" <div class='input-prepend'><span class='add-on'>SIP alias</span><input type=text class=span1 name=alias_username_filter value='%s'></div>",$this->filters['alias_username']);
        printf ("@");

        if (count($this->allowedDomains) > 0) {
            if ($this->filters['alias_domain'] && !in_array($this->filters['alias_domain'], $this->allowedDomains)) {
                printf ("<input type=text size=15 name=alias_domain_filter value='%s'>",$this->filters['alias_domain']);
            } else {
                $selected_domain[$this->filters['alias_domain']]='selected';
                printf ("<select name=alias_domain_filter>
                <option>");

                foreach ($this->allowedDomains as $_domain) {
                    printf ("<option value='$_domain' %s>$_domain",$selected_domain[$_domain]);
                }

                printf ("</select>");
            }
        } else {
            printf ("<input type=text size=15 name=alias_domain_filter value='%s'>",$this->filters['alias_domain']);
        }

        printf (" <div class='input-prepend'><span class='add-on'>Redirect target</span><input type=text class=span2 name=target_username_filter value='%s'></div>",trim($_REQUEST['target_username_filter']));
        printf (" <div class='input-prepend'><span class='add-on'>Owner</span><input type=text class=span1 name=owner_filter value='%s'></div>",$this->filters['owner']);
    }

    function showAddForm()
    {
        if ($this->selectionActive) return;

        if (!count($this->allowedDomains)) {
            print "<p><font color=red>You must create at least one SIP domain before adding SIP aliases</font>";
            return false;
        }

        printf ("<form class=form-inline method=post name=addform action=%s>",$_SERVER['PHP_SELF']);

        print "<div class='well well-small'>
        <input class='btn btn-warning' type=submit name=action value=Add>
        ";

        printf (" <div class='input-prepend'><span class='add-on'>SIP alias</span><input class=span2 type=text name=alias></div>");

        if ($_REQUEST['domain']) {
            $_domain=$_REQUEST['domain'];
            $selected_domain[$_REQUEST['domain']]='selected';
        } else if ($_domain=$this->getCustomerProperty('sip_aliases_last_domain')) {
            $selected_domain[$_domain]='selected';
        }

        if (count($this->allowedDomains) > 0) {
            print "@<select name=domain>";
            foreach ($this->allowedDomains as $_domain) {
                printf ("<option value='%s' %s>%s\n",$_domain, $selected_domain[$_domain], $_domain);
            }
            print "</select>";

        } else {
            printf (" <input type=text name=domain class=span2 value='%s'>",$_domain);
        }

        printf (" <div class='input-prepend'><span class='add-on'>Redirect target</span><input class=span2 type=text name=target></div>");

        printf (" <div class='input-prepend'><span class='add-on'>Owner</span><input class=span1 type=text name=owner></div>");

        $this->printHiddenFormElements();

        print "</div>
        </form>";
    }

    function addRecord($dictionary=array())
    {
        if ($dictionary['alias']) {
            $alias_els  = explode("@", $dictionary['alias']);
            $this->skipSaveProperties=true;
        } else {
            $alias_els  = explode("@", trim($_REQUEST['alias']));
        }

        if ($dictionary['target']) {
            $target_els = explode("@", $dictionary['target']);
        } else {
            $target_els = explode("@", trim($_REQUEST['target']));
        }

        if ($dictionary['owner']) {
            $owner = $dictionary['owner'];
        } else {
            $owner = $_REQUEST['owner'];
        }

        if (preg_match("/:(.*)$/",$target_els[0], $m)) {
            $target_username=$m[1];
        } else {
            $target_username=$target_els[0];
        }

        if (preg_match("/:(.*)$/",$alias_els[0], $m)) {
            $username=$m[1];
        } else {
            $username=$alias_els[0];
        }

        if (strlen($alias_els[1])) {
            $domain=$alias_els[1];
        } else if (trim($_REQUEST['domain'])) {
            $domain=trim($_REQUEST['domain']);
        } else {
            if ($this->html) {
            	printf ("<p><font color=red>Error: Missing SIP domain</font>");
            }
            return false;
        }

        if (!$this->validDomain($domain)) {
            if ($this->html) {
            	print "<font color=red>Error: invalid domain name</font>";
            }
            return false;
        }

        list($customer, $reseller)=$this->customerFromLogin($dictionary);

        if (!$this->skipSaveProperties=true) {
            $_p=array(
                      array('name'       => 'sip_aliases_last_domain',
                            'category'   => 'web',
                            'value'      => strtolower($domain),
                            'permission' => 'customer'
                           )
                      );

            $this->setCustomerProperties($_p);
        }

        $alias=array(
                     'id'     => array('username' => strtolower($username),
                                       'domain'   => strtolower($domain)
                                       ),
                     'target' => array('username' => strtolower($target_username),
                                       'domain'   => strtolower($target_els[1])
                                       ),
                     'owner'      => intval($owner)
                    );

        $deleteAlias=array('username' => strtolower($username),
                           'domain'   => strtolower($domain)
                           );

        $function=array('commit'   => array('name'       => 'addAlias',
                                            'parameters' => array($alias),
                                            'logs'       => array('success' => sprintf('SIP alias %s@%s has been added',$username, $domain)))
                        );

        return $this->SoapEngine->execute($function, $this->html);
    }

    function getAllowedDomains()
    {
        // Filter
        $filter=array(
                      'domain'    => ''
                      );

        // Range
        $range=array('start' => 0,
                     'count' => 500
                     );

        $orderBy = array('attribute' => 'domain',
                         'direction' => 'ASC'
                         );

        // Compose query
        $Query=array('filter'     => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getDomains');
        $result     = $this->SoapEngine->soapclient->getDomains($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl, $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
            return false;
        } else {
            foreach ($result->domains as $_domain) {
                if ($this->validDomain($_domain->domain)) {
                    $this->allowedDomains[]=$_domain->domain;
                }
            }
        }
    }

    function showTextBeforeCustomerSelection()
    {
        print _("Domain owner");
    }
}
