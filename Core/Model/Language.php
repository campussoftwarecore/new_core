<?php
class Core_Model_Language 
{
    public $_labelNames=array();
    
    public function getLabel($label,$node=null,$module=null)
    {
        
        global $currentNode;
        $np=new Core_Model_NodeProperties($currentNode);
        $lablelist=$np->getDefaultLabels();   
        $modulewise_name=null;
        $modulewise_node_name=null;
        $nodewise_name=null;
        if($module!="" && $node!="")
        {
            $modulewise_node_name=$module."_".$node."_".$label;
        }
        if($module!="" && $node=="")
        {
            $modulewise_name=$module."_".$label;
        }
        if($module=="" && $node!="")
        {
            $nodewise_name=$module."_".$label;
        }        
        if($modulewise_node_name)
        {            
            if(Core::keyInArray($modulewise_node_name,$lablelist))
            {
                return  $lablelist[$modulewise_node_name];
            }
        }
        
        if($modulewise_name)
        {            
            if(Core::keyInArray($modulewise_name,$lablelist))
            {
                return  $lablelist[$modulewise_name];
            }
        }
        if($nodewise_name)
        {            
            if(Core::keyInArray($nodewise_name,$lablelist))
            {
                return  $lablelist[$nodewise_name];
            }
        }        
        if(Core::keyInArray($label,$lablelist))
        {
            return  $lablelist[$label];
        }
        
        $customLabels=$np->getLableNames();        
        if($modulewise_node_name)
        {            
            if(Core::keyInArray($modulewise_node_name,$customLabels))
            {
                return  $customLabels[$modulewise_node_name];
            }
        }
        if($modulewise_name)
        {            
            if(Core::keyInArray($modulewise_name,$customLabels))
            {
                return  $customLabels[$modulewise_name];
            }
        }
        if($nodewise_name)
        {            
            if(Core::keyInArray($nodewise_name,$customLabels))
            {
                return  $customLabels[$nodewise_name];
            }
        }
        if(Core::keyInArray($label,$customLabels))
        {
            return  $customLabels[$label];
        }
        
        $label=ucwords($label);
        $list=explode("_",$label);       
        if(count($list)>1)
        {		    
            $count=count($list);
            if(strtolower($list[$count-1])=="id")
            {
                $list[$count-1]="";
                $label=ucwords(implode(" ",array_values($list)));	
            }    
        }       
        $label= ucwords(str_replace("_", " ",$label));
        return $label;
        
    }
}
