<?
/*
    Copyright (c) 2007-2010 AG Projects
    http://ag-projects.com

    This library contains classes and functions for rating functionality
*/

class Rate {
    var $priceDenominator       = 10000; // allow sub cents
    var $priceDecimalDigits     = 4;     // web display
    var $durationPeriodRated    = 60;    // how the prices are indicated in the billing_rates, default is per minute
    var $trafficSizeRated       = 1024;  // in KBytes, default 1MByte
    var $rate_longer_than       = 0;     // minimum duration considered to apply rates for a call, if call is shorter the price is zero
    var $ENUMtld                = '';
    var $ENUMdiscount           = 0;     // how much percentage to substract from the final price
    var $price                  = 0;
    var $spans                  = 0;     // number of spans we looped through
	var $connectCost            = 0;
    var $increment              = 0;     // used to consider the duration of the call in increments (default 1 second)
    var $min_duration           = 0;     // minimum duration considered for calculating the price
    var $max_duration           = 0;     // maximum duration considered for calculating the price
    var $max_price              = 0;     // maximum price for the call
    var $discount_connect       = 0;
    var $discount_duration      = 0;

    var $rateValuesCache        = array(); // used to speed up prepaid apoplication
    var $brokenRates            = array();

    function Rate($settings=array(),&$db) {

        $this->settings = $settings;

        $this->db = &$db;

        $this->db->Halt_On_Error="no";

        if ($this->settings['priceDenominator']) {
            $this->priceDenominator=$this->settings['priceDenominator'];
        }

        if ($this->settings['priceDecimalDigits']) {
            $this->priceDecimalDigits=$this->settings['priceDecimalDigits'];
        }

        if ($this->settings['durationPeriodRated']) {
            $this->durationPeriodRated=$this->settings['durationPeriodRated'];
        }

        if ($this->settings['trafficSizeRated']) {
            $this->trafficSizeRated=$this->settings['trafficSizeRated'];
        }

        if ($this->settings['rate_longer_than']) {
            // if call is shorter than this, it has zero cost
            $this->rate_longer_than=$this->settings['rate_longer_than'];
        }

        if ($this->settings['min_duration']) {
            // if call is shorter than this, it has zero cost
            $this->min_duration=$this->settings['min_duration'];
        }

        if ($this->settings['increment']) {
            $this->increment=$this->settings['increment'];
        }

    }

    function calculateAudio(&$dictionary) {
        // used for calculate rate for audio application

        $this->RatingTables      = &$dictionary['RatingTables'];

        $this->callId            = $dictionary['callId'];
        $this->timestamp         = $dictionary['timestamp'];

        $this->duration          = $dictionary['duration'];
        $this->traffic           = 2 * ($dictionary['inputTraffic'] + $dictionary['outputTraffic']);

        $this->DestinationId     = $dictionary['DestinationId'];

        $this->BillingPartyId    = $dictionary['BillingPartyId'];
        $this->domain            = $dictionary['domain'];
        $this->gateway           = $dictionary['gateway'];
        $this->ResellerId        = $dictionary['ResellerId'];

        $this->aNumber           = $dictionary['aNumber'];
        $this->cNumber           = $dictionary['cNumber'];
        $this->ENUMtld           = $dictionary['ENUMtld'];

        if ($this->rate_longer_than && $this->duration < $this->rate_longer_than) {
            //syslog(LOG_NOTICE, "Duration less than minimum $this->rate_longer_than");
            $this->rateInfo .= "   Duration < $this->rate_longer_than s\n";
            return false;
        }

        if ($this->ENUMtld && $this->ENUMtld != 'n/a' && $this->ENUMtld != 'none' && $this->RatingTables->ENUMtlds[$this->ENUMtld]) {
            $this->ENUMdiscount = $this->RatingTables->ENUMtlds[$this->ENUMtld]['discount'];
            if (!is_numeric($this->ENUMdiscount ) || $this->ENUMdiscount < 0 || $this->ENUMdiscount > 100) {
                  syslog(LOG_NOTICE, "Error: ENUM discount for tld $this->ENUMtld must be between 0 and 100");
            }
        }

        if (!$this->gateway) {
        	$this->gateway="0.0.0.0";
        }

        if (!$this->duration) {
        	$this->duration = 0;
        }

        if (!$this->traffic) {
        	$this->traffic = 0;
        }

        $this->application='audio';

        $durationRate = 0;

        $foundRates=array();

        if (!$this->DestinationId) {
            syslog(LOG_NOTICE, "Error: Cannot calculate rate without destination id for callid=$this->callId");
            return false;
        }

		if (!$this->lookupDestinationDetails()) {
            // get region, increment and other per destination details
            syslog(LOG_NOTICE, "Error: Cannot find destination details for call_id=$this->callId, dest_id=$this->DestinationId)");
            return false;
        }

        if (!$this->lookupProfiles()) {
            // get profiles for the billing party
            syslog(LOG_NOTICE, "Error: Cannot find any profiles for call_id=$this->callId, dest_id=$this->DestinationId)");
            return false;
        }

        // lookup discounts if any
		$this->lookupDiscounts();

        $this->startTimeBilling   = getLocalTime($this->billingTimezone,$this->timestamp);
        list($dateText,$timeText) = explode(" ",trim($this->startTimeBilling));

        $Bdate = explode("-",$dateText);
        $Btime = explode(":",$timeText);

        $this->timestampBilling   = mktime($Btime[0], $Btime[1], $Btime[2], $Bdate[1], $Bdate[2], $Bdate[0]);

        $this->startTimeBilling   = Date("Y-m-d H:i:s",$this->timestampBilling);

        $this->trafficKB=number_format($this->traffic/1024,0,"","");

        // check min_duration and increment per destination
        if ($this->increment >= 1) {
            // increase the billed duration to the next increment
            $this->duration = $this->increment * ceil($this->duration / $this->increment);
        }

        if ($this->max_duration &&  $this->duration > $this->max_duration) {
            // limit the maximum duration for rating
            $this->duration=$this->max_duration;
        }

        $this->rateSyslog="";

        if ($this->duration) {
            if ($this->increment >= 1) {
                $this->rateInfo .= 
                "    Increment: $this->increment s\n";
                $this->rateSyslog .= sprintf("Increment=%s ",$this->increment);
            }
    
            if ($this->min_duration) {
                $this->rateInfo .= 
                " Min duration: $this->min_duration s\n";
                $this->rateSyslog .= sprintf("MinDuration=%s ",$this->min_duration);
            }

            if ($this->max_duration) {
                $this->rateInfo .= 
                " Max duration: $this->max_duration s\n";
                $this->rateSyslog .= sprintf("MaxDuration=%s ",$this->max_duration);
            }

            if ($this->max_price) {
                $this->rateInfo .= 
                " Max price: $this->max_price\n";
                $this->rateSyslog .= sprintf("MaxPrice=%s ",$this->max_price);
            }

            unset($IntervalsForPricing);

            $this->rateInfo .= 
    
                "    Duration: $this->duration s\n".
                "         App: $this->application\n".
                " Destination: $this->DestinationId\n".
                "    Customer: $this->CustomerProfile\n";

            if ($this->region) {
            $this->rateInfo .= 
                "      Region: $this->region\n";
            }

            if ($this->discount_duration || $this->discount_connect) {
            	$this->rateInfo .=
                "    Discount: ";
            }

            if ($this->discount_connect) {
            	$this->rateInfo .= " connect $this->discount_connect% ";
            }

            if ($this->discount_duration) {
	            $this->rateInfo .= " duration $this->discount_duration% ";
            }

            if ($this->discount_duration || $this->discount_connect) {
            	$this->rateInfo .= "\n";
            }

            if ($this->ENUMtld && $this->ENUMtld != 'none' && $this->ENUMtld != 'n/a') {
            $this->rateInfo .= 
    
                "    ENUM tld: $this->ENUMtld\n".
                "    ENUM discount: $this->ENUMdiscount%\n";
            }

            $i=0;
            $durationRatedTotal=0;

            // get recursively a set of arrays with rates
            // until we billed the whole duration

            while ($durationRatedTotal < $this->duration) {

                if ($i == "0") {
                    $dayofweek       = date("w",$this->timestampBilling);
                    $hourofday       = date("G",$this->timestampBilling);
                    $dayofyear       = date("Y-m-d",$this->timestampBilling);
                } else {
                    $dayofweek       = date("w",$this->timestampBilling+$durationRatedTotal);
                    $hourofday       = $foundRate['nextHourOfDay'];
                    $dayofyear       = date("Y-m-d",$this->timestampBilling+$durationRatedTotal);
                }

                $foundRate           = $this->lookupRateAudio($dayofyear,$dayofweek,$hourofday,$durationRatedTotal);
                $durationRatedTotal  = $durationRatedTotal + $foundRate['duration'];

                if (!$foundRate['rate']) {
                    return false;
                }

                $foundRates[]        = $foundRate;

                $i++;

                if ($i > 10) {
                    // possible loop because of wrong coding make sure we end this loop somehow
                    $body="Rating of call $this->callId (DestId=$this->DestinationId) has more than 10 spans. It could be a serious bug.\n";
                    mail($this->toEmail, "CDRTool rating problem", $body , $this->extraHeaders);
                    syslog(LOG_NOTICE, "Error: Rating of call $this->callId (DestId=$this->DestinationId) has more than 10 spans.");
                    break;
                }
            }
        }

        $j=0;
        $span=0;

        foreach ($foundRates as $thisRate) {
            $spanPrice=0;
            $span++;
            if ($j > 0) {
                $payConnect=0;
                $durationForRating=$thisRate['duration'];
            } else {
                $payConnect=1;
                if ($this->min_duration && $this->duration < $this->min_duration) {
                    $durationForRating=$this->min_duration;
                } else {
                    $durationForRating=$thisRate['duration'];
                }
            }

            $connectCost     = $thisRate['values']['connectCost'];
            $durationRate    = $thisRate['values']['durationRate'];

            // apply discounts for connect
            if ($this->discount_connect) {
                $connectCost=$connectCost-$connectCost*$this->discount_connect/100;
            }

            // apply discounts for duration
            if ($this->discount_duration) {
                $durationRate=$durationRate-$durationRate*$this->discount_duration/100;
            }

            $connectCostIn   = $thisRate['values']['connectCostIn'];
            $durationRateIn  = $thisRate['values']['durationRateIn'];

            if ($span=="1") {

                $connectCostSpan=$connectCost;
                $this->connectCost=number_format($connectCost/$this->priceDenominator,$this->priceDecimalDigits);

                $connectCostSpanIn=$connectCostIn;

                $this->connectCostIn=number_format($connectCostIn/$this->priceDenominator,$this->priceDecimalDigits);
            } else {
                $connectCostSpan=0;
                $connectCostSpanIn=0;
            }

            $connectCostPrint     = number_format($connectCostSpan/$this->priceDenominator,$this->priceDecimalDigits);
            $durationRatePrint    = number_format($durationRate/$this->priceDenominator,$this->priceDecimalDigits);

            $connectCostPrintIn   = number_format($connectCostSpanIn/$this->priceDenominator,$this->priceDecimalDigits);
            $durationRatePrintIn  = number_format($durationRateIn/$this->priceDenominator,$this->priceDecimalDigits);

            if (!$connectCostSpan)     $connectCostSpan=0;
            if (!$durationRate)        $durationRate=0;

            if (!$connectCostSpanIn)   $connectCostSpanIn=0;
            if (!$durationRateIn)      $durationRateIn=0;

            if (!$this->inputTraffic)  $this->inputTraffic=0;
            if (!$this->outputTraffic) $this->outputTraffic=0;

            if ($span>1) $this->rateInfo .= "--\n";

            /*
                durationRate*durationForRating/durationPeriodRated/priceDenominator+
                trafficRate/priceDenominator/trafficSizeRated*(inputTraffic+outputTraffic)/8");
    
                $durationRate*$durationForRating/$this->durationPeriodRated/$this->priceDenominator+
                $trafficRate/$this->priceDenominator/$this->trafficSizeRated*($this->inputTraffic+$this->outputTraffic)/8");

            */

            $spanPrice      = $durationRate*$durationForRating/$this->durationPeriodRated/$this->priceDenominator;

            $this->price    = $this->price+$spanPrice;
            $spanPricePrint = number_format($spanPrice,$this->priceDecimalDigits);

            $spanPriceIn      = $durationRateIn*$durationForRating/$this->durationPeriodRated/$this->priceDenominator;
            $this->priceIn    = $this->priceIn+$spanPriceIn;
            $spanPricePrintIn = number_format($spanPriceIn,$this->priceDecimalDigits);

            if ($span=="1" && $thisRate['profile']) {
            	if ($connectCostIn) {
                $this->rateInfo .= 
                "  Connect in: $connectCostPrintIn\n";
                }

                $this->rateInfo .= 
                "     Connect: $connectCostPrint\n".
                "   StartTime: $this->startTimeBilling\n".
                "--\n";
                $this->rateSyslog .= "ConnectFee=$connectCostPrint ";
                $this->price    = $this->price+$connectCostSpan/$this->priceDenominator*$payConnect;
                $this->priceIn  = $this->priceIn+$connectCostSpanIn/$this->priceDenominator*$payConnect;
            }

            $this->rateInfo .= 
            "        Span: $span\n".
            "    Duration: $durationForRating s\n";


            $this->rateSyslog .= sprintf("CallId=%s Span=%s Duration=%s DestId=%s %s",$this->callId,$span,$durationForRating,$this->DestinationId,$thisRate['customer']);

            if ($thisRate['profile']) {
                $this->rateInfo .= 
                "   ProfileId: $thisRate[profile] / $thisRate[day]\n".
                "      RateId: $thisRate[rate] / $thisRate[interval]h\n".
                "        Rate: $durationRatePrint / $this->durationPeriodRated s\n".
                "       Price: $spanPricePrint\n";

                if ($spanPriceIn) {
                $this->rateInfo .= 
                "    Price in: $spanPricePrintIn\n";
                }

                $this->rateSyslog .= sprintf(" Profile=%s Period=%s Rate=%s Interval=%s Cost=%s/%s",$thisRate['profile'],$thisRate['day'],$thisRate['rate'],$thisRate['interval'],$durationRatePrint,$this->durationPeriodRated);

            } else {
                $this->rateInfo .= 
                "   ProfileId: none\n".
                "      RateId: none\n";
                $this->rateSyslog .= " Profile=none, Rate=none";
            }

            $this->rateSyslog .= " Price=".sprintf("%.4f",$spanPrice);
            $this->rateSyslog .= " PriceIn=".sprintf("%.4f",$spanPriceIn);

            if ($this->discount_connect) {
                $this->rateSyslog .= sprintf(" DisCon=%s",$this->discount_connect);
            }
    
            if ($this->discount_duration) {
                $this->rateSyslog .= sprintf(" DisDur=%s",$this->discount_duration);
            }

            syslog(LOG_NOTICE, $this->rateSyslog);

            $j++;
        }
                 
        if ($this->priceIn) {
                $this->rateInfo .= "--\n".
                "   Price out: ".sprintf("%.4f",$this->price)."\n".
                "    Price in: ".sprintf("%.4f",$this->priceIn)."\n".
                "      Margin: ".sprintf("%.4f",$this->price-$this->priceIn)."\n";

        }

        $this->rateInfo=trim($this->rateInfo);

        if ($this->max_price && $this->price > $this->max_price) {
        	$this->price=$this->max_price;
        }

        if ($this->ENUMdiscount) {
            $this->priceBeforeDiscount=sprintf("%.4f",$this->price);
            $this->price = $this->price - $this->price*$this->ENUMdiscount/100;
            $this->price=sprintf("%.4f",$this->price);
                $this->rateInfo .=
                "\n--\n".
                "       Total: $this->priceBeforeDiscount\n".
                "  Total after discount: $this->price\n";
        }

        $this->price=sprintf("%.4f",$this->price);

        if ($this->price > 0) {
            $this->pricePrint=number_format($this->price,$this->priceDecimalDigits);
        } else if ($thisRate[profile]) {
            if ($j) {
                if ($this->DestinationId && !strlen($durationRate)) {
                    $this->brokenRates[$this->DestinationId]++;
                }
            }
            $this->pricePrint="";
        }

        return true;
    }

    function calculateMessage(&$dictionary) {
        // used for calculate rate for SMS application

        $this->RatingTables      = &$dictionary['RatingTables'];

        $this->callId            = $dictionary['callId'];
        $this->timestamp         = $dictionary['timestamp'];

        $this->DestinationId     = $dictionary['DestinationId'];

        $this->BillingPartyId    = $dictionary['BillingPartyId'];
        $this->domain            = $dictionary['domain'];
        $this->gateway           = $dictionary['gateway'];
        $this->ResellerId        = $dictionary['ResellerId'];

        $this->aNumber           = $dictionary['aNumber'];
        $this->cNumber           = $dictionary['cNumber'];

        if (!$this->gateway) {
        	$this->gateway="0.0.0.0";
        }

        $this->application='sms';

        $foundRates=array();

        if (!$this->DestinationId) {
            syslog(LOG_NOTICE, "Error calculateMessage(): Cannot calculate rate without destination id");
            return false;
        }

        if (!$this->lookupProfiles()) {
            // get profiles for the billing party
            syslog(LOG_NOTICE, "Error: calculateMessage() Cannot find any profiles for call_id=$this->callId, dest_id=$this->DestinationId)");
            return false;
        }

        // lookup discounts if any
		$this->lookupDiscounts();

        $this->startTimeBilling = getLocalTime($this->billingTimezone,$this->timestamp);

        list($dateText,$timeText) = explode(" ",trim($this->startTimeBilling));

        $Bdate = explode("-",$dateText);
        $Btime = explode(":",$timeText);

        $this->timestampBilling = mktime($Btime[0], $Btime[1], $Btime[2], $Bdate[1], $Bdate[2], $Bdate[0]);

        $dayofweek = date("w",$this->timestampBilling);
        $hourofday = date("G",$this->timestampBilling);
        $dayofyear = date("Y-m-d",$this->timestampBilling);

        $this->rateInfo .= 

            "         App: sms\n".
            " Destination: $this->DestinationId\n".
            "    Customer: $this->CustomerProfile\n";

        if ($this->region) {
        $this->rateInfo .= 
            "      Region: $this->region\n";
        }

        if ($this->discount_duration || $this->discount_connect) {
            $this->rateInfo .=
            "    Discount: ";
        }

        if ($this->discount_connect) {
            $this->rateInfo .= " connect $this->discount_connect% ";
        }

        if ($this->discount_duration || $this->discount_connect) {
            $this->rateInfo .= "\n";
        }

        $foundRate = $this->lookupRateMessage($dayofyear,$dayofweek,$hourofday);

        if (is_array($foundRate)) {
            $this->price=number_format($foundRate['values']['connectCost']/$this->priceDenominator,$this->priceDecimalDigits);
        	$this->price=sprintf("%.4f",$this->price);
            $this->pricePrint=$this->price;

            $this->rateInfo .= 
            "   ProfileId: $foundRate[profile] / $foundRate[day]\n".
            "      RateId: $foundRate[rate]\n".
            "       Price: $this->price\n";

        	return true;
        } else {
            return false;
        }
    }

    function lookupDiscounts() {
        // get discounts for customer per region if set otherwise per destination id

		if (!$this->CustomerProfile) {
            return false;
        }

        if ($this->region) {
        	$_field='region';
            $_value=$this->region;
        } else {
        	$_field='destination';
            $_value=$this->DestinationId;
        }
		
        if ($this->CustomerProfile == 'default') {

            $query=sprintf("select * from billing_discounts
            where subscriber     = ''
            and domain           = ''
            and gateway          = ''
            and application      = '%s'
            and %s               = '%s'
            ",
            addslashes($this->application),
            addslashes($_field),
            addslashes($_value)
            );
        } else {
        	$els=explode("=",$this->CustomerProfile);

            $query=sprintf("select * from billing_discounts
            where %s = '%s'
            and application = '%s'
            and %s  = '%s'
            ",
            addslashes($els[0]),
            addslashes($els[1]),
            addslashes($this->application),
            addslashes($_field),
            addslashes($_value)
            );
        }

        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            syslog(LOG_NOTICE, $log);
            return false;
        }
        if ($this->db->num_rows()) {
            $this->db->next_record();

            if ($this->db->f('connect') > 0 && $this->db->f('connect') <=100) {
            	$this->discount_connect  = $this->db->f('connect');
            }

            if ($this->db->f('duration') > 0 && $this->db->f('duration') <=100) {
            	$this->discount_duration = $this->db->f('duration');
            }
        }

