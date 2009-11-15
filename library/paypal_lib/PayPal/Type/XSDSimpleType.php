<?php
/**
 * This is one of two base Type files that are not automatically
 * generated from the WSDL.
 *
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDType.php';

/**
 * Base Type classs that allows for conversion of types into
 * SOAP_Value objects.
 *
 * @package PayPal
 */
class XSDSimpleType extends XSDType
{
    /**
     * The simple value of this type.
     *
     * @access protected
     *
     * @var mixed $_value
     */
    var $_value;

    /**
     * The charset of this type's value.
     *
     * @access protected
     *
     * @var string $_charset
     */
    var $_charset = 'iso-8859-1';

    /**
     * Constructor.
     */
    function XSDSimpleType($value = null, $attributes = array())
    {
        $this->_value = $value;
        $this->_attributeValues = $attributes;
    }

    /**
     * Turn this type into a SOAP_Value object useable with
     * SOAP_Client.
     *
     * @param string $name  The name to use for the value.
     *
     * @return SOAP_Value  A SOAP_Value object representing this type instance.
     */
    function &getSoapValue($name, $ns = null)
	    {
	        include_once 'PayPal/SOAP/Value.php';

	        if (isset($this->_value)) {
				// Treat as a XSDSimpleType with only the _value
				$value = $this->_value;
				if (is_string($value) && $this->_charset = 'iso-8859-1' && (utf8_encode(utf8_decode($value)) != $value)) {
					$value = utf8_encode($value);
        		}

        		if (count($this->_attributeValues)) {
					$v =& new SOAP_Value($name, '', $value, $this->_attributeValues);
        		} else {
        			$v =& new SOAP_Value($name, '', $value);
        		}
			} else {
				// Treat as a base XSDType
				$elements = array();
				foreach ($this->_elements as $ename => $element) {
					$value = $this->$ename;

					// Values that are null and not required can be omitted
					// from the serialized XML (and thus the SOAP_Value
					// object) entirely.
					if (is_null($value) && !$element['required']) {
						continue;
					}

					// Append namespace prefixes if this element came from a
					// namespace different from the base type's namespace.
					if (!empty($element['namespace']) && $element['namespace'] != $this->_namespace) {
						$ename = '{' . $element['namespace'] . '}' . $ename;
					}

					if (is_a($value, 'XSDType')) {
						$elements[] =& $value->getSoapValue($ename, $this->_namespace);
					} else {
						if (is_string($value) && $element['charset'] == 'iso-8859-1' && (utf8_encode(utf8_decode($value)) != $value)) {
							$value = utf8_encode($value);
						}
						$elements[] =& new SOAP_Value($ename, $element['type'], $value);
					}
				}

				if (count($elements) == 1) {
					$elements = array_shift($elements);
				}

				if (!is_null($ns) && $ns != $this->_namespace) {
					$this->_attributeValues['xmlns'] = $this->_namespace;
				}

				if (count($this->_attributeValues)) {
					$v =& new SOAP_Value($name, '', $elements, $this->_attributeValues);
				} else {
					$v =& new SOAP_Value($name, '', $elements);
				}
			}

			return $v;
    }

    /**
     * Set the value of this simple object.
     */
    function setval($value, $charset = 'iso-8859-1')
    {
        $this->_value = $value;
        $this->_charset = $charset;
    }

}