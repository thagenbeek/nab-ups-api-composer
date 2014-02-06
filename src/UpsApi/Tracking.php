<?php
	namespace UpsApi;
	include("helpers/xml2array.php");
	class Tracking
	{
		private $access;
		private $userid;
		private $passwd;
		private $endpointurl;
		private $wsdl = "schemas/tracking/Track.wsdl";
  		private $operation = "ProcessTrack";
  		
  		private $tracking_number;
  		private $tracking_info;
  		
  		private $response;
  		private $request;
  		
		public function __construct($access, $user, $pass, $mode="production")
		{
			$this->access = $access;
			$this->userid = $user;
			$this->passwd = $pass;
			
			if($mode=="production")
			{
				$this->endpointurl = 'https://onlinetools.ups.com/webservices/Track';
			}else
			{
				$this->endpointurl = 'https://wwwcie.ups.com/webservices/Track';
			}
		}
		
		public function setTrackingNumber($tracknum)
		{
			$this->tracking_number = $tracknum;	
		}

		public function track()
		{
			try
			{
				$mode = array
    			(
         			'soap_version' => 'SOAP_1_1',  // use soap 1.1 client
         			'trace' => 1
    			);

    			// initialize soap client
				$wsdl_path = __DIR__ . "/".$this->wsdl;
  				$client = new \SoapClient($wsdl_path , $mode);

  				//set endpoint url
  				$client->__setLocation($this->endpointurl);
  				
  				$client->__setSoapHeaders($this->create_header());
  				
  				$resp = $client->__soapCall($this->operation ,array($this->buildRequest()));
  				
  				$this->response = $client->__getLastResponse();
  				$this->request = $client->__getLastRequest();
  				
  				$this->processResponse($resp);
  				
			}
			catch(\Exception $ex)
			{
				echo"<pre>";
				print_r($ex);
				echo"</pre>";
			}catch(\SoapFault $ex)
			{
				echo "<pre>";
				print_r($ex);
				echo "</pre>";
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
    		$req['RequestOption'] = '15';
    		$tref['CustomerContext'] = 'Tracking Information';
    		$req['TransactionReference'] = $tref;
    		$request['Request'] = $req;
    		$request['InquiryNumber'] = $this->tracking_number;
 			$request['TrackingOption'] = '7';
	
		    return $request;
		}
		
		private function processResponse($resp)
		{
			//echo "<pre>";
			//print_r($resp);
			//echo "</pre>";
			
			$this->tracking_info['tracking_number'] = $resp->Shipment->InquiryNumber->Value;
			$this->tracking_info['shipment_type'] = $resp->Shipment->ShipmentType->Description;
			$this->tracking_info['shipment_weight'] = $resp->Shipment->ShipmentWeight->Weight;
			$this->tracking_info['shipment_weight_unit'] = $resp->Shipment->ShipmentWeight->UnitOfMeasurement->Code;
			$this->tracking_info['shipment_service'] = $resp->Shipment->Service->Description;
			$this->tracking_info['shipment_pickup_date'] = $resp->Shipment->PickupDate;
			
			if(count($resp->Shipment->Package) > 1)
			{
				
				foreach($resp->Shipment->Package as $pack)
				{
					$my_package['tracking_number'] = $pack->TrackingNumber;
					$my_package['weight'] = $pack->PackageWeight->Weight;
					$my_package['weight_unit'] = $pack->PackageWeight->UnitOfMeasurement->Code;
					$my_package['activity'] = "";
					if(count($pack->Activity) > 1)
					{
						foreach($pack->Activity as $act)
						{
							if(isset($act->ActivityLocation))
							{
								$my_activity['location_description'] = $act->ActivityLocation->Description;
							}
							$my_activity['status'] = $act->Status->Description;
							$my_activity['date'] = $act->Date;
							$my_activity['time'] = $act->Time;
							$my_activity['rand'] = rand();
							$my_package['activity'][] = $my_activity;
						}
					}else
					{
						if(isset($pack->Activity->ActivityLocation))
						{
							$my_activity['location_description'] = $pack->Activity->ActivityLocation->Description;
						}
						$my_activity['status'] = $pack->Activity->Status->Description;
						$my_activity['date'] = $pack->Activity->Date;
						$my_activity['time'] = $pack->Activity->Time;
						$my_activity['rand'] = rand();
						$my_package['activity'][] = $my_activity;
					}
					$this->tracking_info['package'][] = $my_package;
				}
			}else
			{
				$my_package['tracking_number'] = $resp->Shipment->Package->TrackingNumber;
				$my_package['weight'] = $resp->Shipment->Package->PackageWeight->Weight;
				$my_package['weight_unit'] = $resp->Shipment->Package->PackageWeight->UnitOfMeasurement->Code;
				$my_package['activity'];
				if(count($resp->Shipment->Package->Activity) > 1)
				{
					foreach($resp->Shipment->Package->Activity as $act)
					{
						if(isset($act->ActivityLocation))
						{
							$my_activity['location_description'] = $act->ActivityLocation->Description;
						}
						$my_activity['status'] = $act->Status->Description;
						$my_activity['date'] = $act->Date;
						$my_activity['time'] = $act->Time;
						$my_activity['rand'] = rand();
						$my_package['activity'][] = $my_activity;
					}
				}else
				{
					if(isset($resp->Shipment->Package->Activity->ActivityLocation))
					{
						$my_activity['location_description'] = $resp->Shipment->Package->Activity->ActivityLocation->Description;
					}
					$my_activity['status'] = $resp->Shipment->Package->Activity->Status->Description;
					$my_activity['date'] = $resp->Shipment->Package->Activity->Date;
					$my_activity['time'] = $resp->Shipment->Package->Activity->Time;
					$my_activity['rand'] = rand();
					$my_package['activity'][] = $my_activity;
				}
				$this->tracking_info['package'][] = $my_package;
			}
		}
		
		/**
		 * Output functions
		 */
		public function toArray()
		{
			return $this->tracking_info;
		}
	}
?>