        return true;
    }

    function lookupDestinationDetails() {
        // get rating related details for the destination id

        if (!$this->DestinationId) {
            syslog(LOG_NOTICE, "Error: Cannot lookup destination details without a destination id");
            return false;
        }

        $query=sprintf("select * from destinations
        where dest_id = '%s'
        and (reseller_id = %d or reseller_id = 0) order by reseller_id desc limit 1",
        addslashes($this->DestinationId),
        addslashes($this->ResellerId)
        );

        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            syslog(LOG_NOTICE, $log);
            return false;
        }

        if ($this->db->num_rows()) {
            $this->db->next_record();

            $this->region          = $this->db->Record['region'];
            $this->max_duration    = $this->db->Record['max_duration'];
            $this->max_price       = $this->db->Record['max_price'];

            if ($this->db->Record['increment']) {
                $this->increment = $this->db->Record['increment'];
            }

            if ($this->db->Record['min_duration']) {
                $this->min_duration = $this->db->Record['min_duration'];
            }
        }

        return true;
    }

    function lookupProfiles() {
        unset($this->allProfiles);

        /*
        lookup the profile_name in billing_customers in the following order:
           subscriber, domain, gateway (based on $dayofweek):
           - profile_workday matches days [1-5] (Work-day)
           - profile_weekend matches days [6-0] (Week-end)
           - week starts with 0 Sunday and ends with 6 Saturday

            Alternatively look for profile_workday_alt and profile_weekend_alt
            If no rates are found for destination in the profileX,
            than lookup rates in profileX_alt
        */

        $query=sprintf("select * from billing_customers
        where subscriber = '%s'
        or domain        = '%s'
        or gateway       = '%s'
        or (subscriber = '' and domain = '' and gateway = '')
        order by subscriber desc, domain desc, gateway desc limit 1 ",
        addslashes($this->BillingPartyId),
        addslashes($this->domain),
        addslashes($this->gateway)
        );

        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            syslog(LOG_NOTICE, $log);
            return false;
        }

        if ($this->db->num_rows()) {
            $this->db->next_record();

            if ($this->db->Record['subscriber']) {
                $this->CustomerProfile = sprintf("subscriber=%s",$this->db->Record['subscriber']);
            } else if ($this->db->Record['domain']) {
                $this->CustomerProfile = sprintf("domain=%s",$this->db->Record['domain']);
            } else if ($this->db->Record['gateway']) {
                $this->CustomerProfile = sprintf("gateway=%s",$this->db->Record['gateway']);
            } else {
                $this->CustomerProfile = "default";
            }

            if (!$this->db->Record['profile_name1']) {
                $log=sprintf("Error: customer %s (id=%d) has no weekday profile assigned in profiles table",$this->CustomerProfile,$this->db->Record['id']);
                syslog(LOG_NOTICE, $log);
                return false;
            }

            if (!$this->db->Record['profile_name2']) {
                $log=sprintf("Error: customer %s (id=%d) has no weekend profile assigned in profiles table",$this->CustomerProfile,$this->db->Record['id']);
                syslog(LOG_NOTICE, $log);
                return false;
            }


            if (!$this->db->Record['timezone']) {
                $log = sprintf ("Error: missing timezone for customer %s",$this->CustomerProfile);
                syslog(LOG_NOTICE, $log);
                return false;
            }

            $this->billingTimezone = $this->db->Record['timezone'];

            $this->allProfiles = array (
                                        "profile_workday"     => $this->db->Record['profile_name1'],
                                        "profile_weekend"     => $this->db->Record['profile_name2'],
                                        "profile_workday_alt" => $this->db->Record['profile_name1_alt'],
                                        "profile_weekend_alt" => $this->db->Record['profile_name2_alt'],
                                        "timezone"            => $this->db->Record['timezone']
                                    );
            if ($this->db->Record['increment']) {
                $this->increment = $this->db->Record['increment'];
            }

            if ($this->db->Record['min_duration']) {
                $this->min_duration = $this->db->Record['min_duration'];
            }

            return true;
        } else {
            $log=sprintf("Error: no customer found in billing_customers table for billing party=%s, domain=%s, gateway=%s",$this->BillingPartyId,$this->domain,$this->gateway);
            syslog(LOG_NOTICE, $log);
            return false;
        }
    }

    function lookupRateAudio($dayofyear,$dayofweek,$hourofday,$durationRatedAlready) {

        /*
        // Required information from CDR structure
        $this->BillingPartyId  # calling subscriber
        $this->domain          # multiple callers may belong to same domain
        $this->gateway         # multiple callers may belong to the same gateway

        $this->cNumber         # E164 destination prefixed with 00  (e.g. 0041 CH)
        $this->DestinationId   # longest matched DestinationId
        $this->region          # region the destination belongs to

        // pertinent to the curent rating SPAN (a span = same profile like evening hours)
        $hourofday             # which hour of teh day started for peak/ofpeak rates
        $dayofweek             # which day of the week for matching profiles
        $dayofyear             # which day of the year for matching holidays

        $durationRatedAlready= the full duration for which a profile is defined (e.g. 0800-1800)
        // the call is called recursively until the $durationRatedAlready = $CDR->duration
        // when a call spans multiple profiles. If we span multiple profiles we must call
        // the function again to lookup the corect rates


        Rating logic
        ------------

        1. using the profile_name found, lookup the rate_name based
           on $hourofday in billing_profiles
           - the day may be split in maximum 4 periods
           - each day starts with hour 0 and ends with hour 24
           - rate_name1 defines the first interval after hour 0
           - rate_name2 defines the first interval after rate_name1
           - rate_name3 defines the first interval after rate_name2
           - rate_name4 defines the first interval after rate_name3
           When the hour matches an interval use the rate_nameX found
           to lookup the rate in billing_rates
           - if no record is found use the rate called 'default'
        2. lookup in billing_rates the record having same name found above
           and billing_rates.destination = $this->DestinationId
           - return an array with all the values to
           $this->calculateAudio() function that called us

        */

        // get work-day or weekend profile
        if ($this->RatingTables->holidays[$dayofyear]) {

            $this->profileName           = $this->allProfiles['profile_weekend'];
            $this->profileNameAlt        = $this->allProfiles['profile_weekend_alt'];
            $this->PeriodOfProfile       = "weekend";

        } else {
            if ($dayofweek >=1 && $dayofweek <=5 ) {
                $this->profileName       = $this->allProfiles['profile_workday'];
                $this->profileNameAlt    = $this->allProfiles['profile_workday_alt'];
                $this->PeriodOfProfile   = "weekday";
            } else {
                $this->profileName       = $this->allProfiles['profile_weekend'];
                $this->profileNameAlt    = $this->allProfiles['profile_weekend_alt'];
                $this->PeriodOfProfile   = "weekend";
            }
        }

        // get rate for the time of the day
        $timestampNextProfile    = $this->timestampBilling + $durationRatedAlready;
        $profileValues           = $this->RatingTables->profiles[$this->profileName];

        if (is_array($profileValues)) {
            $this->profileNameLog = $this->profileName;

            if ($hourofday          < $profileValues['hour1'] ) {
                $this->rateName     = $profileValues['rate_name1'];
                $this->timeInterval = "0-".$profileValues['hour1'];
                $foundProfile       = $profileValues['hour1'];
                $this->nextProfile  = $profileValues['hour1'];
            } else if ($hourofday   < $profileValues['hour2']) {
                $this->rateName     = $profileValues['rate_name2'];
                $this->timeInterval = $profileValues['hour1']."-".$profileValues['hour2'];
                $foundProfile       = $profileValues['hour2'];
                $this->nextProfile  = $profileValues['hour2'];
            } else if ($hourofday   < $profileValues['hour3']) {
                $this->rateName     = $profileValues['rate_name3'];
                $this->timeInterval = $profileValues['hour2']."-".$profileValues['hour3'];
                $foundProfile       = $profileValues['hour3'];
                $this->nextProfile  = $profileValues['hour3'];
            } else if ($hourofday   < $profileValues['hour4']) {
                $this->rateName     = $profileValues['rate_name4'];
                $this->timeInterval = $profileValues['hour3']."-".$profileValues['hour4'];
                $foundProfile       = $profileValues['hour4'];
                $this->nextProfile  = 0;
            }

            if ($this->rateName) {

                $found_history=false;

                //get historical rating if exists
                if (is_array($this->RatingTables->ratesHistory[$this->rateName][$this->DestinationId][$this->application])) {
                    $h=0;
                    foreach (($this->RatingTables->ratesHistory[$this->rateName][$this->DestinationId][$this->application]) as $_idx) {
                        $h++;
                        if ($_idx['startDate'] <= $this->timestamp) {

                            if ($_idx['endDate'] > $this->timestamp) {
                                // found historical rate
                                $found_history=true;

                                $this->rateValues=$_idx;
                                break;
                            } else {
                                $_log=sprintf("Interval missmatch %s < %s",$_idx['endDate'],$this->timestamp);

                                continue;
                            }
                        } else {
                            $_log=sprintf("Interval missmatch %s > %s",$_idx['startDate'],$this->timestamp);
                            continue;
                        }

                    }

                }

                if (!$found_history) {
                    if ($this->region) {
                        $this->rateValues=$this->lookupRateValuesAudio($this->rateName,$this->region);
                        if (!$this->rateValues) {
                            // try the destination as last resort
                            $this->rateValues=$this->lookupRateValuesAudio($this->rateName,$this->DestinationId);
                        }

                    } else {
                        if (!$this->rateValues) {
                            $this->rateValues=$this->lookupRateValuesAudio($this->rateName,$this->DestinationId);
                        }
                    }
                }
            }
        }

        $profileValuesAlt        = $this->RatingTables->profiles[$this->profileNameAlt];

        if (!$this->rateValues && is_array($profileValuesAlt)) {
            $this->profileNameLog = $this->profileNameAlt;

            if ($hourofday          < $profileValuesAlt['hour1'] ) {
                $this->rateName     = $profileValuesAlt['rate_name1'];
                $this->timeInterval = "0-".$profileValuesAlt['hour1'];
                $foundProfile       = $profileValuesAlt['hour1'];
                $this->nextProfile  = $profileValuesAlt['hour1'];
            } else if ($hourofday   < $profileValuesAlt['hour2']) {
                $this->rateName     = $profileValuesAlt['rate_name2'];
                $this->timeInterval = $profileValuesAlt['hour1']."-".$profileValuesAlt['hour2'];
                $foundProfile       = $profileValuesAlt['hour2'];
                $this->nextProfile  = $profileValuesAlt['hour2'];
            } else if ($hourofday   < $profileValuesAlt['hour3']) {
                $this->rateName     = $profileValuesAlt['rate_name3'];
                $this->timeInterval = $profileValuesAlt['hour2']."-".$profileValuesAlt['hour3'];
                $foundProfile       = $profileValuesAlt['hour3'];
                $this->nextProfile  = $profileValuesAlt['hour3'];
            } else if ($hourofday   < $profileValuesAlt['hour4']) {
                $this->rateName     = $profileValuesAlt['rate_name4'];
                $this->timeInterval = $profileValuesAlt['hour3']."-".$profileValuesAlt['hour4'];
                $foundProfile       = $profileValuesAlt['hour4'];
                $this->nextProfile  = 0;
            }

            if ($this->rateName) {

                $found_history=false;

                //get historical rating if exists
                if (is_array($this->RatingTables->ratesHistory[$this->rateName][$this->DestinationId][$this->application])) {
                    $h=0;
                    foreach (($this->RatingTables->ratesHistory[$this->rateName][$this->DestinationId][$this->application]) as $_idx) {
                        $h++;
                        if ($_idx['startDate'] <= $this->timestamp) {

                            if ($_idx['endDate'] > $this->timestamp) {
                                // found historical rate
                                $found_history=true;
                                $this->rateValues=$_idx;
                                break;
                            } else {
                                $_log=sprintf("Interval missmatch %s < %s",$_idx['endDate'],$this->timestamp);

                                continue;
                            }
                        } else {
                            $_log=sprintf("Interval missmatch %s > %s",$_idx['startDate'],$this->timestamp);
                            continue;
                        }
                    }
                }

                if (!$found_history) {
                    if ($this->region) {
                        $this->rateValues=$this->lookupRateValuesAudio($this->rateName,$this->region);
                        // try destination as last resort
                        if (!$this->rateValues) {
                            $this->rateValues=$this->lookupRateValuesAudio($this->rateName,$this->DestinationId);
                        }

                    } else {
                        if (!$this->rateValues) {
                            $this->rateValues=$this->lookupRateValuesAudio($this->rateName,$this->DestinationId);
                        }
                    }
                }
            }
        }

        if (!$this->rateValues) {
            $this->rateNotFound=true;
            $log=sprintf("Error: Cannot find rates for callid=%s, billing party=%s, customer %s, gateway=%s, destination=%s, profile=%s, app=%s",
            $this->callId,$this->BillingPartyId,$this->CustomerProfile,$this->gateway,$this->DestinationId,$this->profileName,$this->application);
            syslog(LOG_NOTICE, $log);
            return false;
        }

        if ($this->nextProfile == "24") $this->nextProfile = 0;

        $DST   = Date("I",$timestampNextProfile);

        if (!$this->nextProfile) {
            // check it we change daylight saving time tomorrow
            // yes this cann happen and we must apply a different rate
            $timestampNextProfile =$timestampNextProfile+24*3600;
            $DSTNext   = Date("I",$timestampNextProfile);

            if ($DST != $DSTNext) {
                if ($DSTNext==0) {
                    $timestampNextProfile=$timestampNextProfile+3600;
                } else if ($DSTNext==1) {
                    $timestampNextProfile=$timestampNextProfile-3600;
                }
            }
        }

        // see if we have minimum duration or increment
        if ($this->rateValues['increment']) {
            // increase the billed duration to the next increment
            $this->duration = $this->rateValues['increment'] * ceil($this->duration / $this->rateValues['increment']);
        }

        $durationToRate=$this->duration-$durationRatedAlready;

        $month = Date("m",$timestampNextProfile);
        $day   = Date("d",$timestampNextProfile);
        $year  = Date("Y",$timestampNextProfile);

        $nextProfileTimestamp=mktime($this->nextProfile, 0, 0, $month,$day,$year);

        $npdt=Date("Y-m-d H:i", $nextProfileTimestamp);

        $timeTillNextProfile=$nextProfileTimestamp-$this->timestampBilling;

        if ($durationToRate > $timeTillNextProfile) {
            $diff=$durationToRate-$timeTillNextProfile;
            $this->durationRated=$timeTillNextProfile;
        } else {
            $this->durationRated=$durationToRate;
        }

        $rate=array(
                    "customer"      => $this->CustomerProfile,
                    "application"   => $this->application,
                    "profile"       => $this->profileNameLog,
                    "day"           => $this->PeriodOfProfile,
                    "destinationId" => $this->DestinationId,
                    "duration"      => $this->durationRated,
                    "rate"          => $this->rateName,
                    "values"        => $this->rateValues,
                    "interval"      => $this->timeInterval,
                    "nextHourOfDay" => $this->nextProfile
                    );
        return $rate;
    }

    function lookupRateMessage($dayofyear,$dayofweek,$hourofday) {

        /*
        // Required information from CDR structure
        $this->BillingPartyId  # calling subscriber
        $this->domain          # multiple callers may belong to same domain
        $this->gateway         # multiple callers may belong to the same gateway

        $this->cNumber         # E164 destination prefixed with 00  (e.g. 0041 CH)
        $this->DestinationId   # longest matched DestinationId
        $this->region          # region the destination belongs to

        // pertinent to the curent rating SPAN (a span = same profile like evening hours)
        $hourofday             # which hour of teh day started for peak/ofpeak rates
        $dayofweek             # which day of the week for matching profiles
        $dayofyear             # which day of the year for matching holidays

        $durationRatedAlready= the full duration for which a profile is defined (e.g. 0800-1800)
        // the call is called recursively until the $durationRatedAlready = $CDR->duration
        // when a call spans multiple profiles. If we span multiple profiles we must call
        // the function again to lookup the corect rates


        Rating logic
        ------------

        1. using the profile_name found, lookup the rate_name based
           on $hourofday in billing_profiles
           - the day may be split in maximum 4 periods
           - each day starts with hour 0 and ends with hour 24
           - rate_name1 defines the first interval after hour 0
           - rate_name2 defines the first interval after rate_name1
           - rate_name3 defines the first interval after rate_name2
           - rate_name4 defines the first interval after rate_name3
           When the hour matches an interval use the rate_nameX found
           to lookup the rate in billing_rates
           - if no record is found use the rate called 'default'
        2. lookup in billing_rates the record having same name found above
           and billing_rates.destination = $this->DestinationId
           - return an array with all the values to
           $this->calculateAudio() function that called us

        */

        // get work-day or weekend profile
        if ($this->RatingTables->holidays[$dayofyear]) {

            $this->profileName           = $this->allProfiles['profile_weekend'];
            $this->profileNameAlt        = $this->allProfiles['profile_weekend_alt'];
            $this->PeriodOfProfile       = "weekend";

        } else {
            if ($dayofweek >=1 && $dayofweek <=5 ) {
                $this->profileName       = $this->allProfiles['profile_workday'];
                $this->profileNameAlt    = $this->allProfiles['profile_workday_alt'];
                $this->PeriodOfProfile   = "weekday";
            } else {
                $this->profileName       = $this->allProfiles['profile_weekend'];
                $this->profileNameAlt    = $this->allProfiles['profile_weekend_alt'];
                $this->PeriodOfProfile   = "weekend";
            }
        }

        // get rate for the time of the day
        $timestampNextProfile    = $this->timestampBilling + $durationRatedAlready;
        $profileValues           = $this->RatingTables->profiles[$this->profileName];

        if (is_array($profileValues)) {
            $this->profileNameLog = $this->profileName;

            if ($hourofday          < $profileValues['hour1'] ) {
                $this->rateName     = $profileValues['rate_name1'];
                $this->timeInterval = "0-".$profileValues['hour1'];
                $foundProfile       = $profileValues['hour1'];
            } else if ($hourofday   < $profileValues['hour2']) {
                $this->rateName     = $profileValues['rate_name2'];
                $this->timeInterval = $profileValues['hour1']."-".$profileValues['hour2'];
                $foundProfile       = $profileValues['hour2'];
            } else if ($hourofday   < $profileValues['hour3']) {
                $this->rateName     = $profileValues['rate_name3'];
                $this->timeInterval = $profileValues['hour2']."-".$profileValues['hour3'];
                $foundProfile       = $profileValues['hour3'];
            } else if ($hourofday   < $profileValues['hour4']) {
                $this->rateName     = $profileValues['rate_name4'];
                $this->timeInterval = $profileValues['hour3']."-".$profileValues['hour4'];
                $foundProfile       = $profileValues['hour4'];
            }

            if ($this->rateName) {
                if ($this->region) {
                    $this->rateValues=$this->lookupRateValuesMessage($this->rateName,$this->region);
                    if (!$this->rateValues) {
                        // try the destination as last resort
                        $this->rateValues=$this->lookupRateValuesMessage($this->rateName,$this->DestinationId);
                    }
                } else {
                    if (!$this->rateValues) {
                        $this->rateValues=$this->lookupRateValuesMessage($this->rateName,$this->DestinationId);
                    }
                }
            }
        }

        $profileValuesAlt        = $this->RatingTables->profiles[$this->profileNameAlt];

        if (!$this->rateValues && is_array($profileValuesAlt)) {
            $this->profileNameLog = $this->profileNameAlt;

            if ($hourofday          < $profileValuesAlt['hour1'] ) {
                $this->rateName     = $profileValuesAlt['rate_name1'];
                $this->timeInterval = "0-".$profileValuesAlt['hour1'];
                $foundProfile       = $profileValuesAlt['hour1'];
            } else if ($hourofday   < $profileValuesAlt['hour2']) {
                $this->rateName     = $profileValuesAlt['rate_name2'];
                $this->timeInterval = $profileValuesAlt['hour1']."-".$profileValuesAlt['hour2'];
                $foundProfile       = $profileValuesAlt['hour2'];
            } else if ($hourofday   < $profileValuesAlt['hour3']) {
                $this->rateName     = $profileValuesAlt['rate_name3'];
                $this->timeInterval = $profileValuesAlt['hour2']."-".$profileValuesAlt['hour3'];
                $foundProfile       = $profileValuesAlt['hour3'];
            } else if ($hourofday   < $profileValuesAlt['hour4']) {
                $this->rateName     = $profileValuesAlt['rate_name4'];
                $this->timeInterval = $profileValuesAlt['hour3']."-".$profileValuesAlt['hour4'];
                $foundProfile       = $profileValuesAlt['hour4'];
            }

            if ($this->rateName) {

                if ($this->region) {
                    $this->rateValues=$this->lookupRateValuesMessage($this->rateName,$this->region);
                    // try destination as last resort
                    if (!$this->rateValues) {
                        $this->rateValues=$this->lookupRateValuesMessage($this->rateName,$this->DestinationId);
                    }

                } else {
                    if (!$this->rateValues) {
                        $this->rateValues=$this->lookupRateValuesMessage($this->rateName,$this->DestinationId);
                    }
                }
            }
        }

        if (!$this->rateValues) {
            $this->rateNotFound=true;
            $log=sprintf("Error: Cannot find rates for callid=%s, billing party=%s, customer %s, gateway=%s, destination=%s, profile=%s, app=sms",
            $this->callId,$this->BillingPartyId,$this->CustomerProfile,$this->gateway,$this->DestinationId,$this->profileName);
            syslog(LOG_NOTICE, $log);
            return false;
        }

        $rate=array(
                    "customer"      => $this->CustomerProfile,
                    "application"   => $this->application,
                    "profile"       => $this->profileNameLog,
                    "day"           => $this->PeriodOfProfile,
                    "destinationId" => $this->DestinationId,
                    "rate"          => $this->rateName,
                    "values"        => $this->rateValues,
                    );

        return $rate;
    }

    function MaxSessionTime(&$dictionary) {
        // Used for prepaid application to return maximum session time based on a prepaid balance

		$this->rateValuesCache=array();

        $this->MaxSessionTimeSpans=0;

        $durationRate           = 0;

        /////////////////////////////////////////////////////
        // required fields passed from the CDR structure
        //
        $this->timestamp         = time();
        $this->callId            = $dictionary['callId'];
        $this->DestinationId     = $dictionary['DestinationId'];
        $this->BillingPartyId    = $dictionary['BillingPartyId'];
        $this->domain            = $dictionary['domain'];
        $this->duration          = $dictionary['duration'];
        $this->aNumber           = $dictionary['aNumber'];
        $this->cNumber           = $dictionary['cNumber'];
        $this->gateway           = $dictionary['gateway'];
        $this->RatingTables      = &$dictionary['RatingTables'];
        $this->application       = $dictionary['application'];
        $this->ResellerId        = $dictionary['ResellerId'];
        $Balance                 = $dictionary['Balance'];

        if (!$this->application) $this->application='audio';

        if (!$this->DestinationId) {
            $log=sprintf("Error: no DestinationId supplied in MaxSessionTime()");
            syslog(LOG_NOTICE, $log);
            return false;
        }

		if (!$this->lookupDestinationDetails()) {
            return false;
        }

        if (!$this->lookupProfiles()) {
            return false;
        }

        $this->startTimeBilling   = getLocalTime($this->billingTimezone,$this->timestamp);
        list($dateText,$timeText) = explode(" ",trim($this->startTimeBilling));

        $Bdate = explode("-",$dateText);
        $Btime = explode(":",$timeText);

        $this->timestampBilling   = mktime($Btime[0], $Btime[1], $Btime[2], $Bdate[1], $Bdate[2], $Bdate[0]);
        $this->startTimeBilling   = Date("Y-m-d H:i:s",$this->timestampBilling);

        $i=0;
        $durationRatedTotal=0;

        while ($Balance > 0 ) {
            $span++;
            $this->MaxSessionTimeSpans++;

            if ($i == "0") {
                $dayofweek       = date("w",$this->timestampBilling);
                $hourofday       = date("G",$this->timestampBilling);
                $dayofyear       = date("Y-m-d",$this->timestampBilling);
            } else {
                $dayofweek       = date("w",$this->timestampBilling+$durationRatedTotal);
                $hourofday       = $foundRate['nextHourOfDay'];
                $dayofyear       = date("Y-m-d",$this->timestampBilling+$durationRatedTotal);
            }

            $foundRate            = $this->lookupRateAudio($dayofyear,$dayofweek,$hourofday,$durationRatedTotal);

            if ($this->rateNotFound) {
                // break here to avoid loops
                break;
            }

            $thisRate=$foundRate;

            if ($j > 0) {
                $payConnect=0;
                $durationForRating = $thisRate['duration'];
            } else {
                $payConnect=1;
                if ($this->min_duration && $this->duration < $this->min_duration) {
                    $durationForRating=$this->min_duration;
                } else {
                    $durationForRating=$thisRate['duration'];
                }
            }

            $j++;

            $connectCost     = $thisRate['values']['connectCost'];
            $durationRate    = $thisRate['values']['durationRate'];

            if ($span=="1" && !$dictionary['skipConnectCost']) {
				$this->connectCost=number_format($connectCost/$this->priceDenominator,$this->priceDecimalDigits);

                $connectCostSpan=$connectCost;
                $setupBalanceRequired=$connectCost/$this->priceDenominator;

                if ($connectCost && $Balance <= $setupBalanceRequired) {
                    syslog(LOG_NOTICE,"Balance too small: $Balance <= $setupBalanceRequired");
                    return false;
                }

                $Balance = $Balance-$setupBalanceRequired;

            } else {
                $connectCostSpan=0;
                $setupBalanceRequired=0;
            }

            $connectCostPrint     = number_format($connectCostSpan/$this->priceDenominator,$this->priceDecimalDigits);
            $durationRatePrint    = number_format($durationRate/$this->priceDenominator,$this->priceDecimalDigits);

            $spanPrice            = $this->price+$setupBalanceRequired*$payConnect+
                                    $durationRate*$durationForRating/$this->durationPeriodRated/$this->priceDenominator;

            if ($Balance > $spanPrice) {
                $Balance = $Balance-$spanPrice;
                $durationRatedTotal   = $durationRatedTotal+ $foundRate['duration'];

            } else {

                $durationAllowedinThisSpan = $Balance /
                                             $durationRate * $this->durationPeriodRated * $this->priceDenominator;
                $rateOfThisSpan=$durationRate/$this->priceDenominator;

                $durationRatedTotal=$durationRatedTotal + $durationAllowedinThisSpan;

                $Balance=$Balance-$spanPrice;
                return $durationRatedTotal;
            }

            if ($durationRatedTotal >= $this->duration) {
                return sprintf("%f",$durationRatedTotal);
            }

            $i++;

            if ($i>10) {
                return sprintf("%f",$durationRatedTotal);
                break;
            }
        }

        return false;
    }

    function lookupRateValuesAudio($rateName,$DestinationId) {

    	if (is_array($this->rateValuesCache[$rateName][$DestinationId]['audio'])) {
            return $this->rateValuesCache[$rateName][$DestinationId]['audio'];
        }

        if ($this->settings['split_rating_table']) {
            if ($rateName) {
                $table="billing_rates_".$rateName;
            } else {
                $table="billing_rates_default";
            }
            $query=sprintf("select * from %s where destination = '%s' and application = 'audio'",
            $table,
            $DestinationId
            );

        } else {
            $table="billing_rates";
            $query=sprintf("select * from %s where name = '%s' and destination = '%s' and application = 'audio'",
            $table,
            $rateName,
            $DestinationId
            );
        }

        // lookup rate from MySQL

        if (!$this->db->query($query)) {
            if ($this->db->Errno != 1146) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                syslog(LOG_NOTICE, $log);
                return false;
            }
            // try the main table
            // lookup rate from MySQL
            $query=sprintf("select * from billing_rates where name = '%s' and destination = '%s' and application = 'audio'",
            $rateName,
            $DestinationId
            );

            if (!$this->db->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                syslog(LOG_NOTICE, $log);
                return false;
            }
        }

        if ($this->db->num_rows()) {
            $this->db->next_record();
            $values=array(
                        "connectCost"     => $this->db->Record['connectCost'],
                        "durationRate"    => $this->db->Record['durationRate'],
                        "connectCostIn"   => $this->db->Record['connectCostIn'],
                        "durationRateIn"  => $this->db->Record['durationRateIn']
                       );

            // cache values
            $this->rateValuesCache[$rateName][$DestinationId]['audio']=$values;
            return $values;
        } else {
            return false;
        }
    }

    function lookupRateValuesMessage($rateName,$DestinationId) {

    	if (is_array($this->rateValuesCache[$rateName][$DestinationId]['sms'])) {
            return $this->rateValuesCache[$rateName][$DestinationId]['sms'];
        }

        if ($this->settings['split_rating_table']) {
            if ($rateName) {
                $table="billing_rates_".$rateName;
            } else {
                $table="billing_rates_default";
            }
            $query=sprintf("select * from %s where (destination = '%s' or destination = '') and application = 'sms' order by destination desc limit 1",
            $table,
            $DestinationId
            );

        } else {
            $table="billing_rates";
            $query=sprintf("select * from %s where name = '%s' and (destination = '%s' or destination = '') and application = 'sms' order by destination desc limit 1",
            $table,
            $rateName,
            $DestinationId
            );
        }

        // lookup rate from MySQL

        if (!$this->db->query($query)) {
            if ($this->db->Errno != 1146) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                syslog(LOG_NOTICE, $log);
                return false;
            }
            // try the main table
            // lookup rate from MySQL
            $query=sprintf("select * from billing_rates where name = '%s' and (destination = '%s' or destination = '') and application = 'sms' order by destination desc limit 1",
            $rateName,
            $DestinationId
            );

            if (!$this->db->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                syslog(LOG_NOTICE, $log);
                return false;
            }
        }

        if ($this->db->num_rows()) {
            $this->db->next_record();
            $values=array(
                        "connectCost"     => $this->db->Record['connectCost']
                       );

            // cache values
            $this->rateValuesCache[$rateName][$DestinationId]['sms']=$values;
            return $values;
        } else {
            return false;
        }
    }
}

class RatingTables {
    var $csv_export=array(
                           "destinations"          => "destinations.csv",
                           "billing_customers"     => "customers.csv",
                           "billing_profiles"      => "profiles.csv",
                           "billing_rates"         => "rates.csv",
                           "billing_rates_history" => "ratesHistory.csv",
                           "billing_discounts"     => "discounts.csv",
                           "prepaid"   	           => "prepaid.csv",
                           "billing_enum_tlds"     => "enumtld.csv",
                           "quota_usage"           => "quotausage.csv"
                           );
    var $csv_import=array(
                           "destinations"          => "destinations.csv",
                           "billing_customers"     => "customers.csv",
                           "billing_profiles"      => "profiles.csv",
                           "billing_rates"         => "rates.csv",
                           "billing_rates_history" => "ratesHistory.csv",
                           "billing_discounts"     => "discounts.csv"
                           );

    var $previously_imported_files=0;
    var $maxrowsperpage=15;
	var $insertDomainOption=array();
    var $delimiter=",";
    var $filesToImport=array();
    var $importFilesPatterns=array('ratesHistory',
                                   'rates',
                                   'profiles',
                                   'destinations',
                                   'discounts',
                                   'customers'
                                   );

    var $mustReload = false;
    var $web_elements=array('table',
                            'export',
                            'web_task',
                            'subweb_task',
                            'confirmDelete',
                            'confirmCopy',
                            'next',
                            'id',
                            'search_text',
                            'ReloadRatingTables',
                            'account',
                            'balance',
                            'fromRate',
                            'toRate',
                            'sessionId'
                            );

    var $requireReload       = array('destinations');
    var $whereResellerFilter = " (1=1) ";

    var $cvs_import_dir      = "/var/spool/cdrtool";

    var $tables=array(
                           "destinations"=>array("name"=>"Destinations",
                                                 "skip_math"=> true,
                                                 "keys"=>array("id"),
                                                 "exceptions" =>array(),
                                                 "order"=>"dest_id ASC",
                                                 "domainFilterColumn"=>"domain",
                                                 "fields"=>array(
                                                                  "gateway"=>array("size"=>15,
                                                                                  "checkType"=>'ip',
                                                                                  "name"=>"Trusted peer"
                                                                                ),
                                                                 "reseller_id"=>array("size"=>8,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Reseller"
                                                                                 ),
                                                                 "domain"=>array("size"=>15,
                                                                                  "name"=>"Domain",
                                                                                  "checkType"=>'domain'
                                                                                 ),
                                                                 "subscriber"=>array("size"=>15,
                                                                                  "checkType"=>'sip_account',
                                                                                  "name"=>"Subscriber"
                                                                                 ),
                                                                 "dest_id"=>array("size"=>12,
                                                                                  "name"=>"Destination"
                                                                                 ),
                                                                 "region"=>array("size"=>10,
                                                                                  "name"=>"Region"
                                                                                 ),
                                                                 "dest_name"=>array("size"=>20,
                                                                                  "name"=>"Description"
                                                                                 ),
                                                                 "increment"     =>array("size"=>3,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Incr"
                                                                                 ),
                                                                 "min_duration"  =>array("size"=>3,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Min Dur"
                                                                                 ),
                                                                 "max_duration"  =>array("size"=>5,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Max Dur"
                                                                                 ),
                                                                 "max_price"  =>array("size"=>8,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Max Price"
                                                                                 )

                                                                 )
                                                 ),
                           "billing_customers"=>array("name"=>"Customers",
                                                 "skip_math"=> true,
                                                 "keys"=>array("id"),
                                                 "domainFilterColumn"=>"domain",
                                                 "fields"=>array("gateway"=>array("size"=>15,
                                                                                  "checkType"=>'ip',
                                                                                  "name"=>"Trusted Peer"
                                                                                ),
                                                                 "reseller_id"=>array("size"=>8,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Reseller"
                                                                                 ),
                                                                 "domain"=>array("size"=>15,
                                                                                  "checkType"=>'domain',
                                                                                  "name"=>"Domain"
                                                                                 ),
                                                                 "subscriber"=>array("size"=>25,
                                                                               "checkType"=>'sip_account',
                                                                                  "name"=>"Subscriber",
                                                                                 ),
                                                                 "profile_name1"=>array("size"=>10,
                                                                                  "name"=>"Profile WD"
                                                                                 ),
                                                                 "profile_name1_alt"=>array("size"=>8,
                                                                                  "name"=>"Fallback"
                                                                                 ),
                                                                 "profile_name2"=>array("size"=>10,
                                                                                  "name"=>"Profile WE"
                                                                                 ),
                                                                 "profile_name2_alt"=>array("size"=>8,
                                                                                  "name"=>"Fallback"
                                                                                 ),
                                                                 "timezone"     =>array("size"=>16,
                                                                                  "name"=>"Timezone"
                                                                                 ),
                                                                 "increment"     =>array("size"=>3,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Incr"
                                                                                 ),
                                                                 "min_duration"  =>array("size"=>3,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Min Dur"
                                                                                 )

                                                                 )

                                                 ),
                           "billing_discounts"=>array("name"=>"Discounts",
                                                 "keys"=>array("id"),
                                                 "domainFilterColumn"=>"domain",
                                                 "fields"=>array("gateway"=>array("size"=>15,
                                                                                  "checkType"=>'ip',
                                                                                  "name"=>"Trusted Peer"
                                                                                ),
                                                                 "reseller_id"=>array("size"=>8,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Reseller"
                                                                                 ),
                                                                 "domain"=>array("size"=>15,
                                                                                  "checkType"=>'domain',
                                                                                  "name"=>"Domain"
                                                                                 ),
                                                                 "subscriber"=>array("size"=>25,
                                                                               "checkType"=>'sip_account',
                                                                                  "name"=>"Subscriber",
                                                                                 ),
                                                                 "application"=>array("size"=>6,
                                                                                  "name"=>"App"
                                                                                 ),
                                                                 "destination"=>array("size"=>10,
                                                                                  "name"=>"Destination"
                                                                                 ),
                                                                 "region"=>array("size"=>8,
                                                                                  "name"=>"Region"
                                                                                 ),
                                                                 "connect"=>array("size"=>5,
                                                                                  "name"=>"Connect"
                                                                                 ),
                                                                 "duration"=>array("size"=>5,
                                                                                  "name"=>"Duration"
                                                                                 )
                                                                 )

                                                 ),

                           "billing_profiles"=>array("name"=>"Profiles",
                                                 "skip_math"=> true,
                                                 "keys"=>array("id"),
                                                 "exceptions" =>array(),
                                                 "size"=>6,
                                                 "fields"=>array(
                                                                 "reseller_id"=>array("size"=>8,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Reseller"
                                                                                 ),
                                                                  "name"=>array("size"=>12,
                                                                                  "name"=>"Profile"
                                                                                ),
                                                                 "rate_name1"=>array("size"=>12,
                                                                                  "name"=>"Rate 1"
                                                                                 ),
                                                                 "hour1"=>array("size"=>3,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"00-H1"
                                                                                 ),
                                                                 "rate_name2"=>array("size"=>12,
                                                                                  "name"=>"Rate 2"
                                                                                 ),
                                                                 "hour2"=>array("size"=>3,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"H1-H2"
                                                                                 ),
                                                                 "rate_name3"=>array("size"=>12,
                                                                                  "name"=>"Rate 3"
                                                                                 ),
                                                                 "hour3"=>array("size"=>3,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"H2-H3"
                                                                                 ),
                                                                 "rate_name4"=>array("size"=>12,
                                                                                  "name"=>"Rate 4"
                                                                                 ),
                                                                 "hour4"=>array("size"=>3,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"H3-24"
                                                                                 ),

                                                                 )

                                                 ),
                           "billing_rates"=>array("name"=>"Rates",
                                                 "keys"=>array("id"),
                                                 "size"=>10,
                                                 "exceptions"=>array('maxPrice'),               
                                                 "order"=>"durationRate desc",
                                                 "fields"=>array(
                                                                 "reseller_id"=>array("size"=>8,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Reseller"
                                                                                 ),
                                                                 "name"=>array("size"=>12,
                                                                               "name"=>"Rate"
                                                                                ),
                                                                 "destination"=>array("size"=>12,
                                                                                  "name"=>"Destination"
                                                                                 ),
                                                                 "application"=>array("size"=>6,
                                                                                  "name"=>"App"
                                                                                 ),
                                                                 "connectCost"=>array("size"=>8,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Connect"
                                                                                 ),
                                                                 "durationRate"=>array("size"=>8,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Duration"
                                                                                 ),
                                                                 "connectCostIn"=>array("size"=>8,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Conn In"
                                                                                 ),
                                                                 "durationRateIn"=>array("size"=>8,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Duration In"
                                                                                 )
                                                                  )
                                                   ),
                           "billing_rates_history"=>array("name"=>"Rates history",
                                                 "keys"=>array("id"),
                                                 "size"=>10,
                                                 "order"=>"destination ASC, name ASC",
                                                 "fields"=>array(
                                                                 "reseller_id"=>array("size"=>8,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Reseller"
                                                                                 ),
                                                                 "name"=>array("size"=>10,
                                                                               "name"=>"Rate"
                                                                                ),
                                                                 "destination"=>array("size"=>12,
                                                                                  "name"=>"Destination"
                                                                                 ),
                                                                 "application"=>array("size"=>6,
                                                                                  "name"=>"App"
                                                                                 ),
                                                                 "connectCost"=>array("size"=>8,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Conn"
                                                                                 ),
                                                                 "durationRate"=>array("size"=>8,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Price"
                                                                                 ),
                                                                 "connectCostIn"=>array("size"=>8,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Conn In"
                                                                                 ),
                                                                 "durationRateIn"=>array("size"=>8,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Price In"
                                                                                 ),
                                                                 "startDate"=>array("size"=>11,
                                                                                  "name"=>"Start Date"
                                                                                 ),
                                                                 "endDate"=>array("size"=>11,
                                                                                  "name"=>"End Date"
                                                                                 )

                                                                  )
                                                   ),
                           "billing_enum_tlds"=>array("name"=>"ENUM discounts",
                                                 "skip_math"=> true,
                                                 "keys"=>array("id"),
                                                 "exceptions" =>array(),
                                                 "size"=>6,
                                                 "fields"=>array(
                                                                 "reseller_id"=>array("size"=>8,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Reseller"
                                                                                 ),
                                                                  "enum_tld"=>array("size"=>35,
                                                                               "mustExist"=>true,
                                                                                  "checkType"=>'domain',
                                                                                  "name"=>"ENUM TLD"
                                                                                ),
                                                                  "e164_regexp"=>array("size"=>35,
                                                                               "mustExist"=>true,
                                                                                  "name"=>"E164 Regexp"
                                                                                ),
                                                                 "discount"=>array("size"=>10,
                                                                               "mustExist"=>true,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Discount"
                                                                                 )

                                                                 )

                                                 ),
                           "prepaid"=>array("name"=>"Prepaid accounts",
                                                 "keys"=>array("id"),
                                                 "size"=>15,
                                                 "exceptions" =>array('change_date','active_sessions','domain'),
                                                 "order"=>"change_date DESC",
                                                 "fields"=>array("account"=>array("size"=>35,
                                                                               "name"=>"Subscriber",
                                                                               "checkType"=>'sip_account',
                                                                               "mustExist"=>true
                                                                                ),
                                                                 "reseller_id"=>array("size"=>8,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Reseller"
                                                                                 ),
                                                                 "balance"=>array("size"=>10,
                                                                                  "name"=>"Balance"
                                                                                 ),
                                                                 "change_date"=>array("size"=>19,
                                                                                  "name"=>"Last Change",
                                                                                 "readonly"=>1
                                                                                 ),
                                                                 "session_counter"=>array("size"=>3,
                                                                                  "name"=>"Active Sessions",
                                                                                 "readonly"=>1
                                                                                 ),
                                                                 "max_sessions"=>array("size"=>3,
                                                                                  "name"=>"Max Sessions"
                                                                                 )
                                                                  )
                                                   ),
                           "prepaid_cards"=>array("name"=>"Prepaid cards",
                                                 "keys"=>array("id"),
                                                 "size"=>15,
                                                 "exceptions" =>array('service'),
                                                 "fields"=>array("batch"=>array("size"=>40,
                                                                               "name"=>"Batch name",
                                                                               "readonly"=>1
                                                                                ),
                                                                 "reseller_id"=>array("size"=>8,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Reseller"
                                                                                 ),
                                                                 "date_batch"=>array("size"=>11,
                                                                                  "name"=>"Batch Date"
                                                                                 ),
                                                                 "number"=>array("size"=>20,
                                                                               "checkType"=>'numeric',
                                                                               "mustExist"=>true,
                                                                                  "name"=>"Card Number"
                                                                                 ),
                                                                 "id"=>array("size"=>20,
                                                                               "checkType"=>'numeric',
                                                                               "mustExist"=>true,
                                                                                  "name"=>"Card Id"
                                                                                 ),
                                                                 "value"=>array("size"=>8,
                                                                               "checkType"=>'numeric',
                                                                               "mustExist"=>true,
                                                                                  "name"=>"Card Value"
                                                                                 ),
                                                                 "blocked"=>array("size"=>1,
                                                                                  "name"=>"Lock"
                                                                                 ),
                                                                 "date_active"=>array("size"=>18,
                                                                                  "name"=>"Activation Date"
                                                                                 )

                                                                  )
                                                   ),
                           "prepaid_history"=>array("name"=>"Prepaid history",
                                                 "order"=>"id DESC",
                                                 "skip_math"=> true,
                                                 "keys"=>array("id"),
                                                 "size"=>15,
                                                 "exceptions" =>array('session','destination'),
                                                 "fields"=>array("username"=>array("size"=>15,
                                                                                ),
                                                                 "domain"=>array("size"=>15,
                                                                                 ),
                                                                 "reseller_id"=>array("size"=>8,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Reseller"
                                                                                 ),
                                                                 "action"=>array("size"=>15,
                                                                                 "readonly"=>1
                                                                                 ),
                                                                 "duration"=>array("size"=>5
                                                                                 ),
                                                                 "destination"=>array("size"=>15
                                                                                 ),
                                                                 "session"=>array("size"=>30,
                                                                                 "readonly"=>1
                                                                                 ),
                                                                 "description"=>array("size"=>30
                                                                                 ),
                                                                 "value"=>array("size"=>10
                                                                                 ),
                                                                 "balance"=>array("size"=>10
                                                                                 ),
                                                                 "date"=>array("size"=>18,
                                                                                 ))


                                                   ),
                           "quota_usage"=>array("name"=>"Quota usage",
                                                 "keys"=>array("id"),
                                                 "size"=>15,
                                                 "readonly"=>1,
                                                 "exceptions" =>array("change_date","traffic","duration","calls"),
                                                 "domainFilterColumn"=>"domain",
                                                 "fields"=>array("datasource"=>array("size"=>15,
                                                                               "readonly"=>1
                                                                                ),
                                                                 "reseller_id"=>array("size"=>8,
                                                                               "checkType"=>'numeric',
                                                                                  "name"=>"Reseller",
                                                                                  "readonly" => true
                                                                                 ),
                                                                 "account"=>array("size"=>30,
                                                                               "readonly"=>1,
                                                                               "name" => "Subscriber"
                                                                                 ),
                                                                 "domain"=>array("size"=>15,
                                                                               "readonly"=>1
                                                                                 ),
                                                                 "blocked"=>array("size"=>2,
                                                                               "readonly"=>1
                                                                                 ),
                                                                 "notified"=>array("size"=>20,
                                                                               "readonly"=>1
                                                                                 ),
                                                                 "quota"=>array("size"=>5,
                                                                               "readonly"=>1
                                                                                 ),
                                                                 "cost"=>array("size"=>10,
                                                                               "readonly"=>1,
                                                                               "name"=>"This Month"
                                                                                 ),
                                                                 "cost_today"=>array("size"=>10,
                                                                               "readonly"=>1,
                                                                               "name"=>"Today"
                                                                                 ),
                                                                 "duration"=>array("size"=>10,
                                                                               "readonly"=>1
                                                                                 ),
                                                                 "calls"=>array("size"=>10,
                                                                               "readonly"=>1
                                                                                 ),
                                                                 "traffic"=>array("size"=>20,
                                                                               "readonly"=>1
                                                                                 )

                                                                  )
                                                   )
                           );


