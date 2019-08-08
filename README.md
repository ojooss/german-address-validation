# german-address-validation
PHP class to validate german address with www.postdirekt.de/plzserver

Calls https://www.postdirekt.de/plzserver/ to validate address 

Has methods to 
	- validate postcode or city + street 
	- lookup cities to postcode or postcode to city + street
	
Some samples:
	
	$GermanAddressValidation = new GermanAddressValidation();

	$result = $GermanAddressValidation->searchCityByPostCode('80636');
	echo 'searchCityByPostCode:'.count($result).PHP_EOL;

	$result = $GermanAddressValidation->validatePostCode('80636');
	echo 'validatePostCode:'.($result?'true':'false').PHP_EOL;

	$result = $GermanAddressValidation->searchPostCodeByCityStreet('München', 'Erika-Mann-Straße 33');
	echo 'searchPostCodeByCityStreet:'.count($result).PHP_EOL;

	$result = $GermanAddressValidation->validateAddress('München', 'Erika-Mann-Straße 33', '80636');
	echo 'validateAddress:'.($result?'true':'false').PHP_EOL;
