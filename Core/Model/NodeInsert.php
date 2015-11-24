<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NodeInsert
 *
 * @author ramesh
 */
class Core_Model_NodeInsert 
{

    protected $_nodeName=NULL;
    function __construct($nodeName=NULL) 
    {
        $this->_nodeName=$nodeName;
    }
    public function setNode($nodeName)
    {
        $this->_nodeName=$nodeName;
    }
    public function getTableStructure()
    {
        
    }
    //put your code here
}