    function RatingTables ($readonly=false) {
        global $CDRTool;
        global $RatingEngine;

        $this->settings = $RatingEngine;
        $this->CDRTool  = $CDRTool;

		$this->table = $_REQUEST['table'];
        if (!$this->table || !in_array($this->table,array_keys($this->tables))) $this->table="destinations";

        $this->readonly=$readonly;

        $this->db = new DB_cdrtool;
        $this->db1 = new DB_cdrtool;

        $this->db->Halt_On_Error="no";
        $this->db1->Halt_On_Error="no";

        if ($this->settings['csv_delimiter']) {
            $this->delimiter=$this->settings['csv_delimiter'];
        }

		if (!strlen($this->CDRTool['filter']['reseller'])) {
        	$this->whereResellerFilter = sprintf ("reseller_id = %d",'99999999');
        } else {
            if ($this->CDRTool['filter']['reseller'] && $this->tables[$this->table]['fields']['reseller_id']) {
                $this->whereResellerFilter = sprintf ("reseller_id = %d",$this->CDRTool['filter']['reseller']);
                $this->tables[$this->table]['fields']['reseller_id']['readonly']=true;
            }
        }

        if ($this->settings['split_rating_table']) {
            $this->tables['billing_rates']['fields']['name']['readonly']=1;
        }

		if (strlen($this->settings['socketIP'])) {
            if ($this->settings['socketIP'] == '0.0.0.0' || $this->settings['socketIP'] == '0') {
            	$this->settings['socketIPforClients']='127.0.0.1';
            } else {
            	$this->settings['socketIPforClients']=$this->settings['socketIP'];
            }
        }

    }

    function ImportCSVFiles($dir=false) {
        $results=0;
        if (!$dir) $dir="/var/spool/cdrtool";

        $this->scanFilesForImport($dir);

		if ($this->previously_imported_files) {
            printf("Skipping %d previously imported files\n",$this->previously_imported_files);
        }

		$results=0;
        foreach (array_keys($this->filesToImport) as $file) {
            $importFunction="Import".ucfirst($this->filesToImport[$file]['type']);

            printf("Reading file %s\n",$this->filesToImport[$file]['path']);

            $results = $this->$importFunction($this->filesToImport[$file]['path'],$this->filesToImport[$file]['reseller']);

            $this->logImport($dir,$this->filesToImport[$file]['path'],$this->filesToImport[$file]['watermark'],$results,$this->filesToImport[$file]['reseller']);

        }

        return $results;
    }

    function ImportRates($file,$reseller=0) {
        if (!$file || !is_readable($file) || !$fp = fopen($file, "r")) return false;

        $i=0;
        $inserted = 0;
        $updated  = 0;
        $deleted  = 0;

        printf ("Importing rates from %s for reseller %s:\n",$file,$reseller);

        while ($buffer = fgets($fp,1024)) {
            $buffer=trim($buffer);

            $p = explode($this->delimiter, $buffer);

            $ops            = trim($p[0]);
            $name           = trim($p[2]);
            $destination    = trim($p[3]);
            $application    = trim($p[4]);
            $connectCost    = trim($p[5]);
            $durationRate   = trim($p[6]);
            $connectCostIn  = trim($p[7]);
            $durationRateIn = trim($p[8]);

            if ($reseller) {
            	$reseller_id    = intval($reseller);
            } else {
                $reseller_id    = intval($p[1]);
            }

            if (!is_numeric($destination) && !strstr($destination,'@')) {
                // skip invalid destinations
                $skipped++;
            	continue;
            }

            if (strlen($connectCost) && !is_numeric($connectCost)) {
                $skipped++;
            	continue;
            }

            if (strlen($durationRate) && !is_numeric($durationRate)) {
                $skipped++;
            	continue;
            }

            if (!$application) $application='audio';

            if ($ops=="1") {

                $query=sprintf("insert into billing_rates
                (
                reseller_id,
                name,
                destination,
                application,
                connectCost,
                durationRate,
                connectCostIn,
                durationRateIn
                ) values (
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
                )",
                addslashes($reseller_id),
                addslashes($name),
                addslashes($destination),
                addslashes($application),
                addslashes($connectCost),
                addslashes($durationRate),
                addslashes($connectCostIn),
                addslashes($durationRateIn)
                );

                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                }

                if ($this->db->affected_rows()) {
                    if ($this->settings['split_rating_table']) {
                        if ($name) {
                            $_table='billing_rates_'.$name;
                        } else {
                            $_table='billing_rates_default';
                        }

                        if (!$this->createRatingTable($name)) {

                            $query=sprintf("insert into %s
                            (
                            id,
                            reseller_id,
                            name,
                            destination,
                            application,
                            connectCost,
                            durationRate,
                            connectCostIn,
                            durationRateIn
                            ) values (
                            LAST_INSERT_ID(),
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s'
                            )",
                            addslashes($_table),
                            addslashes($reseller_id),
                            addslashes($name),
                            addslashes($destination),
                            addslashes($application),
                            addslashes($connectCost),
                            addslashes($durationRate),
                            addslashes($connectCostIn),
                            addslashes($durationRateIn)
                            );
            
                            if (!$this->db->query($query)) {
                                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                                print $log;
                                syslog(LOG_NOTICE, $log);
                                return false;
                            }
                        }
                    }

                    $inserted++;
                } else {
                    $failed++;
                }

            } else if ($ops=="3") {
                $query=sprintf("delete from billing_rates
                where
                reseller_id        = '%s'
                and name           = '%s'
                and destination    = '%s'
                and application    = '%s'",
                addslashes($reseller_id),
                addslashes($name),
                addslashes($destination),
                addslashes($application)
                );

                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }

                if ($this->db->affected_rows()) {
                    if ($this->settings['split_rating_table']) {
                        if ($name) {
                            $_table='billing_rates_'.$name;
                        } else {
                            $_table='billing_rates_default';
                        }

                        $query=sprintf("delete from %s
                        where reseller_id     = '%s'
                        and name           = '%s'
                        and destination    = '%s'
                        and application    = '%s'",
                        addslashes($_table),
                        addslashes($reseller_id),
                        addslashes($name),
                        addslashes($destination),
                        addslashes($application)
                        );

                        if (!$this->db->query($query)) {
                            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                            print $log;
                            syslog(LOG_NOTICE, $log);
                        }
                    }

                    $deleted++;
                }
            } else if ($ops=="2") {
                $query=sprintf("select * from billing_rates
                where name       = '%s'
                and destination  = '%s'
                and reseller_id     = '%s'
                and application  = '%s'
                ",
                addslashes($name),
                addslashes($destination),
                addslashes($reseller_id),
                addslashes($application)
                );

                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }

