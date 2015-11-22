<?php
    class Core_Model_NodeRelations
    {
        public $_nodeName;
        public function setNode($nodeName)
        {
            $this->_nodeName=$nodeName;
        }
        public function getNode()
        {
            return  $this->_nodeName;
        }
        public function getCurrentNodeRelation()
        {
            $NodeRelations=array();
            $wp=new Core_WebsiteSettings();    
            $filename=$wp->documentRoot."var/".$wp->identity."/noderelations.json";
            $fp=fopen($filename,"r");
            $filecontent=  fread($fp,filesize($filename));
            $GlobalNodeRelations=json_decode($filecontent,true);
            if(count($GlobalNodeRelations)>0)
            {
                $NodeRelations=$GlobalNodeRelations[$this->getNode()];
                if(!is_array($NodeRelations))
                {
                    $NodeRelations=array();
                }
                
            }
            return $NodeRelations;          
        }
    }
?>