<?php 

	class Database
	{
		//Data Members
		private $user;
		private $pass;
		private $host;
		private $db;
		
		//Constructor
		public function __construct ()
		{
			$num_args = func_num_args();
		  
			if($num_args > 0)
			{
				$args = func_get_args();
				
				$this->host = $args[0];
				$this->user = $args[1];
				$this->pass = $args[2];						
			}
		}
		
		private function MySQLIinstalled ()
		{
			if (function_exists ("mysqli_connect"))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		public function Connect ()
		{
			try
			{
				if ($this->MySQLIinstalled())
				{
					if (!$this->db = new mysqli ($this->host,$this->user,$this->pass))
					{
						$exceptionstring = "Error connection to database: ";
						$exceptionstring .= mysqli_connect_errno() . ": " . mysqli_connect_error();	
						throw new exception ($exceptionstring);																							
					}						
					return true;			
				} 			
				return false;
			}
			catch (exception $e)
			{
				//echo $e->getmessage();				
				return false;
			}
		}
		
		public function SelectDatabase($thedb)
		{
			try
			{			
				if ($this->MySQLIinstalled())
				{
					if (!$this->db->select_db ($thedb))
					{
						$exceptionstring = "Error opening database: $thedb: ";
						$exceptionstring .= $this->db->errno . ": " . $this->db->error;
						throw new exception ($exceptionstring);						
					}				
					return true;
				} 				
				return false;
			}
			catch (exception $e)
			{
				//echo $e->getmessage();
				return false;
			}
		}
		
		public function Execute ($thequery)
		{
			try
			{				
				if ($this->MySQLIinstalled())
				{
					if (!$this->db->query ($thequery))
					{
						$exceptionstring = "Error performing query: $thequery: ";
						$exceptionstring .= $this->db->errno . ": " . $this->db->error;
						throw new exception ($exceptionstring);
					}
					else
					{
						//echo "Query performed correctly: " . $this->db->affected_rows . " row(s) affected.";						
						return $this->db->affected_rows;
					}				
				}
				return 0;
			}
			catch (exception $e)
			{
				//echo $e->getmessage();
				return 0;
			}
		}
		
		public function GetRows ($thequery)
		{
			try
			{			
				if ($this->MySQLIinstalled())
				{
					if ($result = $this->db->query ($thequery))
					{													
						return $result;
					}
					else
					{
						$exceptionstring = "Error performing query: $thequery: ";
						$exceptionstring .= $this->db->errno . ": " . $this->db->error;
						throw new exception ($exceptionstring);
					}
				}
			}
			catch (exception $e)
			{
				//echo $e->getmessage();
				return null;
			}
		}
		
		//Destructor
		public function __destruct()
		{
			try
			{			
				if ($this->MySQLIinstalled())
				{
					if (!$this->db->close())
					{
						$exceptionstring = "Error closing connection: ";
						$exceptionstring .= $this->db->errno . ": " . $this->db->error;
						throw new exception ($exceptionstring);
					}			
				}				
			}
			catch (exception $e)
			{
				//echo $e->getmessage();
			}
		}
	}
?>