                if ($this->db->num_rows()) {
                    $query=sprintf("update billing_rates set
                    connectCost     = '%s',
                    durationRate    = '%s',
                    connectCostIn   = '%s',
                    durationRateIn  = '%s'
                    where name      = '%s'
                    and destination = '%s'
                    and reseller_id    = '%s'
                    and application = '%s'
                    ",
                    addslashes($connectCost),
                    addslashes($durationRate),
                    addslashes($connectCostIn),
                    addslashes($durationRateIn),
                    addslashes($name),
                    addslashes($destination),
                    addslashes($reseller_id),
                    addslashes($application)
                    );

                    if (!$this->db->query($query)) {
                        $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                        print $log;
                        syslog(LOG_NOTICE, $log);
                        return false;
                    }

                    if ($this->db->affected_rows()) {
                        if ($this->settings['split_rating_table']) {
                            if ($name) {
                                $_table = 'billing_rates_'.$name;
                            } else {
                                $_table = 'billing_rates_default';
                            }
                            $query=sprintf("update %s set
                            connectCost     = '%s',
                            durationRate    = '%s',
                            connectCostIn   = '%s',
                            durationRateIn  = '%s'
                            where name      = '%s'
                            and destination = '%s'
                            and reseller_id    = '%s'
                            and application = '%s'
                            ",
                            addslashes($_table),
                            addslashes($connectCost),
                            addslashes($durationRate),
                            addslashes($connectCostIn),
                            addslashes($durationRateIn),
                            addslashes($name),
                            addslashes($destination),
                            addslashes($reseller_id),
                            addslashes($application)
                            );
        
                            if (!$this->db->query($query)) {
                                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                                print $log;
                                syslog(LOG_NOTICE, $log);
                            }
                        }
                        $updated++;
                    }

                } else {
                    $query=sprintf("insert into billing_rates
                    (
                    reseller_id,
                    name,
                    destination,
                    application,
                    connectCost,
                    durationRate,
                    connectCostIn,
                    durationRateIn
                    ) values (
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                    )",
                    addslashes($reseller_id),
                    addslashes($name),
                    addslashes($destination),
                    addslashes($application),
                    addslashes($connectCost),
                    addslashes($durationRate),
                    addslashes($connectCostIn),
                    addslashes($durationRateIn)
                    );

                    if (!$this->db->query($query)) {
                        $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                        print $log;
                        syslog(LOG_NOTICE, $log);
                        return false;
                    }
                    if ($this->db->affected_rows()) {
                        if ($this->settings['split_rating_table']) {
                            if ($name) {
                                $_table='billing_rates_'.$name;
                            } else {
                                $_table='billing_rates_default';
                            }

                            if (!$this->createRatingTable($name)) {

                               $query=sprintf("insert into %s
                               (
                               id,
                               reseller_id,
                               name,
                               destination,
                               application
                               connectCost,
                               durationRate,
                               connectCostIn,
                               durationRateIn
                               ) values (
                               LAST_INSERT_ID(),
                               '%s',
                               '%s',
                               '%s',
                               '%s',
                               '%s',
                               '%s',
                               '%s',
                               '%s'
                               )",
                               addslashes($_table),
                               addslashes($reseller_id),
                               addslashes($name),
                               addslashes($destination),
                               addslashes($application),
                               addslashes($connectCost),
                               addslashes($durationRate),
                               addslashes($connectCostIn),
                               addslashes($durationRateIn)
                               );

                               if (!$this->db->query($query)) {
                                   $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                                   print $log;
                                   syslog(LOG_NOTICE, $log);
                                   return false;
                               }
                            }
                        }

                        $inserted++;
                    } else {
                        $failed++;
                    }
                }
            } else {
                $skipped++;
            }

            $this->showImportProgress($file);

            $i++;
        }

        if ($i) print "Read $i records\n";
        if ($skipped) print "Skipped $skipped records\n";
        if ($inserted) print "Inserted $inserted records\n";
        if ($updated)  print "Updated $updated records\n";
        if ($deleted)  print "Delete $deleted records\n";

        $results=$inserted+$updated+$deleted;

        return $results;

    }

    function ImportRatesHistory($file,$reseller=0) {

        if (!$file || !is_readable($file) || !$fp = fopen($file, "r")) return false;

        $this->mustReload=true;

        $i=0;
        $inserted = 0;
        $updated  = 0;
        $deleted  = 0;

        printf ("Importing rates history from %s for reseller %s:\n",$file,$reseller);

        while ($buffer = fgets($fp,1024)) {
            $buffer=trim($buffer);

            $p = explode($this->delimiter, $buffer);

            $ops            = trim($p[0]);
            $name           = trim($p[2]);
            $destination    = trim($p[3]);
            $application    = trim($p[4]);
            $connectCost    = trim($p[5]);
            $durationRate   = trim($p[6]);
            $connectCostIn  = trim($p[7]);
            $durationRateIn = trim($p[8]);
            $startDate      = trim($p[9]);
            $endDate        = trim($p[10]);

            if ($reseller) {
            	$reseller_id    = intval($reseller);
            } else {
                $reseller_id    = intval($p[1]);
            }

            if (!is_numeric($destination) && !strstr($destination,'@')) {
                // skip invalid destinations
                $skipped++;
            	continue;
            }

            if (strlen($connectCost) && !is_numeric($connectCost)) {
                $skipped++;
            	continue;
            }

            if (strlen($durationRate) && !is_numeric($durationRate)) {
                $skipped++;
            	continue;
            }

            if (preg_match("/^\d{4}\-{\d{2}\-\d{2}$/",$startDate)) {
                $skipped++;
            	continue;
            }

            if (preg_match("/^\d{4}\-{\d{2}\-\d{2}$/",$endDate)) {
                $skipped++;
            	continue;
            }

            if ($ops=="1") {

                $query=sprintf("insert into billing_rates_history
                (
                reseller_id,
                name,
                destination,
                application,
                connectCost,
                durationRate,
                connectCostIn,
                durationRateIn,
                startDate,
                endDate
                ) values (
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
                )",
                addslashes($reseller_id),
                addslashes($name),
                addslashes($destination),
                addslashes($application),
                addslashes($connectCost),
                addslashes($durationRate),
                addslashes($connectCostIn),
                addslashes($durationRateIn),
                addslashes($startDate),
                addslashes($endDate)
                );

                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }

                if ($this->db->affected_rows() >0) {
                    $inserted++;
                } else {
                    $failed++;
                }
            } else if ($ops=="3") {
                $query=sprintf("delete from billing_rates_history
                where reseller_id      = '%s'
                and name           = '%s'
                and destination    = '%s'
                and startDate      = '%s'
                and endDate        = '%s'",
                addslashes($reseller_id),
                addslashes($name),
                addslashes($destination),
                addslashes($startDate),
                addslashes($endDate)
                );

                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }

                if ($this->db->affected_rows() >0) {
                    $deleted++;
                }
            } else if ($ops=="2") {
                $query=sprintf("select * from billing_rates_history
                where name       = '%s'
                and destination  = '%s'
                and reseller_id      = '%s'
                and startDate    = '%s'
                and endDate      = '%s'
                ",
                addslashes($name),
                addslashes($destination),
                addslashes($reseller_id),
                addslashes($startDate),
                addslashes($endDate)
                );
                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }


                if ($this->db->num_rows()) {
                    $query=sprintf("update billing_rates_history set
                    application     = '%s',
                    connectCost     = '%s',
                    durationRate    = '%s',
                    connectCostIn   = '%s',
                    connectCostIn   = '%s'
                    where name      = '%s'
                    and destination = '%s'
                    and reseller_id = '%s'
                    and startDate   = '%s'
                    and endDate     = '%s'
                    ",
                    addslashes($application),
                    addslashes($connectCost),
                    addslashes($durationRate),
                    addslashes($connectCostIn),
                    addslashes($durationRateIn),
                    addslashes($name),
                    addslashes($destination),
                    addslashes($reseller_id),
                    addslashes($startDate),
                    addslashes($endDate)
                    );

                    if (!$this->db->query($query)) {
                        $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                        print $log;
                        syslog(LOG_NOTICE, $log);
                        return false;
                    }

                    if ($this->db->affected_rows() >0) {
                        $updated++;
                    }

                } else {
                    $query=sprintf("insert into billing_rates_history
                    (
                    reseller_id,
                    name,
                    destination,
                    application,
                    connectCost,
                    durationRate,
                    connectCostIn,
                    durationRateIn,
                    startDate,
                    endDate
                    ) values (
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                    )",
                    addslashes($reseller_id),
                    addslashes($name),
                    addslashes($destination),
                    addslashes($application),
                    addslashes($connectCost),
                    addslashes($durationRate),
                    addslashes($connectCostIn),
                    addslashes($durationRateIn),
                    addslashes($startDate),
                    addslashes($endDate)
                    );

                    if (!$this->db->query($query)) {
                        $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                        print $log;
                        syslog(LOG_NOTICE, $log);
                        return false;
                    }

                    if ($this->db->affected_rows() >0) {
                        $inserted++;
                    } else {
                        $failed++;
                    }
                }
            } else {
                $skipped++;
            }

            $j++;

            if ($j=="10000") {
                flush();
                $j=0;
            }

            $this->showImportProgress($file);

            $i++;
        }

        if ($i) print "Read $i records\n";
        if ($skipped) print "Skipped $skipped records\n";
        if ($inserted) print "Inserted $inserted records\n";
        if ($updated)  print "Updated $updated records\n";
        if ($deleted)  print "Delete $deleted records\n";

        $results=$inserted+$updated+$deleted;

        return $results;

    }

    function ImportCustomers($file,$reseller=0) {

        if (!$file || !is_readable($file) || !$fp = fopen($file, "r")) return false;

        $this->mustReload=true;

        $i=0;
        $inserted = 0;
        $updated  = 0;
        $deleted  = 0;

        printf ("Importing customers from %s for reseller %s:\n",$file,$reseller);

        while ($buffer = fgets($fp,1024)) {
            $buffer=trim($buffer);

            $p = explode($this->delimiter, $buffer);

            $ops               = trim($p[0]);
            $gateway           = trim($p[2]);
            $domain            = trim($p[3]);
            $subscriber        = trim($p[4]);
            $profile_name1     = trim($p[5]);
            $profile_name1_alt = trim($p[6]);
            $profile_name2     = trim($p[7]);
            $profile_name2_alt = trim($p[8]);
            $timezone          = trim($p[9]);

            if ($reseller) {
            	$reseller_id    = intval($reseller);
            } else {
                $reseller_id    = intval($p[1]);
            }

            if (strlen($reseller_id) && !is_integer($reseller_id)) {
                $skipped++;
            	continue;
            }

            if ($ops=="1") {
                $query=sprintf("insert into billing_customers
                (
                reseller_id,
                gateway,
                domain,
                subscriber,
                profile_name1,
                profile_name2,
                timezone,
                profile_name1_alt,
                profile_name2_alt
                ) values (
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
                )",
                addslashes($reseller_id),
                addslashes($gateway),
                addslashes($domain),
                addslashes($subscriber),
                addslashes($profile_name1),
                addslashes($profile_name2),
                addslashes($timezone),
                addslashes($profile_name1_alt),
                addslashes($profile_name2_alt)
                );

                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }

                if ($this->db->affected_rows() >0) {
                    $inserted++;
                } else {
                    $failed++;
                }
            } else if ($ops=="3") {
                $query=sprintf("delete from billing_customers
                where gateway      = '%s'
                and reseller_id    = '%s'
                and domain         = '%s'
                and subscriber     = '%s'
                ",
                addslashes($gateway),
                addslashes($reseller_id),
                addslashes($domain),
                addslashes($subscriber)
                );
                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }

                if ($this->db->affected_rows() >0) {
                    $deleted++;
                }
            } else if ($ops=="2") {
                $query=sprintf("select * from billing_customers
                where gateway      = '%s'
                and reseller_id    = '%s'
                and domain         = '%s'
                and subscriber     = '%s'
                ",
                addslashes($gateway),
                addslashes($reseller_id),
                addslashes($domain),
                addslashes($subscriber)
                );

                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }

                if ($this->db->num_rows()) {
                    $query=sprintf("update billing_customers set
                    profile_name1     = '%s',
                    profile_name2     = '%s',
                    profile_name1_alt = '%s',
                    profile_name2_alt = '%s',
                    timezone          = '%s'
                    where gateway     = '%s'
                    and domain        = '%s'
                    and reseller_id   = '%s'
                    and subscriber    = '%s'\n",
                    addslashes($profile_name1),
                    addslashes($profile_name2),
                    addslashes($profile_name1_alt),
                    addslashes($profile_name2_alt),
                    addslashes($timezone),
                    addslashes($gateway),
                    addslashes($domain),
                    addslashes($reseller_id),
                    addslashes($subscriber)
                    );

                    if (!$this->db->query($query)) {
                        $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                        print $log;
                        syslog(LOG_NOTICE, $log);
                        return false;
                    }

                    if ($this->db->affected_rows()) {
                        $updated++;
                    }

                } else {
                    $query=sprintf("insert into billing_customers
                    (
                    reseller_id,
                    gateway,
                    domain,
                    subscriber,
                    profile_name1,
                    profile_name2,
                    timezone,
                    profile_name1_alt,
                    profile_name2_alt
                    ) values (
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                    )",
                    addslashes($reseller_id),
                    addslashes($gateway),
                    addslashes($domain),
                    addslashes($subscriber),
                    addslashes($profile_name1),
                    addslashes($profile_name2),
                    addslashes($timezone),
                    addslashes($profile_name1_alt),
                    addslashes($profile_name2_alt)
                    );

                    if (!$this->db->query($query)) {
                        $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                        print $log;
                        syslog(LOG_NOTICE, $log);
                        return false;
                    }

                    if ($this->db->affected_rows()) {
                        $inserted++;
                    }
                }
            } else {
                $skipped++;
            }

            $this->showImportProgress($file);

            $i++;
        }

        if ($i) print "Read $i records\n";
        if ($skipped) print "Skipped $skipped records\n";
        if ($inserted) print "Inserted $inserted records\n";
        if ($updated)  print "Updated $updated records\n";
        if ($deleted)  print "Delete $deleted records\n";

        $results=$inserted+$updated+$deleted;
        return $results;
    }

    function ImportDestinations($file,$reseller=0) {

        if (!$file || !is_readable($file) || !$fp = fopen($file, "r")) return false;

        $this->mustReload=true;

        $i=0;
        $inserted = 0;
        $updated  = 0;
        $deleted  = 0;

        printf ("Importing destinations from %s for reseller %s:\n",$file,$reseller);

        while ($buffer = fgets($fp,1024)) {
            $buffer=trim($buffer);

            $p = explode($this->delimiter, $buffer);

            $ops             = trim($p[0]);
            $gateway         = trim($p[2]);
            $domain          = trim($p[3]);
            $subscriber      = trim($p[4]);
            $dest_id         = trim($p[5]);
            $region          = trim($p[6]);
            $dest_name       = trim($p[7]);
            $increment       = intval($p[8]);
            $min_duration    = intval($p[9]);
            $max_duration    = intval($p[10]);
            $max_price       = trim($p[11]);

            if ($reseller) {
            	$reseller_id    = intval($reseller);
            } else {
                $reseller_id    = intval($p[1]);
            }

            if (!is_numeric($dest_id) && !strstr($dest_id,'@')) {
                // skip invalid destinations
                $skipped++;
            	continue;
            }

            if ($ops=="1") {
                $query=sprintf("insert into destinations
                (
                reseller_id,
                gateway,
                domain,
                subscriber,
                dest_id,
                region,
                dest_name,
                increment,
                min_duration,
                max_duration,
                max_price
                ) values (
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
                )",
                addslashes($reseller_id),
                addslashes($gateway),
                addslashes($domain),
                addslashes($subscriber),
                addslashes($dest_id),
                addslashes($region),
                addslashes($dest_name),
                addslashes($increment),
                addslashes($min_duration),
                addslashes($max_duration),
                addslashes($max_price)
                );
                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }

                if ($this->db->affected_rows() >0) {
                    $inserted++;
                } else {
                    $failed++;
                }
            } elseif ($ops=="3") {
                $query=sprintf("delete from destinations
                where gateway      = '%s'
                and reseller_id    = '%s'
                and domain         = '%s'
                and subscriber     = '%s'
                and dest_id        = '%s'
                ",
                addslashes($gateway),
                addslashes($reseller_id),
                addslashes($domain),
                addslashes($subscriber),
                addslashes($dest_id)
                );
                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }

                if ($this->db->affected_rows() >0) {
                    $deleted++;
                }
            } elseif ($ops=="2") {
                $query=sprintf("select * from destinations
                where gateway      = '%s'
                and reseller_id    = '%s'
                and domain         = '%s'
                and subscriber     = '%s'
                and dest_id        = '%s'
                ",
                addslashes($gateway),
                addslashes($reseller_id),
                addslashes($domain),
                addslashes($subscriber),
                addslashes($dest_id)
                );

                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }

                if ($this->db->num_rows()) {
                    $query=sprintf("update destinations set
                    region             = '%s',
                    dest_name          = '%s',
                    increment          = '%s',
                    min_duration       = '%s',
                    max_duration       = '%s',
                    max_price          = '%s'
                    where gateway      = '%s'
	                and reseller_id    = '%s'
                    and domain         = '%s'
                    and subscriber     = '%s'
                    and dest_id        = '%s'
                    ",
                    addslashes($region),
                    addslashes($dest_name),
                    addslashes($increment),
                    addslashes($min_duration),
                    addslashes($max_duration),
                    addslashes($max_price),
                    addslashes($gateway),
	                addslashes($reseller_id),
                    addslashes($domain),
                    addslashes($subscriber),
                    addslashes($dest_id)
                    );
                    if (!$this->db->query($query)) {
                        $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                        print $log;
                        syslog(LOG_NOTICE, $log);
                        return false;
                    }

                    if ($this->db->affected_rows()) {
                        $updated++;
                    }
                 } else {
                    $query=sprintf("insert into destinations
                    (
                    reseller_id,
                    gateway,
                    domain,
                    subscriber,
                    dest_id,
                    region,
                    dest_name,
                    increment,
                    min_duration,
                    max_duration,
                    max_price
                    ) values (
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                    )",
                    addslashes($reseller_id),
                    addslashes($gateway),
                    addslashes($domain),
                    addslashes($subscriber),
                    addslashes($dest_id),
                    addslashes($region),
                    addslashes($dest_name),
                    addslashes($increment),
                    addslashes($min_duration),
                    addslashes($max_duration),
                    addslashes($max_price)
                    );
                    if (!$this->db->query($query)) {
                        $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                        print $log;
                        syslog(LOG_NOTICE, $log);
                        return false;
                    }

                    if ($this->db->affected_rows() >0) {
                        $inserted++;
                    } else {
                        $failed++;
                    }

                 }
            } else {
                $skipped++;
            }

            $this->showImportProgress($file);

            $i++;
        }

        if ($i) print "Read $i records\n";
        if ($skipped) print "Skipped $skipped records\n";
        if ($inserted) print "Inserted $inserted records\n";
        if ($updated)  print "Updated $updated records\n";
        if ($deleted)  print "Delete $deleted records\n";

        $results=$inserted+$updated+$deleted;
        return $results;
    }

    function ImportDiscounts($file,$reseller=0) {

        if (!$file || !is_readable($file) || !$fp = fopen($file, "r")) return false;

        $this->mustReload=true;

        $i=0;
        $inserted = 0;
        $updated  = 0;
        $deleted  = 0;

        printf ("Importing discounts from %s for reseller %s:\n",$file,$reseller);

        while ($buffer = fgets($fp,1024)) {
            $buffer=trim($buffer);

            $p = explode($this->delimiter, $buffer);

            $ops             = trim($p[0]);
            $gateway         = trim($p[2]);
            $domain          = trim($p[3]);
            $subscriber      = trim($p[4]);
            $application     = trim($p[5]);
            $destination     = trim($p[6]);
            $region          = trim($p[7]);
            $connect         = intval($p[8]);
            $duration        = intval($p[9]);

            if ($reseller) {
            	$reseller_id    = intval($reseller);
            } else {
                $reseller_id    = intval($p[1]);
            }

            if (!is_numeric($destination) && !strstr($destination,'@')) {
                // skip invalid destinations
                $skipped++;
            	continue;
            }

            if ($ops=="1") {
                $query=sprintf("insert into billing_discounts
                (
                reseller_id,
                gateway,
                domain,
                subscriber,
                application,
                destination,
                region,
                connect,
                duration
                ) values (
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
                )",
                addslashes($reseller_id),
                addslashes($gateway),
                addslashes($domain),
                addslashes($subscriber),
                addslashes($application),
                addslashes($destination),
                addslashes($region),
                addslashes($connect),
                addslashes($duration)
                );

                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }

                if ($this->db->affected_rows() >0) {
                    $inserted++;
                } else {
                    $failed++;
                }
            } elseif ($ops=="3") {
                $query=sprintf("delete from billing_discounts
                where gateway      = '%s'
                and reseller_id    = '%s'
                and domain         = '%s'
                and subscriber     = '%s'
                and application    = '%s'
                and destination    = '%s'
                and region         = '%s'
                ",
                addslashes($gateway),
                addslashes($reseller_id),
                addslashes($domain),
                addslashes($subscriber),
                addslashes($application),
                addslashes($destination),
                addslashes($region)
                );

                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }

                if ($this->db->affected_rows() >0) {
                    $deleted++;
                }
            } elseif ($ops=="2") {
                $query=sprintf("select * from billing_discounts
                where gateway      = '%s'
                and reseller_id    = '%s'
                and domain         = '%s'
                and subscriber     = '%s'
                and application    = '%s'
                and destination    = '%s'
                and region         = '%s'
                ",
                addslashes($gateway),
                addslashes($reseller_id),
                addslashes($domain),
                addslashes($subscriber),
                addslashes($application),
                addslashes($destination),
                addslashes($region)
                );

                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }

                if ($this->db->num_rows()) {
                    $query=sprintf("update billing_discounts set
                    connect            = '%s',
                    duration           = '%s',
                    where gateway      = '%s'
	                and reseller_id    = '%s'
                    and domain         = '%s'
                    and subscriber     = '%s'
                    and application    = '%s'
                    and destination    = '%s'
                    and region         = '%s'
                    ",
                    addslashes($connect),
                    addslashes($duration),
                    addslashes($gateway),
	                addslashes($reseller_id),
                    addslashes($domain),
                    addslashes($subscriber),
                    addslashes($application),
                    addslashes($destination),
                    addslashes($region)
                    );

                    if (!$this->db->query($query)) {
                        $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                        print $log;
                        syslog(LOG_NOTICE, $log);
                        return false;
                    }

                    if ($this->db->affected_rows()) {
                        $updated++;
                    }
                 } else {
                    $query=sprintf("insert into billing_discounts
                    (
                    reseller_id,
                    gateway,
                    domain,
                    subscriber,
                    application,
                    destination,
                    region,
                    connect,
                    duration
                    ) values (
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                    )",
                    addslashes($reseller_id),
                    addslashes($gateway),
                    addslashes($domain),
                    addslashes($subscriber),
                    addslashes($application),
                    addslashes($destination),
                    addslashes($region),
                    addslashes($connect),
                    addslashes($duration)
                    );

                    if (!$this->db->query($query)) {
                        $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                        print $log;
                        syslog(LOG_NOTICE, $log);
                        return false;
                    }

                    if ($this->db->affected_rows() >0) {
                        $inserted++;
                    } else {
                        $failed++;
                    }

                 }
            } else {
                $skipped++;
            }

            $this->showImportProgress($file);

            $i++;
        }

        if ($i) print "Read $i records\n";
        if ($skipped) print "Skipped $skipped records\n";
        if ($inserted) print "Inserted $inserted records\n";
        if ($updated)  print "Updated $updated records\n";
        if ($deleted)  print "Delete $deleted records\n";

        $results=$inserted+$updated+$deleted;
        return $results;
    }

    function ImportProfiles($file,$reseller=0) {
        if (!$file || !is_readable($file) || !$fp = fopen($file, "r")) return false;

        $this->mustReload=true;

        $i=0;
        $inserted = 0;
        $updated  = 0;
        $deleted  = 0;

        print "Importing Profiles:\n";

        while ($buffer = fgets($fp,1024)) {
            $buffer=trim($buffer);

            $p = explode($this->delimiter, $buffer);

            $ops        = trim($p[0]);
            $profile    = trim($p[2]);
            $rate1      = trim($p[3]);
            $hour1      = trim($p[4]);
            $rate2      = trim($p[5]);
            $hour2      = trim($p[6]);
            $rate3      = trim($p[7]);
            $hour3      = trim($p[8]);
            $rate4      = trim($p[9]);
            $hour4      = trim($p[10]);

            if ($reseller) {
            	$reseller_id    = intval($reseller);
            } else {
                $reseller_id    = intval($p[1]);
            }

            if (!$hour1) $hour1=0;
            if (!$hour2) $hour2=0;
            if (!$hour3) $hour3=0;
            if (!$hour4) $hour4=0;

            if ($ops=="1") {
                $query=sprintf("insert into billing_profiles
                (
                reseller_id,
                name,
                rate_name1,
                hour1,
                rate_name2,
                hour2,
                rate_name3,
                hour3,
                rate_name4,
                hour4
                ) values (
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
                )",
                addslashes($reseller_id),
                addslashes($profile),
                addslashes($rate1),
                addslashes($hour1),
                addslashes($rate2),
                addslashes($hour2),
                addslashes($rate3),
                addslashes($hour3),
                addslashes($rate4),
                addslashes($hour4)
                );
                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }

                if ($this->db->affected_rows() >0) {
                    $inserted++;
                } else {
                    $failed++;
                }
            } else if ($ops=="3") {
                $query=sprintf("delete from billing_profiles
                where name     = '%s'
                and reseller_id= '%s'
                ",
                addslashes($profile),
                addslashes($reseller_id)
                );
                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }

                if ($this->db->affected_rows() >0) {
                    $deleted++;
                }

            } else if ($ops=="2") {
                $query=sprintf("select * from billing_profiles
                where name     = '%s'
                and reseller_id= '%s'
                ",
                addslashes($profile),
                addslashes($reseller_id)
                );

                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }

                if ($this->db->num_rows()) {
                    $query=sprintf("update billing_profiles set
                    rate_name1     = '%s',
                    rate_name2     = '%s',
                    rate_name3     = '%s',
                    rate_name4     = '%s',
                    hour1          = '%s',
                    hour2          = '%s',
                    hour3          = '%s',
                    hour4          = '%s'
                    where name     = '%s'
                    and reseller_id= '%s'
                    \n",
                    addslashes($rate1),
                    addslashes($rate2),
                    addslashes($rate3),
                    addslashes($rate4),
                    addslashes($hour1),
                    addslashes($hour2),
                    addslashes($hour3),
                    addslashes($hour4),
                    addslashes($profile),
                    addslashes($reseller_id)
                    );
                    if (!$this->db->query($query)) {
                        $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                        print $log;
                        syslog(LOG_NOTICE, $log);
                        return false;
                    }

                    if ($this->db->affected_rows()) {
                        $updated++;
                    }
                } else {
                    $query=sprintf("insert into billing_profiles
                    (
                    reseller_id,
                    name,
                    rate_name1,
                    hour1,
                    rate_name2,
                    hour2,
                    rate_name3,
                    hour3,
                    rate_name4,
                    hour4
                    ) values (
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                    )",
                    addslashes($reseller_id),
                    addslashes($profile),
                    addslashes($rate1),
                    addslashes($hour1),
                    addslashes($rate2),
                    addslashes($hour2),
                    addslashes($rate3),
                    addslashes($hour3),
                    addslashes($rate4),
                    addslashes($hour4)
                    );
                    if (!$this->db->query($query)) {
                        $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                        print $log;
                        syslog(LOG_NOTICE, $log);
                        return false;
                    }

                    if ($this->db->affected_rows() >0) {
                        $inserted++;
                    } else {
                        $failed++;
                    }
                }
            }

            $this->showImportProgress($file);

            $i++;
        }

        if ($i) print "Read $i records\n";
        if ($inserted) print "Inserted $inserted records\n";
        if ($updated)  print "Updated $updated records\n";
        if ($deleted)  print "Delete $deleted records\n";

        $results=$inserted+$updated+$deleted;
        return $results;
    }

    function LoadRatingTables () {

        $log=sprintf("Memory usage: %0.2fMB, memory limit: %sB",memory_get_usage()/1024/1024,ini_get('memory_limit'));
        syslog(LOG_NOTICE, $log);

        $loaded['profiles']     = $this->LoadProfilesTable();
        $loaded['ratesHistory'] = $this->LoadRatesHistoryTable();
        $loaded['holidays']     = $this->LoadHolidaysTable();
        $loaded['enumTlds']     = $this->LoadENUMtldsTable();

        foreach(array_keys($loaded) as $_load) {
            syslog(LOG_NOTICE, "Loaded $loaded[$_load] $_load into memory");
        }

        $log=sprintf("Memory usage: %0.2fMB, memory limit: %sB",memory_get_usage()/1024/1024,ini_get('memory_limit'));
        syslog(LOG_NOTICE, $log);

        return $loaded;
    }

    function LoadENUMtldsTable() {
        $query="select * from billing_enum_tlds";
        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return false;
        }

        $i=0;
        $rows=$this->db->num_rows();
        while($this->db->next_record()) {
            if ($this->db->Record['enum_tld']) {
                $i++;
                $_app=$this->db->Record['application'];
                if (!$_app) $_app='audio';

                $_ENUMtlds[$this->db->Record['enum_tld']]=
                array(
                     "discount"    => $this->db->Record['discount'],
                     "e164_regexp" => $this->db->Record['e164_regexp']
                );
            }
        }

        $this->ENUMtlds = $_ENUMtlds;
        $this->ENUMtldsCount = $i;
        return $i;
    }

    function LoadRatesHistoryTable() {
        $query="select *,
        UNIX_TIMESTAMP(startDate) as startDateTimestamp,
        UNIX_TIMESTAMP(endDate) as endDateTimestamp
        from billing_rates_history
        order by name ASC,destination ASC,startDate DESC";

        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return false;
        }

        $i=0;
        $rows=$this->db->num_rows();
        while($this->db->next_record()) {
            if ($this->db->Record['name'] && $this->db->Record['destination']) {
                $i++;
                $_app=$this->db->Record['application'];
                if (!$_app) $_app='audio';

                $_rates[$this->db->Record['name']][$this->db->Record['destination']][$_app][$this->db->Record['id']]=
                array(
                     "connectCost"    => $this->db->Record['connectCost'],
                     "durationRate"   => $this->db->Record['durationRate'],
                     "connectCostIn"  => $this->db->Record['connectCostIn'],
                     "durationRateIn" => $this->db->Record['durationRateIn'],
                     "increment"      => $this->db->Record['increment'],
                     "min_duration"   => $this->db->Record['min_duration'],
                     "startDate"      => $this->db->Record['startDateTimestamp'],
                     "endDate"        => $this->db->Record['endDateTimestamp']
                );
            }
        }

        $this->ratesHistory=$_rates;
        $this->ratesHistoryCount=$i;

        return $i;

    }

    function LoadProfilesTable() {
        $query="select * from billing_profiles order by name";
        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return false;
        }

        $i=0;
        while($this->db->next_record()) {
            $i++;
            if ($this->db->Record['name'] && $this->db->Record['hour1'] > 0 ) {
                $_profiles[$this->db->Record['name']]=
                array(
                     "rate_name1"  => $this->db->Record['rate_name1'],
                     "hour1"       => $this->db->Record['hour1'],
                     "rate_name2"  => $this->db->Record['rate_name2'],
                     "hour2"       => $this->db->Record['hour2'],
                     "rate_name3"  => $this->db->Record['rate_name3'],
                     "hour3"       => $this->db->Record['hour3'],
                     "rate_name4"  => $this->db->Record['rate_name4'],
                     "hour4"       => $this->db->Record['hour4'],
                );
            }
        }

        $this->profiles=$_profiles;
        return $i;

    }

    function LoadHolidaysTable() {
        $query="select * from billing_holidays order by day";
        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return false;
        }

        $i=0;
        while($this->db->next_record()) {
            if ($this->db->Record['day']) {
                $i++;
                $_holidays[$this->db->Record['day']]++;
            }
        }

        $this->holidays=$_holidays;
        return $i;
    }

    function checkRatingEngineConnection () {
        if ($this->settings['socketIPforClients'] && $this->settings['socketPort'] &&
          	$fp = fsockopen ($this->settings['socketIPforClients'], $this->settings['socketPort'], $errno, $errstr, 2)) {
            fclose($fp);
            return true;
        }
        return false;
    }

    function showCustomers($filter) {
        return true;
        foreach (array_keys($this->customers) as $key) {
            if (strlen($filter)) {
                if (preg_match("/$filter/",$key)) {
                    $customers=$customers.$key."\n";
                }
            } else {
                $customers=$customers.$key."\n";
            }
        }
        return $customers;
    }

    function showProfiles() {
        foreach (array_keys($this->profiles) as $key) {
            $profiles=$profiles.$key."\n";
        }
        return $profiles;
    }

    function showENUMtlds() {
        foreach (array_keys($this->ENUMtlds) as $key) {
            $ENUMtlds=$ENUMtlds.$key."\n";
        }
        return $ENUMtlds;
    }

    function scanFilesForImport($dir) {
		$import_dirs[$this->cvs_import_dir]=array('path'=>$this->cvs_import_dir,
                                 'reseller' => 0
                                 );

        if ($handle = opendir($this->cvs_import_dir)) {
            while (false !== ($filename = readdir($handle))) {
                $reseller=0;
                if ($filename == "." || $filename == "..") continue;
                $fullPath=$this->cvs_import_dir."/".$filename;
                if (is_dir($fullPath) && is_numeric($filename)) {
                    $reseller=$filename;
                    $import_dirs[$fullPath]=array('path'    => $fullPath,
                                                  'reseller'=> $reseller
                                                  );
                }
            }

        }

        foreach (array_keys($import_dirs) as $_dir) {
            if ($handle = opendir($_dir)) {
                while (false !== ($filename = readdir($handle))) {
                    if ($filename != "." && $filename != "..") {
                        foreach ($this->importFilesPatterns as $_pattern) {
                            if (strstr($filename,$_pattern) && preg_match("/\.csv$/",$filename)) {
                                $fullPath=$_dir."/".$filename;
                                if ($content=file_get_contents($fullPath)) {
                                    $watermark=$filename."-".md5($content);
                                    if ($this->hasFileBeenImported($filename,$watermark)) {
						                $this->previously_imported_files++;

                                    	break;
                                    }
    
                                    $this->filesToImport[$filename]=array( 'name'      => $filename,
                                                                           'watermark' => $watermark,
                                                                           'type'      => $_pattern,
                                                                           'path'      => $fullPath,
                                                                           'reseller'  => $import_dirs[$_dir]['reseller']
                                                                         );
                                }
    
                                break;
                            }
                        }
                    }
                }
            }
        }
    }

    function hasFileBeenImported($filename,$watermark) {
        $query=sprintf("select * from log where url = '%s'\n",$watermark);
        if ($this->db->query($query)) {
            if ($this->db->num_rows()) {
                $this->db->next_record();
                /*
                $log=sprintf ("File %s has already been imported at %s.\n",$filename,$this->db->f('date'));
                syslog(LOG_NOTICE, $log);
                print $log;
                */
                return true;
            } else {
                return false;
            }
        } else {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return false;
        }
    }

    function logImport($dir,$filename,$watermark,$results=0,$reseller=0) {
        $query=sprintf("insert into log (date,login,ip,url,results,description,datasource,reseller_id)
        values (NOW(),'ImportScript','localhost','%s','%s','Imported %s','%s',%d)",
        $watermark,$results,$filename,$dir,$reseller);

        $log=sprintf ("Imported file %s, %d records have been affected\n",$filename,$results);
        syslog(LOG_NOTICE, $log);

        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return false;
        }
    }

    function showImportProgress ($filename='unspecified',$increment=5000) {
        $this->importIndex++;
    
        if ($this->importIndex == $increment) {
            printf ("Loaded %d records from %s\n",$this->importIndex,$filename);
            flush();
            $this->importIndex=0;
        }
    }

    function createRatingTable($name) {
        if ($name) {
            $table='billing_rates_'.$name;
        } else {
            $table='billing_rates_default';
        }

        $query=sprintf("create table %s select * from billing_rates where name = '%s'\n",$table,$name);

        if ($this->db->query($query)) {
            $query=sprintf("alter table %s add index rate_idx (name)",$table);
            if (!$this->db->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                print $log;
                syslog(LOG_NOTICE, $log);
            }
    
            $query=sprintf("alter table %s add index destination_idx (destination)",$table);
            if (!$this->db->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                print $log;
                syslog(LOG_NOTICE, $log);
            }

            printf ("Created table %s\n",$table);
            return true;
        } else {
            return false;
        }
    }

    function splitRatingTable() {
        $query="select count(*) as c from billing_rates";

        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return false;
        }
        $this->db->next_record();
        $rows=$this->db->f('c');

        $query="select distinct(name) from billing_rates order by name ASC";

        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return false;
        }

        while ($this->db->next_record()) {
            $rate_names[]=$this->db->f('name');
        }

        foreach ($rate_names as $name) {
            if (!$name) $name='default';

            $table="billing_rates_".$name;

            $query=sprintf("drop table if exists %s",addslashes($table));

            if (!$this->db->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                print $log;
                syslog(LOG_NOTICE, $log);
                return false;
            }

            if (!$this->db->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                print $log;
                syslog(LOG_NOTICE, $log);
                return false;
            }

            $query=sprintf("create table %s select * from billing_rates where name = '%s'\n",$table,$name);

            if (!$this->db->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                print $log;
                syslog(LOG_NOTICE, $log);
                return false;
            } else {
                $query=sprintf("alter table %s add index rate_idx (name)",$table);
                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }

                $query=sprintf("alter table %s add index destination_idx (destination)",$table);
                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    print $log;
                    syslog(LOG_NOTICE, $log);
                    return false;
                }
                $query=sprintf("select count(*) as c from %s",$table);
                $this->db->query($query);
                $this->db->next_record();
                $records=$this->db->f('c');
                $created_records=$created_records+$records;
                $progress=100*$created_records/$rows;

                printf ("Created table %s with %s records (%.1f %s)\n",$table,$records,$progress,'%');
            }

        }

        return true;
    }

    function updateTable() {

        global $auth;

        $loginname=$auth->auth["uname"];

        foreach ($this->web_elements as $_el) {
            ${$_el}= $_REQUEST[$_el];
        }

        if (!$table) return false;

        if ($this->readonly) {
        	return true;
        }

        // Init table structure
        if (!is_array($this->tables[$table]['exceptions'])) $this->tables[$table]['exceptions']=array();
        if (!is_array($this->tables[$table]['keys']))       $this->tables[$table]['keys']=array();
        if (!is_array($this->tables[$table]['fields']))     $this->tables[$table]['fields']=array();

        $metadata  = $this->db->metadata($table="$table");
        $cc        = count($metadata);
        // end init table structure

        if ($web_task =="update") {
            $affected_rows=0;
            if ($subweb_task == "Update") {
                if ($this->checkValues($table,$_REQUEST)) {
                    $update_set='';
                    $k=0;
                    while ($k < $cc ) {
                        $k++;
                        $Fname=$metadata[$k]['name'];
                        if (!$Fname) continue;
    
                        $value=$_REQUEST[$Fname];
    
                        if ($this->tables[$table]['fields'][$Fname]['readonly']) {
                            continue;
                        }

                        if (in_array($Fname,$this->tables[$table]['exceptions'])) {
                            continue;
                        }
    
                        if (in_array($Fname,$this->tables[$table]['keys'])) {
                            continue;
                        }
    
                        if ($kkk > 0) {
                            $comma = ",";
                        } else {
                            $comma = "";
                        }

                        if (!$this->tables[$table]['skip_math'] && preg_match("/^([\+\-\*\/])(.*)$/",$value,$sign)) {
                            $update_set .= $comma.$Fname."= ROUND(".$Fname. " ".$sign[1]. "'".$sign[2]."')";
                        } else {
                            $update_set .= $comma.$Fname."='".$value."'";
                        }
                        $kkk++;
                    }

                    $k=0;
                    while ($k < $cc ) {
                        if ($metadata[$k]['name'] == 'change_date') {
                        	$update_set .= sprintf("%s %s = NOW() ",$comma,$metadata[$k]['name']);
                            break;
                        }
                        $k++;
                    }
    
                    $log_entity=" id = $id ";
            
                    $where = " id = '".$id."' and $this->whereResellerFilter";
    
                    if ($table == "billing_rates") {
                        if ($this->settings['split_rating_table']) {
        
                            $rate_table_affected=array();
                            $query_r="select distinct (name) from billing_rates where". $where;
                            if ($this->db->query($query_r)) {
                                while($this->db->next_record()) {
                                    $rate_tables_affected[]='billing_rates_'.$this->db->f('name');
                                }
                            } else {
                                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                                print $log;
                                syslog(LOG_NOTICE, $log);
                            }
                        }
                    } else if ($table=="prepaid") {
                        register_shutdown_function("unLockTables",$this->db);
            
                        if ($this->db->query("lock table prepaid write")) {
                            $query_q=sprintf("select * from prepaid where account = '%s'",addslashes($account));
                            if ($this->db->query($query_q) && $this->db->num_rows()) {
                                $this->db->next_record();
                                $old_balance=$this->db->f('balance');
                            }
            
                            $this->db->query("unlock tables");
                        }
                    }
    
                    $query = sprintf("update %s set %s where %s " ,
                    $table,
                    $update_set,
                    $where
                    );

                    if ($this->db->query($query)) {
                        $affected_rows=$this->db->affected_rows();
                        if ($affected_rows) {
                            if ($table=="prepaid") {
                                list($username,$domain)=explode("@",$account);
            
                                $value=$balance-$old_balance;
            
                                if (floatval($balance) != floatval($old_balance))  {
                                    $query=sprintf("insert into prepaid_history
                                    (username,domain,action,description,value,balance,date,reseller_id)
                                    values
                                    ('%s','%s','Set balance','Manual update','%s','%s',NOW(),%d)",
                                    addslashes($username),
                                    addslashes($domain),
                                    addslashes($value),
                                    addslashes($balance),
                                    $this->CDRTool['filter']['reseller']
                                    );

                                    if (!$this->db->query($query)) {
                                    	$log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                                        print $log;
                                        syslog(LOG_NOTICE, $log);
                                    }
                                }
            
                            } else if ($table=='billing_rates') {
                                if ($this->settings['split_rating_table']) {
                                    foreach ($rate_tables_affected as $extra_rate_table) {
                                        $query_u = sprintf("update %s set %s where %s ",
                                        $extra_rate_table,
                                        $update_set,
                                        $where
                                        );
                                        if (!$this->db->query($query_u)) {
                                            $log=sprintf ("Database error for query %s: %s (%s)",$query_u,$this->db->Error,$this->db->Errno);
                                            print $log;
                                            syslog(LOG_NOTICE, $log);
                                        }
                                    }
                                }
                            }
    
                            if (in_array($table,$this->requireReload)) {
                                if (!$this->db->query("update settings setting set var_value= '1' where var_name = 'reloadRating'")){
                                    printf ("<font color=red>Database error: %s (%s)</font>",$this->db->Error,$this->db->Errno);
                                }
                            }
    
                        }
                    } else {
                        printf ("<font color=red>Database error for query '%s': %s (%s)</font>",$query,$this->db->Error,$this->db->Errno);
                    }
                } else {
                    print "<p>Correct the values and try again.";
                }
            } elseif ($subweb_task == "Update selection") {
                $k=0;
                $kkk=0;
                $update_set='';
                while ($k < $cc ) {
                    $k++;
                    $Fname=$metadata[$k]['name'];
                    $value=$_REQUEST[$Fname];
                    if (!strlen($value)) continue;

                    if ($this->tables[$table]['fields'][$Fname]['readonly']) {
                        continue;
                    }

                    if (in_array($Fname,$this->tables[$table]['exceptions'])) {
                        continue;
                    }

                    if (in_array($Fname,$this->tables[$table]['keys'])) {
                        continue;
                    }

                    if ($kkk > 0) {
                        $comma = ",";
                    } else {
                        $comma="";
                    }
                    if ($value == "NULL") {
                        $value="";
                    }
                    if (preg_match("/^([\+\-\*\/])(.*)$/",$value,$sign)) {
                        $update_set .= $comma.$Fname." = ROUND(".$Fname. " ".$sign[1]. "'".$sign[2]."')";
                    } else {
                        $update_set .= $comma.$Fname." = '".$value."'";
                    }
                    
                    $kkk++;
                }
        
                $where = $this->whereResellerFilter;

                if ($kkk) {
                    // reconstruct where clause to apply all changes to selection
                    // build where clause
                    // Search build for each field
                    $j=0;
                    while ($j < $cc ) {
            
                        $Fname=$metadata[$j]['name'];
                        $size=$metadata[$j]['len'];
        
                        if (!in_array($Fname,$this->tables[$table]['exceptions'])) {
            
                            $f_name="search_".$Fname;
                            $value=$_REQUEST[$f_name];
        
                            if (preg_match("/^([<|>]+)(.*)$/",$value,$likes)) {
                                $like=$likes[1];
                                $likewhat=$likes[2];
                                $quotes="";                
                            } else {
                                $like="like";
                                $likewhat=$value;
                                $quotes="'";
                            }                
            
                            if (strlen($value)) {
                                $where .= " and $Fname $like $quotes".$likewhat."$quotes";
                                $t++;
                            }
                        
                        }
                    
                        $j++;    
                    }

                    if ($table == 'billing_rates') {
                        if ($this->settings['split_rating_table']) {
    
                            $rate_table_affected=array();
                            $query_r="select distinct (name) from billing_rates where". $where;
                            if ($this->db->query($query_r)) {
                                while($this->db->next_record()) {
                                    $rate_tables_affected[]='billing_rates_'.$this->db->f('name');
                                }
                            } else {
                                printf ("<font color=red>Database error: %s (%s)</font>",$this->db->Error,$this->db->Errno);
                            }
                        }
                    }

                    $query = sprintf("update %s set %s where %s " ,
                    $table,
                    $update_set,
                    $where
                    );

                    if ($this->db->query($query)) {
                        $affected_rows=$this->db->affected_rows();
                        if ($affected_rows) {
                            if ($table == 'billing_rates') {
                                if ($this->settings['split_rating_table']) {
                                    foreach ($rate_tables_affected as $extra_rate_table) {
                                        $query_u = sprintf("update %s set %s where %s ",
                                        $extra_rate_table,
                                        $update_set,
                                        $where
                                        );
                                        if (!$this->db->query($query_u)) {
                                            printf ("<font color=red>Database error for %s: %s (%s)</font>",$query_u,$this->db->Error,$this->db->Errno);
                                        }
                                    }
                                }
                            }

                            if (in_array($table,$this->requireReload)) {
                                $this->db->query("update settings setting set var_value= '1' where var_name = 'reloadRating'");
                            }
                        }
        
                    } else {
                        printf ("<font color=red>Database error: %s</font>",$this->db->Error);
                    }
                }
            } elseif ($subweb_task == "Delete selection") {
                if ($confirmDelete) {
                    // reconstruct where clause to apply all changes to selection
                    // build where clause
                    // Search build for each field

	                $where = $this->whereResellerFilter;

                    $j=0;
                    while ($j < $cc ) {
            
                        $Fname=$metadata[$j]['name'];
                        $size=$metadata[$j]['len'];
            
                        if (!in_array($Fname,$this->tables[$table]['exceptions'])) {
            
                            $f_name="search_".$Fname;
                            $value=$_REQUEST[$f_name];
            
                            if (preg_match("/^([<|>]+)(.*)$/",$value,$likes)) {
                                $like=$likes[1];
                                $likewhat=$likes[2];
                                $quotes="";                
                            } else {
                                $like="like";
                                $likewhat=$value;
                                $quotes="'";
                            }                
            
                            if (strlen($value)) {
                                $where .= " and $Fname $like $quotes".$likewhat."$quotes";
                                $t++;
                            }
                        
                        }
                    
                        $j++;    
                    }

                    if ($table == 'billing_rates') {
                        if ($this->settings['split_rating_table']) {
    
                            $rate_table_affected=array();
                            $query_r="select distinct (name) from billing_rates where". $where;
                            if ($this->db->query($query_r)) {
                                while($this->db->next_record()) {
                                    $rate_tables_affected[]='billing_rates_'.$this->db->f('name');
                                }
                            } else {
                                printf ("<font color=red>Database error: %s (%s)</font>",$this->db->Error,$this->db->Errno);
                            }
                        }
                    }

                    $query="delete from $table where $where";

                    if ($this->db->query($query)) {
        
                        $affected_rows=$this->db->affected_rows();
        
                        if ($affected_rows) {
                            if ($table == 'billing_rates') {
                                if ($this->settings['split_rating_table']) {
                                    foreach ($rate_tables_affected as $extra_rate_table) {
                                        $query_u = sprintf("delete from %s where %s ",
                                        $extra_rate_table,
                                        $where
                                        );
                                        if (!$this->db->query($query_u)) {
                                            printf ("<font color=red>Database error for %s: %s (%s)</font>",$query_u,$this->db->Error,$this->db->Errno);
                                        }
                                    }
                                }
                            }

                            if (in_array($table,$this->requireReload)) {
                                $this->db->query("update settings setting set var_value= '1' where var_name = 'reloadRating'");
                            }
                        }
                    } else {
                        printf ("<font color=red>Database error: %s</font>",$this->db->Error);
                    }
        
                    unset($confirmDelete);
        
                } else {
                    print "<p><font color=blue>";
                    print "Please confirm the deletion by pressing the Delete button again. ";
                    print "</font>";
                    print "<input type=hidden name=confirmDelete value=1>";
                }
            } elseif ($subweb_task == "Copy rate" && strlen($fromRate) && strlen($toRate)) {
                $toRate=preg_replace("/%/","",$toRate);
                if ($confirmCopy) {
                    if ($toRate == 'history') {
                        $values=sprintf("
                        (reseller_id,name,destination,application,connectCost,durationRate,connectCostIn,durationRateIn,startDate,endDate)
                        select
                        billing_rates.reseller_id,
                        '%s',
                        billing_rates.destination,
                        billing_rates.application,
                        billing_rates.connectCost,
                        billing_rates.durationRate,
                        billing_rates.connectCostIn,
                        billing_rates.durationRateIn,
                        NOW(),
                        NOW()
                        from billing_rates ",
                        $fromRate);
        
                    } else {
        
                        $values=sprintf("
                        (reseller_id,name,destination,application,connectCost,durationRate,connectCostIn,durationRateIn)
                        select
                        billing_rates.reseller_id,
                        '%s',
                        billing_rates.destination,
                        billing_rates.application,
                        billing_rates.connectCost,
                        billing_rates.durationRate,
                        billing_rates.connectCostIn,
                        billing_rates.durationRateIn
                        from billing_rates ",
                        $toRate);
                    }

	                $where = $this->whereResellerFilter;

                    $j=0;
                    while ($j < $cc ) {
            
                        $Fname=$metadata[$j]['name'];
                        $size=$metadata[$j]['len'];
            
                        if (!in_array($Fname,$this->tables[$table]['exceptions'])) {
            
                            $f_name="search_".$Fname;
                            $value=$_REQUEST[$f_name];
            
                            if (preg_match("/^([<|>]+)(.*)$/",$value,$likes)) {
                                $like=$likes[1];
                                $likewhat=$likes[2];
                                $quotes="";                
                            } else {
                                $like="like";
                                $likewhat=$value;
                                $quotes="'";
                            }                
            
                            if (strlen($value)) {
                                $where .= " and $Fname $like $quotes".$likewhat."$quotes";
                                $t++;
                            }
                        }
                    
                        $j++;    
                    }

                    if ($toRate == 'history') {
                        $query="insert into billing_rates_history $values where $where";
                    } else {
                        $query="insert into billing_rates $values where $where";
                    }

                    if ($this->db->query($query)) {
        
                        $affected_rows=$this->db->affected_rows();
        
                        if ($affected_rows) {
                            print "$affected_rows rates copied. ";
                            if ($table == 'billing_rates') {
                                if ($this->settings['split_rating_table']) {
                                    $query=sprintf("create table billing_rates_%s select * from billing_rates where %s ",
                                    $toRate,
                                    $where
                                    );
                                    if (!$this->db->query($query)) {
                                        printf ("<font color=red>Database error for %s: %s (%s)</font>",$query,$this->db->Error,$this->db->Errno);
                                    }
                                }
                            }

                            if (in_array($table,$this->requireReload)) {
                                $this->db->query("update settings setting set var_value= '1' where var_name = 'reloadRating'");
                            }
                        }
        
                        if ($toRate == 'history') {
                            // Switch to history
                            $table      = 'billing_rates_history';
        
                            // Init table structure
                            $this->tables[$table]['exceptions']= $this->tables[$table]['exceptions'];
                            $this->tables[$table]['keys']      = $this->tables[$table]['keys'];
                            $this->tables[$table]['fields']    = $this->tables[$table]['fields'];
                            $metadata  = $this->db->metadata($table="$table");
                            $cc        = count($metadata);
                            // end init table structure
                        }
        
                        unset($confirmCopy);
                    } else {
                        printf ("<font color=red>Database error: %s</font>",$this->db->Error);
                    }
        
                    $log_entity="rate=$toRate";
        
                } else {
                    print "<p><font color=blue>";
                    print "Please confirm the copy of rate $fromRate to $toRate. ";
                    print "</font>";
                }
        
            } elseif ($subweb_task == "Insert") {
                //print "<h3>Insert</h3>";
                if ($this->checkValues($table,$_REQUEST)) {
                    $query="insert into $table ( ";
            
                    $k=1;
                    $kkk=0;
                    while ($k < $cc ) {
                        $Fname=$metadata[$k]['name'];
                        if (!in_array($Fname,$this->tables[$table]['exceptions']) ) {
                            if ($kkk > 0) {
                                $comma = ",";
                            } else {
                                $comma="";
                            }
                            $query .= $comma.$Fname;
                            $kkk++;
                        }
                        $k++;
                    }

                    $query .= ") values ( ";
                    $k=1;
                    $kkk=0;
                    while ($k < $cc ) {
                        $Fname=$metadata[$k]['name'];
                        $value=$_REQUEST[$Fname];
            
                        if (!in_array($Fname,$this->tables[$table]['exceptions']) ) {
                            if ($kkk > 0) {
                                $comma = ",";
                            } else {
                                $comma="";
                            }
                            if ($Fname == 'reseller_id' && $this->CDRTool['filter']['reseller']) {
                            	$query .= $comma."'".$this->CDRTool['filter']['reseller']."'";
                            } else {
                            	$query .= $comma."'".$value."'";
                            }
                            $kkk++;
                        }
                        $k++;
                        
                    }

                    $query .= ") ";
            
                    $k=1;
                    while ($k < $cc ) {
                        $Fname=$metadata[$k]['name'];
                        $value=$_REQUEST[$Fname];
                        if (in_array($Fname,$this->tables[$table]['keys']) ) {
                            if ($value == "") {
                                $Fname_print_insert=substr($Fname,4);
                                print "$Fname_print_insert = ???? <br>";
                                $empty_insert=1;
                            }                
                        }
                        $k++;
                    }


                    if (!$empty_insert) {
                        if ($this->db->query($query)) {
                        	$affected_rows=$this->db->affected_rows();
                        	if ($affected_rows) {

                                $this->db->query("select LAST_INSERT_ID() as lid");
                                $this->db->next_record();
                                $log_entity=sprintf("id=%s",$this->db->f('lid'));

                                if (in_array($table,$this->requireReload)) {
                                    $this->db->query("update settings setting set var_value= '1' where var_name = 'reloadRating'");
                                }
                            }
        
                        } else {
                            printf ("<font color=red>Database error for query %s: %s (%s)</font>",$query,$this->db->Error,$this->db->Errno);
                        }
                    } else {
                        print "<font color=red>
                        Error: The insert statement contains an empty key!
                        </font>
                        ";
                    }
                } else {
                    print "<p>Correct the values and try again.";
                }
            } elseif ($subweb_task == "Delete") {
                if ($confirmDelete) {
                    $query="delete from $table where id = '$id' and $this->whereResellerFilter ";
                    if ($this->db->query($query)) {
                        $affected_rows=$this->db->affected_rows();
                        if ($affected_rows && in_array($table,$this->requireReload)) {
                        
                            $this->db->query("update settings setting set var_value= '1' where var_name = 'reloadRating'");
                        }

                        $log_entity=sprintf("id=%s",$id);
        
                    } else {
                        printf ("<font color=red>Database error: %s</font>",$this->db->Error);
                    }
                    unset($confirmDelete);
                } else {
                    $idForDeletion=$id;
                    print "<p><font color=blue>";
                    print "Please confirm the deletion by pressing the Delete button again. ";
                    print "</font>";
                    print "<input type=hidden name=confirmDelete value=1>";
                }
            } elseif ($subweb_task == "Delete session" && $sessionId && $table=='prepaid') {

                $query=sprintf("select active_sessions from %s where id  = %d and %s",$table,$id,$this->whereResellerFilter);
                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    print $log;
                }

                if (!$this->db->num_rows()) return;

                $this->db->next_record();

                if (strlen($this->db->f('active_sessions'))) {
                    // remove session
                    $active_sessions=array();
                    $old_active_sessions = json_decode($this->db->f('active_sessions'),true);

                    if (!count($old_active_sessions)) return;
                    foreach (array_keys($old_active_sessions) as $_key) {
                        if ($_key==$sessionId) continue;
                        $active_sessions[$_key]=$old_active_sessions[$_key];
                    }
                } else {
                    $active_sessions=array();
                }
        
                $query=sprintf("update %s
                set active_sessions = '%s',
                session_counter = %d
                where id       = %d",
                $table,
                addslashes(json_encode($active_sessions)),
                count($active_sessions),
                addslashes($id)
                );

                if ($this->db->query($query)) {
                    return 1;
                } else {
                    $log=sprintf ("Database error for %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    syslog(LOG_NOTICE, $log);
                    print $log;
                    return 0;
                }

            }
        
            if ($affected_rows && $table!="prepaid") {
                $log_query=sprintf("insert into log
                (date,login,ip,datasource,results,description,reseller_id)
                values (NOW(),'%s','%s','Rating','%d','%s in table %s %s',%d)",
                addslashes($loginname),
                addslashes($_SERVER['REMOTE_ADDR']),
                addslashes($affected_rows),
                addslashes($subweb_task),
                addslashes($table),
                addslashes($log_entity),
                $this->CDRTool['filter']['reseller']
                );
                
                $this->db->query($log_query);
            }
        } 
    }

    function showTable() {

        $PHP_SELF=$_SERVER['PHP_SELF'];

        foreach ($this->web_elements as $_el) {
            ${$_el}= $_REQUEST[$_el];
        }

        if ($this->table == 'prepaid_cards') {
            print "<p>
            <a href=prepaid_cards.phtml>Prepaid card generator</a>";
        }

        // Init table structure
        if (!is_array($this->tables[$this->table]['exceptions'])) $this->tables[$this->table]['exceptions']=array();
        if (!is_array($this->tables[$this->table]['keys']))       $this->tables[$this->table]['keys']=array();
        if (!is_array($this->tables[$this->table]['fields']))     $this->tables[$this->table]['fields']=array();

        if ($this->table=='prepaid' && strlen($_REQUEST['search_session_counter'])) {
        	$this->readonly=true;
        }

        if ($this->readonly) {
            $this->tables[$this->table]['readonly']=1;
        }

        $metadata  = $this->db->metadata($this->table);
        $cc        = count($metadata);
        // end init table structure

        // delimiter for exporting records
        if ($this->settings['csv_delimiter']) {
            $delimiter=$this->settings['csv_delimiter'];
        } else {
            $delimiter=",";
        }

        $query=sprintf("select count(*) as c from %s where %s",
        $this->table,
        $this->whereResellerFilter);
        
        $t=0;
        $j=0;
        while ($j < $cc ) {
            $Fname=$metadata[$j]['name'];
            $size=$metadata[$j]['len'];
            if (!in_array($Fname,$this->tables[$this->table]['exceptions'])) {
                $f_name="search_".$Fname;
                $value=$_REQUEST[$f_name];
                if (preg_match("/^([<|>]+)(.*)$/",$value,$likes)) {
                    $like=$likes[1];
                    $likewhat=$likes[2];
                    $quotes="";                
                } else {
                    $like="like";
                    $likewhat=$value;
                    $quotes="'";
                }

                if (strlen($value)) {
                    $where.=" and $Fname $like $quotes".$likewhat."$quotes";
                    $t++;
                }
                
            }
        
            $j++;    
        }
            
        $query .= $where;

        $this->db->query($query);
        $this->db->next_record();
        $rows=$this->db->Record[0];
        
        if (!$export) {
            print "
            <table border=0 align=center>
            <tr><td>
            ";
            
            if ($rows == 0) {
                print "No records found. ";
            } else {
                print "$selectie $rows records found. ";
            }         
            
            if ($this->settings['socketIPforClients'] && $this->settings['socketPort']) {
        
                if ($ReloadRatingTables) {
                    reloadRatingEngineTables();
                } else {
                    $this->db->query("select var_value from settings where var_name = 'reloadRating' and var_value='1'");
                    if ($this->db->num_rows()) {
                        print " | <a href=rating_tables.phtml?ReloadRatingTables=1&table=$this->table><font color=red>Reload rating tables</font></a>";
                    }
                }
        
                $engineAddress=$this->settings['socketIPforClients'].":".$this->settings['socketPort'];

                if ($this->checkRatingEngineConnection()) {
                    print " | <font color=green>Rating engine running at $engineAddress</font>";
                } else {
                    print " | <font color=red>Cannot connect to rating engine $engineAddress</font>";
                }
            }
        
            print " | <a href=doc/RATING.txt target=rating_help>Rating documentation</a>";
        
            print "
            </td>
            </tr>
            </table>
            ";
        } else {
            $this->maxrowsperpage=10000000;
        }
        
        if (!$next) {
            $i=0;
            $next=0;
        } else {
            $i=$next;
        }
        
        $j=0;
        $z=0;
        
        if ($rows > $this->maxrowsperpage)  {
            $maxrows=$this->maxrowsperpage+$next;
            if ($maxrows > $rows) {
                $maxrows=$rows;
                $prev_rows=$maxrows;
            }
        } else  {
            $maxrows=$rows;
        }
        
        if (!$order && $this->tables[$this->table]['order']) {
        	$order=sprintf(" order by %s  ",$this->tables[$this->table]['order']);
        }
        
        $query=sprintf("select * from %s where (1=1) %s and %s %s limit %s, %s",
        $this->table,
        $where,
        $this->whereResellerFilter,
        $order,
        $i,
        $this->maxrowsperpage
        );

        //print $query;
        $this->db->query($query);
        $num_fields=$this->db->num_fields();
        $k=0;
        
        if (!$export) {
            print "
            <table border=0 class=border align=center width=100%>
            <tr bgcolor=lightgrey>
            <td></td>
            ";
        }
        
        while ($k < $cc) {
            $th=$metadata[$k]['name'];
            if (!in_array($th,$this->tables[$this->table]['exceptions']) ) {
                if ($this->tables[$this->table]['fields'][$th]['name']) {
                    $th=$this->tables[$this->table]['fields'][$th]['name'];
                } else {
                    $th=ucfirst($th);
                }
                if (!$export) {
                    print "<td class=border><b>$th</b></td>";
                } else {
                    if ($k) {
                        printf ("%s%s",$delimiter,$th);
                    } else {
                        print "Ops";
                    }
                }
                $t_columns++;
            }
            $k++;
        }
        
        if ($export) {
            print "\n";
        }
        
        if (!$export) {
            
            print "
            <td class=border><b>Action</b></td>
            </tr>";
            $t_columns=$t_columns+2;
            
            // SEARCH FORM
            print "
            <tr>
            <td class=border colspan=$t_columns>
                Use _ to match one character and % to match any. Use > or < 
                to find greater or smaller values.</td>    
            </tr>
            ";
            
            // Search form
            print "
            <form action=$PHP_SELF method=post name=rating>
            <input type=hidden name=web_task value=Search>
            <tr>
            <td>&nbsp; </td>";
            $j=0;
            
            while ($j < $cc ) {
                $Fname=$metadata[$j]['name'];
                $size=$metadata[$j]['len'];
            
                if (!in_array($Fname,$this->tables[$this->table]['exceptions'])) {
                    $SEARCH_NAME="search_".$Fname;
                    $value=$_REQUEST[$SEARCH_NAME];
                    if ($value != "") {
                        $selection_made=1;
                    }
                    $maxlength=$size;
            
                    if ($this->tables[$this->table]['fields'][$Fname]['size']) {
                        $field_size=$this->tables[$this->table]['fields'][$Fname]['size'];
                    } else {
                        $field_size=$el_size;
                    }
            
                    if (!in_array($Fname,$this->tables[$this->table]['keys']) ) {
                    	print "<td><input type=text size=$field_size maxlength=$maxlength name=search_$Fname value=\"$value\"></td>";
        
                    } else {
                        print "<td></td>";
                    }
        
                }
                
                $j++;
            }
            
            printf("
            <script type=\"text/JavaScript\">
            function jumpMenu(){
                location.href=\"%s?table=\" + document.rating.table.options[document.rating.table.selectedIndex].value;
            }
            </script>",
            $PHP_SELF
            );
            print "
            <td>
            ";
            printf("<select name='table' onChange=\"jumpMenu('this.form')\">\n");

            $selected_table[$this->table]="selected";
            foreach (array_keys($this->tables) as $tb) {
                $sel_name=$this->tables[$tb]['name'];
                print "<option value=$tb $selected_table[$tb]>$sel_name";
            }

            print "
            </select>
            <input type=submit name=subweb_task value=Search>
            </form>

            <form action=$PHP_SELF method=post target=export>
            <input type=hidden name=export value=1>
            ";
            $j=0;
            while ($j < $cc ) {
                $Fname=$metadata[$j]['name'];
                $size=$metadata[$j]['len'];
                if (!in_array($Fname,$this->tables[$this->table]['exceptions'])) {
                    $SEARCH_NAME="search_".$Fname;
                    $value=$_REQUEST[$SEARCH_NAME];
                    print "<input type=hidden name=search_$Fname value=\"$value\">";
                }
            
                $j++;    
            }
        
            if ($this->table!=='prepaid_cards' ) {
                printf ("
                <input type=hidden name=table value=%s>
                <input type=submit value=\"Export %s\">
                ",$this->table,$this->csv_export[$this->table]);
            }
        
            print "
            </form>
            ";

            if ($this->csv_import[$this->table]) {
                print "
                <form action=$PHP_SELF method='post' enctype='multipart/form-data'>
                <input type=hidden name=import value=1>
                ";
                printf ("
                <input type='hidden' name=table value=%s>
                <input type='submit' value=\"Import\">
                <input type='hidden' name='MAX_FILE_SIZE' value=1024000>
			    <input type='file'   name='%s'>
                ",$this->table,$this->table
                );
            
                print "
                </form>
                ";
            }

            print "
            </td>
            </tr>
            ";
            
            print "
            <tr>
            <td colspan=$t_columns><hr noshade size=2></td>
            </tr>
            ";
            
            if ($selection_made && !$this->tables[$this->table]['readonly']) {
                // Update all form
                print "
                <tr><td class=border colspan=$t_columns>
                Use + or - to add/substract from curent values.
                Use * or / to multiply/divide curent values.</td>
                </tr>";
            
                $j=0;
            
                print "
                <form action=$PHP_SELF method=post>
                <input type=hidden name=web_task value=update>
                <input type=hidden name=next value=$next>
                <tr>
                    <td>&nbsp;</td>";
            
                while ($j < $cc ) {
                    $Fname=$metadata[$j]['name'];
                    $size=$metadata[$j]['len'];
                    if ($this->tables[$this->table]['fields'][$Fname]['size']) {
                        $field_size=$this->tables[$this->table]['fields'][$Fname]['size'];
                    } else {
                        $field_size=$el_size;
                    }
            
            
                    if (!in_array($Fname,$this->tables[$this->table]['exceptions'])) {
                        if (!in_array($Fname,$this->tables[$this->table]['keys']) ) {
                        	print "<td><input type=text size=$field_size maxlength=$size name=$Fname></td>";
                        } else {
                            print "<td></td>";
                        }
                    }
                
                    $j++;    
                }
            
                $j=0;
                while ($j < $cc ) {
                    $Fname=$metadata[$j]['name'];
                    $size=$metadata[$j]['len'];
                    if (!in_array($Fname,$this->tables[$this->table]['exceptions'])) {
                        $SEARCH_NAME="search_".$Fname;
                        $value=$_REQUEST[$SEARCH_NAME];
                        print "<input type=hidden name=search_$Fname value=\"$value\">";
                    }
                
                    $j++;    
                }
        
                if ($subweb_task=="Delete selection" && !$confirmDelete) {
                    print "<td bgcolor=lightgrey>";
                    print "<input type=hidden name=confirmDelete value=1>";
                    print "<input type=submit name=subweb_task value=\"Delete selection\">";
                    print " ($rows records)";
                } else if (!$this->tables[$this->table]['readonly']){
        
                    if ($this->table == "billing_rates" && strlen($_REQUEST['search_name'])) {
                        if ($subweb_task=="Copy rate" && !$confirmCopy) {
                        print "<td bgcolor=lightgrey>";
                            print "<input type=hidden name=confirmCopy value=1>";
                        } else {
                            print "<td>";
                            print "
                            <input type=submit name=subweb_task value=\"Update selection\">
                            <input type=submit name=subweb_task value=\"Delete selection\">
                            <br>";
                        }
                        print "
                        <input type=submit name=subweb_task value=\"Copy rate\">";
                        printf (" id %s to",$_REQUEST['search_name']);
        
                        $query=sprintf("select distinct(name) as name
                        from billing_rates where
                        name like '%s'
                        order by name DESC
                        limit 1",$_REQUEST['search_name']);

                        $this->db1->query($query);
                        $this->db1->next_record();
                        $_rateName=$this->db1->f('name');

        		        $_rateName=preg_replace("/%/","",$_rateName);

                        if (preg_match("/^(.*)_(\d+)$/",$_rateName,$m)) {
                            $_idx=$m[2]+1;
                            $newRateName=$m[1]."_".$_idx;
                        } else {
                            $newRateName=$_rateName."_1";
                        }
                        printf ("<input type=hidden name=fromRate value=\"%s\">",$_REQUEST['search_name']);
                        $selected_newtable[$toRate]='selected';
                        printf ("<select name=toRate>
                        <option value=\"%s\" %s>Rate id %s
                        <option value=history %s>Rate history table
                        </select>",
                        $newRateName,
                        $selected_newtable[$newRateName],
                        $newRateName,
                        $selected_newtable['history']
                        );
        
                    } else {
                        print "<td>";
                        print "
                        <input type=submit name=subweb_task value=\"Update selection\">
                        <input type=submit name=subweb_task value=\"Delete selection\">
                        <br>";
        
                    }
                }
        
                print "
                    <td>
                    <input type=hidden name=table value=$this->table>
                    <input type=hidden name=search_text value=\"$search_text\">
                    </td>
                </tr>
                </form>
                ";
            
            } else if (!$this->tables[$this->table]['readonly']){
                // Insert form
                $j=0;
                print "
                <form action=$PHP_SELF method=post>
                <input type=hidden name=web_task value=update>
                <input type=hidden name=next value=$next>
                <tr>
                <td>&nbsp; </td>
                ";
                
                while ($j < $cc ) {
                    $Fname=$metadata[$j]['name'];
                    $size=$metadata[$j]['len'];
            
                    if ($this->tables[$this->table]['fields'][$Fname]['size']) {
                        $field_size=$this->tables[$this->table]['fields'][$Fname]['size'];
                    } else {
                        $field_size=$el_size;
                    }
            
                    if (!in_array($Fname,$this->tables[$this->table]['exceptions'])) {
                        if (!in_array($Fname,$this->tables[$this->table]['keys']) ) {
                        	print "<td><input type=text size=$field_size maxlength=$size name=$Fname></td>";
                        } else {
                            print "<td></td>";
                
                        }
                    }
                    $j++;    
                }
                
                $j=0;
                while ($j < $cc ) {
                    $Fname=$metadata[$j]['name'];
                    $size=$metadata[$j]['len'];
                    if (!in_array($Fname,$this->tables[$this->table]['exceptions'])) {
                        $SEARCH_NAME="search_".$Fname;
                        $value=$_REQUEST[$SEARCH_NAME];
                        print "<input type=hidden name=search_$Fname value=\"$value\">";
                    }
                
                    $j++;    
                }
                
                print "
                    <td class=border>
                    <input type=hidden name=table value=\"$this->table\">
                    <input type=hidden name=search_text value=\"$search_text\">
                    <input type=submit name=subweb_task value=Insert>
                    </td>
                </tr>
                </form>
                ";
                print "
                <tr>
                <td colspan=$t_columns><hr noshade size=2></td>
                </tr>
                ";

            }
            
        }
        
        while  ($i<$maxrows)  {
            $this->db->next_record();
            $id     = $this->db->f('id');
            $status = $this->db->f('status');
            $found  = $i+1;

            if (!$export) {
                print "
                <form action=$PHP_SELF method=post>
                <input type=hidden name=web_task value=update>
                <input type=hidden name=next value=$next>
                <input type=hidden name=id value=$id>
                <tr>
                ";

                if ($this->table == 'prepaid') {
                    $active_sessions = json_decode($this->db->f('active_sessions'),true);

                    $account=$this->db->f('account');

                    $extraInfo="
                    <table border=0 bgcolor=#CCDDFF class=extrainfo id=row$found cellpadding=0 cellspacing=0>
                    <form action=$PHP_SELF method=post>
                    <input type=hidden name=web_task value=update>
                    <input type=hidden name=next value=$next>
                    <input type=hidden name=id value=$id>
                    <tr>
                    <td valign=top>
                    <table border=0>
                    ";

                    $t=0;
                    foreach (array_keys($active_sessions) as $_session) {
                        $t++;
                    	$maxsessiontime=$active_sessions[$_session]['MaxSessionTime'];

                        $extraInfo.=sprintf ("<tr bgcolor=lightgrey><td class=border>%d. Session id</td><td>%s</td></tr>",$t,$_session);
                        $duration=time()-$active_sessions[$_session]['timestamp'];
                        foreach (array_keys($active_sessions[$_session]) as $key) {
                            if ($key=='timestamp') {
                            	$extraInfo.= sprintf ("<tr><td class=border><b>StartTime</b></td><td>%s</td></tr>",Date("Y-m-d H:i",$active_sessions[$_session]['timestamp']));
                                $extraInfo.= sprintf ("<tr><td class=border><b>Progress</b></td><td>%s (%s s)</td></tr>",sec2hms($duration),$duration);
                            } else {
                            	$extraInfo.= sprintf ("<tr><td class=border><b>%s</b></td><td>%s</td></tr>",ucfirst($key),$active_sessions[$_session][$key]);
                            }
                        }
                        if ($maxsessiontime < $duration ) {
                            $extraInfo.= sprintf ("<tr><td class=border colspan=2><font color=red><b>Session expired since %d s</b></font></td></tr>",$duration-$maxsessiontime);
                    		$extraInfo.= sprintf("<tr><td colspan=2><input type=submit name=subweb_task value='Delete session'></td></tr>");
                        }
                        //if (!$this->readonly) {
                        //}
                    }

                    $extraInfo.=sprintf("
                    <input type=hidden name=table value='%s'>
                    <input type=hidden name=next value='%s'>
                    <input type=hidden name=sessionId value='%s'>
                    <input type=hidden name=search_text value='%s'>
                    </form>
                    </table>
                    </td>
                    </tr>
                    </table>",
                    $this->table,$next,$_session,$search_text
                    );

                }
                print "
                <td>$found. </td>
                ";

            }

            $j=0;
            while ($j < $this->db->num_fields()) {
                $value=$this->db->Record[$j];
                $Fname=$metadata[$j]['name'];
                $size=$metadata[$j]['len'];
        
                if ($this->tables[$this->table]['fields'][$Fname]['size']) {
                    $field_size=$this->tables[$this->table]['fields'][$Fname]['size'];
                } else {
                    $field_size=$el_size;
                }
        
                if ($this->tables[$this->table]['fields'][$Fname]['readonly']=="1") {
                    $extra_form_els="disabled=true";
                } else {
                    $extra_form_els="";
                }
        
                if (!in_array($Fname,$this->tables[$this->table]['exceptions'])) {
                    if (!$export) {
                        if (!in_array($Fname,$this->tables[$this->table]['keys']) && !$this->readonly) {
                            if ($this->table == 'prepaid' && $Fname == 'session_counter' && $value) {
                                if (count($active_sessions) > 1) {
                                    $session_counter_txt=sprintf("%d sessions",$value);
                                } else {
                                    $session_counter_txt=sprintf("%d session",$value);
                                }

                                printf("<td onClick=\"return toggleVisibility('row%s')\"><a href=#>%s</td>",$found,$session_counter_txt);

                            } else {
                                print "<td>
                                <input type=text bgcolor=grey size=$field_size maxlength=$size name=$Fname value=\"$value\" $extra_form_els>
                                </td>";
                            }
        
                        } else {
                            if ($this->table == 'prepaid' && $Fname == 'session_counter' && $value) {
                                if (count($active_sessions) > 1) {
                                    $session_counter_txt=sprintf("%d sessions",$value);
                                } else {
                                    $session_counter_txt=sprintf("%d session",$value);
                                }

                                printf("<td onClick=\"return toggleVisibility('row%s')\"><a href=#>%s</td>",$found,$session_counter_txt);

                            } else {
                            	print "<td>$value</td>";
                            }

                        }

                    } else {
                        if ($j) {
                            printf ("%s%s",$delimiter,$value);
                        } else {
                            print "2";
                        }
                    }
                }
        
                $j++;    
            }
        
            $j=0;
            while ($j < $cc ) {
                $Fname=$metadata[$j]['name'];
                $size=$metadata[$j]['len'];
        
                if (!in_array($Fname,$this->tables[$this->table]['exceptions'])) {
                    $SEARCH_NAME="search_".$Fname;
                    $value=$_REQUEST[$SEARCH_NAME];
                    if (!$export) {
                        print "<input type=hidden name=search_$Fname value=\"$value\">";
                    }
                }
        
                $j++;    
            }
        
            if ($export) {
                print "\n";
            }
        
            if (!$export) {
            	if (!$this->tables[$this->table]['readonly']) {
                    if ($subweb_task=="Delete" && $idForDeletion == $id && !$confirmDelete) {
                        print "<td class=border bgcolor=lightgrey>";
                        print "<input type=hidden name=confirmDelete value=1>";
                        print "<input type=submit name=subweb_task value=Delete>";
                    } else {
                        print "
                        <td class=border>
                        <input type=submit name=subweb_task value=Update>
                        <input type=submit name=subweb_task value=Delete>
                        ";
                        print "<input type=hidden name=confirmDelete value=1>";
                    }
            
                    print "
                    <input type=hidden name=table value=$this->table>
                    <input type=hidden name=next value=$next>
                    <input type=hidden name=search_text value=\"$search_text\">
                    </td>
                    </tr>
                    </form>

                        <td></td>
                        <td colspan=$t_columns>$extraInfo</td>
                        </tr>
                    ";
                } else {
                    if ($this->table=='prepaid') {
                        print "
                        <tr>
                        <td></td>
                        <td colspan=$t_columns>$extraInfo</td>
                        </tr>
                        ";
                    }
                }
            }
            $i++;
        }
        
        if (!$export) {
            print "
            </table>
            <p>
            ";
            
            print "
            <center>
            <table border=0>
            <tr>
            <form method=post>

            <td>
            ";
            if ($next!= 0 ) {
                $show_next=$this->maxrowsperpage-$next;
            
                if  ($show_next<0)  {
                    $mod_show_next  =  $show_next-2*$show_next;
                }
            
                print "
                <input type=hidden name=maxrowsperpage value=$this->maxrowsperpage>
                <input type=hidden name=next           value=$mod_show_next>
                <input type=hidden name=web_task         value=Search>
                <input type=hidden name=table          value=$this->table>
                <input type=hidden name=search_text    value=\"$search_text\">
                ";
            
                $j=0;
                while ($j < $cc ) {
                    $Fname=$metadata[$j]['name'];
                    $size=$metadata[$j]['len'];
            
                    if (!in_array($Fname,$this->tables[$this->table]['exceptions'])) {
                        $SEARCH_NAME="search_".$Fname;
                        $value=$_REQUEST[$SEARCH_NAME];
                        print "<input type=hidden name=search_$Fname value=\"$value\">
                        ";        
                    }
                
                    $j++;    
                }
            
                print "    
                <input type=submit value=\"Previous\">
                ";
            }
            print "</td>
            </form>
            <form method=post>
            <td>
            ";
            
            if  ($rows>$this->maxrowsperpage &&  $rows!=$maxrows)  {  
                $show_next=$this->maxrowsperpage+$next;
            
                print "
                <input type=hidden name=maxrowsperpage value=$this->maxrowsperpage>
                <input type=hidden name=next           value=$show_next>
                <input type=hidden name=table           value=$this->table>
                <input type=hidden name=web_task           value=Search>
                ";
                $j=0;
                while ($j < $cc ) {
                    $Fname=$metadata[$j]['name'];
                    $size=$metadata[$j]['len'];
            
                    if (!in_array($Fname,$this->tables[$this->table]['exceptions'])) {
                        $SEARCH_NAME="search_".$Fname;
                        $value=$_REQUEST[$SEARCH_NAME];
                        print "<input type=hidden name=search_$Fname value=\"$value\">";
                    }
                
                    $j++;    
                }
            
                print "
                <input type=hidden name=search_text value=\"$search_text\">
                <input type=submit value=\"Next\">
                ";
            }
            
            print "
            </form>
            </td>
            </tr>
            </table>";
            
            
            print "
            </body>
            </html>
            ";
        }
    }

    function checkValues($table,$values=array()) {
        if (!$table) return false;

        $metadata  = $this->db->metadata($table);

        if (!is_array($metadata)) return false;

        $k=1;
        while ($k < count($metadata)) {
            $db_name   = $metadata[$k]['name'];
        	$k++;

            $web_name  = $this->tables[$table]['fields'][$db_name]['name'];
            $value     = $values[$db_name];
			$checkType = $this->tables[$table]['fields'][$db_name]['checkType'];
            $mustExist = $this->tables[$table]['fields'][$db_name]['mustExist'];

            if ($web_name) {
            	$name_print=$web_name;
            } else {
            	$name_print=$db_name;
            }

            if ($mustExist) {
                if (!strlen($value)) {
                	printf ("Error: field '%s' must be filled in\n",$name_print);
                	return false;
                }
            }

            if ($checkType) {
                if (!strlen($value)) {
                	if (!$mustExist) continue;
                }

                if ($checkType == 'sip_account') {
                    if (!checkEmail($value)) {
                        printf ("Error: value '%s' for field '%s' must be of format 'user@domain'\n",$value,$name_print);
                        return false;
                    }
                }

                if ($checkType == 'domain') {
                	if (stristr($value,"-.") || !preg_match("/^([a-zA-Z0-9][a-zA-Z0-9-]*\.)+[a-zA-Z]{2,}$/i",$value)) {
                        printf ("Error: value '%s' for field '%s' must be of format 'example.com'\n",$value,$name_print);
        				return false;
    				}
                }

                if ($checkType == 'ip') {
                	if (!preg_match("/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$/i",$value,$m)) {
                        printf ("Error: value '%s' for field '%s' must be of format 'X.X.X.X'\n",$value,$name_print);
        				return false;
    				} else {
                        $i=1;
                        while ($i<=4) {
                            if ($m[$i] < 1 || $m[$i] > 255) {
                        		printf ("Error: value '%s' for field '%s' must be of a valid IP address\n",$value,$name_print);
        						return false;
                            }
                            $i++;
                        }
                    }
                }

                if ($checkType == 'numeric') {
                    if (!is_numeric($value)) {
                        printf ("Error: value '%s' for field '%s' must be of type '%s'\n",$value,$name_print,$checkType);
                        return false;
                    }
                }
            }
        }

        return true;
    }

    function importTable($table='') {
        // import a table from web
    	if (!is_array($_FILES[$table]) || $_FILES[$table]['size'] == 0) return false;

        foreach ($this->importFilesPatterns as $_pattern) {
            if (strstr($_FILES[$table]['name'],$_pattern) && preg_match("/\.csv$/",$_FILES[$table]['name'])) {

                if ($this->CDRTool['filters']['reseller']) {
                	$dir=$this->cvs_import_dir.'/'.$this->CDRTool['filters']['reseller'];
                    if (!is_dir($dir)) {
                        if (!mkdir($dir)) {
                            printf ("<font color=red>Error: cannot create directory %s</font>",$dir);
                            return false;
                        }
                    }
                	$fullPath=$this->cvs_import_dir.'/'.$this->CDRTool['filters']['reseller'].'/'.$_FILES[$table]['name'];
                } else {
                	$fullPath=$this->cvs_import_dir.'/'.$_FILES[$table]['name'];
                }

                if (!is_file($fullPath)) {
                    if ($fp = fopen($fullPath, "w")) {
                    } else {
                        printf ("<font color=red>Error: cannot open file %s for writing</font>",$fullPath);
                        return false;
                    }
                } else {
                	list($basename,$extension)=explode('.',$_FILES[$table]['name']);
                    $j=0;
                    while (1) {
                        $j++;
                        if ($this->CDRTool['filters']['reseller']) {
                    		$fullPath=$this->cvs_import_dir.'/'.$this->CDRTool['filters']['reseller'].'/'.$basename.'-'.$j.'.'.$extension;
                        } else {
                    		$fullPath=$this->cvs_import_dir.'/'.$basename.'-'.$j.'.'.$extension;
                        }

                        if (is_file($fullPath)) continue;

                        if ($fp = fopen($fullPath, "w")) {
                            break;
                        } else {
                            printf ("<font color=red>Error: cannot open file %s for writing</font>",$fullPath);
                            return false;
                        }
                    }
                }

                $content=fread(fopen($_FILES[$table]['tmp_name'], "r"), $_FILES[$table]['size']);
                fwrite($fp,$content);
                fclose($fp);
                printf ("<p><font color=green>Imported %s bytes into %s</font>",$_FILES[$table]['size'],$fullPath);
                break;
            }
        }
    }
}

class OpenSIPSQuota {
    var $localDomains  = array();
    var $quotaGroup    = 'quota'; // group set if subscriber was blocked by quota
    var $timeout       = 5;       // soap connection timeout
    var $daily_quota   = 0;       // by default do not check daily quota

    function OpenSIPSQuota(&$parent) {

        global $DATASOURCES;

        $this->CDRdb           = &$parent->CDRdb;
        $this->table           = &$parent->table;
        $this->CDRTool         = &$parent->CDRTool;
        $this->cdr_source      = &$parent->cdr_source;

        $this->path=$this->CDRTool['Path'];

        $this->db_subscribers = &$parent->db_subscribers;
        if (!class_exists($this->db_subscribers)) {
            print("Info: No database defined for SIP accounts $this->cdr_source.\n");
            return false;
        }

        $this->AccountsDB       = new $this->db_subscribers;
        $this->enableThor       = $parent->enableThor;

        $parent->LoadDomains();

        $this->localDomains        = &$parent->localDomains;
        $this->cdr_source          = &$parent->cdr_source;
        $this->BillingPartyIdField = &$parent->CDRFields['BillingPartyId'];

        $this->parent = &$parent;

        $this->db = new DB_cdrtool;
        $this->db->Halt_On_Error="no";

        $this->db1 = new DB_cdrtool;
        $this->db1->Halt_On_Error="no";

        $this->db1 = new DB_cdrtool;
        $this->db1->Halt_On_Error="no";

        $this->CDRS = &$parent;

        $this->quota_init_flag   = &$parent->quota_init_flag;
        $this->quota_reset_flag  = &$parent->quota_reset_flag;

        if ($parent->daily_quota && is_numeric($parent->daily_quota) && $parent->daily_quota > 1 && $parent->daily_quota < 100) {
       		$this->daily_quota = $parent->daily_quota;
        }

        // load e-mail addresses for quota notifications
        $query="select * from settings where var_module = 'notifications'";

        if ($this->db->query($query) && $this->db->num_rows()) {

            while ($this->db->next_record()) {
                $_bp    =$this->db->f('billing_party');
                $_name  =$this->db->f('var_name');
                $_value =$this->db->f('var_value');
                if ($_bp && $_name && $_value) {
                    $this->notificationAddresses[$_bp][$_name]=$_value;
                }
            }
        }

        if ($DATASOURCES[$this->cdr_source]['soapEngineId']) {
            require("/etc/cdrtool/ngnpro_engines.inc");
            require_once("ngnpro_soap_library.php");

            if (in_array($DATASOURCES[$this->cdr_source]['soapEngineId'],array_keys($soapEngines))) {

                $this->SOAPurl  = $soapEngines[$DATASOURCES[$this->cdr_source]['soapEngineId']]['url'];
                $log=sprintf("Using SOAP engine %s to block accounts at %s\n",$DATASOURCES[$this->cdr_source]['soapEngineId'],$this->SOAPurl);
                syslog(LOG_NOTICE, $log);

                $this->SOAPlogin = array(
                                           "username"    => $soapEngines[$DATASOURCES[$this->cdr_source]['soapEngineId']]['username'],
                                           "password"    => $soapEngines[$DATASOURCES[$this->cdr_source]['soapEngineId']]['password'],
                                           "admin"       => true
                                           );
        
                $this->SoapAuth=array('auth', $this->SOAPlogin , 'urn:AGProjects:NGNPro', 0, '');

                $this->soapclient  = new WebService_NGNPro_SipPort($this->SOAPurl);

                $this->soapclient->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
                $this->soapclient->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);
                $this->soapclient->_options['timeout'] = $this->timeout;
        
            } else {
            	$e=$DATASOURCES[$this->cdr_source]['soapEngineId'];
                $log=sprintf("Error: soap engine id $e not found in /etc/cdrtool/ngnpro_engines.inc\n");
                print $log;
                syslog(LOG_NOTICE, $log);
                return false;
            }
        } else {
            $log=sprintf("Using database queries to block accounts\n");
            syslog(LOG_NOTICE, $log);
        }
    }

    function ShowAccountsWithQuota($treshhold='') {

        $query=sprintf("select * from quota_usage where datasource = '%s' and quota > 0 and cost > 0",$this->CDRS->cdr_source);

        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return false;
        }

        while ($this->db->next_record()) {
            if ($this->db->f('blocked')) {
                $blockedStatus="blocked";
            } else {
                $blockedStatus='';;
            }

            $usageRatio=$this->db->f('cost')*100/$this->db->f('quota');

            if ($treshhold && $treshhold > $usageRatio) continue;

            $usageStatus=sprintf("usage=%-10s",$this->db->f('cost'));

            printf ("%-35s quota=%-6s %s %.2f%s %s\n",
            $this->db->f('account'),
            $this->db->f('quota'),
            $usageStatus,
            $usageRatio,
            '%',
            $blockedStatus
            );
        }
    }

    function deblockAccounts($reset_quota_for=array()) {
        // deblock users blocked by quota

        if (!$this->db_subscribers) {
            print("Info: No database defined for SIP accounts.\n");
            return false;
        }

        if (!count($reset_quota_for)) {
            printf("Deblocking all SIP accounts blocked by quota\n");
        } else {
            printf("Deblocking %d SIP accounts blocked by quota\n",count($reset_quota_for));
        }

        if ($this->enableThor) {
            $query=sprintf("select username, domain, profile from sip_accounts where (1=1) ");

            if (count($reset_quota_for)) {
                $k=0;
                foreach ($reset_quota_for as $_account) {
                    if ($k) $usage_keys.= ", ";
                    $usage_keys.="'".$_account."'";
                    $k++;
                }
                $query.= "and CONCAT(username,'@',domain) in (".$usage_keys.")";
            }

            if (!$this->AccountsDB->query($query)) {
                $log=sprintf("Error: %s (%s)",$this->AccountsDB->Error,$this->AccountsDB->Errno);
                syslog(LOG_NOTICE,$log);
                return false;
            }
    
            while ($this->AccountsDB->next_record()) {
                $i++;

                $_account=$this->AccountsDB->f('username')."@".$this->AccountsDB->f('domain');
                $_profile=json_decode(trim($this->AccountsDB->f('profile')));

                if (in_array('quota',$_profile->groups)) {
                    $blockedAccounts[]=$_account;
                }

                if ($i%5000 == 0) {
                    print "$i accounts checked for deblocking\n";
                    flush();
                }
            }

            if ($i) {
                print "$i accounts checked for deblocking\n";
                flush();
            }

        } else {
             $query=sprintf("select CONCAT(username,'@',domain) as account from grp where grp = '%s'",$this->quotaGroup);

             if (count($reset_quota_for)) {
                 $k=0;
                 foreach ($reset_quota_for as $_account) {
                     if ($k) $usage_keys.= ", ";
                     $usage_keys.="'".$_account."'";
                     $k++;
                 }
                 $query.= "and CONCAT(username,'@',domain) in (".$usage_keys.")";
             }

             if (!$this->AccountsDB->query($query)) {
                 $log=sprintf ("Database error: %s (%s)",$this->AccountsDB->Error,$this->AccountsDB->Errno);
                 print $log;
                 syslog(LOG_NOTICE,$log);
                 return false;
             }

             $blockedAccounts=array();
             while ($this->AccountsDB->next_record()) {

                $i++;
                $blockedAccounts[]=$this->AccountsDB->f('account');

                if ($i%10000 == 0) {
                    print "$i accounts checked for deblocking\n";
                    flush();
                }

             }
        }

        if (count($reset_quota_for)) {
            $blockedAccounts=array_intersect($blockedAccounts,$reset_quota_for);
        }

        if (count($blockedAccounts) >0 ) {
            $this->unBlockRemoteAccounts($blockedAccounts);

            if (!$this->enableThor) {
                $query=sprintf("delete from grp where grp = '%s'",$this->quotaGroup);
                if (count($reset_quota_for)) {
                    $k=0;
                    foreach ($reset_quota_for as $_account) {
                        if ($k) $usage_keys.= ", ";
                        $usage_keys.="'".$_account."'";
                        $k++;
                    }
                    $query.= "and CONCAT(username,'@',domain) in (".$usage_keys.")";
                }

        
                if (!$this->AccountsDB->query($query)) {
                    $log=sprintf ("Database error: %s (%s)",$this->AccountsDB->Error,$this->AccountsDB->Errno);
                    print $log;
                    syslog(LOG_NOTICE,$log);
                    return false;
                }
            }
        }

        if  (count($blockedAccounts)) {
            $log=sprintf ("Reset %d users blocked by quota\n",count($blockedAccounts));
            print $log;
            syslog(LOG_NOTICE, $log);
        }
    }

    function initQuotaUsageFromDatabase($month="",$reset_quota_for=array()) {

        if (!$month) {
            $this->startTime=Date("Y-m-01 00:00",time());
        } else {
            $this->startTime=$month."-01 00:00";
        }

        $j=0;

        $usage_keys='';
        if (count($reset_quota_for)) {
            $log=sprintf ("Init quota of data source %s for %d accounts\n",$this->CDRS->cdr_source,count($reset_quota_for));
            print $log;
            syslog(LOG_NOTICE, $log);

            $k=0;
            foreach ($reset_quota_for as $_account) {
                if ($k) $usage_keys.= ", ";
                $usage_keys.="'".$_account."'";
                $k++;
            }
            $usage_keys="and ".$this->BillingPartyIdField. " in (".$usage_keys.")";
        } else {
            if (count($this->localDomains)) {
                $domain_filter="and Realm in (";
                $t=0;
                foreach (array_keys($this->localDomains) as $_domain) {
                    if (!$_domain) continue;
                    if ($t) $domain_filter .= ",";
                    $domain_filter .= sprintf("'%s'",$_domain);
                    $t++;
                }
                $domain_filter .= ") ";
            }

            $log=sprintf ("Init quota of data source %s for all accounts\n",$this->CDRS->cdr_source);
            print $log;
            syslog(LOG_NOTICE, $log);
        }

        $query=sprintf("select %s,
        count(*) as calls,
        sum(AcctSessionTime) as duration,
        sum(Price) as cost,
        sum(AcctInputOctets + AcctOutputOctets)/2 as traffic
        from %s
        where AcctStartTime >= '%s'
        and Normalized = '1'
        %s
        %s
        group by %s\n",
        addslashes($this->BillingPartyIdField),
        addslashes($this->table),
        addslashes($this->startTime),
        $domain_filter,
        $usage_keys,
        addslashes($this->BillingPartyIdField)
        );

        if (!$this->CDRdb->query($query)) {
            if ($this->CDRdb->Errno != 1146) {
                $log=sprintf ("Database error: %s (%s)",$this->CDRdb->Error,$this->CDRdb->Errno);
                print $log;
                syslog(LOG_NOTICE,$log);
                return false;
            }
        }

        $rows=$this->CDRdb->num_rows();
        $log=sprintf ("%d callers generated traffic in %s for data source %s\n",$rows,Date("Y-m",time()),$this->CDRS->cdr_source);
        print $log;
        flush();
        syslog(LOG_NOTICE, $log);

        $j=0;
		$progress=0;

        while($this->CDRdb->next_record()) {

            if ($rows > 1000) {
                if ($j > $progress*$rows/100) {
                    $progress++;
                    if ($progress%10 == 0) {
                        print "$progress% ";
                    	flush();
                    }
                }
            }
            unset($accounts);

            $accounts[$this->CDRdb->f($this->BillingPartyIdField)]['usage']['calls']    = $this->CDRdb->f('calls');
            $accounts[$this->CDRdb->f($this->BillingPartyIdField)]['usage']['duration'] = $this->CDRdb->f('duration');
            $accounts[$this->CDRdb->f($this->BillingPartyIdField)]['usage']['cost']     = $this->CDRdb->f('cost');
            $accounts[$this->CDRdb->f($this->BillingPartyIdField)]['usage']['traffic']  = $this->CDRdb->f('traffic');
            $accounts[$this->CDRdb->f($this->BillingPartyIdField)]['usage']['cost_today'] = 0;

            $this->CDRS->cacheQuotaUsage(&$accounts);
            $j++;
        }
    }

    function checkQuota($notify) {
        global $UserQuota;
        $this->initQuotaUsage();

        $query=sprintf("select * from quota_usage where datasource = '%s' and quota > 0 and cost > quota",$this->CDRS->cdr_source);

        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return false;
        }

        $toNotify=array();

        $_checks=0;

        while ($this->db->next_record()) {

            $account=$this->db->f('account');
            list($username,$domain)=explode("@",$account);

            if ($this->db->f('cost') >= $this->db->f('quota')) {
                $quota_exceeded=true;
                $exceeded_period='monthly';
            } else if ($this->daily_quota && ($this->db->f('cost_daily') >= $this->db->f('quota') * $this->daily_quota/100)) {
                $quota_exceeded=true;
                $exceeded_period='daily';
            } else {
            	$quota_exceeded= false;
            }

            if ($quota_exceeded) {
                $exceeding_accounts++;

                if (!$this->db->f('blocked')) {
                    $reason='Cost exceeded';

                    if (!$seen_title) {
                        $line=sprintf ("%40s %6s %8s %8s %13s %s\n","User","Calls","Price","Minutes","Traffic","Reason");
                        print $line;
                        $email_body=$line;
                        $seen_title++;
                    }

                    $line          = sprintf ("%40s %6s %8s %8s %10s MB %s\n",
                    $account,
                    $this->db->f('calls'),
                    $this->db->f('cost'),
                    number_format($this->db->f('duration')/60,0,"",""),
                    number_format($this->db->f('traffic')/1024/1024,2),
                    $reason
                    );

                    $email_body = $email_body.$line;
                    print $line;

                    if ($this->enableThor) {
                        $this->domain_table          = "sip_domains";
                    } else {
                        $this->domain_table          = "domain";
                    }
            
                    $query=sprintf("select * from %s where domain = '%s'",$this->domain_table,$prepaidDomain);
        
                    if (!$this->AccountsDB->query($query)) {
                        $log=sprintf ("Database error: %s (%d) %s\n",$this->AccountsDB->Error,$this->AccountsDB->Errno,$query);
                        syslog(LOG_NOTICE,$log);
                    }
            
                    if ($this->AccountsDB->num_rows()){
                        $this->AccountsDB->next_record();
                        $_reseller=$this->AccountsDB->f('reseller_id');
                    } else {
                        $_reseller=0;
                    }

                    $log=sprintf("%s quota exceeded for %s (%s > %s)",ucfirst($exceeded_period),$account,$this->db->f('cost'), $this->db->f('quota'));
                    syslog(LOG_NOTICE, $log);

                    $log_query=sprintf("insert into log
                    (date,login,ip,datasource,results,description,reseller_id)
                    values (NOW(),'quotacheck','localhost','QuotaCheck','1','%s',%d)",
                    addslashes($log),
                    $_reseller
                    );
            
                    if (!$this->db1->query($log_query)) {
                        $log=sprintf ("Database error: %s (%s)",$this->db1->Error,$this->db1->Errno);
                        print $log;
                        syslog(LOG_NOTICE,$log);
                    }

                    if ($this->blockAccount($account)) {
                        if ($notify) {
                            $toNotify[]=$account;
                        }
                        $blocked_now++;
                        $blockedAccountsNow=$blockedAccountsNow.$account."\n";
                    }
                } else {
                    $blockedAccountsPrevious=$blockedAccountsPrevious.$account."\n";
                    $blocked_previous++;
                }
            }
            $_checks++;
        }
        
        if ($exceeding_accounts) {
            $line=sprintf("%6d accounts have exceeded their traffic limits\n",$exceeding_accounts);
            print $line;
            $email_body=$email_body.$line;
        } else {
              $log=sprintf("No quota has been exceeded\n");
              syslog(LOG_NOTICE, $log);
        }
        
        if ($blocked_now) {
            $line=sprintf("%6d accounts have been blocked now\n",$blocked_now);
            $email_body=$email_body.$line;
        }
        
        if ($blockedAccountsNow) {
            $line="Blocked accounts now:\n".$blockedAccountsNow;
            print $line;
            $email_body=$email_body.$line.$batch_block;
        }
        
        if ($blockedAccountsPrevious) {
            $line="Blocked acccounts previously:\n".$blockedAccountsPrevious;
            print $line;
            $email_body=$email_body.$line.$batch_unblock;
        }

        // send notification to the provider
        if ($this->CDRTool['provider']['toEmail'] && $blockedAccountsNow) {

            $from = $this->CDRTool['provider']['fromEmail'];
            $to   = $this->CDRTool['provider']['toEmail'];
            $bcc  = $this->CDRTool['provider']['bccEmail'];

            $service  = $this->CDRTool['provider']['service'];
            if (!$service) $service = "SIP";

            if ($from) $extraHeaders="From: $from\r\nBCC: $from";
            if ($bcc)  $extraHeaders=$extraHeaders.",".$bcc;

            print("Notify CDRTool provider at $to\n");
            mail($to, "$service platform - CDRTool quota check", $email_body, $extraHeaders);

        }

        if ($notify && is_array($toNotify) && count($toNotify) >0) {
            // send notification to accounts
            foreach($toNotify as $rcpt) {
                $this->notify($rcpt);
            }
        }
    }

    function notify($account) {
        global $DATASOURCES;

        list($username,$domain)=explode("@",$account);

        if (!$DATASOURCES[$this->cdr_source]['UserQuotaNotify']) {
            return false;
        }

        // get account information
        if ($this->enableThor) {
            $query=sprintf("select first_name,last_name,email from sip_accounts where username = '%s' and domain = '%s'",$username,$domain);
        } else {
            $query=sprintf("select first_name,last_name,email_address as email from subscriber where username = '%s' and domain = '%s'",$username,$domain);
        }

        if (!$this->AccountsDB->query($query)) {
            $log=sprintf("Database error: %s (%s)",$this->AccountsDB->Error,$this->AccountsDB->Errno);
            syslog(LOG_NOTICE,$log);
            return false;
        }

        if (!$this->AccountsDB->num_rows()) return false;
        $this->AccountsDB->next_record();

        $fullname = $this->AccountsDB->f('first_name')." ".$this->AccountsDB->f('last_name');
        $toEmail  = $this->AccountsDB->f('email');

        $providerName=$this->notificationAddresses[$domain]['providerName'];

        if (!$providerName)  $providerName="your SIP service provider";

        $body=sprintf("Dear __NAME__,\n\n".
        "Your SIP account %s has been temporarily blocked\n".
        "because your monthly quota has been exceeded.\n\n".
        "To unblock your account you may contact %s.\n\n".
        "N.B. This is an automatically generated message. Do not reply to it.\n",
        $account,
        $providerName);

        $fromEmail = $this->CDRTool['provider']['fromEmail'];
        $bccEmail  = $this->CDRTool['provider']['bccEmail'];
        $seen_bcc[$bccEmail]++;

        if ($this->notificationAddresses[$domain]['fromEmail']) {
            $fromEmail=$this->notificationAddresses[$domain]['fromEmail'];
        }

        if ($this->notificationAddresses[$domain]['quotaBody']) {
            $body=$this->notificationAddresses[$domain]['quotaBody'];
        }

        if ($this->notificationAddresses[$domain]['quotaSubject']) {
            $subject=$this->notificationAddresses[$domain]['quotaSubject'];
        }

        $body=preg_replace("/__NAME__/",$fullname,$body);
        $body=preg_replace("/__ACCOUNT__/",$account,$body);

        if (!$subject) {
            $subject=sprintf("Monthly quota exceeded for account %s",$account);
        } else {
            $subject=preg_replace("/__ACCOUNT__/",$account,$subject);
        }

        if (!$toEmail || !$fromEmail) {
            return false;
        }

        $seen_bcc[$toEmail]++;

        $extraHeaders="From: $fromEmail";

        if ($this->notificationAddresses[$domain][bccEmail]) {
            if ($bccEmail) $bccEmail.= ",";
            $bccEmail.=$this->notificationAddresses[$domain][bccEmail];
        }

        if ($bccEmail) $extraHeaders = $extraHeaders."\r\nBCC: ".$bccEmail;

        mail($toEmail,$subject,$body, $extraHeaders);

          $log_msg=sprintf("Monthly quota exceeded for %s. Notified To:%s From:%s\n",$account, $toEmail,$fromEmail);
          syslog(LOG_NOTICE, $log_msg);
        print $log_msg;

    }

    function blockAccount($account) {
        list($username,$domain)=explode("@",$account);

        if (is_object($this->soapclient)) {
              return  $this->blockAccountRemote($account);
        } else {

            $query=sprintf("insert into grp
            (username,domain,grp,last_modified)
            values
            ('%s','%s','%s',NOW())",
            addslashes($username),
            addslashes($domain),
            addslashes($this->quotaGroup)
            );

            if (!$this->AccountsDB->query($query)) {
                if ($this->AccountsDB->Errno != 1062) {
                    $log=sprintf ("Database error: %s (%s)",$this->AccountsDB->Error,$this->AccountsDB->Errno);
                    print $log;
                    syslog(LOG_NOTICE,$log);
                    return false;
                } else {
                    return true;
                }
            } else {
                $this->markBlocked($account);
                return true;
            }
        }
    }

    function blockAccountRemote($account) {
        list($username,$domain)=explode("@",$account);

        if (!$username || !$domain) {
            $log=sprintf("Error: misssing username/domain in blockAccountRemote()");
            syslog(LOG_NOTICE, $log);
            return false;
        }

        $this->soapclient->addHeader($this->SoapAuth);
        $result     = $this->soapclient->addToGroup(array("username" => $username,"domain"=> $domain), "quota");

        if (PEAR::isError($result)) {
            $error_msg   = $result->getMessage();
            $error_fault = $result->getFault();
            $error_code  = $result->getCode();

            $log=sprintf("Error from %s: %s (%s)",$this->SOAPurl,$error_fault->faultstring,$error_fault->faultcode);
            syslog(LOG_NOTICE, $log);
            print $log;

            if ($error_fault->detail->exception->errorcode != "1030") {

                $from         = $this->CDRTool['provider']['fromEmail'];
                $to           = $this->CDRTool['provider']['toEmail'];
                $extraHeaders = "From: $from";
                $email_body   = "Remote SOAP request failure when calling blockAccountRemote(): \n\n".
                                $log;

                mail($to, "CDRTool SOAP client failure", $email_body, $extraHeaders);
            }

            return false;
        } else {
            $log=sprintf ("Block account %s at %s",$account,$this->SOAPurl );
            syslog(LOG_NOTICE, $log);

            $this->markBlocked($account);
            return true;
        }

    }

    function unBlockRemoteAccounts($accounts) {
        if (!is_object($this->soapclient)) {
            return;
        }

        foreach ($accounts as $account) {
            list($username,$domain)=explode("@",$account);
    
            if (!$username || !$domain) return true;
    
            $this->soapclient->addHeader($this->SoapAuth);
            $result     = $this->soapclient->removeFromGroup(array("username" => $username,"domain"=> $domain), "quota");
    
            if (PEAR::isError($result)) {
                $error_msg   = $result->getMessage();
                $error_fault = $result->getFault();
                $error_code  = $result->getCode();
                if ($error_fault->detail->exception->errorcode &&
                    $error_fault->detail->exception->errorcode != "1030" &&
                    $error_fault->detail->exception->errorcode != "1031"
                    ) {
                    $from = $this->CDRTool[provider][fromEmail];
                    $to   = $this->CDRTool[provider][toEmail];
        
                    $extraHeaders="From: $from";
                    $email_body="SOAP request failure: \n\n".
        
                    $log=sprintf ("SOAP client error: %s %s\n",$error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    syslog(LOG_NOTICE, $log);
        
                    mail($to, "CDRTool SOAP failure", $email_body, $extraHeaders);
                }
            } else {
                $log=sprintf ("Unblock remote account %s at %s",$account,$this->SOAPurl);
                syslog(LOG_NOTICE, $log);
            }
        }
    }

    function saveQuotaInitFlag() {
        $query=sprintf("insert into memcache (`key`,`value`) values ('%s','1')",$this->quota_init_flag);
        if (!$this->db->query($query)) {
            if ($this->db->Errno != '1062') {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                print $log;
                syslog(LOG_NOTICE, $log);
                return false;
            }
        }
        return true;
    }

    function deleteQuotaInitFlag() {
        $query=sprintf("delete from memcache where `key` in ('%s','%s')",$this->quota_init_flag,$this->quota_reset_flag);
        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return false;
        }

        return true;
    }

    function deleteQuotaUsageFromCache ($reset_quota_for=array()) {

        $query=sprintf("delete from quota_usage where datasource = '%s' ",$this->CDRS->cdr_source);

        if (count($reset_quota_for)) {
            $query.= " and account in (";
            $t=0;
            foreach($reset_quota_for as $_account) {
                if ($t) $query.=",";
                $query.= sprintf("'%s'",$_account);

                $t++;
            }
            $query.=")";
        }

        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return false;
        }
        

        if ($this->db->affected_rows()) {
            $log=sprintf("Deleted %d keys from cache\n",$this->db->affected_rows());
            print $log;
            syslog(LOG_NOTICE, $log);
        }

        return true;
    }

    function initQuotaUsage() {

        $query=sprintf("select value from memcache where `key` = '%s'",$this->quota_init_flag);

        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return false;
        }

        if ($this->db->num_rows()) return true;

        $lockName=sprintf("%s:%s",$this->CDRS->cdr_source,$this->CDRS->table);

        if (!$this->CDRS->getNormalizeLock($lockName)) {
            $log=sprintf("Error: cannot initialize now the quota because a normalization process in progress\n");
            print $log;
            syslog(LOG_NOTICE, $log);
            return false;
        }

        $query=sprintf("select value from memcache where `key` = '%s'",$this->quota_reset_flag);
        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return false;
        }

		$reset_quota_for= array();

        if ($this->db->num_rows()) {
            $this->db->next_record();
            $reset_quota_for = json_decode($this->db->f('value'));
        }

        $this->deblockAccounts($reset_quota_for);

        $this->deleteQuotaUsageFromCache($reset_quota_for);

        $this->initQuotaUsageFromDatabase('',$reset_quota_for);
    
        if ($this->CDRS->status['cached_keys']['saved_keys']) {
            $log=sprintf("Saved %d accounts in quota cache\n",$this->CDRS->status['cached_keys']['saved_keys']);
            print $log;
            syslog(LOG_NOTICE, $log);
        }
    
        if ($this->CDRS->status['cached_keys']['failed_keys']) {
            $log=sprintf("Error: failed to save %d account\n",$this->CDRS->status['cached_keys']['failed_keys']);
            print $log;
            syslog(LOG_NOTICE, $log);
        }
    
        if ($this->saveQuotaInitFlag()) {
            $query=sprintf("delete from memcache where `key` = '%s'",$this->quota_reset_flag);
            if (!$this->db->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                print $log;
                syslog(LOG_NOTICE, $log);
                return false;
            }
            return true;
        } else {
            $log=sprintf ("Error: failed to save key quotaCheckInit");
            syslog(LOG_NOTICE, $log);
            return false;
        }
    }

    function markBlocked($account) {
        $query=sprintf("update quota_usage set blocked = '1', notified = NOW() where account = '%s' and datasource = '%s'",$account,$this->CDRS->cdr_source);
        if (!$this->db1->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db1->Error,$this->db1->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return false;
        }
    }

    function resetDailyQuota () {
        $query=sprintf("update quota_usage set cost_today = 0 where datasource = '%s'",$this->CDRS->cdr_source);
        if (!$this->db1->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db1->Error,$this->db1->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
            return false;
        }
        return true;
    }
}

