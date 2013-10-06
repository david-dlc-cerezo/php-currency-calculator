<?php
/**
 * Performs a currency conversion with the sent data via GET ot POST showing a text result
 *
 * @package CurrencyCalculator
 * @author  David de la Calle Cerezo <david.dlc.cerezo@gmail.com>
 * @version 1.0.0 2013-10-06
 */

spl_autoload_register('autoLoader');
session_start();

//Recover saved data or initialice
$oCEM = null;
if (array_key_exists('currencyExchangeManager', $_SESSION))
    $oCEM = unserialize($_SESSION['currencyExchangeManager']);
if (!$oCEM instanceof CurrencyExchangeManager)
    $oCEM = new CurrencyExchangeManager();

$oCM = null;
if (array_key_exists('currencyManager', $_SESSION))
    $oCM = unserialize($_SESSION['currencyManager']);
if (!$oCM instanceof CurrencyManager)
    $oCM = new CurrencyManager();

//Recover sent values
$value = (array_key_exists('base_value', $_REQUEST)) ? $_REQUEST['base_value'] : null;
$baseCurrency = (array_key_exists('base_currency', $_REQUEST)) ? $_REQUEST['base_currency'] : null;
$targetCurrency = (array_key_exists('target_currency', $_REQUEST)) ? $_REQUEST['target_currency'] : null;

//Performs the conversion
if ($value && $baseCurrency && $targetCurrency) {
    $targetValue = $oCEM->calculateTarget($value, $baseCurrency, $targetCurrency);
                
    if ($targetValue === null)
        echo "<p>There is no currency exchange rate defined for this currency pair ($baseCurrency - $targetCurrency)</p>";
    else
        echo "<p>$value $baseCurrency = $targetValue $targetCurrency</p>";
}
    
/**
 * Autoload Classes when needed
 *
 * @param string $className Name of the class to load
 *
 * @return null
 */
function autoLoader($className)
{
    $path = '../classes/';

    include_once $path.$className.'.php';
}
?>