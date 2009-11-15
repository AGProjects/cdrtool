<?php
/**
 * This is one of two base Type files that are not automatically
 * generated from the WSDL.
 *
 * @package PayPal
 */

/**
 * Base Type classs that allows for conversion of types into
 * SOAP_Value objects.
 *
 * @package PayPal
 */
class XSDType
{
    /**
     * Information about all of the member variables of this type.
     *
     * @access protected
     *
     * @var array $_elements
     */
    var $_elements = array();

    /**
     * Information about all of the attributes of this type.
     *
     * @access protected
     *
     * @var array $_attributes
     */
    var $_attributes = array();

    /**
     * Actual values of any attributes for this type.
     *
     * @access protected
     *
     * @var array $_attributeValues
     */
    var $_attributeValues = array();

    /**
     * What namespace is this type in?
     *
     * @access protected
     *
     * @var string $_namespace
     */
    var $_namespace;

    /**
     * Constructor. Base class constructor is empty.
     */
    function XSDType()
    {
    }

    /**
     * Turn this type into a SOAP_Value object useable with
     * SOAP_Client.
     *
     * @param string $name  The name to use for the value.
     * @param string $ns    The namespace of the parent value.
     *
     * @return SOAP_Value  A SOAP_Value object representing this type instance.
     */
    function &getSoapValue($name, $ns = null)
    {
        include_once 'PayPal/SOAP/Value.php';

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
                if (is_string($value) && $element['charset'] == 'iso-8859-1' &&
                    (utf8_encode(utf8_decode($value)) != $value)) {
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
        return $v;
    }

    /**
     * Set the value of an attribute on this object.
     */
    function setattr($attribute, $value)
    {
        $this->_attributeValues[$attribute] = $value;
    }

    /**
     * Get the value of an attribute on this object.
     */
    function getattr($attribute)
    {
        return isset($this->_attributeValues[$attribute]) ?
            $this->_attributeValues[$attribute] :
            null;
    }

    /**
     * Callback for SOAP_Base::_decode() to set attributes during
     * response decoding.
     */
    function __set_attribute($key, $value)
    {
        return $this->setattr($key, $value);
    }

}