class RatingEngine {

    var $method        = '';
    var $log_runtime   = false;
    var $prepaid_table = "prepaid";
	var $init_ok       = false;

    function RatingEngine () {

    	global $RatingEngine;   // set in global.inc
        global $DATASOURCES;    // set in global.inc

        if (!strlen($RatingEngine['socketIP']) || !$RatingEngine['socketPort'] || !$RatingEngine['cdr_source']) {
            $log=sprintf("Please define \$RatingEngine['socketIP'], \$RatingEngine['socketPort'] and \$RatingEngine['cdr_source'] in /etc/cdrtool/global.inc\n");
            syslog(LOG_NOTICE,$log);
            return false;
        }

        if (preg_match("/^\d{1-3}\.\d{1-3}\.\d{1-3}\.\d{1-3}$/",$RatingEngine['socketIP'])) {
            $log=sprintf("Invalid \$RatingEngine['socketIP'] in /etc/cdrtool/global.inc\n");
            syslog(LOG_NOTICE,$log);
            return false;
        }

        if (intval($RatingEngine['socketPort']) < 1 || intval($RatingEngine['socketPort']) > 65535) {
            $log=sprintf("Invalid \$RatingEngine['socketPort'] in /etc/cdrtool/global.inc\n");
            syslog(LOG_NOTICE,$log);
            return false;
        }

        if (!is_array($DATASOURCES[$RatingEngine['cdr_source']])) {
            $log=sprintf("Datasource '%s' does not exist in /etc/cdrtool/global.inc\n",$RatingEngine['cdr_source']);
            syslog(LOG_NOTICE,$log);
            return false;
        }

        $this->settings = $RatingEngine;

        if ($this->settings['log_runtime']) {
            $this->log_runtime=true;
        }

        // init database
        $this->db            = new DB_CDRTool;
        $query=sprintf("delete from memcache where `key` = 'destinations_sip' or `key` = 'destinations'");
        
        if (!$this->db->query($query)) {
            $log=sprintf ("Database error: %s (%s) for query %s",$db->Error,$db->Errno,$query);
            syslog(LOG_NOTICE,$log);
        }

        // init CDR datasource
        $CDR_class  = $DATASOURCES[$RatingEngine['cdr_source']]['class'];
        $this->CDRS = new $CDR_class($RatingEngine['cdr_source']);
        
        // load Rating Tables
        $this->CDRS->RatingTables = new RatingTables();
        $this->CDRS->RatingTables->LoadRatingTables();

        // init subscribers database
        $this->db_subscribers_class = &$this->CDRS->db_subscribers;

        if (!class_exists($this->db_subscribers_class)) {
            syslog(LOG_NOTICE,"Error: No database defined for SIP accounts");
            return false;
        }

        $this->AccountsDB       = new $this->db_subscribers_class;

        $this->enableThor       = $this->CDRS->enableThor;

		$this->init_ok = true;
    }

