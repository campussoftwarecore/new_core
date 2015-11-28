<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Settings
 *
 * @author ramesh
 */

class Core_Model_Settings
{
    protected $_nodeName=NULL;
    protected $_tableName=NULL; 
    protected $_tableFieldWithData=array();
    protected $_pkName=NULL;
    protected $_NodeProperties=array();
    protected $_TableStructure=array();
    protected $_autoKey=NULL;
    
    function __construct($nodeName=NULL) 
    {
        $this->_nodeName=$nodeName;
    }
    public function setNode($nodeName)
    {
        $this->_nodeName=$nodeName;
        $this->setNodeProperties();
       
    }
    public function setNodeProperties()
    {
        $np=new Core_Model_NodeProperties();
        $np->setNode($this->_nodeName);
        $this->_NodeProperties=$np->currentNodeStructure();
        $this->setPkName($this->_NodeProperties['primkey']);       
        $this->setTableName($this->_NodeProperties['tablename']);
        
    }

    public function setData($FieldName,$Value)
    {
        $this->_tableFieldWithData[$FieldName]=$Value;
    }
    public function setPkName($pkName)
    {
        $this->_pkName=$pkName;
    }
    public function getId()
    {
        return $this->_tableFieldWithData[$this->_pkName];
    }
    public function setId($pkValue)
    {
        $this->_tableFieldWithData[$this->_pkName]=$pkValue;
    }
    public function setTableName($tableName)
    {        
       $this->_tableName=$tableName;
       $this->getTableStructure();
    }    
    public function getTableStructure()
    {
        $nodeTable=new Core_Model_TableStructure();
        $nodeTable->setTable($this->_tableName);
        $TS=$nodeTable->getStructure();
        $_TableStructure=array();
        foreach ($TS as $FieldData)
        {
            $M=0;
            $FieldName=$FieldData['Field'];
            if($FieldData['Extra']=='auto_increment')
            {
                $this->_autoKey=$FieldData['Field'];
            }            
            else if($FieldData['Null']=='NO')
            {
                if(!$FieldData['Default'])
                {
                    $M=1;
                }
            }
            $this->_TableStructe[$FieldData]['M']=$M;
            $Type=$FieldData['Type'];
            $typeList=explode("(",$Type);
            $Size="";
            if(count($typeList)>1)
            {
                $Type=$typeList['0'];
                $typeList=explode(")",$typeList[1]);
                $Size=$typeList[0];
            }
            $_TableStructure[$FieldName]['Id']=$FieldName;
            $_TableStructure[$FieldName]['Type']=$Type;
            $_TableStructure[$FieldName]['Size']=$Size;            
        }        
        $this->_TableStructure=$_TableStructure;
    }
}