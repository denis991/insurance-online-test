<?php

class CarBase
{
  protected $brand;
  protected $photoFileName;
  protected $carrying;

  public function __construct($brand, $photoFileName, $carrying)
  {
    $this->brand = $brand;
    $this->photoFileName = $photoFileName;
    $this->carrying = $carrying;
  }

  public function getPhotoFileExt()
  {
    $fileParts = pathinfo($this->photoFileName);
    return '.' . $fileParts['extension'];
  }
}

class Car extends CarBase
{
  public $passengerSeatsCount;
  public $carType;

  public function __construct($brand, $photoFileName, $carrying, $passengerSeatsCount)
  {
    parent::__construct($brand, $photoFileName, $carrying);
    $this->passengerSeatsCount = $passengerSeatsCount;
    $this->carType = "car";
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
    $this->carType = "truck";
    $this->parseBodyWhl($bodyWhl);
  }

  private function parseBodyWhl($bodyWhl)
  {
    $parameters = explode('x', $bodyWhl);
    if (count($parameters) === 3) {
      $this->bodyWidth = floatval($parameters[0]);
      $this->bodyHeight = floatval($parameters[1]);
      $this->bodyLength = floatval($parameters[2]);
    } else {
      $this->bodyWidth = $this->bodyHeight = $this->bodyLength = 0;
    }
  }

  public function getBodyVolume()
  {
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
    $this->carType = "spec_machine";
    $this->extra = $extra;
  }
}

function get_car_list($csvFilename)
{
  $carList = [];
  if (($fp = fopen($csvFilename, "r")) !== FALSE) {
    fgetcsv($fp, 10000, ";");
    while (($data = fgetcsv($fp, 10000, ";")) !== FALSE) {
      if (isValidData($data)) {
        $carList[] = createCarObject($data);
      }
    }
    fclose($fp);
  }
  return $carList;
}

function isValidData($data)
{
  return (count($data) === 7 &&
    $data[1] !== "" &&
    $data[3] !== "" &&
    $data[5] !== ""
  );
}

function createCarObject($data)
{
  $carType = $data[0];
  $brand = $data[1];
  $photoFileName = $data[3];
  $carrying = (float)$data[5];

  switch ($carType) {
    case "car":
      $passengerSeatsCount = $data[2] !== "" ? (int)$data[2] : 0;
      return new Car($brand, $photoFileName, $carrying, $passengerSeatsCount);

    case "truck":
      $bodyWhl = $data[4];
      return new Truck($brand, $photoFileName, $carrying, $bodyWhl);

    case "spec_machine":
      $extra = $data[6];
      return new SpecMachine($brand, $photoFileName, $carrying, $extra);

    default:
      return null;
  }
}
