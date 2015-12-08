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
            $wp=new Core_WebsiteSettings();    
            $filename=$wp->documentRoot."var/".$wp->identity."/noderelations.json";
            $fp=fopen($filename,"r");
            $filecontent=  fread($fp,filesize($filename));
            $globalNodeRelations=json_decode($filecontent,true);            
            if(count($globalNodeRelations)>0)
            {
                $nodeRelations=$globalNodeRelations[$this->getNode()];                
                if(!is_array($nodeRelations))
                {
                    $nodeRelations=array();
                }
                else 
                {
                    $nodeRelations=$nodeRelations['MTO'];
                    if(!Core::isArray($nodeRelations))
                    {
                        $nodeRelations=array();
                    }
                }
                
            }
            return $nodeRelations;          
        }
        public function getCurrentNodeOneToOneRelation()
        {
            $nodeRelations=array();
            $wp=new Core_WebsiteSettings();    
            $filename=$wp->documentRoot."var/".$wp->identity."/noderelations.json";
            $fp=fopen($filename,"r");
            $filecontent=  fread($fp,filesize($filename));
            $globalNodeRelations=json_decode($filecontent,true);            
            if(count($globalNodeRelations)>0)
            {
                $nodeRelations=$globalNodeRelations[$this->getNode()];                
                if(!is_array($nodeRelations))
                {
                    $nodeRelations=array();
                }
                else 
                {
                    $nodeRelations=$nodeRelations['OTO'];
                    if(!Core::isArray($nodeRelations))
                    {
                        $nodeRelations=array();
                    }
                }
                
            }
            return $nodeRelations;          
        }
        public function getCurrentNodeOneToManyRelation()
        {
            $nodeRelations=array();
            $wp=new Core_WebsiteSettings();    
            $filename=$wp->documentRoot."var/".$wp->identity."/noderelations.json";
            $fp=fopen($filename,"r");
            $filecontent=  fread($fp,filesize($filename));
            $globalNodeRelations=json_decode($filecontent,true);                
            if(count($globalNodeRelations)>0)
            {
                $nodeRelations=$globalNodeRelations[$this->getNode()];                
                if(!is_array($nodeRelations))
                {
                    $nodeRelations=array();
                }
                else 
                {
                    $nodeRelations=$nodeRelations['OTM'];
                    if(!Core::isArray($nodeRelations))
                    {
                        $nodeRelations=array();
                    }
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