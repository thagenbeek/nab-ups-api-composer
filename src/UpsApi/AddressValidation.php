<?php
	namespace UpsApi;
	include("helpers/xml2array.php");
	
	class AddressValidation
	{
		
		private $access;
  		private $userid;
  		private $passwd;
  		private $wsdl =  "/schemas/address_validation/XAV.wsdl";
  		private $operation = "ProcessXAV";
  		private $endpointurl = 'https://onlinetools.ups.com/webservices/XAV';
  		private $outputFileName = "XOLTResult.xml";
  		
  		//address information
  		private $cosigneeName;
  		private $line1;
  		private $line2;
  		private $city;
  		private $zip;
  		private $state;
  		private $country = "US";
  		
  		private $isValid;
  		private $response;
  		private $request;
  		private $responseArray;
  		
		public function __construct($access, $user, $pass)
		{
			$this->access = $access;
			$this->userid = $user;
			$this->passwd = $pass;
		}
		
		public static function world()
		{
			return __DIR__;
		}
		public function set_cosignee($co)
		{
			$this->cosigneeName = $co;
		}
		
		public function set_address($line1)
		{
			$this->line1 = $line1;	
		}
		
		public function set_city($city)
		{
			$this->city = $city;
		}
		
		public function set_zip($zip)
		{
			$this->zip = $zip;
		}
		
		public function set_state($state)
		{
			$this->state = $state;
		}
		
		public function set_country($country)
		{
			$this->country = $country;
		}
		
		public function validate()
		{
			try
			{
				$mode = array
    			(
         			'soap_version' => 'SOAP_1_1',  // use soap 1.1 client
         			'trace' => 1
    			);

    			// initialize soap client
				$wsdl_path = __DIR__ . $this->wsdl;
				//echo $wsdl_path;
				//die();
  				$client = new \SoapClient($wsdl_path , $mode);
				//$client = SoapClient::__construct($this->wsdl, $mode);

  				//set endpoint url
  				$client->__setLocation($this->endpointurl);
  				
  				$client->__setSoapHeaders($this->create_header());
  				
  				$resp = $client->__soapCall($this->operation ,array($this->processXAV()));
  				$this->response = $client->__getLastResponse();
  				$this->request = $client->__getLastRequest();
  				
  				$this->responseArray = xml2array($this->response);
  				
  				
  				if(isset($resp->ValidAddressIndicator))
  				{
  					$this->isValid = true;
  				}else
  				{
  					$this->isValid = false;
  				}
			}
			catch(Exception $ex)
			{
				echo"<pre>";
				print_r($ex);
				echo"</pre>";
			}catch(SoapFault $e)
			{
				echo "<pre>";
			        print_r($e);
				echo "</pre>";
			}
		}
		
		public function isValid()
		{
			return $this->isValid;
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
		 * Utility functins below
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
		
		private function processXAV()
		{
			//create soap request
      		$option['RequestOption'] = '3';
      		$request['Request'] = $option;
	
    		//$request['RegionalRequestIndicator'] = '1';
      		$addrkeyfrmt['ConsigneeName'] = $this->cosigneeName;
      		$addrkeyfrmt['AddressLine'] = $this->line1;
      		//$addrkeyfrmt['Region'] = $this->city.','.$this->state.','.$this->zip;
 	  		$addrkeyfrmt['PoliticalDivision2'] = $this->city;
 	  		$addrkeyfrmt['PoliticalDivision1'] = $this->state;
 	  		$addrkeyfrmt['PostcodePrimaryLow'] = $this->zip;
 	  		$addrkeyfrmt['CountryCode'] = $this->country;
 	  		$request['AddressKeyFormat'] = $addrkeyfrmt;
 	  		
 	  		return $request;
		}
		
		
	}

?>
