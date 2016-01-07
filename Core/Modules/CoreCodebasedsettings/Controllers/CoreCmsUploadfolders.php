<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CoreCmsUploadfolders
 *
 * @author ramesh
 */
class Core_Modules_CoreCodebasedsettings_Controllers_CoreCmsUploadfolders extends Core_Controllers_NodeController
{
    //put your code here
    public function coreCmsUploadfoldersAfterDataUpdate()
    {        
        Core::createFolder($this->_requestedData['name'], "U");
        return TRUE;        
    }
    public function coreCmsUploadfoldersNodeDataValidateAfter($errorsArray)
    {
        if(Core::countArray(Core::covertStringToArray($this->_requestedData['name']," "))>1)
        {
            $errorsArray['name']="Please Enter Characters Only";
        }
        return $errorsArray;
    }
}
