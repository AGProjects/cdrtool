<?php

class PaypalProcessor
{
    private $CardProcessor;
    private $account;

    var $deny_countries      = array();
    var $allow_countries     = array();
    var $deny_ips            = array();
    var $make_credit_checks  = true;
    var $transaction_results = array('success' => false);
    var $vat                 = 0;

    public function __construct($account)
    {
        require('cc_processor.php');
        $this->CardProcessor = new CreditCardProcessor();
        $this->account = &$account;
    }

    function refundTransaction($transaction_id)
    {
    }

    function doDirectPayment($basket)
    {
        if (!is_object($this->account)) {
            print "
            <tr>
            <td colspan=3>
            ";

            print 'Invalid account data';

            print "
            </td>
            </tr>
            ";

            return false;
        }

        if (!is_array($basket)) {
            print "
            <tr>
            <td colspan=3>
            ";

            print 'Invalid basket data';

            print "
            </td>
            </tr>
            ";
            return false;
        }

        if (is_array($this->test_credit_cards) && in_array($_POST['creditCardNumber'], $this->test_credit_cards)) {
            $this->CardProcessor->environment='sandbox';
        }

        $this->CardProcessor->chapter_class  = 'chapter';
        $this->CardProcessor->odd_row_class  = 'oddc';
        $this->CardProcessor->even_row_class = 'evenc';

        $this->CardProcessor->note = $this->account->account;
        $this->CardProcessor->account = $this->account->account;

        $this->CardProcessor->vat = $this->vat;

        // set hidden elements we need to preserve in the shopping cart application
        $this->CardProcessor->hidden_elements = $this->account->hiddenElements;

        // load shopping items
        $this->CardProcessor->cart_items=$basket;

        // load user information from owner information if available otherwise from sip account settings

        if ($this->account->owner_information['firstName']) {
            $this->CardProcessor->user_account['FirstName']=$this->account->owner_information['firstName'];
        } else {
            $this->CardProcessor->user_account['FirstName']=$this->account->firstName;
        }

        if ($this->account->owner_information['lastName']) {
            $this->CardProcessor->user_account['LastName']=$this->account->owner_information['lastName'];
        } else {
            $this->CardProcessor->user_account['LastName']=$this->account->lastName;
        }

        if ($this->account->owner_information['email']) {
            $this->CardProcessor->user_account['Email']=$this->account->owner_information['email'];
        } else {
            $this->CardProcessor->user_account['Email']=$this->account->email;
        }

        if ($this->account->owner_information['address'] && $this->account->owner_information['address']!= 'Unknown') {
            $this->CardProcessor->user_account['Address1']=$this->account->owner_information['address'];
        } else {
            $this->CardProcessor->user_account['Address1']='';
        }

        if ($this->account->owner_information['city'] && $this->account->owner_information['city']!= 'Unknown') {
            $this->CardProcessor->user_account['City']=$this->account->owner_information['city'];
        } else {
            $this->CardProcessor->user_account['City']='';
        }

        if ($this->account->owner_information['country'] && $this->account->owner_information['country']!= 'Unknown') {
            $this->CardProcessor->user_account['Country']=$this->account->owner_information['country'];
        } else {
            $this->CardProcessor->user_account['Country']='';
        }

        if ($this->account->owner_information['state'] && $this->account->owner_information['state']!= 'Unknown') {
            $this->CardProcessor->user_account['State']=$this->account->owner_information['state'];
        } else {
            $this->CardProcessor->user_account['State']='';
        }

        if ($this->account->owner_information['postcode'] && $this->account->owner_information['postcode']!= 'Unknown') {
            $this->CardProcessor->user_account['PostCode']=$this->account->owner_information['postcode'];
        } else {
            $this->CardProcessor->user_account['PostCode']='';
        }

        if ($_REQUEST['purchase'] == '1') {
            $chapter=sprintf(_("Transaction Results"));
            $this->account->showChapter($chapter);

            print "
            <tr>
            <td colspan=3>
            ";

            // ensure that submit requests are coming only from the current page
            if ($_SERVER['HTTP_REFERER'] == $this->CardProcessor->getPageURL()) {
                // check submitted values
                $errors = $this->CardProcessor->checkForm($_POST);
                if (count($errors) > 0) {
                    print $this->CardProcessor->displayFormErrors($errors);

                    foreach (array_keys($errors) as $key) {
                        $log_text.=sprintf("%s:%s ", $errors[$key]['field'], $errors[$key]['desc']);
                    }

                    $log=sprintf("CC transaction for %s failed with error: %s", $this->account->account, $log_text);
                    syslog(LOG_NOTICE, $log);
                    return false;
                }

                // process the payment
                $b=time();

                $pay_process_results = $this->CardProcessor->processPayment($_POST);
                if (count($pay_process_results['error']) > 0) {
                    // there was a problem with payment
                    // show error and stop

                    if ($pay_process_results['error']['field'] == 'reload') {
                        print $pay_process_results['error']['desc'];
                    } else {
                        print $this->CardProcessor->displayProcessErrors($pay_process_results['error']);
                    }

                    $e=time();
                    $d=$e-$b;

                    $log = sprintf(
                        "CC transaction for %s failed with error: %s (%s) after %d seconds",
                        $this->account->account,
                        $pay_process_results['error']['short_message'],
                        $pay_process_results['error']['error_code'],
                        $d
                    );

                    syslog(LOG_NOTICE, $log);

                    return false;
                } else {
                    $e=time();
                    $d=$e-$b;

                    $log = sprintf(
                        "CC transaction %s for %s completed succesfully in %d seconds",
                        $pay_process_results['success']['desc']->TransactionID,
                        $this->account->account,
                        $d
                    );
                    syslog(LOG_NOTICE, $log);

                    print "<p>";
                    print _("Transaction completed sucessfully. ");

                    /*
                    if ($this->CardProcessor->environment!='sandbox' && $this->account->first_transaction) {
                        print "<p>";
                        print _("This is your first payment. ");

                        print "<p>";
                        print _("Please allow the time to check the validity of your transaction before activating your Credit. ");

                        print "<p>";
                        print _("You can speed up the validation process by sending a copy of an utility bill (electriciy, gas or TV) that displays your address. ");

                        print "<p>";
                        printf(_("For questions related to your payments or to request a refund please email to <i>%s</i> and mention your transaction id <b>%s</b>. "),
                        $this->account->billing_email,
                        $pay_process_results['success']['desc']->TransactionID
                        );

                        $this->make_credit_checks=true;

                    } else {
                       print "<p>";
                       print _("You may check your new balance in the Credit tab. ");
                    }
                    */
                }

                if ($this->account->Preferences['ip'] && $_loc=geoip_record_by_name($this->account->Preferences['ip'])) {
                    $enrollment_location=$_loc['country_name'].'/'.$_loc['city'];
                } elseif ($this->account->Preferences['ip'] && $_loc=geoip_country_name_by_name($this->account->Preferences['ip'])) {
                    $enrollment_location=$_loc;
                } else {
                    $enrollment_location='Unknown';
                }

                if ($_loc=geoip_record_by_name($_SERVER['REMOTE_ADDR'])) {
                    $transaction_location=$_loc['country_name'].'/'.$_loc['city'];
                } elseif ($_loc=geoip_country_name_by_name($_SERVER['REMOTE_ADDR'])) {
                    $transaction_location=$_loc;
                } else {
                    $transaction_location='Unknown';
                }

                if ($this->account->Preferences['timezone']) {
                    $timezone=$this->account->Preferences['timezone'];
                } else {
                    $timezone='Unknown';
                }

                $extra_information=array(
                                         'Account Page'         => $this->account->admin_url_absolute,
                                         'Account First Name'   => $this->account->firstName,
                                         'Account Last Name '   => $this->account->lastName,
                                         'Account Timezone'     => $this->account->timezone,
                                         'Enrollment IP'        => $this->account->Preferences['ip'],
                                         'Enrollment Location'  => $enrollment_location,
                                         'Enrollment Email'     => $this->account->Preferences['registration_email'],
                                         'Enrollment Timezone'  => $timezone,
                                         'Transaction Location' => $transaction_location
                                         );

                $result = $this->account->addInvoice($this->CardProcessor);
                if ($result) {
                    $extra_information['Invoice Page']=sprintf("https://admin.ag-projects.com/admin/invoice.phtml?iId=%d&adminonly=1", $result['invoice']);
                }

                if ($this->CardProcessor->saveOrder($_POST, $pay_process_results, $extra_information)) {

                    $this->transaction_results=array('success' => true,
                                                     'id'      => $this->CardProcessor->transaction_data['TRANSACTION_ID']
                                                     );

                    return true;
                } else {
                    $log=sprintf("Error: SIP Account %s - CC transaction %s failed to save order", $this->account->account, $this->CardProcessor->transaction_data['TRANSACTION_ID']);
                    syslog(LOG_NOTICE, $log);
                    return false;
                }
            } else {
                print _("Invalid CC Request");
                return false;
            }

            print "
            </td>
            </tr>
            ";
        } else {
            print "
            <tr>
            <td colspan=3>
            ";

            // print the submit form
            $arr_form_page_objects = $this->CardProcessor->showSubmitForm();
            print $arr_form_page_objects['page_body_content'];

            print "
            </td>
            </tr>
            ";
        }
    }

