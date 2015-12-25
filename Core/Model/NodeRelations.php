<?php
    class Core_Model_NodeRelations
    {
        public $_nodeName;
        public $_parentNode;
        public function setNode($nodeName)
        {
            $this->_nodeName=$nodeName;
        }
        public function setParentNode($parentNode)
        {
            $this->_parentNode=$parentNode;
        }
        public function getNode()
        {
            return  $this->_nodeName;
        }
        public function getCurrentNodeRelation()
        {
            $nodeRelations=array();
            $filePath=Core::getCachefilePath($this->_nodeName, "R");
            $nodeRelations=Core::getFileContent($filePath);
            if($nodeRelations)
            {
                $nodeRelations=Core::JsontoArray($nodeRelations);
                $nodeRelations=$nodeRelations['MTO'];
                if(!Core::isArray($nodeRelations))
                {
                    $nodeRelations=array();
                }

            }           
            return $nodeRelations;                  
        }
        public function getCurrentNodeOneToOneRelation()
        {
            $nodeRelations=array();
            $filePath=Core::getCachefilePath($this->_nodeName, "R");
            $nodeRelations=Core::getFileContent($filePath);
            if($nodeRelations)
            {
                $nodeRelations=Core::JsontoArray($nodeRelations);
                $nodeRelations=$nodeRelations['OTO'];
                if(!Core::isArray($nodeRelations))
                {
                    $nodeRelations=array();
                }

            }           
            return $nodeRelations;                      
        }
        public function getCurrentNodeOneToManyRelation()
        {
            $nodeRelations=array();
            $filePath=Core::getCachefilePath($this->_nodeName, "R");
            $nodeRelations=Core::getFileContent($filePath);
            if($nodeRelations)
            {
                $nodeRelations=Core::JsontoArray($nodeRelations);
                $nodeRelations=$nodeRelations['OTM'];
                if(!Core::isArray($nodeRelations))
                {
                    $nodeRelations=array();
                }

            }           
            return $nodeRelations;                    
        }
        public function getParentColName()
        {
            $relations=$this->getCurrentNodeOneToManyRelation();
            return $relations[$this->_parentNode];
        }
    }
?>