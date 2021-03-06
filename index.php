<?php
/**
 * Currency Caculator Interface
 *
 * @package CurrencyCalculator
 * @author  David de la Calle Cerezo <david.dlc.cerezo@gmail.com>
 * @version 1.0.0 2013-10-06
 */

spl_autoload_register('autoLoader');
session_start();

$title = "Currency Calculator";

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
    
//Add a new currency (if sent by post/get)
$code = (array_key_exists('newCurrency_code', $_REQUEST)) ? $_REQUEST['newCurrency_code'] : null;
$name = (array_key_exists('newCurrency_name', $_REQUEST)) ? $_REQUEST['newCurrency_name'] : null;
if ($code)
    $oCM->add($code, $name);

//Add a new currency exchange rate (if sent by post/get)
$currency1 = (array_key_exists('new_base_currency', $_REQUEST)) ? $_REQUEST['new_base_currency'] : null;
$currency2 = (array_key_exists('new_target_currency', $_REQUEST)) ? $_REQUEST['new_target_currency'] : null;
$rate = (array_key_exists('new_rate', $_REQUEST)) ? $_REQUEST['new_rate'] : null;
if ($currency1 && $currency2 && $rate)
    $oCEM->add($currency1, $currency2, $rate);

//Recover other sent values
$value = (array_key_exists('base_value', $_REQUEST)) ? $_REQUEST['base_value'] : null;
$baseCurrency = (array_key_exists('base_currency', $_REQUEST)) ? $_REQUEST['base_currency'] : null;
$targetCurrency = (array_key_exists('target_currency', $_REQUEST)) ? $_REQUEST['target_currency'] : null;
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title><?php echo $title?></title>
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <script>
            $(function ()
            {
                $("nav#buttonSet").buttonset();

                $("#btnAddCurrency").click(function ()
                {
                    $("#addCurrency").dialog(
                    {
                        width: 450,
                        modal: true,
                        buttons: [
                            {
                                text: "Save currency",
	                            type: "submit",
	                            click: function() { 
		                            //IE doesn't link this submit with the form
		                            if ( navigator.userAgent.match(/msie/i) )
		                            {
			                            $("#newCurrencyForm").submit();
			                        } 
			                    },
	                            form: "newCurrencyForm"
                            },
                            {
                            	text: "Cancel",
                            	click: function ()
	                            {
	                                $(this).dialog("close");
	                            }
                            }
                        ]
                    });
                });

                $("#btnAddRate").click(function ()
                {
                    $("#addCurrencyXchgRate").dialog(
                    {
                        width: 550,
                        modal: true,
                        buttons: [
                            {
                                text: "Save exchange rate",
	                            click: function() { 
		                            //IE doesn't link this submit with the form
		                            if ( navigator.userAgent.match(/msie/i) )
		                            {
			                            $("#newCurrencyXchgRateForm").submit();
			                        }
		                        },
	                            type: "submit",
	                            form: "newCurrencyXchgRateForm"
                            },
                            {
                            	text: "Cancel",
                            	click: function ()
                            {
                                $(this).dialog("close");
                            }
                            }
                        ]
                    });
                });

                $("#btnViewRates").click(function ()
                {
                    $("#viewRates").dialog(
                    {
                        width: 350,
                        modal: true,
                        buttons: {
                            Cancel: function ()
                            {
                                $(this).dialog("close");
                            }
                        }
                    });
                });

                //Select values
                $("#base_currency").val('<?php echo $baseCurrency ?>');
                $("#target_currency").val('<?php echo $targetCurrency ?>');

                $("#btnConvert").button().click(function (e)
                {
                    //IE doesn't have HTML5 form validity check
                    if ( navigator.userAgent.match(/msie/i) || $('#convertForm')[0].checkValidity() )
                    {
                        e.preventDefault();
						$("div#result").load(
                            './ajax/currencyConversion.php',
                            $("#convertForm").serialize(),
                            function( response, status, xhr )
                            {
                                if ( status == "error" ) {
                                    var msg = "Sorry, but there was an error: ";
                                    $( "div#result" ).html( "<p class='error'>" + msg + xhr.status + " " + xhr.statusText + "</p>" );
                            	}
                            }
    					);
                    }
                });
            })
        </script>

        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/redmond/jquery-ui.css">        
        <style>
            body {
                font-family: Verdana, Arial, sans-serif;
            }

            nav {
                margin-bottom: 1em;
            }
            
            .ui-widget{
                font-size: 0.8em;
            }

            .currencyDialog {
                display: none; 
                text-align: center;
                margin: 0.5em;
            }
            
            .currencyDialog input,  .currencyDialog select {
                margin-top: 0.5em;
                margin-bottom: 0.5em;
            }

            #result {
                font-size: 1.5em;
            }
            
            .error {
            	color: red;
            }
        </style>
    </head>
    <body>
        <h1><?php echo $title?></h1>

        <nav id="buttonSet">
            <input id="btnAddCurrency" type="button" value="Add/edit currency">
            <input id="btnAddRate" type="button" value="Add/edit currency exchange rate">
            <input id="btnViewRates" type="button" value="View currency exchange rates">
        </nav>

        <div id="main" style="line-height: 2.3em;">
            <form id="convertForm" method="post" action="">
                <input name="base_value" id="base_value" type="number" step="0.01" placeholder="Base currency value" 
                   title="Base currency value to convert" value="<?php echo $value?>" required>
                <select name="base_currency" id="base_currency" class="currencyCodes" title="Select base currency" required>
                    <option value="" disabled>Select base currency</option>
                    <?php $oCM->printOptions() ?>
                </select>
                &rarr;
                <select name="target_currency" id="target_currency" class="currencyCodes" title="Select target currency" required>
                    <option  value="" disabled>Select target currency</option>
                    <?php $oCM->printOptions() ?>
                </select>
                <input id="btnConvert" type="submit" value="Convert">
            </form>
        </div>

        <div id="result">
        <?php
        	if ( array_key_exists('base_value', $_REQUEST) ) // If data was sent calculate the exchange.
				$oCEM->printConversion($value, $baseCurrency, $targetCurrency);
		?>
        </div>

        
        <div id="addCurrency" title="Add/edit currency" class="currencyDialog">
            <form id="newCurrencyForm" method="post" action="">
                <input name="newCurrency_code" id="newCurrency_code" type="text" value="" maxlength="3"
                       placeholder="Currency code" title="Currency code" required>
                <br/>
                <input name="newCurrency_name" id="newCurrency_name" type="text" value=""
                       placeholder="Currency name [Optional]" title="Currency name [Optional]"> 
                <br>               
                <small>*If the currency code already exist, this will be updated with the new information.</small>
            </form>
        </div>
        
        <div id="addCurrencyXchgRate" title="Add/edit currency exchange rate" class="currencyDialog">
            <form id="newCurrencyXchgRateForm" method="post" action="">
                <div style="white-space: nowrap; *vertical-align: middle;">
                    1 <select name="new_base_currency" id="new_base_currency" class="currencyCodes" title="Select base currency" required>
                        <option disabled selected>Select base currency</option>
                        <?php $oCM->printOptions() ?>
                    </select>
                    =
                    <input name="new_rate" id="new_rate" type="number" step="any" placeholder="Exchange rate" 
                        title="Exchange rate" value="" required>
                    <select name="new_target_currency" id="new_target_currency" class="currencyCodes" title="Select target currency" required>
                        <option disabled selected>Select target currency</option>
                        <?php $oCM->printOptions() ?>
                    </select>
                </div>
              
                <small>*If the exchange rate already exist, this will be updated with the new information.</small>
            </form>
        </div>

        <div id="viewRates" title="View rates" class="currencyDialog">
            <?php 
                $oCEM->printTable();
            ?>
        </div>
    </body>
</html>
<?php
//Save changes in Session
$_SESSION['currencyManager'] = serialize($oCM);
$_SESSION['currencyExchangeManager'] = serialize($oCEM);


/**
 * Autoload Classes when needed
 *
 * @param string $className Name of the class to load
 *
 * @return null
 */
function autoLoader($className)
{
    $path = '/classes/';

    include_once $path.$className.'.php';
}
?>