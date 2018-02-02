<?php 

	class SQL
	{
	    private $params=array();
		private $PDO;

		public function __construct()
		{
		}
		
        public function queryAll($sql,$echo=FALSE)
        {
            $result="";
            $query=$this->PDO->prepare($sql);
            if(count($this->params))
            {
                foreach($this->params as $param) { $query->bindParam($param['id'],$param['value'],$param['type']); }
            }
            $query->execute();
            if($query->errorCode()!=0) { $result=array("status"=>"Error", "message"=>"Error al ejecutar la sentencia SQL ".$sql); }
            else { $result=$query->fetchAll(PDO::FETCH_ASSOC); }

            return $result;
        }

        public function queryRow($sql,$echo=FALSE)
        {
            $result="";
            $sql.= " LIMIT 1";
            $query=$this->PDO->prepare($sql);
            if(count($this->params))
            {
                foreach($this->params as $param) { $query->bindParam($param['id'],$param['value'],$param['type']); }
            }
            $query->execute();
            if($query->errorCode()!=0) 
            { 
                $result=array("status"=>"Error", "message"=>"Error al ejecutar la sentencia SQL ".$sql);
            }
            else { $result=$query->fetch(PDO::FETCH_ASSOC); }

            return $result;
        }
        
        public function execute($sql,$echo=FALSE)
        {
            $result="";
            $query=$this->PDO->prepare($sql);
            if(count($this->params))
            {
                foreach($this->params as $param) { $query->bindParam($param['id'],$param['value'],$param['type']); }
            }
            $query->execute();
            if($query->errorCode()!=0) 
            { 
                $result=array("status"=>"Error", "message"=>"Error al ejecutar la sentencia SQL ".$sql);
            }
            return $result;
        }

        public function addParam($id,$value,$type)
        {
            if(!$type) { throw new Exception(__METHOD__.". No se ha especificado el tipo del parámetro."); }
            $result=array();
            $result['id']=$id;
            if($type==PDO::PARAM_INT) { $value=(int)$value; }
            $result['value']=$value;
            $result['type']=$type;
            $this->params[]=$result;
        }

        public function addIntParam($id,$value)
        {
            $result=array();
            $result['id']=$id;
            $result['value']=(int)$value;
            $result['type']=PDO::PARAM_INT;
            $this->params[]=$result;
        }

        public function addStringParam($id,$value)
        {
            $result=array();
            $result['id']=$id;
            $result['value']=$value;
            $result['type']=PDO::PARAM_STR;
            $this->params[]=$result;
        }

		public function resetParams()
		{
			$this->params=array();
		}

    }