<?php
class CarBase
{
	private $brand;
	private $photoFileName;
	private $carrying;

	public function __construct($brand, $photoFileName, $carrying) 
    {
    	$this -> brand = $brand;
    	$this -> photoFileName = $photoFileName;
    	$this -> carrying = $carrying;
	}

	public function getPhotoFileExt(){
		list($fileName,$ect) = explode('.', $this->photoFileName);
		return '.'.$ect;
	}
}

class Car extends CarBase
{
	public $passengerSeatsCount;
	public $carType;

    public function __construct($brand, $photoFileName, $carrying, $passengerSeatsCount)
	{
		parent::__construct($brand, $photoFileName, $carrying);
		$this -> passengerSeatsCount = $passengerSeatsCount;
		$this -> carType = "car";
	}
 }

class Truck extends CarBase
{
	private $carType;
	public $bodyWidth;
	public $bodyHeight;
	public $bodyLength;

    public function __construct($brand, $photoFileName, $carrying, $bodyWhl)
	{
		parent::__construct($brand, $photoFileName, $carrying);
		$this -> carType = "truck";
		$paramenters = explode('x', $bodyWhl);
		if ($paramenters == [""]){
            $this -> bodyWidth = $this -> bodyHeight = $this -> bodyLength = 0;
		} else {
            $this -> bodyWidth = floatval($paramenters[0]);
            $this -> bodyHeight = floatval($paramenters[1]);
            $this -> bodyLength = floatval($paramenters[2]);
		}
	} 

	public function getBodyVolume(){
		return $this->bodyWidth * $this->bodyLength * $this->bodyHeight;
	}
}

class SpecMachine extends CarBase
{	
	private $carType;
	private $extra;
    public function __construct($brand, $photoFileName, $carrying, $extra)
	{
		parent::__construct($brand, $photoFileName, $carrying);
		$this -> carType = "spec_machine";
		$this -> extra = $extra;		
	} 
}

function get_car_list($csvFilename)
{
    $carList = [];
    if (($fp = fopen($csvFilename, "r")) !== FALSE) {

	fgetcsv($fp, 10000, ";");
	while (($data = fgetcsv($fp, 10000, ";")) !== FALSE) {
		if (count($data) == 7 && $data[1] != "" && $data[3] != "" && $data[5] != "") {
			if ($data[0] == "car") {
				if ($data[2] != ""){
					array_push($carList, new Car($data[1], $data[3], (float) $data[5], (int) $data[2]));
				}
			} else if($data[0] == "truck"){
				array_push($carList, new Truck($data[1], $data[3], (float) $data[5], $data[4]));
			} else {
				if($data[6] != ""){
					array_push($carList, new SpecMachine($data[1], $data[3], (float) $data[5], $data[6]));
				}
			}
		}
	}
	fclose($fp);
	}
	return $carList;
}
?>