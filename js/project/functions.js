window.hosturl= "";
$(document).ready(function () 
{
    window.hosturl=$("#sitehost").val();
    if(document.getElementById('childdatadisplay'))
    {
	var childdatadisplay=document.getElementById('childdatadisplay').value;
        samplefun(childdatadisplay);
    }
    calldefaultfunctions();
});
var ram=0;
function calldefaultfunctions()
{
	
	var action=null;
	if(document.getElementById('module'))
	var module=document.getElementById('module').value;
	if(document.getElementById('node'))
	var node=document.getElementById('node').value;
	if(document.getElementById('action_id'))
	var action=document.getElementById('action_id').value;	
	if(action!="admin" && action!=null)
	{
		var defaultonchange=null;
		if(document.getElementById('noderelations'))
		{
			var noderelations=document.getElementById('noderelations').value;			
			if(noderelations!="")
			{
                            var noderelations=$.parseJSON(noderelations);	
                            $.each(noderelations,function(colname,destinationnode){                                
                                defaultphpfile(node,action,destinationnode,colname)
                                
                            });
			}
		}
                if(node=='core_node_settings')
                {
                    getNodeStructure();
                }
        }
}


function defaultphpfile(node,action,destinationNode,replacediv)
{
    
	//$("#div_loading").show();
	var casevalue;
	if ($("#"+node))
	{
		casevalue=1;
	}
	else
	{
		casevalue=2;
	}	
        
	if (casevalue=='1')
	{
		var formdata = $(".form_"+node).serialize();
		
	}
	else
	{
		var formdata = $("form#result_"+node).serialize();
		
	}
	
	var posturl=window.hosturl+destinationNode+"/descriptor";
        
       $.ajax({
		url : posturl,
		type : "POST",
		dataType : "html",
		data : formdata+"&destinationNode="+destinationNode+"&action_id"+action+"&defaultfile=1"+"&idname="+replacediv,
		success : function (html)
		{
			$("#div_loading").hide();
			if(html)
			{
				var ivid="#value_"+replacediv;
				$(ivid).html(html);								
				return true;
			}
		}
	});
}

