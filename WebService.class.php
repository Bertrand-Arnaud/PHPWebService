<?php 
	class WebService
	{
		private $_auth; // Authentification Key.
		private $_WSDataPath; // Path to the JSON file containing the queries dictionnary.
		private $_WSData; // Associative array containing the different queries and all the relative datas.
		
		public $_HTTPRequest; // Associative array with the datas of the request. See the documentation for more infos about it.
		public $_response; //  Associative array with the response datas relative to HTTPRequest.  See the documentation for more infos about it.
	
		
		public function __construct($obj)
		{
			$this->_auth = "Any authentification key you would like to use";
			$this->_WSDataPath = "WSDictionnary.json";
			$this->_WSData = $this->getWSData();
			
			$this->_HTTPRequest = $obj;

			$this->_response["error"] = true;
			$this->_response["msg"] = "";
			$this->_response["data"] = null;
			$this->_response["lastId"] = null;
		}
		
		// This method get the different queries contained in the JSON file and decode them. Queries and relative datas are stored in _WSData
		private function getWSData()
		{
			return file_exists($this->_WSDataPath) ? json_decode(file_get_contents($this->_WSDataPath), true) : null ;
		}
		
		// This method get the HTTP request, check everything is ok, execute the query and store result in variable response.
		public function getWSResponse()
		{
			try 
			{
				// Check if _HTTPRequest variable is correctly instancied.
				if (!isset($this->_HTTPRequest["auth"], $this->_HTTPRequest["name"]))
				{
					throw new Exception("Requête incorrecte, variables non définies");
				}

				// Check if the queries dictionnary has been correctly loaded.
				if ($this->_WSData == null)
				{
					throw new Exception("Impossible d'accéder aux données. Merci de réessayer.");	
				}

				// Check if the authentification key is correct.
				if ($this->_auth != $this->_HTTPRequest["auth"])
				{
					throw new Exception("Echec de l'authentification.");
				}

				// Check if the query requested (by name) is in the dictionnary.
				$QueryName = null;
				foreach($this->_WSData as $WSname => $WSdata)
				{
					if ($WSname == $this->_HTTPRequest["name"])
					{
						$QueryName = $WSname;
						break;
					}
				}
				
				// Throw new exception if the query requested isn't in the dictionnary
				if (is_null($QueryName))
				{
					throw new Exception("Impossible de trouver la requête demandée.");
				}

				// Check if the param's list sent is the same as param's list relative to the query in the dictionnary.
				$allParamExist = false;
				if (count($this->_WSData[$QueryName]["param"]) < 1)
				{
					$allParamExist = true;
				} 
				else 
				{
					foreach($this->_HTTPRequest["param"] as $HRp => $HRv)
					{
						$paramExist = false;
						foreach($this->_WSData[$QueryName]["param"] as $WSp)
						{
							if ($HRp == $WSp)
							{
								$paramExist = true;
								break;
							}									
						}
						if (!$paramExist)
						{
							$allParamExist = false;
							break;
						}
						else
						{
							$allParamExist = true;
						}
					}
				}				

				// Throw new exception if the param's list sent doesn't match.
				if (is_null($allParamExist))
				{
					throw new Exception("Erreur : les paramètres passés sont incorrects.");
				}

				// Prepare the query and execute it. Here we are using our own db class (simplification of PDO object, you can adapt this part with your own database class).
				$bdd = new Bdd();
				$bdd = $bdd->bddConnection();
				
				$query = $bdd->prepare($this->_WSData[$QueryName]["sql"]);
				foreach($this->_WSData[$QueryName]["param"] as $p)
				{
					$query->bindValue($p, $this->_HTTPRequest["param"][$p]);
				}

				$query->execute();
			
				// Get the result of the query and prepare the response.
				if ($this->_WSData[$QueryName]["type"] == "select")
				{
					$this->_response["data"] = $query->fetchAll();
				}
				else if ($this->_WSData[$QueryName]["type"] == "insert")
				{
					$this->_response["lastId"] = $bdd->lastInsertId();
				}

				$this->_response["error"] = false;

			}
			
			// In case of any other exception (mostly for PDO/SQL error).
			catch (Exception $e) 
			{
				$this->_response["msg"] = $e->getMessage();
			}
		}
	}
?>