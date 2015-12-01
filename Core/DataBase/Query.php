<?php
    class Core_DataBase_Query
    {
        public $whereCon=NULL;
        public $joinArray=array();
        public $table=NULL;        
        public $tablealias=NULL;
        public $orderColumnName=NULL;
        public $groupByColumnName=NULL;
        public $selectedFieldArray=array();
        public $selectedFieldName=NULL;
        public $limitStart=NULL;
        public $limitEnd=NULL;
        public $havingCon=NULL;
        public $output=array();
        
        public function setTable($tablename,$aliastable=NULL)
        {
            $this->table=$tablename;
            if($aliastable ===NULL)
            {
                $aliastable=$this->table;
            }
            $this->tablealias=$aliastable;
        }
        public function addWhere($where=NULL)
        {
            
            if($where!="")
            {
                if($this->whereCon!=NULL && $this->whereCon!="")
                {
                    $this->whereCon=$this->whereCon." and ";
                }
                if(strpos($where,".") === false)
                {
                    $where=$this->tablealias.".".$where;
                }                
                $this->whereCon=$this->whereCon." ".$where;
                
            }
            
        }
        public function addJoin($columname,$tablename,$aliasname,$joincondition,$joinType="LEFT")
        {
            $this->joinArray[$columname]['tablename']=$tablename;
            $this->joinArray[$columname]['aliasname']=$aliasname;
            $this->joinArray[$columname]['condition']=$joincondition;         
            $this->joinArray[$columname]['type']=$joinType;
        }
        public function addOrderBy($orderColumnName=NULL)
        {
            if($this->orderColumnName!=NULL && $this->orderColumnName!="")
            {
                $this->orderColumnName.=" , ";
            }
            if(strpos($orderColumnName,".") === false)
            {
                $orderColumnName=$this->tablealias.".".$orderColumnName;
            }
            $this->orderColumnName.=$orderColumnName;
        }
        public function addGroupBy($groupColumnName=NULL)
        {
            if($this->groupByColumnName!=NULL && $this->groupByColumnName!="")
            {
                $this->groupByColumnName.=" , ";
            }
            if(strpos($groupColumnName,".") === false)
            {
                $groupColumnName=$this->tablealias.".".$groupColumnName;
            }
            $this->groupByColumnName.=$groupColumnName;
        }
        public function addFieldArray($selectArray=array())
        {
            if(count($selectArray)>0)
            {
                foreach ($selectArray as $key => $displayValue) 
                {
                    $this->selectedFieldArray[$key]=$displayValue;                    
                }
            }
        }
        public function addField($selectColumnName=NULL)
        {
            $fieldscount=  explode(",", $selectColumnName);
            if($this->selectedFieldName!=NULL && $this->selectedFieldName!="")
            {
                $this->selectedFieldName.=",";
            }
            if(count($fieldscount)==1)
            {
                $selectColumnName=$this->tablealias.".".$selectColumnName;
            }                
            $this->selectedFieldName=$this->selectedFieldName.$selectColumnName;     
            
        }
        public function setLimit($startNumber,$endNumber)
        {
            $this->limitStart=$startNumber;
            $this->limitEnd=$endNumber;
        }
        public function setRecordsPerPage($noOfRecordsPerPage)
        {
            $this->rpp=$noOfRecordsPerPage;
        }
        public function setLimitStart($startNumber)
        {
            $this->limitStart=$startNumber;
        }
        public function setLimitEnd($endNumber)
        {
            $this->limitEnd=$endNumber;
        }
        public function addHavingCondition($havingcon)
        {
            if($this->havingCon!=NULL && $this->havingCon!="")
            {
                $this->havingCon.=" , ";
            }
            if(strpos($havingcon, ".")===false)
            {
                $havingcon=$this->tablealias.".".$havingcon;
            }
            $this->havingCon.=$havingcon;
        }        
    }
?>