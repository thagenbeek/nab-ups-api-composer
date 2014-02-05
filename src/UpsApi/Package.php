<?php
	namespace UpsApi;

	class Package
	{
		//packaging type
		var $typeCode;
		var $typeDescArray = array("0" => "Unknown",
								   "1" => "UPS Letter",
							  	   "2" => "Customer Supplied",
							  	   "3" => "UPS Tube",
							       "4" => "UPS Pak",
							  	   "21" => "Express Box",
							  	   "24" => "25KG Box",
							  	   "25" => "10KG Box",
							  	   "30" => "Pallet",
							  	   "2a" => "Small Express Box",
							  	   "2b" => "Medium Express Box",
							  	   "2c" => "Large Express Box");
		var $typeDesc;
		
		//dimensions
			//unit of measurement
			var $measUnit;
			var $measUnitDesc;
		var $length;
		var $width;
		var $height;
		
		//weight
			//unit of measurement
			var $weightUnit;
			var $weightUnitDesc;
		var $weight;
		
		public function __construct()
		{
		}
		
		public function setType($type)
		{
			$this->typeCode = $type;
			$this->typeDesc = $this->typeDescArray[$type];
		}
		
		public function setMeasurement($l,$w,$h,$unit)
		{
			$this->length = $l;
			$this->width = $w;
			$this->height = $h;
			$this->measUnit = $unit;
			$this->measUnitDesc = ($unit == "IN") ? "Inches" : "Centimeters";
		}
		
		public function setWeight($w, $unit)
		{
			$this->weight = $w;
			$this->weightUnit = $unit;
			$this->weightUnitDesc = ($unit == "LBS") ? "Pounds" : "Kilograms";
		}
	}
?>
