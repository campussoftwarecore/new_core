<?php
class Core_Model_AdminSettings 
    {
        public $_requestedData;
        public $_currentAction=NULL;
        public $_currentNode=NULL;
        public $_currentSelector=NULL;
        public $_parentNode=NULL;
        public $_parentAction=NULL;
        public $_parentValue=NULL;
        public $_exactSearchAttributes=array();
        public $_nodesFullStructure;
        public $_currentNodeStructure;  
        public $_navigationPath=array();
        public $_tableName=null;
        public $_primaryKey=null;
        public $_descriptor=NULL;
        public $_filesData=array();
        public $_nodeProperties=array();


        public function __construct($requesteddata,$filesData) 
        {
            
            $this->_requestedData=$requesteddata;
            $this->_filesData=$filesData;            
            if(isset($this->_requestedData['reditectpath']))
            {
                $list=explode("/",$this->_requestedData['reditectpath']);    
                             
                $this->_currentNode=$list['0'];
                $this->_currentAction=$list['1'];
                $this->_currentSelector=$list['2'];
                $this->_parentNode=$list['3'];
                $this->_parentAction=$list['4'];
                $this->_parentValue=$list['5'];  
                if(Core::keyInArray("parentnode", $this->_requestedData))
                {
                    $this->_parentNode=$this->_requestedData['parentnode'];
                }
                if(Core::keyInArray("parentidvalue", $this->_requestedData))
                {
                    $this->_parentValue=$this->_requestedData['parentidvalue'];
                }
                if(Core::keyInArray("parentaction", $this->_requestedData))
                {
                    $this->_parentAction=$this->_requestedData['parentaction'];
                }
                $np = new Core_Model_NodeProperties();
                $np->setNode($this->_currentNode);
                $this->_nodeDetails=$np->getNodeDetails();
                
            }           
        }                
    }
?>
