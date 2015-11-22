function setfinalamount()
{
	var discount=document.getElementById("discount").value;
	var amount=document.getElementById("amount").value;
	var finalamount=parseFloat(amount-(amount*(discount/100))).toFixed(2);
	$("#final_amount").val(finalamount);
	$("#readonly_final_amount").html(finalamount);	
}
function make_payment()
{
	var formdata=$('form').serialize();
	var amount=document.getElementById('amount_paid').value;
	if(isNaN(amount))
	{
		$('#amount_paid').val("0");
		alert("Amount should be  Numbers Only"); return false;
	}
	if(amount<=0)
	{
		alert("Amount should be greater than 0 ");
		return false;
	}
	var payment_type=$('#payment_type_id').val();
	
	$('#submitamount').prop("disabled",true);
	$.ajax({
		url:"phpfiles/projectfunction.php",
		data:formdata,
		type:"POST",
		dataType:"html",
		success:function(html)
		{
			var list=html.split('_');
			if(list[0]!="success")
			{
				alert(html);
				$('#error_div').html(html);
				$('#submitamount').prop("disabled",false);
				return false
			}
			else
			{
				$('#error_div').html("");
				submitstudentdetails();
				$('#transactionid').val(list[1]);
				document.getElementById('transactionprint_div').style.display = "";
				document.getElementById('refreshdetails_div').style.display = "";
				alert("Payment Done Successfully");
				return true;
			}
			
		}
		});
}
function showreferenceno(type)
{
	if(type=='CS')
	{
		$('#reference_no_div').hide();
		$('#reference_no').val("");
	}
	else
	{
		$('#reference_no_div').show();
	}
	return true;
}
function refreshpaymentdetails()
{
	location.reload();
}
function make_concession()
{
	var formdata=$('form').serialize();
	var transactionamount=document.getElementById('transactionamount').value;
	if(isNaN(transactionamount))
	{
		$('#transactionamount').val("0");
		alert("Amount should be  Numbers Only"); return false;
	}
	if(transactionamount<=0)
	{
		alert("Amount should be greater than 0 ");
		return false;
	}
	$('#submitamount').prop("disabled",true);
	$.ajax({
		url:"phpfiles/projectfunction.php",
		data:formdata,
		type:"POST",
		dataType:"html",
		success:function(html)
		{
			var list=html.split('_');
			if(list[0]!="success")
			{
				alert(html);
				$('#error_div').html(html);
				$('#submitamount').prop("disabled",false);
				return false
			}
			else
			{
				submitstudentdetails();
				$('#error_div').html("");
				document.getElementById('refreshdetails_div').style.display = "";
				alert("Concession Done Successfully");
				return true;
			}
			
		}
		});
}
function refreshconcessiondetails()
{
	document.getElementById('refreshdetails_div').style.display = "none";
	$('#transactionamount').val("0");
	$('#submitamount').prop("disabled",false);
}
function transactionprint()
{
	var transactionid=document.getElementById('transactionid').value;
	window.open('phpfiles/printfunction.php?id='+transactionid+'&node=transaction_logs','_blank');
	return true;
}