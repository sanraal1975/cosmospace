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


        public function queryScalar($sql,$echo=FALSE)
        {
            $result="";
            $query=$this->PDO->prepare($sql);
            if(count($this->params))
            {
                foreach($this->params as $param) { $query->bindParam($param['id'],$param['value'],$param['type']); }
            }
            $query->execute();

            if($query->errorCode()!=0) { $result=array("status"=>"Error", "message"=>"Error al ejecutar la sentencia SQL ".$sql); }
            else { $result=$query->fetch(PDO::FETCH_COLUMN); }

            return $result;
        }

        public function queryColumn($sql,$echo=FALSE)
        {
            if($echo) 
            { 
                Debug::f($sql);
                if(count($this->params)) { $this->viewParams(); }
            }
            try 
            { 
                $command=$this->createCommand($sql);
                if(count($this->params))
                {
                    foreach($this->params as $param) { $command->bindParam($param['id'],$param['value'],$param['type']); }
                }
                $result=$command->queryColumn();
            }
            catch (Exception $e) { Debug::f($e->getMessage()); throw new Exception(__METHOD__.": Error el ejecutar la sentencia SQL ".$sql); }
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

        public function addBigIntParam($id,$value)
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

        public function viewParams()
        {

            $array_tipos=array("Int"=>PDO::PARAM_INT,
                "String"=>PDO::PARAM_STR);
            Debug::f($this->params);
            Debug::f($array_tipos);
        }

        public function createCommand($sql)
        {
//            return Yii::app()->db->createCommand($sql);
        }

        public function resetAutoIncrement($tablename)
        {
            $query="ALTER TABLE ".$tablename." ALTER id DROP DEFAULT";
            $this->execute($query);
            $query="ALTER TABLE ".$tablename." CHANGE COLUMN id id BIGINT(20) UNSIGNED NOT NULL FIRST";
            $this->execute($query);
            $query="ALTER TABLE ".$tablename." AUTO_INCREMENT=0";
            $this->execute($query);
            $query="ALTER TABLE ".$tablename." CHANGE COLUMN id id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT FIRST";
            $this->execute($query);
        }

	}