<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CoreDbbackfile
 *
 * @author ramesh
 */
class Core_Modules_CoreBackup_Controllers_CoreCodebackup extends Core_Controllers_NodeController
{
    public function gridContent() 
    {
        $this->loadLayout("codebackup.phtml");        
    }    
    public function savedbtoseverAction() 
    {
        $requestedData=$this->_requestedData;
        $tableName=$requestedData['db_table'];
        try
        {   
            $folderName="uploadData";
            $targetfilepath=Core::createFolder("UPLOAD",'B').$folderName;
            $codeProcess=new Core_CodeProcess();
            $codeProcess->createZipFile(Core::createFolder("", "U"), $targetfilepath);            
            
            $data=array("core_backup_type_id"=>"UP","filepath"=>$folderName,"dateandtime"=>date('Y-m-d H:i:s'));
            $nodeSave=new Core_Model_NodeSave();
            $nodeSave->setNode($this->_nodeName);
            foreach ($data as $key=>$value)
            {
                $nodeSave->setData($key,$value);
            }
            $nodeSave->save();           
            
        }
        catch (Exception $ex)
        {
            Core::Log(__METHOD__.$ex->getMessage());
        }        
        $output=array();
        $output['status']="success";
        $output['redirecturl']=$this->_websiteAdminUrl."core_backupdetails";            
        echo json_encode($output);
         return true;
    }
}
