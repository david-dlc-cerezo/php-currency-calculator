<?php
/** 
 * This clasee manage a colection of Currency Exchanges Rates
 *
 * @property CurrencyExchangeRate[] $currencies Array with the saved currency exchages
 *
 * @package CurrencyCalculator
 * @author  David de la Calle Cerezo <david.dlc.cerezo@gmail.com>
 * @version 1.0.0 2013-10-05
 */
class CurrencyManager
{
    protected $currencies = array();

    /**
     * Class Constructor
     */
    public function __construct()
    {
        //Initialice default Currency Exchange Rates Collection
        $this->currencies = array();
        $this->currencies[] = new Currency("EUR", "Euro");
        $this->currencies[] = new Currency("CHF", "Swiss Franc");
        $this->currencies[] = new Currency("GBP", "British Pound");
        $this->currencies[] = new Currency("USD", "US dollar");
        $this->currencies[] = new Currency("JPY");
        $this->currencies[] = new Currency("CAD");
        array_multisort($this->currencies);
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
     * Add a new currency to the currency collection
     *
     * @param string $code Currency Code for de new currency
     * @param string $name Currency Name for de new currency
     *
     * @return void
     */
    public function add($code, $name = null)
    {
        try{
            $code = Currency::validateCode($code);
        }
        catch(Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        if ($code) {
            $updated = false;
            foreach ($this->currencies as &$currency) {
                if ($currency->code == $code) {
                    $currency->name = $name;
                    $updated = true;
                    break;
                }
            }

            if (!$updated) {
                $this->currencies[] = new Currency($code, $name);
                array_multisort($this->currencies);
            }
        }
    }

    /**
     * Prints options tags for all the elements in the currencies array
     *
     * @return void
     */
    public function printOptions()
    {
        foreach ($this->currencies as $currency) {
            $value = $currency->code;
            $description = $currency->code.(($currency->name) ? " - ".$currency->name : "");
            echo "\n<option value='$value'>$description</option>";
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