    function fraudDetected()
    {
        if (count($this->deny_ips)) {
            foreach ($this->deny_ips as $_ip) {
                if ($this->account->Preferences['ip'] && preg_match("/^$_ip/", $this->account->Preferences['ip'])) {
                    $this->fraud_reason=$this->account->Preferences['ip'].' is Blocked';
                    return true;
                }

                if (preg_match("/^$_ip/", $_SERVER['REMOTE_ADDR'])) {
                    $this->fraud_reason=$_SERVER['REMOTE_ADDR'].' is a Blocked';
                    return true;
                }
            }
        }

        if (count($this->deny_countries)) {
            if ($_loc=geoip_record_by_name($this->account->Preferences['ip'])) {
                if (in_array($_loc['country_name'], $this->deny_countries)) {
                    $this->fraud_reason=$_loc['country_name'].' is Blocked';
                    return true;
                }
            }
        }

        if (count($this->allow_countries)) {
            if ($_loc=geoip_record_by_name($this->account->Preferences['ip'])) {
                if (!in_array($_loc['country_name'], $this->allow_countries)) {
                    $this->fraud_reason=$_loc['country_name'].' is Not Allowed';
                    return true;
                }
            }
        }


        if (count($this->deny_email_domains)) {
            if (count($this->accept_email_addresses)) {
                if (in_array($this->account->email, $this->accept_email_addresses)) return false;
            }

            list($user, $domain)= explode("@", $this->account->email);
            foreach ($this->deny_email_domains as $deny_domain) {
                if ($domain == $deny_domain) {
                    $this->fraud_reason=sprintf('Domain %s is Not Allowed', $domain);
                    return true;
                }
            }
        }

        return false;
    }
}
