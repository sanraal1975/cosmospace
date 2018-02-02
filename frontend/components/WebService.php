<?php

	namespace frontend\components;
	use frontend\components\Debug;
	use Exception;

	class WebService
	{
		private $ch;
		private $params;
		private $urlCosmospace;

		public function __construct()
		{
			$this->params=array();
			$this->urlCosmospace="http://localhost/cosmospace/backend/controllers/";
			$this->ch=curl_init();
			curl_setopt($this->ch, CURLOPT_POST, TRUE);
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
		}

		public function __destruct()
		{
			curl_close($this->ch);
		}

		private function setURL($url)
		{
			curl_setopt($this->ch,CURLOPT_URL, $url);
		}

		private function setParams()
		{
			curl_setopt($this->ch,CURLOPT_POSTFIELDS,$this->params);
		}

		private function execute()
		{
			$result=curl_exec($this->ch);
			$result=json_decode($result,1);
			$result['http']=curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
			return $result;
		}

		public function getAllEntries($token=NULL)
		{
			$this->params=array();
			$this->params['function']='getAllEntries';
			$this->params['token']=$token;
			$this->setUrl($this->urlCosmospace."ApiController.php");
			$this->setParams();
			$result = $this->execute();
			return $result;
		}

		public function getToken()
		{
			$this->params=array();
			$this->params['function']='getToken';
			$this->setUrl($this->urlCosmospace."ApiController.php");
			$this->setParams();
			$result = $this->execute();
			return $result;
		}

		public function addEntry($input)
		{
			$this->params=array();
			$this->params['function']='addEntry';
			$this->params['token']=$input['token'];
			$this->params['phone']=$input['phone'];
			$this->params['lastname']=$input['lastname'];
			$this->setUrl($this->urlCosmospace."ApiController.php");
			$this->setParams();
			$result = $this->execute();
			return $result;
		}

		public function searchEntry($input)
		{
			$this->params=array();
			$this->params['function']='searchEntry';
			$this->params['token']=$input['token'];
			$this->params['inputsearch']=$input['inputsearch'];
			$this->params['searchfield']=$input['searchfield'];
			$this->setUrl($this->urlCosmospace."ApiController.php");
			$this->setParams();
			$result = $this->execute();
			return $result;
		}
	}

?>