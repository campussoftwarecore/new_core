<?php

    class Core_Pages_NavigationPage extends Core_Pages_PageLayout
    {
        public $_navigationPath=Array();
        public $_currentDetails;
        public function __construct($data)
        {
            $this->_currentDetails=$data;
            $fileName="navigation.phtml";          
            $this->loadLayout($fileName);
            
        }
        public function getNavigationPath()
        {            
            global $currentNode;
            $wp=new Core_WebsiteSettings();
            $lb=new Core_Model_Language(); 
            if($this->_currentDetails->_currentNode)
            {
                $i=0;
                if($this->_currentDetails->_parentNode)
                {
                    $this->_navigationPath[$i]['label']=$lb->getLabel($this->_currentDetails->_parentNode);
                    $this->_navigationPath[$i]['url']=$wp->websiteUrl.$this->_currentDetails->_parentNode;
                    $i++;
                    
                    $np=new Core_Model_NodeProperties();
                    $np->setNode($this->_currentDetails->_parentNode);
                    $nodeStructure=$np->currentNodeStructure();
                    $nr=new Core_Model_NodeRelations();
                    $nr->setNode($this->_currentDetails->_parentNode);
                    $relations=$nr->getCurrentNodeRelation();
                    if(Core::keyInArray($nodeStructure['descriptor'], $relations))
                    {
                        $np=new Core_Model_NodeProperties();
                        $np->setNode($relations[$nodeStructure['descriptor']]);
                        $nodeStructure=$np->currentNodeStructure();
                        $db=new Core_DataBase_ProcessQuery();
                        $db->setTable($nodeStructure['tablename']);
                        $db->addField($nodeStructure['descriptor']);
                        $db->addWhere($nodeStructure['primkey']."='".$this->_currentDetails->_parentValue."'");
                        $db->buildSelect(); 
                        
                        $descriptor=$db->getValue();
                    }
                    else 
                    {
                        $db=new Core_DataBase_ProcessQuery();
                        $db->setTable($nodeStructure['tablename']);
                        $db->addField($nodeStructure['descriptor']);
                        $db->addWhere($nodeStructure['primkey']."='".$this->_currentDetails->_parentValue."'");
                        $db->buildSelect();                    
                        $descriptor=$db->getValue();
                    }
                    
                    $this->_navigationPath[$i]['label']=$lb->getLabel($this->_currentDetails->_parentNode)."( ".$descriptor." )";
                    $this->_navigationPath[$i]['url']=$wp->websiteUrl.$this->_currentDetails->_parentNode."/".$this->_currentDetails->_parentAction."/".$this->_currentDetails->_parentValue;
                    $i++;
                    $this->_navigationPath[$i]['label']=$lb->getLabel($this->_currentDetails->_currentNode);
                    $this->_navigationPath[$i]['url']=$wp->websiteUrl.$this->_currentDetails->_parentNode."/".$this->_currentDetails->_parentAction."/".$this->_currentDetails->_parentValue;
                    $i++;
                }
                else 
                {                    
                    $this->_navigationPath[$i]['label']=$lb->getLabel($this->_currentDetails->_currentNode);
                    $this->_navigationPath[$i]['url']=$wp->websiteUrl.$this->_currentDetails->_currentNode;
                    $i++;
                }
                if($this->_currentDetails->_currentSelector)
                {
                    $np=new Core_Model_NodeProperties();
                    $np->setNode($this->_currentDetails->_currentNode);
                    $nodeStructure=$np->currentNodeStructure();
                    $nr=new Core_Model_NodeRelations();
                    $nr->setNode($this->_currentDetails->_currentNode);
                    $relations=$nr->getCurrentNodeRelation();
                    if(Core::keyInArray($nodeStructure['descriptor'], $relations))
                    {
                        $np=new Core_Model_NodeProperties();
                        $np->setNode($relations[$nodeStructure['descriptor']]);
                        $nodeStructure=$np->currentNodeStructure();
                        $db=new Core_DataBase_ProcessQuery();
                        $db->setTable($nodeStructure['tablename']);
                        $db->addField($nodeStructure['descriptor']);
                        $db->addWhere($nodeStructure['primkey']."='".$this->_currentDetails->_currentSelector."'");
                        $db->buildSelect(); 
                        
                        $descriptor=$db->getValue();
                    }
                    else 
                    {
                        $db=new Core_DataBase_ProcessQuery();
                        $db->setTable($nodeStructure['tablename']);
                        $db->addField($nodeStructure['descriptor']);
                        $db->addWhere($nodeStructure['primkey']."='".$this->_currentDetails->_currentSelector."'");
                        $db->buildSelect();                    
                        $descriptor=$db->getValue();
                    }
                    $this->_navigationPath[$i]['label']=$lb->getLabel($this->_currentDetails->_currentNode)." ( ".$descriptor." )";
                    $this->_navigationPath[$i]['url']="#";
                }
            }           
            return $this->_navigationPath;
            
        }
    }
?>