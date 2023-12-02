<style type="text/css">
	body{
		display: grid;
		place-content: center;
	}
	:root{
		color-scheme: dark;
	}
	input{
		display: block;
		width: 100%;
		border: 0.5px solid;		
	}
	input[type = "text"]{
		height: 25px;
		text-align: center;
	}
	input:focus{
		outline: 0;
	}
	input[type="submit"]{
		border-radius: 9999px;
		border: 1px solid #eee;
		flex: 1;
		margin: 7px auto;
	}
</style>
<form action="./GUI.php" method="GET" >
	<input type="text" name="mensaje" placeholder="Ingrese un mensaje" maxlength="8" minlength="1" required autocomplete="off">
	<input type="submit" >
</form>
<?php 
	include 'php_serial.class.php';
	if (isset($_GET['mensaje'])) {
		$serial = new phpSerial();
		$serial->deviceSet("/dev/cu.usbserial-1410");
		// $serial->confBaudRate(9600);
		// $serial->confParity("none");
		// $serial->confCharacterLength(8);
		// $serial->confStopBits(1);
		// $serial->confFlowControl("none");
		// $serial->deviceOpen();
		$hamming = hamming(convertToBits($_GET['mensaje']));
		$str = implode($hamming);
		$normalizado= str_repeat("0",strlen(count($hamming) - strlen($str))).$str;		
        // $serial->sendMessage($normalizado);
		// $fp = fopen("/dev/cu.usbserial-1420",mode:"w");
		// fwrite($fp, $normalizado);
		// fclose($fp);
		echo "Salida ".$normalizado;
		// $serial->sendMessage($normalizado);
		// echo var_dump($salida);
	}	
	function calcRedundantBits(int $m)
	{
		for ($i=0; $i < $m; $i++) { 
			if (2**$i >= $m+$i+1) {
				return $i;
			}
		}        
	}
	function isInteger($input){
    	return(ctype_digit(strval($input)));
	}
	function hamming(array $bits):array{		
		$array=[];		
		$contador = calcRedundantBits(count($bits));	
		$step = 0;		
		$index_pariedad = array_map(fn ($n) =>pow(2,$n) , range(0,$contador-1));	
		echo "Paridad Posicion: ";		
		echo var_dump($index_pariedad)."<br>";
		foreach ($index_pariedad as $key => $value) {			
			$array[$value-1] = 0;
		}		
		for ($i=0; $i <count($bits)+$contador; $i++) { 
			if (!isset($array[$i])) {
				$array[$i]=$bits[$step];
				$step++;
			}
		}
		ksort($array);		
		foreach ($index_pariedad as $key => $value) {
			$array[$value-1] = encontrarBitParidad($value-1,count($bits)+count($index_pariedad),$array);
		}		
		return $array;
	}
	function encontrarBitParidad(int $paridad_pos,int $tam,array $bits){	
		$cant_unos=0;		
		$step =$paridad_pos+1;		
		$bandera = $paridad_pos+$step-1;
		$i = $paridad_pos;
		while ($i<$tam) {
			if ($bits[$i]==1) {
				$cant_unos++;
			}			
			if ($i == $bandera) {//2,6,10 6,14 22
				$bandera += 2*$step;
				$i += ($step+1);
			}else{
				$i++;
			}			
		}		
		if ($cant_unos % 2 == 0) {
			return 0;
		}		
		return 1;
	}
	function convertToBits($value='',int $frec=8){
		$message = "";
		for ($i=0; $i < strlen($value); $i++) {
			$dec = decbin(ord($value[$i]));	
			$message = str_repeat("0",$frec-strlen($dec)).$dec.$message;
			echo $value[$i]."->".str_repeat("0",$frec-strlen($dec)).$dec."<br>";
		}		
		$message = str_split($message);		
		return array_map(fn($c)=>(int)$c, $message);
	}
 ?>