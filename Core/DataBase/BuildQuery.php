<?php
    class Core_DataBase_BuildQuery extends Core_DataBase_Query
    {
        public  $sql=NULL;
        public  $joinCondition=NULL;
        public  $processFieldsData=NULL;
        public  $actionType=NULL;
        public  $fileterCondition=NULL;
        
        function __construct() 
        {
            $this->sql=NULL;
            $this->joinCondition=NULL;
            $this->processFieldsData=NULL;
            $this->actionType=NULL;
            $this->fileterCondition=NULL;
        }
        public function buildFileterCondition()
        {
            $this->fileterCondition="";
            if($this->whereCon!="" && $this->whereCon!=NULL)
            {
                $this->fileterCondition=" WHERE ".$this->whereCon;
            }
            if($this->groupByColumnName!="" && $this->groupByColumnName!=NULL)
            {
                $this->fileterCondition.=" GROUP BY ".$this->groupByColumnName;
            }
            if($this->havingCon!="" && $this->havingCon!=NULL)
            {
                $this->fileterCondition.=" HAVING ".$this->havingCon;
            }
            if($this->orderColumnName!="" && $this->orderColumnName!="")
            {
                $this->fileterCondition.=" ORDER BY ".$this->orderColumnName;
            }
            if($this->limitEnd>0)
            {
                if($this->limitStart<=0)
                {
                    $this->limitStart="0";
                }                
                $this->fileterCondition.=" LIMIT ".$this->limitStart." , ".$this->limitEnd;
            }            
        }             
        public function buildProcessData()
        {
            $k=0;
            $this->processFieldsData="";
            if(count($this->selectedFieldArray))
            {                
                foreach ($this->selectedFieldArray as $selectedColumnName => $displayValue)
                {
                    if($k!=count($this->selectedFieldArray) && $k!=0)
                    {
                        $this->processFieldsData.=", ";
                    }
                    $k++;
                    if(strtoupper($this->actionType) == "SELECT")
                    {
                        if(strpos($selectedColumnName,".")>0)
                        {                        
                            $this->processFieldsData.=$selectedColumnName." AS ".$displayValue."";
                        }
                        else
                        {
                            $this->processFieldsData.=$this->tablealias.".".$selectedColumnName." AS ".$displayValue."";
                        }
                    }
                    else
                    {
                        $this->processFieldsData.=$selectedColumnName." = '".$displayValue."'";
                    }
                    
                }
            }            
            if($this->selectedFieldName!=NULL && $this->selectedFieldName!="")
            {
                if($k>0)
                {
                    $this->processFieldsData.=",";
                }
                $this->processFieldsData.=$this->selectedFieldName;
            }   
            $this->processFieldsData;
            
        }
        public function buildJoin()
        {
            $this->joinCondition=NULL;            
            if(count($this->joinArray)>0)
            {
                foreach($this->joinArray as $joinData)
                {
                    $joinData=(object)$joinData;
                    $this->joinCondition.=" ".$joinData->type." JOIN ";
                    $this->joinCondition.=$joinData->tablename." AS ";
                    $this->joinCondition.=$joinData->aliasname;
                    $this->joinCondition.=" ON (";
                    $this->joinCondition.=$joinData->condition;
                    $this->joinCondition.=" )";                    
                }              
            }            
        }
        public function buildSelect()
        {
            $this->actionType="SELECT";
            $this->sql=$this->actionType." ";
            $this->buildProcessData();     
            if($this->processFieldsData===NULL || $this->processFieldsData=="")
            {
                $this->processFieldsData="*";
            }
            $this->sql.=$this->processFieldsData." ";
            $this->sql.=" FROM ";
            $this->sql.=" ".$this->table." AS ".$this->tablealias;
            $this->buildJoin();
            $this->sql.=" ".$this->joinCondition;
            $this->buildFileterCondition();
            $this->sql.=$this->fileterCondition;
            
            return $this->sql;                    
        }
        public function buildUpdate()
        {
            $this->actionType=" UPDATE";
            $this->sql=$this->actionType." ".$this->table." SET ";
            $this->buildProcessData();            
            $this->sql.=$this->processFieldsData." ";
            $this->buildJoin();
            $this->sql.=$this->joinCondition;
            $this->buildFileterCondition();
            $this->sql.=$this->fileterCondition;
            return $this->sql;
        }
        
        public function buildInsert()
        {
            $this->actionType=" Insert";
            $this->sql=$this->actionType." ".$this->table." SET ";
            $this->buildProcessData();            
            $this->sql.=$this->processFieldsData." ";            
            return $this->sql;
        }
        
        public function buildDelete()
        {
            $this->actionType=" DELETE ";
            $this->sql=$this->actionType." FROM ";                     
            $this->sql.=$this->table;
            $this->buildJoin();
            $this->sql.=$this->joinCondition;
            $this->buildFileterCondition();
            $this->sql.=$this->fileterCondition;
            return $this->sql;
        }
        public function buildDesc()
        {
            $this->actionType=" DESC ";     
            $this->sql=$this->actionType." ";
            $this->sql.=" ".$this->table;            
            return $this->sql;                    
        }
    }
?>

