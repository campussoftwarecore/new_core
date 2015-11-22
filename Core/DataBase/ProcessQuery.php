<?php
    class Core_DataBase_ProcessQuery extends Core_DataBase_BuildQuery
    {
        public $query=NULL;
        public $result=array();
        public function getRows($key=NULL,$value=NULL)
        {
            try
            {
                $this->query=$this->buildSelect();
                $db=new Core_DataBase_DbConnect();
                $output=$db->executeQuery($this->query);
                $tempresult=$output['result'];
                $i=0;
                while($rs=mysqli_fetch_assoc($tempresult))
                {
                   if($key!="")
                    {

                        if($value!="")
                        {
                            $this->result[$rs[$key]]=$rs[$value];
                        }
                        else
                        {
                            $this->result[$rs[$key]]=$rs;
                        }

                    }
                    else
                    {
                            $this->result[$i]=$rs;
                    }                
                    $i++;
                }
                return $this->result;
            }
            catch (Exception $ex)
            {
                echo $ex->getMessage();
            }
        }
        public function getRow()
        {
            try
            {
                $this->query=$this->buildSelect();
                $db=new Core_DataBase_DbConnect();
                $output=$db->executeQuery($this->query);
                $tempresult=$output['result'];
                $i=0;
                while($rs=mysqli_fetch_assoc($tempresult))
                {

                    $this->result=$rs;

                }
                return $this->result;
            }
            catch (Exception $ex)
            {
                echo $ex->getMessage();
            }
        }
        public function getValue()
        {
            $this->query=$this->buildSelect();
            $db=new Core_DataBase_DbConnect();
            $output=$db->executeQuery($this->query);
            $tempresult=$output['result'];
            $i=0;
            while($rs=mysqli_fetch_assoc($tempresult))
            {
                foreach ($rs as $key=>$value)
                {
                    $this->result=$value;
                    break;
                }              
               
            }
            return $this->result;
        }
        public function getDescription()
        {
            try
            {
                $this->query=$this->buildDesc();
                $db=new Core_DataBase_DbConnect();
                $output=$db->executeQuery($this->query);
                $tempresult=$output['result'];
                while($rs=mysqli_fetch_assoc($tempresult))
                {
                    $this->result[$rs['Field']]=$rs;                   
                }
                return $this->result;
            }
            catch (Exception $ex)
            {
                echo $ex->getMessage();
            }
        }
    }
?>