function hidevalues()
{
	var is_module=$("#is_module").is(":checked");
	if(is_module==true)
	{
		document.getElementById("row_module_id").style.display = "none";
		document.getElementById("row_module_display").style.display = "none";
		
	}
	else
	{
		document.getElementById("row_module_id").style.display = "";
		document.getElementById("row_module_display").style.display = "";
	}	
}
function getformsubmit(node,action)
{
        $(".formsubmit").prop( "disabled", true);
        if(node==undefined)
	var node=document.getElementById("node").value;
        if(action==undefined)
	var action=document.getElementById("action").value;	
	var postUrl=window.hosturl+node+"/"+action;
	var x=confirm("Due Want to Submit");
        
	if(x==true)
	{		
                $(".formsubmit").prop( "disabled", false);
		$("form#"+node).click(function(event){
                    
                    if($("#"+event.toElement.id).hasClass("formsubmit"))
                    {
                        var formData = new FormData($("form#"+node)[0]);		
                        event.preventDefault();
                        $(".formsubmit").prop( "disabled", true);
			
			$.ajax({
				url : postUrl,
				type: 'POST',
				data: formData,
				async: false,
				cache: false,
				contentType: false,
				processData: false,
				success: function (responseData)
				{     
                                    $("#error_div").html(responseData);
                                    $(".formsubmit").prop( "disabled", false);
                                    
                                        try
                                        {
                                            var obj = jQuery.parseJSON(responseData)
                                            if(obj.status=="success")
                                            {
                                                window.location.replace(obj.redirecturl);
                                            }
                                            else if(obj.status=="error")
                                            {
                                                
                                                var errorsArray=obj.errors;
                                                $.each(errorsArray, function(key, errorMessage) 
                                                {
                                                    try
                                                    {
                                                        var idname="#error_"+key;                                                    
                                                        $(idname).html(errorMessage);                                                        
                                                    }
                                                    catch(e)
                                                    {
                                                        console.log(e);
                                                       
                                                    }
                                                });                                                
                                            }
                                            else
                                            {
                                                $(".formsubmit").prop( "disabled", false);
                                                $("#error_div").html(responseData);
                                                return false;
                                            }
                                            return true;
                                        }
                                        catch(e)
                                        {
                                            $(".formsubmit").prop( "disabled", false);
                                            $("#error_div").html(responseData);
                                            return false;
                                        }
                                       
				}
			});
                    }
                    else
                    {
                        console.log(event.toElement.className);
                    }
		
		});
	}
	else
	{
            $(".formsubmit").prop( "disabled", false);
		$('#validate_value').val("0");
		$("#error_div").html("");
		$( "#saveandclose").prop( "disabled", false);
		return false;
	}
}
function removedisable()
{
	$('#validate_value').val("0");
	$("#error_div").html("");
	$( "#saveandclose").prop( "disabled", false);
	$("#refreshsaveandclose").hide();
	return true;
}
function samplefun(node)
{
    
	$('#multiedit_'+node).val("0");
	$('#mrahtml_'+node).val("");
	updateresultdiv("cancel",node);
	return true;
}
function updateresultdiv(action,node)
{
	if(action=="cancel")
	{
		$('#'+node+'_multiedit').val("0");
	}
	else
	{
		$('#'+node+'_multiedit').val("1");	
	}
        var formname="form#result_"+node;
        
	var formdata = $(formname).serialize();	
        console.log(formdata);
        var POSTURL=window.hosturl+node+"/adminRefresh";
        console.log(POSTURL);
	$.ajax({
			url : POSTURL,
			type : "POST",
			dataType : "html",
			data:formdata+"&resultchange=1"+"&gridsearch=search",
			success : function (html)
			{
                                console.log(html);
				var idname="#total_"+node;
                                $(idname).html(html);
				//$("#div_loading").hide();
				return true;
					
			}
			
	});
	
	return true;
}
function multieditformsubmit(node)
{
	
	var formname="form#result_"+node;       
	var formdata = $(formname).serialize();	
        console.log(formname);
        console.log(formdata);
        var postUrl=window.hosturl+node+"/multiEditSave";
        console.log(postUrl);
	$.ajax({
			url : postUrl,
			type : "POST",
			dataType : "html",
			data:formdata,
			success : function (responseData)
			{
                            try
                            {
                                var obj = jQuery.parseJSON(responseData)
                                if(obj.status=="success")
                                {
                                    window.location.replace(obj.redirecturl);
                                }
                            }
                            catch(err)
                            {
                                $("#error_div").html(responseData);
                                console.log(err);
                            }                            		
			}
			
	});	
	return true;
	
}
function getPrimarykey()
{
    var destinationNode=$("#node").val();
    var formData = $("form").serialize();
    var posturl=window.hosturl+destinationNode+"/getPrimaryKey";
        
    $.ajax({
            url : posturl,
            type : "POST",
            dataType : "html",
            data : formData+"&idname=tablename",
            success : function (html)
            {                
               $("#primkey").val(html);

            }
     });
    
}
function getAutokey()
{
    var destinationNode=$("#node").val();
    var formData = $("form").serialize();
    var posturl=window.hosturl+destinationNode+"/getAutokey";
        
    $.ajax({
            url : posturl,
            type : "POST",
            dataType : "html",
            data : formData,
            success : function (html)
            {                
               $("#autokey").val(html);

            }
     });
    
}
function getNodeStructure()
{
    
    var destinationNode=$("#node").val();
    var formData = $("form").serialize();
    var posturl=window.hosturl+destinationNode+"/getNodeStructureDetails";
    var columnarray=new Array("mandotatory_add","mandotatory_edit","uniquefields","hide_add","hide_edit","hide_view","hide_admin","readonly_add","readonly_edit","search","boolattributes","file","fck","checkbox","selectbox","multivalues","exactsearch","editlist","numberattribute","total","colorattributes");
    for(var j=0;j<columnarray.length;j++)
    {
            var columnname=columnarray[j];
            getNodeStructureFields(columnname);
    }
    
}
function getNodeStructureFields(columnname)
{
    var destinationNode=$("#node").val();
    var formData = $("form").serialize();
    var posturl=window.hosturl+destinationNode+"/getNodeStructureDetails";
    
        $.ajax({
        url : posturl,
        type : "POST",
        dataType : "html",
        data : formData+"&idname="+columnname,
        success : function (html)
        {                
           var ivid="#value_"+columnname;
           $(ivid).html(html);

        }
    });
   
    
}
function setpagezero(node)
{
	$('#page_'+node).val(1);
}
function setpage(node,pagevalue)
{
	$('#page_'+node).val(pagevalue);
}
function setrpp(node,rppvalue)
{	
	$('#rpp_'+node).val(rppvalue);
	$('#page_'+node).val(1);
}
function formvalidations(value,colname,type)
{
	if(type=="EMD")
	{
		validateEmail(value,colname);
	}
	if(type=="PHN")
	{
		validatePhone(value,colname);
	}
}
function checkdateformate(colname,value)
{
	var pattern =/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/;
	if(value!="")
	{
		if (!pattern.test(value))
		{
			var idname="#"+colname
			$(idname).val("");
		}
	}
	
}
function validateEmail(sEmail,colname)
{
    var idname="#"+colname;
    var statusidname="#status_"+colname;
    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (filter.test(sEmail))
    {	$(idname).css('color', 'green');
        return true;
    }
    else
    {	
	$(idname).val("");
        return false;
    }
}
function validatePhone(value,colname)
{
	var filter = /^[0-9-+]+$/;
	if (filter.test(value))
	{
		$(idname).css('color', 'green');
		return true;
	}
	else
	{
		var idname="#"+colname;
		$(idname).val("");
		return false;
	}
}
function rameshajaxfunction(fileurl,formdata,replacediv,type)
{
	if(type == undefined) 
	{
		type="html";
	}
	else
	{
		type="value";
	}
	$.ajax({
		
		url:fileurl,
		dataType:"html",
		data:formdata,
		type:"POST",
		success : function (html)
		{
			if(type == 'html') 
			{
				$("#"+replacediv).html(html);
			}
			else
			{
				$("#"+replacediv).value(html);
			}
		}
		
	});	
   //do this
	return true;
}
function checkaction(nodename,value,type)
{
	
	if(type == undefined) 
	{
		var namevaluearray=document.getElementsByName(nodename+"[]");
	}
	else
	{
		var namevaluearray=document.getElementsByClassName(nodename);
	}
	
	for(var i=0;i<namevaluearray.length;i++)
	{
		var idvalue=namevaluearray[i].id;
                $("#"+idvalue).attr('checked',value);
		$("#"+idvalue).css("opacity","1");
	}
	return true;
}
function getmraaction(nodeName)
{   
    var actionName=$("#"+nodeName+"_mraAction").val();
    var selectorValues="";
    if(actionName)
    {
        var selected=0;
        var namevaluearray=document.getElementsByName("mra_"+nodeName+"[]");
        for(var i=0;i<namevaluearray.length;i++)
        {
            var idvalue=namevaluearray[i].id;            
            if(namevaluearray[i].checked)
            {
                selected=1;   
                if(selectorValues!="")
                {
                    selectorValues=selectorValues+"|";
                }
                selectorValues=selectorValues+$("#"+idvalue).val();
            }
        }
        if(selected==0)
        {
            $("#"+nodeName+"_selector").val("");
            alert("Please Select Records");
            return false;
        }    
        else
        {          
            var x=confirm("Due Want to Submit");
            if(x==true)
            {
                $("#"+nodeName+"_selector").val(selectorValues);
                var parentAction=$("#"+nodeName+"_parentaction").val();
                var parentNode=$("#"+nodeName+"_parentnode").val();
                var parentSelector=$("#"+nodeName+"_parentidvalue").val();
                var postUrl=window.hosturl+nodeName+"/"+actionName;
                if(parentNode)
                {
                    postUrl=postUrl+"/0/"+parentNode+"/"+parentAction+"/"+parentSelector;
                }
                var formData =$("#mradata_"+nodeName).serialize(); 
                $.ajax({
                            url : postUrl,
                            type: 'POST',
                            data: formData,				
                            success: function (responseData)
                            {   
                                try
                                {
                                    var obj = jQuery.parseJSON(responseData)
                                    if(obj.status=="success")
                                    {
                                        window.location.replace(obj.redirecturl);
                                    }
                                    else if(obj.status=="error")
                                    {
                                        $("#mraerror_"+nodeName).html(responseData.errors);
                                        return false;
                                    }
                                    else
                                    {

                                        $("#mraerror_"+nodeName).html(responseData);
                                        return false;
                                    }
                                    return true;
                                }
                                catch(e)
                                {
                                    $("#mraerror_"+nodeName).html(responseData);
                                    return false;
                                }                                       
                            }
                    });
            }            
        }
    }
    else
    {
        alert("Please Select Action ");
        return false;
    }
    
}
function getFieldsForUniqueFieldset()
{
    var node=$("#node").val();    
    var formData=$("form#"+node).serialize();
  
    var posturl=window.hosturl+node+"/getStructure";
     
    $.ajax({
            url : posturl,
            type : "POST",
            dataType : "html",
            data : formData+"&idname=uniquefieldset",
            success : function (responseData)
            {   
               $("#value_uniquefieldset").html(responseData);

            }
     });
}
function getFieldsForFormSettings()
{
    var node=$("#node").val();    
    var formData=$("form#"+node).serialize();
  
    var posturl=window.hosturl+node+"/getStructure";
     
    $.ajax({
            url : posturl,
            type : "POST",
            dataType : "html",
            data : formData+"&idname=filedname",
            success : function (responseData)
            {   
               $("#value_filedname").html(responseData);

            }
     });
}
function getFieldsForRelationDependee()
{
    var node=$("#node").val();    
    var formData=$("form#"+node).serialize();
  
    var posturl=window.hosturl+node+"/getStructure";
     
    $.ajax({
            url : posturl,
            type : "POST",
            dataType : "html",
            data : formData+"&idname=dependee_fields",
            success : function (responseData)
            {   
               $("#value_dependee_fields").html(responseData);

            }
     });
}
function getFieldsForDefualtFields()
{
    var node=$("#node").val();    
    var formData=$("form#"+node).serialize();
  
    var posturl=window.hosturl+node+"/getStructure";
     
    $.ajax({
            url : posturl,
            type : "POST",
            dataType : "html",
            data : formData+"&idname=fieldname",
            success : function (responseData)
            {   
               $("#value_fieldname").html(responseData);

            }
     });
}
function getFieldsForAttributeFields()
{
    var node=$("#node").val();    
    var formData=$("form#"+node).serialize();
  
    var posturl=window.hosturl+node+"/getStructure";
     
    $.ajax({
            url : posturl,
            type : "POST",
            dataType : "html",
            data : formData+"&idname=fieldname",
            success : function (responseData)
            {   
               $("#value_fieldname").html(responseData);

            }
     });
}
function getFieldsforReport()
{
    var node=$("#node").val();    
    var formData=$("form#"+node).serialize();
  
    var posturl=window.hosturl+node+"/getStructure";
     
    $.ajax({
            url : posturl,
            type : "POST",
            dataType : "html",
            data : formData+"&idname=fieldsdata",
            success : function (responseData)
            {   
               $("#value_fieldsdata").html(responseData);

            }
     });
}
function getreportfilter(reportname)
{	
	if(reportname!="")
	{
             var posturl=window.hosturl+'core_reportsengine'+"/filter";
		$.ajax({
			
			url:posturl,
			type:"POST",
			data:"&reportname="+reportname,
			dataType:"html",
			success : function (output)
			{
                            console.log(output);
				$("#filterdiv").html(output);
				$("#buttons_div").show();
				$("#report_submit").attr("disabled", false);
				$("#report_submitrefresh").hide();
				$("#div_loading").hide();
				$("#reportoutput_div").html("");
				$("#page").val(1);
			}
			
			});
		
	}
	else
	{
		$("#filterdiv").html("");
		$("#reportoutput_div").html("");
		$("#buttons_div").hide();
		$("#div_loading").hide();
		$("#page").val(1);
	}
	return true;
}
function reportdatasubmit()
{
	var formdata=$("form").serialize();
	var outputtype=document.getElementById("output_type").value;
	if(outputtype=="csv" || outputtype=='pdf')
	{
		var url1="phpfiles/reportoutput.php?"+formdata;
		window.open(url1);
	}
	else
	{
            var posturl=window.hosturl+'core_reportsengine'+"/getReportDetails";
		$("#report_submit").attr("disabled", true);
		$("#report_submitrefresh").show();		
		$.ajax({
				
			url:posturl,
			type:"POST",
			data:formdata,
			dataType:"html",
			success : function (output)
			{
				$("#reportoutput_div").html(output);
				$("#report_submit").attr("disabled", false);
			}
				
		});
	}
	
	return false;
	
}
function setpageforreport(page)
{
	$("#page").val(page);
	reportdatasubmit();
}