window.hosturl= "http://localhost/new_core/";
$(document).ready(function () {
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
function getformsubmit()
{
    console.log("ramesh");
    var x=confirm("Due Want to Submit");
    if(x==true)
    {
        var node=document.getElementById("node").value;
	var action=document.getElementById("action").value;
        console.log(node);
        console.log(action);
        $("form#"+node).click(function(event){
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            console.log(formData);
            console.log("formsubmitted");
            var posturl=window.hosturl+node+"/"+action;
            console.log(posturl);
            $.ajax({
				url : posturl,
				type: 'POST',
				data: formData,
				async: false,
				cache: false,
				contentType: false,
				processData: false,
				success: function (returndata)
				{
                                    $("#error_div").html(returndata);
                                }
                            });
        });
    }
    else
    {
        
    }
}
function getformsubmittemp()
{
	var node=document.getElementById("node").value;
	var action=document.getElementById("action").value;	
	$( "#saveandclose").prop( "disabled", true);
	var x=confirm("Due Want to Submit");
	if(x==true)
	{
		$( "#saveandclose").prop( "disabled", false);
		$("#refreshsaveandclose").show();
		var formsubmit=$("#formsubmit").val();		
		$("#formsubmit").val("1");
		//$("#div_loading").show();
		$("form#"+node).submit(function(event){
		var formData = new FormData($(this)[0]);
		$( "#saveandclose").prop( "disabled", true);
		//disable the default form submission
		event.preventDefault();
		var formsubmit=$("#formsubmit").val();
		if(formsubmit==1)
		{
			
			$.ajax({
				url : "validations.php",
				type: 'POST',
				data: formData,
				async: false,
				cache: false,
				contentType: false,
				processData: false,
				success: function (returndata)
				{
					
					$("#error_div").html(returndata);
					$("#formsubmit").val("0");
					$( "#saveandclose").prop( "disabled", false);
					if(returndata.search("script")<0)
					{
						$("#error_div").html(returndata);
						$( "#saveandclose").prop( "disabled", false);
						$("#refreshsaveandclose").hide();
						$("#div_loading").hide();
						return false;
					}
				}
			});
		}
		else
		{
			return true;
		}
		});
	}
	else
	{
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
	$('#multiedit').val("0");
	$('#mrahtml_'+node).val("");
	updateresultdiv("cancel",node);
	return true;
}
function updateresultdiv(action,node)
{
	if(action=="cancel")
	{
		$('#multiedit').val("0");
	}
	else
	{
		$('#multiedit').val("1");	
	}
        var formname="form#result_"+node;
	var formdata = $(formname).serialize();
	//$("#div_loading").show();
	
	//return false;
	$.ajax({
			url : window.hosturl+node+"/adminRefresh",
			type : "POST",
			dataType : "html",
			data:formdata+"&resultchange=1"+"&search=search",
			success : function (html)
			{
                                
				var idname="#total_"+node;
                                console.log(idname);
                                console.log(html);
				$(idname).html(html);
				//$("#div_loading").hide();
				return true;
					
			}
			
	});
	
	return true;
}
function multieditformsubmit(node,nodeencrypt)
{
	
	var formname="#result_"+node;
	var formdata = $(formname).serialize();	
	$("#div_loading").show();
	$.ajax({
			url : "multieditformsubmit.php",
			type : "POST",
			dataType : "html",
			data:formdata,
			success : function (html)
			{
				$("#div_loading").hide();
				if(html!="success")
				{
					$("#sample_div").html(html);
					return false;
				}
				else
				{
					updateresultdiv("cancel",node,nodeencrypt);
					return true;
				}
						
			}
			
	});	
	return true;
	
}
function getregisternode()
{
	var node=document.getElementById('node').value;
	var registernode=document.getElementById('node_settings_id').value;
	var action=document.getElementById('action_id').value;
	var formdata = $("#"+node).serialize();
	var columnname="uniquefieldset";
	$.ajax({
	url : "phpfiles/getreplacediv_values.php",
	type : "POST",
	dataType : "html",
	data : formdata+"&idname="+columnname+"&node="+node+"&registernode="+registernode+"&action_id="+action+"&nodefile=contgettabledata1",
	success : function (html)
	{
		if(html)
		{
			var ivid="#value_"+columnname;
			$(ivid).html(html);
			return true;
		}	
	}
	});
}
function getregisternodesettings()
{
	var node=document.getElementById('node').value;
	var registernode=document.getElementById('registernode_id').value;
	var action=document.getElementById('action_id').value;
	var formdata = $("#"+node).serialize();
	var columnname="register_col";
	$.ajax({
	url : "phpfiles/getreplacediv_values.php",
	type : "POST",
	dataType : "html",
	data : formdata+"&idname="+columnname+"&node="+node+"&registernode="+registernode+"&action_id="+action+"&nodefile=contgettabledata1",
	success : function (html)
	{
		if(html)
		{
			var ivid="#value_"+columnname;
			$(ivid).html(html);
			return true;
		}	
	}
	});
}
function displayfilefields()
{
	var node=document.getElementById('node').value;
	var registernode=document.getElementById('node_settings_id').value;
	var action=document.getElementById('action_id').value;
	var formdata = $("#"+node).serialize();
	var columnname="colmanname";
	$.ajax({
	url : "phpfiles/getreplacediv_values.php",
	type : "POST",
	dataType : "html",
	data : formdata+"&idname="+columnname+"&node="+node+"&registernode="+registernode+"&action_id="+action+"&nodefile=contgettabledata1"+"&filedata=1",
	success : function (html)
	{
		if(html)
		{
			var ivid="#value_"+columnname;
			$(ivid).html(html);
			return true;
		}	
	}
	});
}
function setpagezero()
{
	$('#page').val("");
}
function setpage(pagevalue)
{
	$('#page').val(pagevalue);
}
function setrpp(rppvalue)
{
	
	$('#rpp').val(rppvalue);
	$('#page').val("");
}
function checkall(formname,node,idname,source)
{
	var checkvalue=source.checked;
	var mraselect="mra_"+node;
	var actionidvaluelist=new Array();
	var action=$("#mra_action").val();
	if(action)
	{
		var actionidname=node+"_"+action;
		var actionidvalue=document.getElementById(actionidname).value;
		if(actionidvalue!="")
		{
			actionidvaluelist=actionidvalue.split('|');
		}
	}	
	
	var namevaluearray=document.getElementsByClassName(mraselect);
	for (var i = 0; i < document.getElementById(formname).elements.length; i++)
	{
		if(document.getElementById(formname).elements[i].type=="checkbox")
		{
			var idvalue1=document.getElementById(formname).elements[i].id;
			if(idvalue1!="" && idvalue1!=null)
			{
				var idvalue="#"+idvalue1;				
				var spanidvalue="#"+node+"_"+idvalue1;
				
				var idactualvalue=document.getElementById(idvalue1).value;
				if(checkvalue==true)
				{
					var keyvalue=$(idvalue).val();
					var t=0;
					for(var k=0;actionidvaluelist.length;k++)
					{
						if(actionidvaluelist[k]==undefined)
						{
							break;
						}
						else
						{
							if(actionidvaluelist[k]==keyvalue)
							{
								t=1;
							}
						}
					}
					if(t==0)
					{
						var checkedvalue="checked";
						//$(idvalue).css("opacity","1");
						$(idvalue).attr("checked",true);
					}
					
					
				}
				else
				{				
					$(idvalue).attr("checked",false);
					var checkedvalue="";
				}		
			}			
		}
	}
	return true;

}
function checkactionsrestrictions(action,node)
{
	var checkallname="#check_"+node;
	if(action!="")
	{
		var actionidname=node+"_"+action;
		var actionidvalue=document.getElementById(actionidname).value;
		var actionidvaluelist=new Array();
		if(actionidvalue!="")
		{
			actionidvaluelist=actionidvalue.split('|');
		}
		var mraselect="mra_"+node;		
		//var namearray=document.getElementsByClassName(mraselect);
		for(var i=0;i<namearray.length;i++)
		{
			var idvalue="#"+namearray[i].id;			
			var keyvalue=document.getElementById(namearray[i].id).value;
			var t=0;
			for(var k=0;actionidvaluelist.length;k++)
			{
				if(actionidvaluelist[k]==undefined)
				{
					break;
				}
				else
				{
					if(actionidvaluelist[k]==keyvalue)
					{
						t=1;
					}
				}
			}
			if(t==1)
			{
				$(idvalue).attr("disabled", true);
				$(idvalue).attr("checked", false);
				$(idvalue).addClass("checked");
			}
			else
			{
				$(idvalue).attr("disabled", false);
				$(idvalue).removeClass("checked");
			}
			
		}
		
		//$(checkallname).attr("disabled", true);
		//$(checkallname).attr("checked", false);
		return true;
	}
	else
	{
		
		var mraselect="mra_"+node;
		var namearray=document.getElementsByClassName(mraselect);
		for(var i=0;i<namearray.length;i++)
		{
			var idvalue="#"+namearray[i].id;
			$(idvalue).attr("disabled", false);
			//$(idvalue).attr("checked", false);
		}
		
		//$(checkallname).attr("disabled", false);
		//$(checkallname).attr("checked", false);
		return true;
	}
	
}
function getmrahtml(action,node)
{
	var formdata=$('form').serialize();
	$.ajax({
	url : "phpfiles/customhtml.php",
	type : "POST",
	dataType : "html",
	data : formdata,
	success : function (html)
	{
		if(html)
		{
			var ivid="#mrahtml_"+node;
			$(ivid).html(html);
			return true;
		}	
	}
	});
}
function custommravalidate(node,primvalues)
{
	var formdata1=$('form').serialize();
	$("#mra_submit").prop("disabled",true);
	//alert(formdata1);
	//return false;
	$.ajax({
	url : "phpfiles/coustommravalidations.php",
	type : "POST",
	dataType : "html",
	data : formdata1+"&primvalues="+primvalues,
	success : function (html)
	{
		
		var list=html.split("|||");
		if(list[0]!="success")
		{
			$("#mra_submit").prop("disabled",false);
			if(html.search("script")<0)
			{
				alertify.alert(html);
			}
			var idname="#mraerror_"+node;
			$(idname).html(html);
			return false;
		}
		else
		{
			self.location=list[1];
	
			return true;	
		}
	}
	});
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
function checkdate(colname,value)
{
	alert(colname);
	alert(value);
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
function getreportfilter(reportname)
{
	$("#div_loading").show();
	if(reportname!="")
	{
		$.ajax({
			
			url:"phpfiles/getreportfilters.php",
			type:"POST",
			data:"&reportname="+reportname,
			dataType:"html",
			success : function (output)
			{
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
		$("#report_submit").attr("disabled", true);
		$("#report_submitrefresh").show();
		$("#div_loading").show();
		$.ajax({
				
			url:"phpfiles/reportoutput.php",
			type:"POST",
			data:formdata,
			dataType:"html",
			success : function (output)
			{
				$("#reportoutput_div").html(output);
				$("#div_loading").hide();
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
function actionform(action,node,parentnode,parentidvalue,parentaction)
{
	$.ajax({
	
		url:"index.php",
		data:"node="+node+"&action="+action+"&action_id="+action+"&parentnode="+parentnode+"&parentidvalue="+parentidvalue+"&parentaction="+parentaction+"&resultchange=1"+"&actionform=1",
		dataType:"html",
		type:"GET",
		success : function (html)
		{
			$("#"+node+"_format").html(html);
		}
	});
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
function queryclausechange()
{
	var clausevalue=$("#queryclause_id").val();	
	if(clausevalue=="ORDERBY")
	{
		$("#row_orderclausetype_id").show();
	}
	else
	{
		$("#orderclausetype_id").val("");
		$("#row_orderclausetype_id").hide();
	}
	if(clausevalue=="AGGREGATE")
	{
		$("#row_aggregate_function_id").show();
	}
	else
	{
		$("#aggregate_function_id").val("");
		$("#row_aggregate_function_id").hide();
	}
	return true;
}
function getreportnodestructure()
{
	var formdata = $("form" ).serialize();
	$.ajax({
	url : "phpfiles/getreplacediv_values.php",
	type : "POST",
	dataType : "html",
	data : formdata+"&idname=fieldsdata"+"&nodefile=reportfieldsdata",
	success : function (html)
	{
		if(html)
		{
			var ivid="#value_fieldsdata";
			$(ivid).html(html);
			return true;
		}	
	}
	});
}