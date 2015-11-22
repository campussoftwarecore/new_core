<?php
    class Core_Attributes_LoadAttribute 
	{        
		public $_attributeName=NULL;
        
        function __construct($attributeType=NULL) 
        {
            $className="Core_Attributes_".ucwords($attributeType)."Attribute";
            $classResponse=class_exists($className, true);
            if($classResponse)
            {
                $this->_attributeName=ucwords($attributeType)."Attribute";
            }
			else
			{
				$this->_attributeName="TextAttribute";				
			}
            
        }       
        
    }
?>