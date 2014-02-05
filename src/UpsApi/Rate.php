<?php
	namespace UpsApi;
	include("helpers/xml2array.php");
	
	class Rate
	{
		private $access;
  		private $userid;
  		private $passwd;
  		private $wsdl = "/schemas/rating/RateWS.wsdl";
  		private $operation = "ProcessRate";
  		private $endpointurl;
  		private $outputFileName = "XOLTResult.xml";
		
  		//pickup type
  		private $pickupCode;
  		private $pickupDescArray = array("1" => "Daily Pickup", 
  							   	         "3" =>"Customer Counter", 
  									     "6" => "One Time Pickup", 
  									     "7" => "On Call Air", 
  									     "19" => "Letter Center", 
  									     "20" => "Air Service Center");
  		private $pickupDesc;
  		
  		//customer classification
  		private $customerClassCode;
  		private $customerDescArray = array("0" => "Rates Associated with Shipper Number",
  									       "1" => "Daily Rates",
  									       "4" => "Retail Rates",
  									       "53" => "Standard List Rates");
  		private $customerDesc;
  		
  		//Shipper Details
  		private $shipperName;
  		private $shipperNumber;
  			//shipper address
  			private $shipperAddress;
  			private $shipperCity;
  			private $shipperState;
  			private $shipperZip;
  			private $shipperCountry;
  		
  		//shipTo Details
  		private $toName;
  			//to address
  			private $toAddress;
  			private $toCity;
  			private $toState;
  			private $toZip;
  			private $toCountry;
  			private $toResidential;
  			
  		//shipFrom Details
  		private $fromName;
  			//from address
  			private $fromAddress;
  			private $fromCity;
  			private $fromState;
  			private $fromZip;
  			private $fromCountry;
  			
  		//Service
  		private $serviceCode;
  		private $serviceDescArray = array("1" => "Next Day Air",
  									      "2" => "2nd Day Air",
  									      "3" => "Ground",
  									      "12" => "3 Day Select",
  									      "13" => "Next Day Air Saver",
  									      "14" => "Next Day Air Early AM",
  									      "59" => "2nd Day Air AM");
  		private $serviceDesc;
		
  		//packages
  		private $packages = array();
  		
  		//debugging variables
  		private $request;
  		private $response;
  		
  		private $options = array();
  		
		public function __construct($access, $user, $pass, $mode="production")
		{
			$this->access = $access;
			$this->userid = $user;
			$this->passwd = $pass;
			
			if($mode=="production")
			{
				$this->endpointurl = 'https://onlinetools.ups.com/webservices/Rate';
			}else
			{
				$this->endpointurl = 'https://wwwcie.ups.com/webservices/Rate';
			}
		}
		
		/**
		 * 
		 * @param $type valid types are 1, 3, 6, 7, 19, 20
		 */
		public function setPickupType($type)
		{
			$this->pickupCode = $type;
			$this->pickupDesc = $this->pickupDescArray[$type];
		}
		
		/**
		 * 
		 * @param $class valid class are 0, 1, 4, 53
		 */
		public function setCustomerClass($class)
		{
			$this->customerClassCode = $class;
			$this->customerDesc = $this->customerDescArray[$class];
		}
		
		/**
		 * 
		 * @param $type 1-shipper, 2-to, 3-from
		 * @param $name
		 */
		public function setName($type, $name)
		{
			if($type == 1)
			{
				$this->shipperName = $name;
			}elseif($type == 2)
			{
				$this->toName = $name;
			}else
			{
				$this->fromName = $name;
			}
		}
		
		public function setShipperNumber($number)
		{
			$this->shipperNumber = $number;
		}
		
		/**
		 * 
		 * @param $type 1-shipper, 2-to, 3-from
		 * @param $line1
		 * @param $line2
		 * @param $line3
		 */
		public function setAddress($type, $line1, $line2="", $line3="")
		{
			if($type == 1)
			{
				$this->shipperAddress = array($line1, $line2, $line3);
			}elseif($type == 2)
			{
				$this->toAddress = array($line1, $line2, $line3);
			}else
			{
				$this->fromAddress = array($line1, $line2, $line3);
			}
		}
		
		/**
		 * 
		 * @param $type 1-shipper, 2-to, 3-from
		 * @param $city
		 */
		public function setCity($type, $city)
		{
			if($type == 1)
			{
				$this->shipperCity = $city;
			}elseif($type == 2)
			{
				$this->toCity = $city;
			}else
			{
				$this->fromCity = $city;
			}
		}
		
		/**
		 * 
		 * @param $type 1-shipper, 2-to, 3-from
		 * @param $state
		 */
		public function setState($type, $state)
		{
			if($type == 1)
			{
				$this->shipperState = $state;
			}elseif($type == 2)
			{
				$this->toState = $state;
			}else
			{
				$this->fromState = $state;
			}
		}
		
		/**
		 * 
		 * @param $type 1-shipper, 2-to, 3-from
		 * @param $zip
		 */
		public function setZip($type, $zip)
		{
			if($type == 1)
			{
				$this->shipperZip = $zip;
			}elseif($type == 2)
			{
				$this->toZip = $zip;
			}else
			{
				$this->fromZip = $zip;
			}
		}
		
		/**
		 * 
		 * @param $type 1-shipper, 2-to, 3-from
		 * @param $country
		 */
		public function setCountry($type, $country)
		{
			if($type == 1)
			{
				$this->shipperCountry = $country;
			}elseif($type == 2)
			{
				$this->toCountry = $country;
			}else
			{
				$this->fromCountry = $country;
			}
		}
		
		/**
		 * 
		 * @param $service valid codes are 1, 2, 3, 12, 13, 14, 59
		 */
		public function setService($service)
		{
			$this->serviceCode = $service;
			$this->serviceDesc = $this->serviceDescArray[$service];
		}
		
		public function addPackage($package)
		{
			$this->packages[] = $package;
		}
		
		public function resetPackages()
		{
			$this->packages = array();
		}
		
		public function get_rates()
		{
			try
			{
				$mode = array
    			(
         			'soap_version' => 'SOAP_1_1',  // use soap 1.1 client
         			'trace' => 1,
				'exceptions' => true
    			);

    			// initialize soap client
				$wsdl_path = __DIR__ . $this->wsdl;
  				$client = new \SoapClient($wsdl_path , $mode);

  				//set endpoint url
  				$client->__setLocation($this->endpointurl);
  				
  				$client->__setSoapHeaders($this->create_header());
  				
				try{
  				$resp = $client->__soapCall($this->operation ,array($this->buildRequest()));
				}catch(\SoapFault $e)
				{
	  				echo $client->__getLastResponse();
				}
  				$this->response = $client->__getLastResponse();
  				$this->request = $client->__getLastRequest();
  				$fw = fopen('request.xml', 'w');
				fwrite($fw, $client->__getLastRequest());
				fclose($fw);
				$fw = fopen('response.xml', 'w');
				fwrite($fw, $client->__getLastResponse());
				fclose($fw);
  				$this->processResponse($resp);
  				
			}
			catch(\SoapFault $e){
				echo $e;
			}
			catch(\Exception $ex)
			{
				$fw = fopen('request.xml', 'w');
				fwrite($fw, $client->__getLastRequest());
				fclose($fw);
				echo"<pre>";
				print_r($ex);
				echo"</pre>";
			}
		}
		
		/**
		 * Debugging Functions
		 */
		public function show_response()
		{
			print_r($this->response);
		}
		
		public function show_request()
		{
			print_r($this->request);
		
		}
		
		/**
		 * Utility functions below
		 */
		private function create_header()
		{
			//create soap header
    		$usernameToken['Username'] = $this->userid;
    		$usernameToken['Password'] = $this->passwd;
    		$serviceAccessLicense['AccessLicenseNumber'] = $this->access;
    		$upss['UsernameToken'] = $usernameToken;
    		$upss['ServiceAccessToken'] = $serviceAccessLicense;
    		
    		$header = new \SoapHeader('http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0','UPSSecurity',$upss);
    		
    		return $header;
		}
		
		private function buildRequest()
		{
			//create soap request
      		$option['RequestOption'] = 'Shop';
      		$request['Request'] = $option;
			
      		//set pickup type
      		$pickuptype['Code'] = $this->pickupCode;
      		$pickuptype['Description'] = $this->pickupDesc;
      		$request['PickupType'] = $pickuptype;
      		
      		//customer classification
      		$customerclassification['Code'] = $this->customerClassCode;
      		$customerclassification['Description'] = $this->customerDesc;
      		$request['CustomerClassification'] = $customerclassification;
			
      		//shipper address
      		$shipper['Name'] = $this->shipperName;
      		$shipper['ShipperNumber'] = $this->shipperNumber;
      		$address['AddressLine'] = $this->shipperAddress;
      		$address['City'] = $this->shipperCity;
      		$address['StateProvinceCode'] = $this->shipperState;
      		$address['PostalCode'] = $this->shipperZip;
      		$address['CountryCode'] = $this->shipperCountry;
      		$shipper['Address'] = $address;
      		$shipment['Shipper'] = $shipper;
      		
      		//to address
      		$shipto['Name'] = $this->toName;
      		$address['AddressLine'] = $this->toAddress;
      		$address['City'] = $this->toCity;
      		$address['StateProvinceCode'] = $this->toState;
      		$address['PostalCode'] = $this->toZip;
      		$address['CountryCode'] = $this->toCountry;
      		$shipto['Address'] = $address;
      		$shipment['ShipTo'] = $shipto;
      		      		
      		//from address
      		$shipfrom['Name'] = $this->fromName;
      		$address['AddressLine'] = $this->fromAddress;
      		$address['City'] = $this->fromCity;
      		$address['StateProvinceCode'] = $this->fromState;
      		$address['PostalCode'] = $this->fromZip;
      		$address['CountryCode'] = $this->fromCountry;
      		$shipfrom['Address'] = $address;
      		$shipment['ShipFrom'] = $shipfrom;      		
      		
      		//service type
      		$service['Code'] = $this->serviceCode;
      		$service['Description'] = $this->serviceDesc;
      		$shipment['Service'] = $service;
      		$shipment['ShipmentRatingOptions']['NegotiatedRatesIndicator']="";
			
      		//packages
      		foreach($this->packages as $p)
      		{
      			if($p->typeCode < 10)
      			{
      				$packaging['Code'] = "0".$p->typeCode;
      			}else
      			{
      				$packaging['Code'] = $p->typeCode;
      			}
      			$packaging['Description'] = $p->typeDesc;
      			$package['PackagingType'] = $packaging;
      			$dunit['Code'] = $p->measUnit;
      			$dunit['Description'] = $p->measUnitDesc;
      			$dimensions['Length'] = $p->length;
      			$dimensions['Width'] = $p->width;
      			$dimensions['Height'] = $p->height;
      			$dimensions['UnitOfMeasurement'] = $dunit;
      			$package['Dimensions'] = $dimensions;
      			$punit['Code'] = $p->weightUnit;
      			$punit['Description'] = $p->weightUnitDesc;
      			$packageweight['Weight'] = $p->weight;
      			$packageweight['UnitOfMeasurement'] = $punit;
      			$package['PackageWeight'] = $packageweight;
      			
      			$shipment['Package'][] = $package;
      		}
      		
      		$request['Shipment'] = $shipment;
      		
    		return $request;
		}
		
		private function processResponse($response)
		{
			//echo "<pre>";
			//print_r($response->RatedShipment);
			//echo "</pre>";
			foreach($response->RatedShipment as $shipOption)
			{
				$option['ServiceCode'] = $shipOption->Service->Code;
				switch($shipOption->Service->Code)
				{
					case "01":
						$option['ServiceDesc'] = "Next Day Air";
						break;
					case "02":
						$option['ServiceDesc'] = "2nd Day Air";
						break;
					case "03":
						$option['ServiceDesc'] = "Ground";
						break;
					case "12":
						$option['ServiceDesc'] = "3 Day Select";
						break;
					case "13":
						$option['ServiceDesc'] = "Next Day Air Saver";
						break;
					case "14":
						$option['ServiceDesc'] = "Next Day Air Early AM";
						break;
					case "59":
						$option['ServiceDesc'] = "2nd Day Air AM";
						break;
					default:
						$option['ServiceDesc'] = "Unknown";
						break;
				}
				$option['Cost'] = $shipOption->TotalCharges->MonetaryValue;
				
				if(isset($shipOption->GuaranteedDelivery))
				{
					$option['BusinessDaysInTransit'] = $shipOption->GuaranteedDelivery->BusinessDaysInTransit;
					if(isset($shipOption->GuaranteedDelivery->DeliveryByTime))
					{
						$option['DeliveryByTime'] = $shipOption->GuaranteedDelivery->DeliveryByTime;
					}else
					{
						$option['DeliveryByTime'] = "";
					}
				}else
				{
					$option['BusinessDaysInTransit'] = "";
					$option['DeliveryByTime'] = "";
				}
				
				$this->options[] = $option;
			}
		}
		
		/**
		 * Output functions
		 */
		public function toTable()
		{
			echo "<table>";
			echo "<tr>";
				echo "<th>Service</th>";
				echo "<th>Cost</th>";
			echo "</tr>";
			foreach($this->options as $option)
			{
				echo "<tr>";
					echo "<td>".$option['ServiceDesc']."</td>";
					echo "<td>".number_format($option['Cost'],2,'.',',')."</td>";
				echo "</tr>";
			}
			echo "</table>";
		}
		
		public function toForm($type="radio", $name="shipping")
		{
			if($type == "radio")
			{
				echo "<table>";
				echo "<tr>";
					echo "<th>&nbsp;</th>";
					echo "<th>Service</th>";
					echo "<th>Cost</th>";
				echo "</tr>";
				foreach($this->options as $option)
				{
					echo "<tr>";
						echo "<td><input type=\"radio\" name=\"".$name."\" value=\"".$option['Cost']."\" /></td>";
						echo "<td>".$option['ServiceDesc']."</td>";
						echo "<td>".number_format($option['Cost'],2,'.',',')."</td>";
					echo "</tr>";
				}
				echo "</table>";
			}else
			{
				echo "<select name=\"".$name."\">";
				foreach($this->options as $option)
				{
					echo "<option value=\"".$option['Cost']."\">".$option['ServiceDesc']." - ".number_format($option['Cost'],2,'.',',')."</option>";
				}
				echo "</select>";
			}
		}
		
		public function toArray()
		{
			return $this->options;
		}
	}
?>
