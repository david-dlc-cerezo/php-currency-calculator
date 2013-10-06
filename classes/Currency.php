<?php
/** 
 * Currency Class describes a Currency
 *
 * @property string $code Currency Code
 * @property string $name Currency Name
 *
 * @package CurrencyCalculator
 * @author  David de la Calle Cerezo <david.dlc.cerezo@gmail.com>
 * @version 1.0.0 2013-10-05
 */
class Currency
{
    protected $code;
    protected $name;

    /**
     * Class Constructor
     *
     * @param string $code [Optional] Currency code
     * @param string $name [Optional] Currency name
     */
    public function __construct($code = null, $name = null)
    {
        if ($code)
            $this->__set("code", $code);
        $this->__set("name", $name);
    }

    /**
     * Getter: Magic method
     *
     * @param string $property Name of the property to get.
     *
     * @return mixed Property content
     */
    public function __get($property) 
    {
        if (property_exists($this, $property))
            return $this->$property;
    }
    
    /**
     * Setter: Magic method
     *
     * @param string $property Name of the property to set.
     * @param mixed  $value    Value to set in the property
     *
     * @return null
     */
    public function __set($property, $value) 
    {
        if ($property == "code") {
            try{
                 $this->code = self::validateCode($value);
            }
            catch(Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
        }
        elseif (property_exists($this, $property))
            $this->$property = $value;
    }

    /**
     * Curency code must be 3 character long and uppercase.
     * This function ensure this conditions and returns a valid code
     *
     * @param string $code Input code
     *
     * @return string Valid code based on the input one
     * @throws Exception When $code is too short
     */
    public static function validateCode($code)
    {
        if (strlen($code) < 3)
            throw new Exception('Currency code too short (must have 3 characters)');

        return substr(strtoupper($code), 0, 3);
    }
}