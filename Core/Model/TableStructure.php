<?php
    
    class Core_Model_TableStructure 
    {
        public $_tableName;
        public $_tableStructure=array();
        public function setTable($tableName)
        {
            $this->_tableName=$tableName;
        }
        public function getStructure()
        {
            if($this->_tableName)
            {
                $qp=new Core_DataBase_ProcessQuery();
                $qp->setTable($this->_tableName);
                $this->_tableStructure=$qp->getDescription();
            }
            return $this->_tableStructure;
        }
        
    }
    
?>