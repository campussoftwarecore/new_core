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
class Core_Modules_CoreBackup_Controllers_CoreDbbackfile extends Core_Controllers_NodeController
{
    //put your code here
    protected $_tableList=array();
    public function gridContent() 
    {
        $this->getTableNames();
        $this->loadLayout("dbbackup.phtml");        
    }
    public function getTableNames() 
    {
        $db=new Core_DataBase_ProcessQuery();
        $output=$db->getTablesFromDatabase();
        $this->_tableList=$output['tables'];       
    }
    public function savedbtoseverAction() 
    {
        $requestedData=$this->_requestedData;
        $tableName=$requestedData['db_table'];
        
        if($tableName)
        {
            $this->_tableList=array($tableName);
        }
        else
        {
            $db=new Core_DataBase_ProcessQuery();
            $output=$db->getTablesFromDatabase();
            $this->_tableList=$output['tables'];
        }
        if($tableName)
        {
            $folderName=$tableName;
        }
        else 
        {
            $folderName="fulldbbackup";
        }
        $datetime= strtotime(date('Y-m-d'));
        $folderName.=date('Y_m_d_H_i_s');
        foreach($this->_tableList as $tableName)
        {
            $db=new Core_DataBase_ProcessQuery();
            $db->setTable($tableName);
            $createQuery=$db->getTableCreateQuery();
            $folderPath=Core::createFolder($datetime."/".$tableName, "B");
            $fp=  fopen($folderPath."create.sql", "w+");            
            fwrite($fp, $createQuery);
            fclose($fp);
            unlink($folderPath."data.sql");
            $db=new Core_DataBase_ProcessQuery();
            $db->setTable($tableName);
            $db->getTableDataQuery($folderPath."data.sql");
            $content=file_get_contents($folderPath."data.sql");
            
            if($content)
            {
                $tableNameStructe=$db->getDescription();
                $content="INSERT INTO ".$tableName." (".  implode(",", Core::getKeysFromArray($tableNameStructe)).") VALUES (".$content;   
                $lines=explode("\n",$content);
                unset($lines[count($lines)-1]);
                $lastLine=$lines[count($lines)-1];
                $lastLine=substr_replace($lastLine, ";", -1);
                $lines[count($lines)-1]=$lastLine;
                $fp = fopen($folderPath."data.sql", "w+");
                fwrite($fp, implode("\n",$lines));
                fclose($fp);
            }
        }
        
        try
        {   
            $targetfilepath=Core::createFolder("DATABASE",'B').$folderName;
            $codeProcess=new Core_CodeProcess();
            $codeProcess->createZipFile(Core::createFolder($datetime, "B"), $targetfilepath);            
            $folderPath=substr_replace(Core::createFolder($datetime,'B'), "", -1);
            $codeProcess->rmdir_recursive($folderPath);
            $data=array("core_backup_type_id"=>"DB","core_backup_type_id"=>"DB","core_backup_type_id"=>"DB","filepath"=>$folderName,"dateandtime"=>date('Y-m-d H:i:s'));
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
