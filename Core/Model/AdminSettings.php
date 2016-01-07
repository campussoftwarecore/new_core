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
        public $_methodType="REQUEST";
        public $_isAPI=NULL;
        public $_childNode=NULL;

        public function __construct($requesteddata,$filesData) 
        {
            if($_POST)
            {
                $this->_methodType="POST";
            }            
            $this->_requestedData=$requesteddata;
            $this->_filesData=$filesData;            
            if(isset($this->_requestedData['reditectpath']))
            {
                $list=explode("/",$this->_requestedData['reditectpath']);    
                if(Core::covertStringToLower($list['0'])=='api')
                {
                    $this->_isAPI=1; 
                    $this->_currentNode=$list['1'];
                    $this->_currentAction=$list['2'];
                    $this->_currentSelector=$list['3'];
                    $this->_parentNode=$list['4'];
                    $this->_parentAction=$list['5'];
                    $this->_parentValue=$list['6']; 
                }
                else 
                {
                    
                    if($this->_methodType!="POST" && $list['3']=='MTO')
                    {
                        $this->_currentNode=$list['0'];
                        $this->_currentAction=$list['1'];
                        $this->_currentSelector=$list['2'];
                        if($list[4])
                        {
                            $this->_childNode=$list[4];
                        }
                    }
                    else 
                    {
                        $this->_currentNode=$list['0'];
                        $this->_currentAction=$list['1'];
                        $this->_currentSelector=$list['2'];
                        if($list['3']!='MTO')
                        {
                            $this->_parentNode=$list['3'];
                            $this->_parentAction=$list['4'];
                            $this->_parentValue=$list['5']; 
                        }
                    }
                    
                    if(Core::keyInArray($this->_currentNode."_parentnode", $this->_requestedData))
                    {
                        $this->_parentNode=$this->_requestedData[$this->_currentNode."_parentnode"];
                    }
                    if(Core::keyInArray($this->_currentNode."_parentidvalue", $this->_requestedData))
                    {
                        $this->_parentValue=$this->_requestedData[$this->_currentNode."_parentidvalue"];
                    }
                    if(Core::keyInArray($this->_currentNode."_parentaction", $this->_requestedData))
                    {
                        $this->_parentAction=$this->_requestedData[$this->_currentNode."_parentaction"];
                    }
                    if(Core::keyInArray("parentformNode", $this->_requestedData))
                    {
                        $this->_parentNode=$this->_requestedData['parentformNode'];
                    }
                    if(Core::keyInArray("parentformvalue", $this->_requestedData))
                    {
                        $this->_parentValue=$this->_requestedData['parentformvalue'];
                    }
                    if(Core::keyInArray("parentformAction", $this->_requestedData))
                    {
                        $this->_parentAction=$this->_requestedData['parentformAction'];
                    }
                    $np = new Core_Model_NodeProperties();
                    $np->setNode($this->_currentNode);
                    $this->_nodeDetails=$np->getNodeDetails();
                }                 
                
            }           
        }                
    }
?>