    function reloadRatingTables () {

        $b=time();
        $query="delete from memcache where `key` in ('destinations','destinations_sip','ENUMtlds')";

        if (!$this->db->query($query)) {
            $log=sprintf ("Database error: %s (%s)",$this->db->Error,$this->db->Errno);
            syslog(LOG_NOTICE,$log);
        }

		$this->CDRS->RatingTables->LoadRatingTables();
        $this->CDRS->LoadDestinations();

        $e=time();
        $d=$e-$b;

        if ($d > 0 ) syslog(LOG_NOTICE, "Reloaded rating tables in $d seconds");

        $this->db->query("update settings set var_value = '' where var_name = 'reloadRating'");
        return 1;
    }

    function reloadCustomers ($customerFilter) {
        return 1;
    }

    function reloadDomains () {
        return 1;
    }

    function reloadQuota($account) {
        if (!$account) return false;

        $quota   = $this->getQuota($account);
        $blocked = $this->getBlockedByQuotaStatus($account);

        $query=sprintf("update quota_usage set
        quota = '%s',
        blocked = '%s'
        where datasource = '%s'
        and account = '%s'",
        $quota,
        intval($blocked),
        $this->CDRS->cdr_source,
        $account);

        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            syslog(LOG_NOTICE, $log);
            return 0;
        }

