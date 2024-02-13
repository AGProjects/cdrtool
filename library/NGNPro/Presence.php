<?php

class Presence {
    function __construct($SoapEngine) {
        $this->SoapEngine         = $SoapEngine;
    }

    function publishPresence ($soapEngine,$SIPaccount=array(),$note='None',$activity='idle') {

        if (!in_array($soapEngine,array_keys($this->SoapEngine->soapEngines))) {
            print "Error: soapEngine '$soapEngine' does not exist.\n";
            return false;
        }

        if (!$SIPaccount['username'] || !$SIPaccount['domain'] || !$SIPaccount['password'] ) {
            print "Error: SIP account not defined\n";
            return false;
        }

        $this->SOAPurl       = $this->SoapEngine->soapEngines[$soapEngine]['url'];
        $this->PresencePort  = new WebService_SoapSIMPLEProxy_PresencePort($this->SOAPurl);

        $this->PresencePort->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
        $this->PresencePort->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);

        $allowed_activities=array('open',
                                  'idle',
                                  'busy',
                                  'available'
                                 );

        if (in_array($activity,$allowed_activities)) {
            $presentity['activity'] = $activity;
        } else {
            $presentity['activity'] = 'open';
        }

        $presentity['note']     = $note;

        $result = $this->PresencePort->setPresenceInformation(array("username" =>$SIPaccount['username'],"domain"   =>$SIPaccount['domain']),$SIPaccount['password'], $presentity);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        return true;
    }

    function getPresenceInformation ($soapEngine,$SIPaccount) {

        if (!in_array($soapEngine,array_keys($this->SoapEngine->soapEngines))) {
            print "Error: soapEngine '$soapEngine' does not exist.\n";
            return false;
        }

        if (!$SIPaccount['username'] || !$SIPaccount['domain'] || !$SIPaccount['password'] ) {
            print "Error: SIP account not defined";
            return false;
        }

        $this->SOAPurl       = $this->SoapEngine->soapEngines[$soapEngine]['url'];
        $this->PresencePort  = new WebService_SoapSIMPLEProxy_PresencePort($this->SOAPurl);

        $this->PresencePort->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
        $this->PresencePort->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);

        $result = $this->PresencePort->getPresenceInformation(array("username" =>$SIPaccount['username'],"domain"   =>$SIPaccount['domain']),$SIPaccount['password']);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            return $result;
        }
    }
}

