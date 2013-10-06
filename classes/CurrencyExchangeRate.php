<?php
/** 
 * This class describes a Currency Exchange between two currencies.
 *
 * @property string $currencyCode1 Currency Code for the 1st currency in the pair
 * @property string $currencyCode2 Currency Code for the 2nd currency in the pair
 * @property decimal $rate Exchange rate 1 currency1 = $rate currency2
 * @property DateTime $date Exchange Rate's date and time
 *
 * @package CurrencyCalculator
 * @author  David de la Calle Cerezo <david.dlc.cerezo@gmail.com>
 * @version 1.0.0 2013-10-05
 */
class CurrencyExchangeRate
{
    protected $currencyCode1;
    protected $currencyCode2;
    protected $rate;
    protected $date;

    /**
     * Class Constructor
     *
     * @param string   $currencyCode1 Currency Code for the 1st currency in the pair
     * @param string   $currencyCode2 Currency Code for the 2nd currency in the pair
     * @param float    $rate          Exchange rate 1 currency1 = $rate currency2
     * @param DateTime $date          Exchange Rate's date and time
     */
    public function __construct($currencyCode1 = null, $currencyCode2 = null, $rate = 0, $date = null)
    {
        $this->__set("currencyCode1", $currencyCode1);
        $this->__set("currencyCode2", $currencyCode2); 
        $this->__set("rate", $rate);
        $this->__set("date", $date);
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
        if ($property == "currencyCode1" || $property == "currencyCode2") {
            try{
                 $this->$property = Currency::validateCode($value);
            }
            catch(Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
        } elseif ($property == "rate") {
            $this->$property = filter_var($value, FILTER_VALIDATE_FLOAT);
        } elseif ($property == "date") {
            try{
                 $this->$property = self::validateDate($value);
            }
            catch(Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
        }
    }

    /**
     * Validates a date
     *
     * @param mixed $date Input date
     *
     * @return string Valid date
     * @throws Exception When $date is not valid
     */
    public static function validateDate($date)
    {
        if ( !($date === null || $date instanceof DateTime) )
            throw new Exception('Invalid date.');

        return $date;
    }
}