        return 1;
    }

    function getBalanceHistory($account,$limit=50) {

        list($username,$domain)=explode("@",$account);
        if (!$username || !$domain) return 0;
        $query=sprintf("select * from prepaid_history where username = '%s' and domain = '%s' order by id desc",
        addslashes($username),
        addslashes($domain)
        );

        if ($limit) $query.= sprintf (" limit %d",$limit);
    
        if (!$this->db->query($query)) {
            $log=sprintf ("getBalanceHistory error: %s (%s)",$this->db->Error,$this->db->Errno);
            syslog(LOG_NOTICE, $log);
            return 0;
        }

        while ($this->db->next_record()) {
            $history[]=array('account'     => $account,
                             'action'      => $this->db->f('action'),
                             'description' => $this->db->f('description'),
                             'value'       => $this->db->f('value'),
                             'balance'     => $this->db->f('balance')
                            );
        }

        $line=json_encode($history);
        return $line;
    }

    function DebitBalanceAudio($account,$balance,$session_id,$duration,$force=false) {

        $els=explode(":",$account);

        if (count($els) == 2) {
            $account=$els[1];
        }

        if (!$account) {
            syslog(LOG_NOTICE, "DebitBalanceAudio() error: missing account");
            return 0;
        }

        if (!is_numeric($balance)) {
            syslog(LOG_NOTICE, "DebitBalanceAudio() error: balance must be numeric");
            return 0;
        }

        if (!$session_id) {
            syslog(LOG_NOTICE, "DebitBalanceAudio() error: missing call id");
            return 0;
        }

        $query=sprintf("select * from %s where account = '%s'",
        addslashes($this->prepaid_table),
        addslashes($account)
        );

        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            syslog(LOG_NOTICE,$log);
            $this->logRuntime();
            return 0;
        }

        if (!$this->db->num_rows()) {
            $log=sprintf ("DebitBalanceAudio() error: account $account does not exist");
            syslog(LOG_NOTICE, $log);
            $this->logRuntime();
            return 0;
        }

        $this->db->next_record();

        if (strlen($this->db->f('active_sessions'))) {
            // remove active session
            $active_sessions=array();
            $old_active_sessions = json_decode($this->db->f('active_sessions'),true);
            $destination=$old_active_sessions[$session_id]['Destination'];

            if (!$force) {
               if (!in_array($session_id,array_keys($old_active_sessions))) {
                   $this->sessionDoesNotExist=true;
                   $log=sprintf("Error: session %s of %s does not exist",$session_id,$account);
                   syslog(LOG_NOTICE, $log);
                   return 0;
               }
            }

            foreach (array_keys($old_active_sessions) as $_key) {
                if ($_key==$session_id) continue;
                $active_sessions[$_key]=$old_active_sessions[$_key];
            }

        } else {
            if (!$force) {
                $this->sessionDoesNotExist=true;
                $log=sprintf ("Error: session %s for %s does not exist",$session_id,$account);
                syslog(LOG_NOTICE, $log);
                return 0;
            }
        }

        $next_balance=$this->db->f('balance')-$balance;

        //get parallel calls and remaining_balance
        $this->getActivePrepaidSessions($active_sessions,$next_balance,$account);

        // calculate the updated maxsessiontime
        $maxsessiontime=$this->getAggregatedMaxSessiontime($this->parallel_calls,$this->remaining_balance,$account);

        $query=sprintf("update %s
        set balance          = balance - '%s',
        change_date          = NOW(),
        active_sessions      = '%s',
        session_counter      = '%s'
        where account        = '%s'",
        $this->prepaid_table,
        $balance,
        addslashes(json_encode($active_sessions)),
        count($active_sessions),
        addslashes($account)
        );

        if (!$this->db->query($query)) {
        	$log=sprintf ("Database error for %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            syslog(LOG_NOTICE, $log);
            return 0;
        }

        if ($balance > 0) {
            list($prepaidUser,$prepaidDomain)=explode("@",$account);

			if ($this->enableThor) {
                $this->domain_table          = "sip_domains";
            } else {
                $this->domain_table          = "domain";
            }
    
            $query=sprintf("select * from %s where domain = '%s'",$this->domain_table,$prepaidDomain);

            if (!$this->AccountsDB->query($query)) {
                $log=sprintf ("Database error: %s (%d) %s\n",$this->AccountsDB->Error,$this->AccountsDB->Errno,$query);
                syslog(LOG_NOTICE,$log);
            }
    
            if ($this->AccountsDB->num_rows()){
            	$this->AccountsDB->next_record();
            	$_reseller=$this->AccountsDB->f('reseller_id');
            } else {
            	$_reseller=0;
            }

            $query=sprintf("insert into prepaid_history
            (username,domain,action,description,value,balance,date,session,duration,destination,reseller_id)
            values 
            ('%s','%s','Debit balance','Session to %s for %ds','-%s','%s',NOW(),'%s','%d','%s',%d)",
            addslashes($prepaidUser),
            addslashes($prepaidDomain),
            addslashes($destination),
            $duration,
            $balance,
            $next_balance,
            addslashes($session_id),
            $duration,
            addslashes($destination),
            $_reseller
            );
    
            if (!$this->db->query($query)) {
                $log=sprintf ("Database error for %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                syslog(LOG_NOTICE, $log);
            }
        }

        return $maxsessiontime;
    }

    function DebitBalanceMessage($account,$destination,$balance,$session_id) {

        $els=explode(":",$account);

        if (count($els) == 2) {
            $account=$els[1];
        }

        if (!$account) {
            syslog(LOG_NOTICE, "DebitBalanceMessage() error: missing account");
            return 0;
        }

        if (!is_numeric($balance)) {
            syslog(LOG_NOTICE, "DebitBalanceMessage() error: balance must be numeric");
            return 0;
        }

        if (!$session_id) {
            syslog(LOG_NOTICE, "DebitBalanceMessage() error: missing call id");
            return 0;
        }

        $query=sprintf("select * from %s where account = '%s'",
        addslashes($this->prepaid_table),
        addslashes($account)
        );

        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            syslog(LOG_NOTICE,$log);
            $this->logRuntime();
            return 0;
        }

        if (!$this->db->num_rows()) {
            $log=sprintf ("DebitBalanceMessage() error: account $account does not exist");
            syslog(LOG_NOTICE, $log);
            $this->logRuntime();
            return 0;
        }

        $this->db->next_record();

        if (strlen($this->db->f('active_sessions'))) {
            $active_sessions = json_decode($this->db->f('active_sessions'),true);
        }

        $next_balance=$this->db->f('balance')-$balance;

        //get parallel calls and remaining_balance
        $this->getActivePrepaidSessions($active_sessions,$next_balance,$account);

        // calculate the updated maxsessiontime
        $maxsessiontime=$this->getAggregatedMaxSessiontime($this->parallel_calls,$this->remaining_balance,$account);

        $query=sprintf("update %s
        set balance          = balance - '%s',
        change_date          = NOW()
        where account        = '%s'",
        $this->prepaid_table,
        $balance,
        addslashes($account)
        );

        if (!$this->db->query($query)) {
        	$log=sprintf ("Database error for %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            syslog(LOG_NOTICE, $log);
            return 0;
        }

        if ($balance > 0) {
            list($prepaidUser,$prepaidDomain)=explode("@",$account);

			if ($this->enableThor) {
                $this->domain_table          = "sip_domains";
            } else {
                $this->domain_table          = "domain";
            }
    
            $query=sprintf("select * from %s where domain = '%s'",$this->domain_table,$prepaidDomain);

            if (!$this->AccountsDB->query($query)) {
                $log=sprintf ("Database error: %s (%d) %s\n",$this->AccountsDB->Error,$this->AccountsDB->Errno,$query);
                syslog(LOG_NOTICE,$log);
            }
    
            if ($this->AccountsDB->num_rows()){
            	$this->AccountsDB->next_record();
            	$_reseller=$this->AccountsDB->f('reseller_id');
            } else {
            	$_reseller=0;
            }

            $query=sprintf("insert into prepaid_history
            (username,domain,action,description,value,balance,date,session,destination,reseller_id)
            values 
            ('%s','%s','Debit balance','Message to %s','-%s','%s',NOW(),'%s','%s',%d)",
            addslashes($prepaidUser),
            addslashes($prepaidDomain),
            addslashes($destination),
            $balance,
            $next_balance,
            addslashes($session_id),
            addslashes($destination),
            $_reseller
            );
    
            if (!$this->db->query($query)) {
                $log=sprintf ("Database error for %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                syslog(LOG_NOTICE, $log);
            }
        }

        return true;
    }

    function CreditBalance($account,$balance) {

        if (!is_numeric($balance)) {
            syslog(LOG_NOTICE, "CreditBalance() error: balance \"$balance\"is invalid");
            return 0;
        }

        $els=explode(":",$account);

        if (count($els) == 2) {
            $account=$els[1];
        }

        if (!$account) {
            syslog(LOG_NOTICE, "CreditBalance() error: missing account");
            return 0;
        }

        list($prepaidUser,$prepaidDomain)=explode("@",$account);

        $query=sprintf("select * from %s where account = '%s'",
        addslashes($this->prepaid_table),
        addslashes($account)
        );

        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            syslog(LOG_NOTICE,$log);
            $this->logRuntime();
            return 0;
        }

        if ($this->db->num_rows()) {
            $this->db->next_record();
            $current_balance = $this->db->f('balance');
            $query=sprintf("update %s
            set balance   = balance + '%s',
            change_date   = NOW()
            where account = '%s'",
            $this->prepaid_table,
            $balance,
            addslashes($account)
            );

            $this->db->query($query);

            if ($this->db->affected_rows()) {
                $new_balance = $current_balance + $balance;

                $log=sprintf ("Prepaid account $account credited with $balance");
                syslog(LOG_NOTICE, $log);

                // log to prepaid_history
                $query=sprintf("insert into prepaid_history
                (username,domain,action,description,value,balance,date)
                values 
                ('%s','%s','Set balance','Manual update','%s','%s',NOW())",
                addslashes($prepaidUser),
                addslashes($prepaidDomain),
                $balance,
                $new_balance
                );

                if (!$this->db->query($query)) {
                    $log=sprintf("Error: %s (%s)",$this->db->Error,$this->db->Errno);
                    syslog(LOG_NOTICE, $log);
                }

                return 1;
            } else {
                $log=sprintf ("CreditBalance() error: failed to credit balance: %s (%s)",$this->db->Error,$this->db->Errno);
                syslog(LOG_NOTICE, $log);
                return 0;
            }

        } else {
            $query=sprintf("insert into %s (balance, account, change_date) values ('%s','%s',NOW())",
            $this->prepaid_table,
            $balance,
            addslashes($account)
            );

            $this->db->query($query);

            if ($this->db->affected_rows()) {

                $log=sprintf ("Added prepaid account $account with balance=$balance");
                syslog(LOG_NOTICE, $log);

                // log to prepaid_history
                $query=sprintf("insert into prepaid_history 
                (username,domain,action,description,value,balance,date)
                values 
                ('%s','%s','Set balance','Manual update','%s','%s',NOW())",
                addslashes($prepaidUser),
                addslashes($prepaidDomain),
                $balance,
                $balance
                );

                if (!$this->db->query($query)) {
                    $log=sprintf("Error: %s (%s)",$this->db->Error,$this->db->Errno);
                    syslog(LOG_NOTICE, $log);
                }

                return 1;
            } else {
                $log=sprintf ("CreditBalance() error: failed to credit balance: %s (%s)",$this->db->Error,$this->db->Errno);
                syslog(LOG_NOTICE, $log);
                return 0;
            }
        }

    }

    function DeleteBalance($account) {

        $els=explode(":",$account);

        if (count($els) == 2) {
            $account=$els[1];
        }

        if (!$account) {
            syslog(LOG_NOTICE, "DeleteBalance() error: missing account");
            return 0;
        }

        $query=sprintf("delete from %s where account = '%s'",
        $this->prepaid_table,addslashes($account)
        );

        if (!$this->db->query($query)) {
            $log=sprintf("DeleteBalance error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            syslog(LOG_NOTICE, $log);
            return 0;
        }

        $log=sprintf ("Prepaid account %s has been deleted",$account);
        syslog(LOG_NOTICE, $log);

        return 1;
    }

    function DeleteBalanceHistory($account) {

        $account=trim($account);

        $els=explode(":",$account);

        if (count($els) == 2) {
            $account=$els[1];
        }

        if (!$account) {
            syslog(LOG_NOTICE, "DeleteBalanceHistory() error: missing account");
            return 0;
        }

        list($username,$domain)=explode('@',$account);

        $query=sprintf("delete from prepaid_history where username = '%s' and domain = '%s'",
        addslashes($username),
        addslashes($domain)
        );

        if (!$this->db->query($query)) {
            $log=sprintf("DeleteBalanceHistory error for query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            syslog(LOG_NOTICE, $log);
            return 0;
        }

        $log=sprintf ("History of prepaid account %s has been deleted",$account);
        syslog(LOG_NOTICE, $log);

        return 1;
    }

    function GetEntityProfiles($entity) {

        if (!$entity) {
            syslog(LOG_NOTICE, "GetEntityProfiles");
            return 0;
        }

        $query=sprintf("select * from billing_customers where
        subscriber = '%s' or domain = '%s' or gateway = '%s'",
        addslashes($entity),
        addslashes($entity),
        addslashes($entity)
        );

        if (!$this->db->query($query)) {
            $log=sprintf ("GetEntityProfiles error: %s (%s)",$this->db->Error,$this->db->Errno);
            syslog(LOG_NOTICE, $log);
            return 0;
        }

        if ($this->db->num_rows() ==1 ) {
            $this->db->next_record();
            $entity = array('entity'            => $entity,
                            'profileWeekday'    => $this->db->f('profile_name1'),
                            'profileWeekdayAlt' => $this->db->f('profile_name1_alt'),
                            'profileWeekend'    => $this->db->f('profile_name2'),
                            'profileWeekendAlt' => $this->db->f('profile_name2_alt'),
                            'timezone'          => $this->db->f('timezone'),
                            'increment'         => $this->db->f('increment'),
                            'min_duration'      => $this->db->f('min_duration')
                            );
        }

        $line=json_encode($entity);
        return $line;

    }

    function SetEntityProfiles($entity,$profiles) {

        if (!$entity) {
            syslog(LOG_NOTICE, "SetEntityProfiles");
            return 0;
        }

    }

    function showHelp() {
        $help=
        "Version\n".
        "Help\n".
        "ShowClients\n".
        "MaxSessionTime        CallId=6432622xvv@1 From=sip:123@example.com To=sip:0031650222333@example.com Duration=7200 Gateway=10.0.0.1 Lock=1\n".
        "ShowPrice             From=sip:123@example.com To=sip:0031650222333@example.com Gateway=10.0.0.1 Duration=59\n".
        "DebitBalance          CallId=6432622xvv@1 From=sip:123@example.com To=sip:0031650222333@example.com Gateway=10.0.0.1 Duration=59\n".
        "AddBalance            From=123@example.com Value=10.00\n".
        "GetBalance            From=123@example.com\n".
        "GetBalanceHistory     From=123@example.com\n".
        "DeleteBalance         From=123@example.com\n".
        "DeleteBalanceHistory  From=123@example.com\n".
        "ReloadQuota           Account=abc@example.com\n".
        "GetEntityProfiles     Entity=abc@example.com\n".
        "ReloadRatingTables\n".
        "ReloadDomains\n".
        "ShowProfiles\n".
        "ShowENUMtlds\n"
        ;

        return $help;
    }

    function logRuntime() {
        if (!$this->log_runtime) return;

        $t=0;
        $log='';
        foreach (array_keys($this->runtime) as $_key) {
            $stamp=$this->runtime[$_key];

            if ($prev_stamp) {
                $_exec_time=$stamp-$prev_stamp;
                $log .= sprintf("%s=%1.7f ",$_key,$_exec_time);
            }

            $prev_stamp=$stamp;

            $t++;
        }
    
        syslog(LOG_NOTICE, $log);

    }

    function processNetworkInput($tinput) {

        // Read key=value pairs from input
        // Strip any unnecessary spaces
        $this->runtime=array();

        $tinput=preg_replace("/\s+/"," ",$tinput);

        $_els=explode(" ",trim($tinput));

        $this->runtime['start']=microtime_float();

        syslog(LOG_NOTICE, $tinput);

        if (!$_els[0]) return 0;

        // read fields from input
        unset($NetFields);
        unset($seenField);

        $i=0;
        while ($i < count($_els)) {
            $i++;

            $_dict  = explode("=",$_els[$i]);
            $_key   = strtolower(trim($_dict[0]));
            if ($_key == 'callid') {
            	$_value = trim($_dict[1]);
            } else {
            	$_value = strtolower(trim($_dict[1]));
            }

            if ($_key && $seenField[$_key]) {
                $log=sprintf ("Error: '$_key' attribute is present more than once in $tinput");
                syslog(LOG_NOTICE, $log);
                return 0;
            } else {
                if ($_key) {
                    $NetFields[$_key]=$_value;
                    $seenField[$_key]++;
                }
            }
        }

        $NetFields['action']=strtolower($_els[0]);

        $this->method = $NetFields['action'];

        // begin processing
        if ($NetFields['action']=="maxsessiontime") {

            if (!$NetFields['from']) {
                $log=sprintf ("error: missing From parameter");
                syslog(LOG_NOTICE, $log);
                return $log;
            }

            if (!$NetFields['to']) {
                $log=sprintf ("error: missing To parameter");
                syslog(LOG_NOTICE, $log);
                return $log;
            }

            if (!$NetFields['gateway']) {
                $log=sprintf ("error: missing gateway parameter");
                syslog(LOG_NOTICE, $log);
                return $log;
            }

            if (!$NetFields['callid']) {
                $log=sprintf ("error: missing Call Id parameter");
                syslog(LOG_NOTICE, $log);
                return $log;
            }

            if (!$NetFields['duration'] && $this->settings['MaxSessionTime']) {
            	$NetFields['duration']=$this->settings['MaxSessionTime'];
            }

            $CDRStructure=array (
                              $this->CDRS->CDRFields['callId']         => $NetFields['callid'],
                              $this->CDRS->CDRFields['aNumber']        => $NetFields['from'],
                              $this->CDRS->CDRFields['CanonicalURI']   => $NetFields['to'],
                              $this->CDRS->CDRFields['gateway']        => $NetFields['gateway'],
                              $this->CDRS->CDRFields['duration']       => floor($NetFields['duration']),
                              $this->CDRS->CDRFields['timestamp']      => time(),
                              'skip_fix_prepaid_duration'              => true
                              );

            $CDR = new $this->CDRS->CDR_class(&$this->CDRS, &$CDRStructure);
            $CDR->normalize();

            $this->runtime['normalize_cdr']=microtime_float();

            $query=sprintf("select * from %s where account = '%s'",addslashes($this->prepaid_table),addslashes($CDR->BillingPartyId));

            if (!$this->db->query($query)) {
                $log=sprintf ("Database error for query '%s': %s (%s), link_id =%s, query_id =%s",$query,$this->db->Error,$this->db->Errno,$this->db->Link_ID,$this->db->Query_ID);
                syslog(LOG_NOTICE,$log);
                $this->logRuntime();
                $ret=sprintf("error: database error for query '%s': %s (%s)",$query,$this->db->Error,$this->db->Errno)."\n"."type=prepaid";
                return $ret;
            }

            if (!$this->db->num_rows()) {
                $log=sprintf ("MaxSessionTime=unlimited Type=postpaid CallId=%s BillingParty=%s",$NetFields['callid'],$CDR->BillingPartyId);
                syslog(LOG_NOTICE, $log);
                $ret="none"."\n"."type=postpaid";
                return $ret;
            }

            $this->db->next_record();
            $Balance             = $this->db->f('balance');
            $session_counter     = $this->db->f('session_counter');
            $max_sessions        = $this->db->f('max_sessions');

            if (strlen($this->db->f('active_sessions'))) {
            	// load active sessions
                $active_sessions = json_decode($this->db->f('active_sessions'),true);

                if (count($active_sessions)) {
        
                    $active_sessions_new=array();

        		 	$expired=0;

                    foreach (array_keys($active_sessions) as $_session) {
                    
                        $expired_since=time() - $active_sessions[$_session]['timestamp'] - $active_sessions[$_session]['MaxSessionTime'];
                        if ($expired_since > 120) {
                            // this session has passed its maxsessiontime plus its reasonable setup time of 2 minutes,
                            // it could be stale
                            // because the call control module did not call debitbalance, so we purge it
        
                            $log = sprintf ("Session %s for %s has expired since %d seconds",
                            $_session,
                            $active_sessions[$_session]['BillingPartyId'],
                            $expired_since);
                            syslog(LOG_NOTICE, $log);
                            $expired++;
                        } else {
                            $active_sessions_new[$_session]=$active_sessions[$_session];
                        }
                    }
        
                    if ($expired) {
                        $active_sessions=$active_sessions_new;
                    }
                }

            } else {
            	$active_sessions=array();
            }

            if (!$Balance) {
                $log=sprintf ("No balance found");
                syslog(LOG_NOTICE,$log);
                $this->logRuntime();
                $ret="0"."\n"."type=prepaid";
                return $ret;

            }

            if (!preg_match("/^0/",$CDR->CanonicalURINormalized)) {
                $log=sprintf ("MaxSessionTime=unlimited Type=prepaid CallId=%s BillingParty=%s DestId=None",$NetFields['callid'],$CDR->BillingPartyId);
                syslog(LOG_NOTICE, $log);
                $this->logRuntime();
                $ret="none"."\n"."type=prepaid";
                return $ret;
            } else {
                if (!$CDR->DestinationId) {
                    $log = sprintf ("error: cannot figure out the destination id for %s",$CDR->CanonicalURI);
                    $this->logRuntime();
                    syslog(LOG_NOTICE, $log);
                    $ret=$log."\n"."type=prepaid";
                    return $ret;
                }
            }

			$session_counter=count($active_sessions);

            if ($max_sessions && $session_counter >= $max_sessions) {
                $log = sprintf ("Locked: maximum number of concurrent calls %s reached, $max_sessions allowed");
                syslog(LOG_NOTICE, $log);
                $ret="Locked"."\n"."type=prepaid";
                return $ret;
            }

            $maxduration=0;
            // Build Rate dictionary containing normalized CDR fields plus customer Balance

			if (count($active_sessions)) {
                // set  $this->remaining_balance and $this->parallel_calls for ongoing calls:
                if (!$this->getActivePrepaidSessions($active_sessions,$Balance,$CDR->BillingPartyId,array($CDR->callId))) {
                    $ret="0"."\n"."type=prepaid";
                    return $ret;
                }

                $this->runtime['get_parallel_calls']=microtime_float();

                // add current call to the list of parallel calls
                $RateDictionary=array(
                                      'duration'        => $CDR->duration,
                                      'callId'          => $CDR->callId,
                                      'Balance'         => $this->remaining_balance,
                                      'timestamp'       => $CDR->timestamp,
                                      'DestinationId'   => $CDR->DestinationId,
                                      'region'          => $CDR->region,
                                      'domain'          => $CDR->domain,
                                      'gateway'         => $CDR->gateway,
                                      'BillingPartyId'  => $CDR->BillingPartyId,
                                      'ENUMtld'         => $CDR->ENUMtld,
                                      'RatingTables'    => &$this->CDRS->RatingTables
                                      );
    
                $Rate = new Rate($this->settings, $this->db);

            	$_maxduration = round($Rate->MaxSessionTime($RateDictionary));

	            $log = sprintf ("Maximum duration for session %s of %s to destination %s having balance=%s is %s",
                $CDR->callId,
                $CDR->BillingPartyId,
                $CDR->DestinationId,
                $this->remaining_balance,
                $_maxduration);
                syslog(LOG_NOTICE, $log);

                if ($_maxduration > 0) {
                    $this->parallel_calls[$CDR->callId]=array('pricePerSecond' => $this->remaining_balance/$_maxduration);
                } else {
                    $log = sprintf ("Maxduration for session %s of %s will become negative",$CDR->callId,$CDR->BillingPartyId);
                    syslog(LOG_NOTICE, $log);
                    $ret="0"."\n"."type=prepaid";
                    return $ret;
                }

    			$this->parallel_calls[$CDR->callId]=array('pricePerSecond' => $this->remaining_balance/$_maxduration);

			    $maxduration=$this->getAggregatedMaxSessiontime($this->parallel_calls,$this->remaining_balance,$CDR->BillingPartyId);

            } else {
                $RateDictionary=array(
                                      'duration'        => $CDR->duration,
                                      'callId'          => $CDR->callId,
                                      'Balance'         => $Balance,
                                      'timestamp'       => $CDR->timestamp,
                                      'DestinationId'   => $CDR->DestinationId,
                                      'region'          => $CDR->region,
                                      'domain'          => $CDR->domain,
                                      'gateway'         => $CDR->gateway,
                                      'BillingPartyId'  => $CDR->BillingPartyId,
                                      'ENUMtld'         => $CDR->ENUMtld,
                                      'RatingTables'    => &$this->CDRS->RatingTables
                                      );
    
                $Rate = new Rate($this->settings, $this->db);
    
                $this->runtime['instantiate_rate']=microtime_float();
            	$maxduration = round($Rate->MaxSessionTime($RateDictionary));
            }

            // add new active session
			$active_sessions[$CDR->callId]= array('timestamp'       => $CDR->timestamp,
            			                          'duration'        => $CDR->duration,
                                      			  'BillingPartyId'  => $CDR->BillingPartyId,
                                                  'MaxSessionTime'  => $maxduration,
                                                  'domain'          => $CDR->domain,
                                                  'gateway'         => $CDR->gateway,
                                                  'Destination'     => $CDR->destinationPrint,
                                                  'DestinationId'   => $CDR->DestinationId,
                                                  'region'          => $CDR->region,
                                                  'connectCost'     => $Rate->connectCost
                                                  );

			if ($CDR->ENUMtld) {
            	$active_sessions[$CDR->callId]['ENUMtld']=$CDR->ENUMtld;
            }

            $this->runtime['calculate_maxduration']=microtime_float();

            if ($maxduration < 0) {
                $log = sprintf ("error: maxduration %s is negative",$maxduration);
                syslog(LOG_NOTICE, $log);
                $ret=$log."\n"."type=prepaid";
                return $ret;
            }

            if ($Rate->min_duration && $maxduration < $Rate->min_duration) {
                $log = sprintf ("Notice: maxduration of %s is less then min_duration (%s)",$maxduration,$Rate->min_duration);
                syslog(LOG_NOTICE, $log);
                $ret="0"."\n"."type=prepaid";
                return $ret;
            }

            if (!$Rate->billingTimezone) {
                $log = sprintf ("error: cannot figure out the billing timezone")."\n"."type=prepaid";
                syslog(LOG_NOTICE, $log);
                $ret=$log."\n"."type=prepaid";
                return $ret;
            }

            if (!$Rate->startTimeBilling) {
                $log = sprintf ("error: cannot figure out the billing start time")."\n"."type=prepaid";
                syslog(LOG_NOTICE, $log);
                $ret=$log."\n"."type=prepaid";
                return $ret;
            }

            $log=sprintf ("MaxSessionTime=%s Type=prepaid CallId=%s BillingParty=%s DestId=%s Balance=%s Spans=%d",
            $maxduration,
            $NetFields['callid'],
            $CDR->BillingPartyId,
            $CDR->DestinationId,
            $RateDictionary['Balance'],
            $Rate->MaxSessionTimeSpans
            );

            syslog(LOG_NOTICE, $log);

			if ($maxduration > 0) {
                $query=sprintf("update %s
                set
                active_sessions = '%s',
                session_counter  = '%s'
                where account  = '%s'",
                addslashes($this->prepaid_table),
                addslashes(json_encode($active_sessions)),
                count($active_sessions),
                addslashes($CDR->BillingPartyId));

                if (!$this->db->query($query)) {
                    $log=sprintf ("Database error for %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                    syslog(LOG_NOTICE,$log);
                    $log=sprintf ("error: database error %s (%s)",$this->db->Error,$this->db->Errno);
                    return $log;
                }
            }

            $this->runtime['update_prepaid']=microtime_float();

            $this->logRuntime();

			$ret=$maxduration."\n"."type=prepaid";
            return $ret;

        } else if ($NetFields['action'] == "debitbalance") {

            if (!$NetFields['from']) {
                $log=sprintf ("error: missing From parameter");
                syslog(LOG_NOTICE, $log);
                return $log;
            }

            if (!$NetFields['to']) {
                $log=sprintf ("error: missing To parameter");
                syslog(LOG_NOTICE, $log);
                return $log;
            }

            if (!strlen($NetFields['application']) || (strlen($NetFields['application']) && $NetFields['application'] == 'audio')) {
                if (!strlen($NetFields['duration'])) {
                    $log=sprintf ("error: missing Duration parameter");
                    syslog(LOG_NOTICE, $log);
                    return $log;
                }
            }

            if (strlen($NetFields['application'])) {
                if ($NetFields['application'] == 'audio' || $NetFields['application'] == 'sms' ) {
                	$application=$NetFields['application'];
                } else {
                    $log=sprintf ("error: unsupported application %s",$NetFields['application']);
                    syslog(LOG_NOTICE, $log);
                    return $log;
                }
            } else {
            	$application='audio';
            }

            if (!$NetFields['gateway']) {
                $log=sprintf ("error: missing gateway parameter");
                syslog(LOG_NOTICE, $log);
                return $log;
            }

            if (!$NetFields['callid']) {
                $log=sprintf ("error: missing Call Id parameter");
                syslog(LOG_NOTICE, $log);
                return $log;
            }

            if ($NetFields['force']) {
                $force=true;
            } else {
            	$force=false;
            }

            $timestamp=time();

            $CDRStructure=array (
                              $this->CDRS->CDRFields['callId']         => $NetFields['callid'],
                              $this->CDRS->CDRFields['aNumber']        => $NetFields['from'],
                              $this->CDRS->CDRFields['CanonicalURI']   => $NetFields['to'],
                              $this->CDRS->CDRFields['gateway']        => $NetFields['gateway'],
                              $this->CDRS->CDRFields['ENUMtld']        => $NetFields['enumtld'],
                              $this->CDRS->CDRFields['duration']       => floor($NetFields['duration']),
                              $this->CDRS->CDRFields['timestamp']      => time(),
                              'skip_fix_prepaid_duration'              => true
                              );


            // Init CDR
            $CDR = new $this->CDRS->CDR_class($this->CDRS, $CDRStructure);
            $CDR->normalize();

            $this->runtime['normalize_cdr']=microtime_float();

            // Build Rate dictionary containing normalized CDR fields plus customer Balance
            $RateDictionary=array(
                                  'callId'          => $NetFields['callid'],
                                  'timestamp'       => $CDR->timestamp,
                                  'duration'        => $CDR->duration,
                                  'DestinationId'   => $CDR->DestinationId,
                                  'region'          => $CDR->region,
                                  'domain'          => $CDR->domain,
                                  'gateway'         => $CDR->gateway,
                                  'BillingPartyId'  => $CDR->BillingPartyId,
                                  'ENUMtld'         => $CDR->ENUMtld,
                                  'RatingTables'    => &$this->CDRS->RatingTables
                                  );


            $Rate = new Rate($this->settings, $this->db);

            $this->runtime['instantiate_rate']=microtime_float();

            if ($application == 'audio') {
                if ($Rate->calculateAudio($RateDictionary)) {
    
                    $this->runtime['calculate_rate']=microtime_float();
        
                    $this->sessionDoesNotExist=false;
        
                    $result = $this->DebitBalanceAudio($CDR->BillingPartyId,$Rate->price,$NetFields['callid'],$CDR->duration,$force);
            
                    if ($this->sessionDoesNotExist) {
                        return "Failed";
                    }
        
                    $this->runtime['debit_balance']=microtime_float();
        
                    if ($CDR->duration) {
                        $log = sprintf ("Price=%s Duration=%s CallId=%s BillingParty=%s DestId=%s MaxSessionTime=%d",
                        $Rate->price,
                        $CDR->duration,
                        $NetFields['callid'],
                        $CDR->BillingPartyId,
                        $CDR->DestinationId,
                        $result
                        );
        
                        syslog(LOG_NOTICE, $log);
                    }
        
                    $RateReturn = "Ok";
                    $RateReturn.= sprintf("\nMaxSessionTime=%d",$result);
        
                    if (strlen($Rate->price)) {
                        $RateReturn.="\n".$Rate->price;
                        if ($Rate->rateInfo) {
                            $RateReturn.="\n".trim($Rate->rateInfo);
                        }
                    }
        
                    return $RateReturn;
                } else {
                    return "Failed\n";
                }
            } else if ($application == 'sms') {
                // return Ok, No credit, Error
                if ($Rate->calculateMessage($RateDictionary)) {

    	            if ($this->DebitBalanceMessage($CDR->BillingPartyId,$CDR->destinationPrint,$Rate->price,$NetFields['callid'])) {
    
                        $log = sprintf ("Price=%s CallId=%s BillingParty=%s DestId=%s Application=sms",
                        $Rate->price,
                        $NetFields['callid'],
                        $CDR->BillingPartyId,
                        $CDR->DestinationId
                        );
        
                        syslog(LOG_NOTICE, $log);
            
                        $RateReturn = "Ok";
            
                        if (strlen($Rate->price)) {
                            $RateReturn.="\n".$Rate->price;
                            if ($Rate->rateInfo) {
                                $RateReturn.="\n".trim($Rate->rateInfo);
                            }
                        }

                        return $RateReturn;
                    } else {
                    	return "Failed";
                    }

                } else {
                    return "Failed";
                }

            } else {
                return false;
            }


        } else if ($NetFields['action'] == "addbalance") {

            if (!$NetFields['from']) {
                $log=sprintf ("Error: Missing From parameter");
                syslog(LOG_NOTICE, $log);
                return 0;
            }

            if (!is_numeric($NetFields['value'])) {
                $log=sprintf ("Error: Missing Value parameter, it must be numeric");
                syslog(LOG_NOTICE, $log);
                return 0;
            }

            return $this->CreditBalance($NetFields['from'],$NetFields['value']);

        } else if ($NetFields['action'] == "deletebalance") {

            if (!$NetFields['from']) {
                $log=sprintf ("Error: Missing From parameter");
                syslog(LOG_NOTICE, $log);
                return 0;
            }

            return $this->DeleteBalance($NetFields['from']);

        } else if ($NetFields['action'] == "deletebalancehistory") {

            if (!$NetFields['from']) {
                $log=sprintf ("Error: Missing From parameter");
                syslog(LOG_NOTICE, $log);
                return 0;
            }

            return $this->DeleteBalanceHistory($NetFields['from']);

        } else if ($NetFields['action'] == "showprice") {

            if (!$NetFields['from']) {
                $log=sprintf ("Error: Missing From parameter");
                    syslog(LOG_NOTICE, $log);
                return 0;
            }

            if (!$NetFields['to']) {
                $log=sprintf ("Error: Missing To parameter");
                syslog(LOG_NOTICE, $log);
                return 0;
            }

            if (!strlen($NetFields['duration'])) {
                $log=sprintf ("Error: Missing Duration parameter");
                syslog(LOG_NOTICE, $log);
                return 0;
            }

            if ($NetFields['timestamp']) {
                $timestamp=$NetFields['timestamp'];
            } else {
                $timestamp=time();
            }

            if (!$NetFields['gateway']) {
                $log=sprintf ("error: missing gateway parameter");
                syslog(LOG_NOTICE, $log);
                return $log;
            }

            $CDRStructure=array (
                              $this->CDRS->CDRFields['callId']         => $NetFields['callid'],
                              $this->CDRS->CDRFields['aNumber']        => $NetFields['from'],
                              $this->CDRS->CDRFields['CanonicalURI']   => $NetFields['to'],
                              $this->CDRS->CDRFields['gateway']        => $NetFields['gateway'],
                              $this->CDRS->CDRFields['ENUMtld']        => $NetFields['enumtld'],
                              $this->CDRS->CDRFields['duration']       => floor($NetFields['duration']),
                              $this->CDRS->CDRFields['timestamp']      => time(),
                              'skip_fix_prepaid_duration'              => true
                              );

            $CDR = new $this->CDRS->CDR_class(&$this->CDRS, &$CDRStructure);
            $CDR->normalize();

            $Rate    = new Rate($this->settings, $this->db);

            $RateDictionary=array(
                                  'callId'          => $CDR->callId,
                                  'timestamp'       => $CDR->timestamp,
                                  'duration'        => $CDR->duration,
                                  'DestinationId'   => $CDR->DestinationId,
                                  'region'          => $CDR->region,
                                  'domain'          => $CDR->domain,
                                  'gateway'         => $CDR->gateway,
                                  'BillingPartyId'  => $CDR->BillingPartyId,
                                  'ENUMtld'         => $CDR->ENUMtld,
                                  'RatingTables'    => &$this->CDRS->RatingTables
                                  );

            $Rate->calculateAudio($RateDictionary);

            $this->runtime['calculate_rate']=microtime_float();

            if (strlen($Rate->price)) {
                $RateReturn=$Rate->price;
                if ($Rate->rateInfo) {
                    $RateReturn.="\n".trim($Rate->rateInfo);
                }
            } else {
                $RateReturn="0";
            }

            return $RateReturn;

        } else if ($NetFields['action'] == "getbalance") {
            if (!$NetFields['from']) {
                $log=sprintf ("Error: Missing From parameter");
                syslog(LOG_NOTICE, $log);
                return 0;
            }

            $query=sprintf("select * from %s where account = '%s'",
            addslashes($this->prepaid_table),
            addslashes($NetFields['from'])
            );
    
            if (!$this->db->query($query)) {
                $log=sprintf ("Database error for %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
                syslog(LOG_NOTICE,$log);
                $this->logRuntime();
                return 0;
            }
    
            if ($this->db->num_rows()) {
                $this->db->next_record();
                return number_format($this->db->f('balance'),4,".","");
            } else {
                return sprintf("%0.4f",0);
            }

        } else if ($NetFields['action'] == "getbalancehistory") {
            if (!$NetFields['from']) {
                $log=sprintf ("Error: Missing From parameter");
                syslog(LOG_NOTICE, $log);
                return 0;
            }

            $history=$this->getBalanceHistory($NetFields['from']);
            return trim($history);
        } else if ($NetFields['action'] == "getentityprofiles") {
            if (!$NetFields['entity']) {
                $log=sprintf ("Error: Missing Entity parameter");
                    syslog(LOG_NOTICE, $log);
                return 0;
            }

            $entity=$this->GetEntityProfiles($NetFields['entity']);
            return trim($entity);

        } else if ($NetFields['action'] == "showprofiles") {
            return trim($this->CDRS->RatingTables->showProfiles());
        } else if ($NetFields['action'] == "showenumtlds") {
            return trim($this->CDRS->RatingTables->showENUMtlds());
        } else if ($NetFields['action'] == "version") {
            $version_file=$this->CDRS->CDRTool['Path']."/version";
            $version="CDRTool version ".trim(file_get_contents($version_file));
            return $version;

        } else if ($NetFields['action'] == "help") {
            return $this->showHelp();
        } else if ($NetFields['action'] == "reloadratingtables") {
            return $this->reloadRatingTables();
        } else if ($NetFields['action'] == "keepalive") {
            return $this->keepAlive();
        } else if ($NetFields['action'] == "reloadquota") {
            if (!$NetFields['account']) {
                $log=sprintf ("Error: Missing Account parameter");
                syslog(LOG_NOTICE, $log);
                return 0;
            }

            return $this->reloadQuota($NetFields['account']);
        } else if ($NetFields['action'] == "reloaddomains") {
            return $this->CDRS->LoadDomains();
        } else if ($NetFields['action'] == "reloadcustomers") {
            if ($NetFields['customer'] && $NetFields['type']) {
                $_customerFilter=array('customer'=>$NetFields['customer'],
                                      'type'=>$NetFields['type']);
            }
            return $this->reloadCustomers($_customerFilter);

        } else {
            $log=sprintf ("Error: Invalid request");
                syslog(LOG_NOTICE, $log);
            return 0;
        }
    }

    function getQuota($account) {
        if (!$account) return;

        list($username,$domain) = explode("@",$account);

        if ($this->enableThor) {
            $query=sprintf("select * from sip_accounts where username = '%s' and domain = '%s'",$username,$domain);
            if (!$this->AccountsDB->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->AccountsDB->Error,$this->AccountsDB->Errno);
                syslog(LOG_NOTICE,$log);
                return 0;
            }

            if ($this->AccountsDB->num_rows()) {
            	$this->AccountsDB->next_record();
            	$_profile=json_decode(trim($this->AccountsDB->f('profile')));
                return $_profile->quota;

            } else {
                return 0;
            }
        } else {
            $query=sprintf("select quota from subscriber where username = '%s' and domain = '%s'",$username,$domain);

            if (!$this->AccountsDB->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->AccountsDB->Error,$this->AccountsDB->Errno);
                syslog(LOG_NOTICE,$log);
                return 0;
            }

            if ($this->AccountsDB->num_rows()) {
            	$this->AccountsDB->next_record();
                return $this->AccountsDB->f('quota');
            } else {
                return 0;
            }
        }
    }

    function getBlockedByQuotaStatus($account) {

        if (!$account) return 0;
        list($username,$domain) = explode("@",$account);

        if ($this->enableThor) {
            $query=sprintf("select * from sip_accounts where username = '%s' and domain = '%s'",$username,$domain);
            if (!$this->AccountsDB->query($query)) {

                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->AccountsDB->Error,$this->AccountsDB->Errno);
                syslog(LOG_NOTICE,$log);
                return 0;
            }

            if ($this->AccountsDB->num_rows()) {
            	$this->AccountsDB->next_record();

            	$_profile=json_decode(trim($this->AccountsDB->f('profile')));
                if (in_array('quota',$_profile->groups)) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            $query=sprintf("select CONCAT(username,'@',domain) as account from grp where grp = 'quota' and username = '%s' and domain = '%s'",$username,$domain);
    
            if (!$this->AccountsDB->query($query)) {
                $log=sprintf ("Database error for query %s: %s (%s)",$query,$this->AccountsDB->Error,$this->AccountsDB->Errno);
                syslog(LOG_NOTICE,$log);
                return 0;
            }

            if ($this->AccountsDB->num_rows()) {
                return 1;
            } else {
                return 0;
            }
        }

        return 0;
    }

    function getActivePrepaidSessions($active_sessions,$Balance,$BillingPartyId,$exceptSessions=array()) {
        $this->parallel_calls=array();
        $this->remaining_balance=$Balance;

        $ongoing_rates=array();

        foreach (array_keys($active_sessions) as $_session) {
			if (in_array($_session,$exceptSessions)) {
                /*
                $log = sprintf ("Ongoing prepaid session %s for %s updated",
                $_session,
                $BillingPartyId
                );
                syslog(LOG_NOTICE, $log);
            	*/
                continue;
            }

            $Rate_session = new Rate($this->settings, $this->db);
    
            $passed_time=time()-$active_sessions[$_session]['timestamp'];
    
            $active_sessions[$_session]['passed_time']=$passed_time;
    
            $RateDictionary_session=array(
                                  'duration'        => $passed_time,
                                  'callId'          => $_session,
                                  'timestamp'       => $active_sessions[$_session]['timestamp'],
                                  'DestinationId'   => $active_sessions[$_session]['DestinationId'],
                                  'region'          => $active_sessions[$_session]['region'],
                                  'domain'          => $active_sessions[$_session]['domain'],
                                  'BillingPartyId'  => $active_sessions[$_session]['BillingPartyId'],
                                  'ENUMtld'         => $active_sessions[$_session]['ENUMtld'],
                                  'RatingTables'    => &$this->CDRS->RatingTables
                                  );
    
            $Rate_session->calculateAudio($RateDictionary_session);
    
            $log = sprintf ("Ongoing prepaid session %s for %s to %s: duration=%s, price=%s ",
            $_session,
            $BillingPartyId,
            $active_sessions[$_session]['Destination'],
            $passed_time,
            $Rate_session->price
            );
            syslog(LOG_NOTICE, $log);
    
            $ongoing_rates[$_session] = array(
                                               'duration'    => $passed_time,
                                               'price'       => $Rate_session->price
                                              );
        }
    
        if (count($ongoing_rates)) {
            // calculate de virtual balance of the user at this moment in time
            $due_balance=0;
            foreach (array_keys($ongoing_rates) as $_o) {
                $due_balance = $due_balance+$ongoing_rates[$_o]['price'];
            }
    
            $this->remaining_balance = $this->remaining_balance-$due_balance;
    
            $log = sprintf ("Balance for %s having %d ongoing sessions: database=%s, due=%s, real=%s",
            $BillingPartyId,count($ongoing_rates),sprintf("%0.4f",$Balance),sprintf("%0.4f",$due_balance),sprintf("%0.4f",$this->remaining_balance));
            syslog(LOG_NOTICE, $log);
        }
    
        foreach (array_keys($active_sessions) as $_session) {
			if (in_array($_session,$exceptSessions)) {
                continue;
            }

            $RateDictionary_session=array(
                                  'callId'          => $_session,
                                  'timestamp'       => time(),
                                  'Balance'         => $this->remaining_balance,
                                  'DestinationId'   => $active_sessions[$_session]['DestinationId'],
                                  'region'          => $active_sessions[$_session]['region'],
                                  'domain'          => $active_sessions[$_session]['domain'],
                                  'BillingPartyId'  => $active_sessions[$_session]['BillingPartyId'],
                                  'ENUMtld'         => $active_sessions[$_session]['ENUMtld'],
                                  'RatingTables'    => &$this->CDRS->RatingTables,
                                  'skipConnectCost' => true
                                  );
    
            if ($active_sessions[$_session]['duration']) {
                $RateDictionary_session['duration'] = $active_sessions[$_session]['duration']-$active_sessions[$_session]['passed_time'];
            }
    
            $Rate = new Rate($this->settings, $this->db);
            $_maxduration = round($Rate->MaxSessionTime($RateDictionary_session));
    
            $log = sprintf("Maximum duration for session %s of %s to destination %s having balance=%s is %s",
            $_session,
            $BillingPartyId,
            $active_sessions[$_session]['DestinationId'],
            $this->remaining_balance,
            $_maxduration);
            syslog(LOG_NOTICE, $log);
    
            if ($_maxduration > 0) {
                $this->parallel_calls[$_session]=array('pricePerSecond' => $this->remaining_balance/$_maxduration);
            } else {
                /*
                $log = sprintf ("Maxduration for session %s of %s will be negative",$_session,$active_sessions[$_session]['BillingPartyId']);
                syslog(LOG_NOTICE, $log);
                */
                return 0;
            }
        }

        return 1;
    }

    function getAggregatedMaxSessiontime($parallel_calls=array(),$balance,$BillingPartyId) {
    	$maxduration=0;
        $sum_price_per_second=0;

        foreach (array_keys($parallel_calls) as $_call) {
            $sum_price_per_second=$sum_price_per_second+$parallel_calls[$_call]['pricePerSecond'];
        }
    
        if ($sum_price_per_second >0 ) {
            $maxduration=intval($balance/$sum_price_per_second);

            if (count($parallel_calls) > 1) {
            	$log = sprintf ("Maximum duration agregated for %s is (Balance=%s)/(Sum of price per second for each destination=%s)=%s s",
            	$BillingPartyId,$balance,sprintf("%0.4f",$sum_price_per_second),$maxduration);
            	syslog(LOG_NOTICE, $log);
            }
    
        } else {
            /*
            $log = sprintf ("Error: sum_price_per_second for %s is negative",$BillingPartyId);
            syslog(LOG_NOTICE, $log);
            */
            $maxduration = 0;
        }

        return round($maxduration);
    }

    function keepAlive() {
        $query=sprintf("select * from auth_user");
    
        if (!$this->db->query($query) || !$this->db->num_rows()) {
            $log=sprintf ("Database error for keepalive query %s: %s (%s)",$query,$this->db->Error,$this->db->Errno);
            syslog(LOG_NOTICE, $log);
            return false;
        }

        $log=sprintf("Keepalive successful");
        syslog(LOG_NOTICE, $log);
        return true;
    }
}

function reloadRatingEngineTables () {
    global $RatingEngine;
    if (strlen($RatingEngine['socketIP']) && $RatingEngine['socketPort']) {

		if ($RatingEngine['socketIP']=='0.0.0.0' || $RatingEngine['socketIP'] == '0') {
        	$RatingEngine['socketIPforClients']= '127.0.0.1';
        } else {
        	$RatingEngine['socketIPforClients']=$RatingEngine['socketIP'];
        }

        if ($fp = fsockopen ($RatingEngine['socketIPforClients'], $RatingEngine['socketPort'], $errno, $errstr, 2)) {
        	fputs($fp, "ReloadRatingTables\n");
	        fclose($fp);
    	    return true;
        }
    }
    return false;
}

function keepAliveRatingEngine() {
    global $RatingEngine;
    if (strlen($RatingEngine['socketIP']) && $RatingEngine['socketPort']) {

		if ($RatingEngine['socketIP']=='0.0.0.0' || $RatingEngine['socketIP'] == '0') {
        	$RatingEngine['socketIPforClients']= '127.0.0.1';
        } else {
        	$RatingEngine['socketIPforClients']=$RatingEngine['socketIP'];
        }

        if ($fp = fsockopen ($RatingEngine['socketIPforClients'], $RatingEngine['socketPort'], $errno, $errstr, 2)) {
        	fputs($fp, "KeepAlive\n");
	        fclose($fp);
    	    return true;
        }
    }
    return false;
}

function testRatingTables () {
    global $RatingEngine;
    if (!strlen($RatingEngine['socketIP']) || !$RatingEngine['socketPort']) {
        return false;
    }

    if ($RatingEngine['socketIP']=='0.0.0.0' || $RatingEngine['socketIP'] == '0') {
        $RatingEngine['socketIPforClients']= '127.0.0.1';
    } else {
        $RatingEngine['socketIPforClients']=$RatingEngine['socketIP'];
    }

    $i=0;
    $b=time();

    while ($i < 1000) {
        if (!$fp = fsockopen ($RatingEngine['socketIPforClients'], $RatingEngine['socketPort'], $errno, $errstr, 2)) {
            print "Error connecting to rating engine\n";
            break;
        }

        $i++;
        $number='00'.RandomNumber(1,true).RandomNumber(12).'@example.com';
        $duration=RandomNumber(3,true);
        $command=sprintf("ShowPrice From=sip:123@example.com To=sip:%s Gateway=10.0.0.1 Duration=%d\n",$number,$duration);
        fputs($fp, $command,strlen($command));
        $response = fgets($fp, 8192);
    	fclose($fp);
    }

    $e=time();
    $d=$e-$b;
    if ($d) printf("Commands=%d, Time=%s seconds, Speed=%s cps\n",$i,$d,number_format($i/$d,1));

}

?>
