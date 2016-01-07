<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CoreReportsengine
 *
 * @author ramesh
 */
class Core_Modules_CoreReports_Controllers_CoreReportsengine extends Core_Controllers_NodeController
{
    //put your code here
    public $_reportDetails=array();
    public $_reportResult=array();    
    public $_reportNode;
    public $_reportrpp;
    public $_reportpage;
    public function adminAction($param) 
    {
        $this->getReportDetails();
        $this->loadLayout("reportengine.phtml");
    }
    public function getReportDetails()
    {
        $this->_reportDetails=array();
        $db=new Core_DataBase_ProcessQuery();
        $db->setTable("core_reportsdetails","rd");
        $db->addFieldArray(array("rrnd.displayvalue"=>"root","mrnd.displayvalue"=>"md","rd.name"=>"name","rd.id"=>"id"));
        $joincondition="rnd.nodename=rd.node_id";
        $db->addJoin("node_id", "core_registernode", "rnd", $joincondition);
        $joincondition="rrnd.nodename=rnd.core_root_module_id";
        $db->addJoin("core_root_module_id", "core_registernode", "rrnd", $joincondition);
        $joincondition="mrnd.nodename=rnd.core_module_display_id";
        $db->addJoin("core_module_display_id", "core_registernode", "mrnd", $joincondition);
        $db->addWhere("rd.is_publish='1'");        
        $db->buildSelect();
        $results=$db->getRows();
        if(Core::countArray($results)>0)
        {
            foreach ($results as $reportData)
            {
                $this->_reportDetails[$reportData['root']][$reportData['md']][$reportData['id']]=$reportData;
            }
        }        
    }
    function filterAction()
    {
        $this->loadLayout("reportfilter.phtml");
        return true;
    }
    function getReportDetailsAction()
    {
        $db=new Core_DataBase_ProcessQuery();
        $db->setTable("core_reportsdetails");
        $db->addWhere("core_reportsdetails.id='".$this->_requestedData['reportname']."'");
        $db->buildSelect();
        $result=$db->getRow();
        $nodeName=$result['node_id'];
        $node=new Core_Model_Node();
        $node->setNodeName($nodeName);
        $node->setActionName("admin");
        $node->setShowAttributes();
        $node->setReport();
        $node->getTotalResultCount();
        $rpp=$this->_requestedData['rpp'];
        $this->_reportpage=$this->_requestedData['page'];
        if($rpp=="")
        {
            $rpp=$node->_totalRecordsCount;
        }
        if($this->_reportpage>0)
        {
            $node->setPage($this->_reportpage);
        }
        $node->setRpp($rpp);
        $this->_reportrpp=$rpp;
        echo "<pre>";print_r($this); echo "</pre>";
        $node->getCollection();
        $this->_reportNode=$node;
        
        
        $this->_reportResult=$node->_collections;
        $this->loadLayout("reportoutput.phtml");
    }
}
