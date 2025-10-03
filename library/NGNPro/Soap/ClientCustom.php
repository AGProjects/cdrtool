<?php

class SOAP_Client_Custom extends SOAP_Client
{
    public function __construct($path = 'https://mdns.sipthor.net/ngnpro/')
    {
        parent::__construct($path, false);
    }

    function _serializeValue(&$value, $name = '', $type = false, $elNamespace = null, $typeNamespace = null, $options = array(), $attributes = array(), $artype = '')
    {
        $namespaces = array();
        $arrayType = $array_depth = $xmlout_value = null;
        $typePrefix = $elPrefix = $xmlout_offset = $xmlout_arrayType = $xmlout_type = $xmlns = '';
        $ptype = $array_type_ns = '';

        if (!$name || is_numeric($name)) {
            $name = 'item';
        }

        if ($this->_wsdl)
            list($ptype, $arrayType, $array_type_ns, $array_depth)
                    = $this->_wsdl->getSchemaType($type, $name, $typeNamespace);

        if (!$arrayType) $arrayType = $artype;
        if (!$ptype) $ptype = $this->_getType($value);
        if (!$type) $type = $ptype;

        if (strcasecmp($ptype, 'Struct') == 0 || strcasecmp($type, 'Struct') == 0) {
            // struct
            $vars = null;
            if (is_object($value)) {
                $vars = get_object_vars($value);
            } else {
                $vars = &$value;
            }
            if (is_array($vars)) {
                foreach (array_keys($vars) as $k) {
                    if ($k[0]=='_') continue; // hide private vars
                    if (is_object($vars[$k])) {
                        if (is_a($vars[$k], 'soap_value')) {
                            $xmlout_value .= $vars[$k]->serialize($this);
                        } else {
                            // XXX get the members and serialize them instead
                            // converting to an array is more overhead than we
                            // should realy do, but php-soap is on it's way.
                            $xmlout_value .= $this->_serializeValue(
                                get_object_vars($vars[$k]),
                                $k,
                                false,
                                $this->_section5 ? null : $elNamespace
                            );
                        }
                    } else {
                        $xmlout_value .= $this->_serializeValue(
                            $vars[$k],
                            $k,
                            false,
                            $this->_section5 ? null : $elNamespace
                        );
                    }
                }
            }
        } elseif (strcasecmp($ptype, 'Array') == 0 || strcasecmp($type, 'Array') == 0) {
            // array
            $typeNamespace = SOAP_SCHEMA_ENCODING;
            $orig_type = $type;
            $type = 'Array';
            $numtypes = 0;
            // XXX this will be slow on larger array's.  Basicly, it flattens array's to allow us
            // to serialize multi-dimensional array's.  We only do this if arrayType is set,
            // which will typicaly only happen if we are using WSDL
            if (isset($options['flatten']) || ($arrayType && (strchr($arrayType,',') || strstr($arrayType,'][')))) {
                $numtypes = $this->_multiArrayType($value, $arrayType, $ar_size, $xmlout_value);
            }

            $array_type = $array_type_prefix = '';
            if ($numtypes != 1) {
                $arrayTypeQName =new QName($arrayType);
                $arrayType = $arrayTypeQName->name;
                $array_types = array();
                $array_val = NULL;

                // serialize each array element
                $ar_size = count($value);
                foreach ($value as $array_val) {
                    if ($this->_isSoapValue($array_val)) {
                        $array_type = $array_val->type;
                        $array_types[$array_type] = 1;
                        $array_type_ns = $array_val->type_namespace;
                        $xmlout_value .= $array_val->serialize($this);
                    } else {
                        $array_type = $this->_getType($array_val);
                        $array_types[$array_type] = 1;
                        $xmlout_value .= $this->_serializeValue(
                            $array_val,
                            'item',
                            $array_type,
                            $this->_section5 ? null : $elNamespace
                        );
                    }
                }

                $xmlout_offset = " SOAP-ENC:offset=\"[0]\"";
                if (!$arrayType) {
                    $numtypes = count($array_types);
                    if ($numtypes == 1) $arrayType = $array_type;
                    // using anyType is more interoperable
                    if ($array_type == 'Struct') {
                        $array_type = '';
                    } elseif ($array_type == 'Array') {
                        $arrayType = 'anyType';
                        $array_type_prefix = 'xsd';
                    } elseif (!$arrayType) {
                        $arrayType = $array_type;
                    }
                }
            }
            if (!$arrayType || $numtypes > 1) {
                $arrayType = 'xsd:anyType'; // should reference what schema we're using
            } else {
                if ($array_type_ns) {
                    $array_type_prefix = $this->_getNamespacePrefix($array_type_ns);
                } elseif (array_key_exists($arrayType, $this->_typemap[$this->_XMLSchemaVersion])) {
                    $array_type_prefix = $this->_namespaces[$this->_XMLSchemaVersion];
                }
                if ($array_type_prefix) {
                    $arrayType = $array_type_prefix.':'.$arrayType;
                }
            }

            $xmlout_arrayType = " SOAP-ENC:arrayType=\"" . $arrayType;
            if ($array_depth != null) {
                for ($i = 0; $i < $array_depth; $i++) {
                    $xmlout_arrayType .= '[]';
                }
            }
            $xmlout_arrayType .= "[$ar_size]\"";
        } elseif ($this->_isSoapValue($value)) {
            $xmlout_value =& $value->serialize($this);
        } elseif ($type == 'string') {
            $xmlout_value = htmlspecialchars($value);
        } elseif ($type == 'rawstring') {
            $xmlout_value =& $value;
        } elseif ($type == 'boolean') {
            $xmlout_value = $value?'true':'false';
        } else {
            $xmlout_value =& $value;
        }

        // add namespaces
        if ($elNamespace) {
            $elPrefix = $this->_getNamespacePrefix($elNamespace);
            $xmlout_name = "$elPrefix:$name";
        } else {
            $xmlout_name = $name;
        }

        if ($typeNamespace) {
            $typePrefix = $this->_getNamespacePrefix($typeNamespace);
            $xmlout_type = "$typePrefix:$type";
        } elseif ($type && array_key_exists($type, $this->_typemap[$this->_XMLSchemaVersion])) {
            $typePrefix = $this->_namespaces[$this->_XMLSchemaVersion];
            $xmlout_type = "$typePrefix:$type";
        }

        // handle additional attributes
        $xml_attr = '';
        if (is_array($attributes) ? count($attributes) : 0 > 0) {
            foreach ($attributes as $k => $v) {
                $kqn =new QName($k);
                $vqn =new QName($v);
                $xml_attr .= ' '.$kqn->fqn().'="'.$vqn->fqn().'"';
            }
        }

        // store the attachement for mime encoding
        if (isset($options['attachment']))
            $this->__attachments[] = $options['attachment'];

        if ($this->_section5) {
            if ($xmlout_type) $xmlout_type = " xsi:type=\"$xmlout_type\"";
            if (is_null($xmlout_value)) {
                $xml = "\r\n<$xmlout_name$xmlout_type$xmlns$xmlout_arrayType$xml_attr/>";
            } else {
                $xml = "\r\n<$xmlout_name$xmlout_type$xmlns$xmlout_arrayType$xmlout_offset$xml_attr>".
                    $xmlout_value."</$xmlout_name>";
            }
        } else {
            if (is_null($xmlout_value)) {
                $xml = "\r\n<$xmlout_name$xmlns$xml_attr/>";
            } else {
                $xml = "\r\n<$xmlout_name$xmlns$xml_attr>".
                    $xmlout_value."</$xmlout_name>";
            }
        }
        return $xml;
    }

    function addHeader($soap_value)
    {
        // add a new header to the SOAP message if not already exists

        if (is_array($soap_value) && is_array($this->headersOut)) {
            foreach ($this->headersOut as $_header) {
                if ($_header->name == $soap_value[0]) {
                    return true;
                }
            }
        }

        if (is_a($soap_value, 'soap_header')) {
            $this->headersOut[] =& $soap_value;
        } elseif (gettype($soap_value) == 'array') {
            // name, value, namespace, mustunderstand, actor
            $this->headersOut[] = new SOAP_Header($soap_value[0], null, $soap_value[1], $soap_value[2], $soap_value[3]);
        } else {
            $this->_raiseSoapFault("Don't understand the header info you provided.  Must be array or SOAP_Header.");
        }
    }
}
