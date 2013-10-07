<?php
/** 
 * This clasee manage a colection of Currency Exchanges Rates
 *
 * @property CurrencyExchangeRate[] $currencyExchangeRates Array with the saved currency exchages
 *
 * @package CurrencyCalculator
 * @author  David de la Calle Cerezo <david.dlc.cerezo@gmail.com>
 * @version 1.0.0 2013-10-05
 */
class CurrencyExchangeManager
{
    protected $currencyExchangeRates = array();

    /**
     * Class Constructor
     */
    public function __construct()
    {
        //Initialice default Currency Exchange Rates Collection
        $this->currencyExchangeRates = array();
        $this->currencyExchangeRates[] = new CurrencyExchangeRate("EUR", "USD", 1.3764);
        $this->currencyExchangeRates[] = new CurrencyExchangeRate("EUR", "CHF", 1.2079);
        $this->currencyExchangeRates[] = new CurrencyExchangeRate("EUR", "GBP", 0.8731);
        $this->currencyExchangeRates[] = new CurrencyExchangeRate("USD", "JPY", 76.7200);
        $this->currencyExchangeRates[] = new CurrencyExchangeRate("CHF", "USD", 1.1379);
        $this->currencyExchangeRates[] = new CurrencyExchangeRate("GBP", "CAD", 1.5648);
        
        array_multisort($this->currencyExchangeRates);
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
     * Add a new currency exchange rate to the currency exchange rates collection
     *
     * @param string $currencyCode1 Currency Code for the 1st currency in the pair
     * @param string $currencyCode2 Currency Code for the 2nd currency in the pair
     * @param float  $rate          Exchange rate 1 currency1 = $rate currency2
     *
     * @return void
     */
    public function add($currencyCode1, $currencyCode2, $rate)
    {
        $new = new CurrencyExchangeRate($currencyCode1, $currencyCode2, $rate, new DateTime);
        
        //Prevents from processing an invalid input or a exchange rate with base currency = target currency
        if ($new->currencyCode1 && $new->currencyCode2 && $new->rate && ($new->currencyCode1 != $new->currencyCode2) ) {
            $updated = false;
            foreach ($this->currencyExchangeRates as &$exchange) {
                if ($exchange->currencyCode1 == $new->currencyCode1
                    && $exchange->currencyCode2 == $new->currencyCode2
                ) {
                    $exchange = $new;
                    $updated = true;
                    break;
                }
            }

            if (!$updated) {
                $this->currencyExchangeRates[] = $new;
                array_multisort($this->currencyExchangeRates);
            }
        }
    }

    /**
     * Calculates the equivalent value of the target currency, given a value on a base currency
     *
     * @param float  $value          Value on the base currency
     * @param string $baseCurrency   Base currency code
     * @param string $targetCurrency Target currency code
     *
     * @return float|null The equivalent value on the target currency 
     */
    public function calculateTarget($value, $baseCurrency, $targetCurrency)
    {
    	try{
        	$baseCurrency = Currency::validateCode($baseCurrency);
        	$targetCurrency = Currency::validateCode($targetCurrency);
    	}
    	catch(Exception $e) {
    		echo 'Caught exception: ',  $e->getMessage(), "\n";
    		exit();
    	}
        $targetValue = null;
        
        if ($baseCurrency == $targetCurrency) {
            $targetValue = $value;
        } else {
            //Search direct conversion
            foreach ($this->currencyExchangeRates as $exchange) {
                if ($exchange->currencyCode1 == $baseCurrency && $exchange->currencyCode2 == $targetCurrency) {
                    $targetValue = $value * $exchange->rate;
                    break;
                }
            }

            //If not found, search for reverse conversion
            if ($targetValue === null) {
                foreach ($this->currencyExchangeRates as $exchange) {
                    if ($exchange->currencyCode2 == $baseCurrency && $exchange->currencyCode1 == $targetCurrency) {
                        $targetValue = $value / $exchange->rate;
                        break;
                    }
                }
            }
        }

        return $targetValue;
    }
    
    /**
     * Calculates the equivalent value of the target currency, given a value on a base currency
     * and prints a message with the result.
     *
     * @param float  $value          Value on the base currency
     * @param string $baseCurrency   Base currency code
     * @param string $targetCurrency Target currency code
     *
     * @return float|null The equivalent value on the target currency
     */
    public function printConversion($value, $baseCurrency, $targetCurrency)
    {
    	if (!$value || is_nan($value))
    		echo "<p class='error'>You must give a valid Base currency value</p>";
    	elseif(!$baseCurrency)
    		echo "<p class='error'>You must select a Base Currency</p>";
    	elseif(!$targetCurrency)
    		echo "<p class='error'>You must select a Target Currency</p>";
    	else {
	    	$targetValue = $this->calculateTarget($value, $baseCurrency, $targetCurrency);
	    	
	    	if ($targetValue === null)
	    		echo "<p>There is no currency exchange rate defined for this currency pair ($baseCurrency - $targetCurrency)</p>";
	    	else
	    		echo "<p>$value $baseCurrency = $targetValue $targetCurrency</p>";
    	}
    }

    /**
     * Prints a table with all the currency exchange rates stored
     *
     * @return void
     */
    public function printTable()
    {
        ?>
        <table style="margin: auto;">
            <thead>
                <tr>
                    <th>Exchange rate</th>
                    <th>Updated</th>
                </tr>
            </thead>
            <tbody>
        <?php
        foreach ($this->currencyExchangeRates as $exchange) {
            $rate = "1 ".$exchange->currencyCode1." = ".$exchange->rate." ".$exchange->currencyCode2;
            $date = ($exchange->date instanceof DateTime) ? $exchange->date->format('Y-m-d H:i:s') : "&mdash;";
            echo "
                <tr>
                    <td style='text-align:left;padding-right:1em;'>$rate</td>
                    <td style='text-align:right'>$date</td>
                </tr>";
        }
        ?>
            </tbody>
        </table>
        <?php
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