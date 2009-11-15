<?
require_once "PayPal/SOAP/Value.php";
require_once "PayPal/Type/XSDType.php";

/**
 * PayPal SDK class for handling items that can occur multiple times, e.g.
 * PaymentDetailsItem in the DoExpressCheckout call.
 */
class MultiOccurs extends XSDType
{
    /**
     * Constructor. Pass the node and element name you will be using this in.
     * For example:
     *
     * $PaymentDetailsItem=&new MultiOccurs($PaymentDetails,
     *                                         'PaymentDetailsItem');
     * $PaymentDetailsItem->setKids($items);
     * $PaymentDetails->setPaymentDetailsItem($PaymentDetailsItem);
     *
     * @param XSDType $xsd      The XSDType object this will be used in.
     * @param string  $element  The element in $xsd this will be used for.
     */
    function MultiOccurs($xsd, $item)
    {
        if (!isset($xsd->_elements[$item])) return null;
        $this->_type = $xsd->_elements[$item]['type'];
        $this->_namespace = $xsd->_elements[$item]['namespace'];
        $this->kids = array();
    }

    /**
     * Add an instance of the multiply-occuring item.
     *
     * @param XSDType $child    Instance of the multiply-occurring XSDType
     *                          object.
     */
    function addChild($child)
    {
        $this->kids[] = $child;
    }

    /**
     * Set the multiply-occurring XSD nodes as an array.
     *
     * @param array $children   Array of XSDType objects.
     */
    function setChildren($children)
    {
        $this->kids = $children;
    }

    /**
     * Get the multiply-occurring XSD nodes as an array.
     *
     * @return array            Array of XSDType objects.
     */
    function getChildren()
    {
        return $this->kids;
    }

    function &getSoapValue($name, $ns=null)
    {
        $elements = array();
        foreach ($this->kids as $value)
        {
            if (is_a($value, 'XSDType'))
            {
                $elements[] =& $value->getSoapValue($name, $ns);
            }
            else
            {
                if (is_string($value) && $element['charset'] == 'iso-8859-1' &&
                    (utf8_encode(utf8_decode($value)) != $value))
                {
                    $value = utf8_encode($value);
                }
                $elements[] =& new SOAP_Value($name, $this->_type, $value);
            }
        }

        if(count($elements) == 1)
        {
            return array_shift($elements);
        }

        return new SOAP_Multi($elements);
    }
}

/**
 * SOAP_Value that will output as a list of multiply-occurring items as
 * siblings.
 */
class SOAP_Multi extends SOAP_Value
{
    /**
     * Constructor. Pass an array of SOAP_Values which will be output as
     * siblings.
     *
     * @param array $kids   SOAP_Values.
     */
    function SOAP_Multi($kids=array())
    {
        $this->kids = $kids;
    }

    function serialize(&$serializer)
    {
        $value = '';
        foreach ($this->kids as $k)
        {
            $value .= $k->serialize($serializer);
        }
        return $value;
    }
}
?>