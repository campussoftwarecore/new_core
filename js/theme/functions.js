$( ".has-supersub" ).on( "click", function() {
	var idname=$(this).attr("id");
	var linkname=$(this).attr("name");
	var list=idname.split("_");
	var menuname="menu";
	for(var k=1;k<list.length;k++)
	{
		menuname+="_"+list[k];
	}
	var iconname=(menuname).replace("menu_","icon_");
	var list=linkname.split("_");
	var modulename="menu";
	for(var k=1;k<list.length;k++)
	{
		modulename+="_"+list[k];
	}
	var modulenamelist=document.getElementsByName(modulename);
	if($("#"+menuname).css("display")=="none")
	{
		$("#"+menuname).css("display","block");
		$("#"+iconname).removeClass("icon-chevron-up");
		$("#"+iconname).addClass("icon-chevron-down");
		for(var i=0;i<modulenamelist.length;i++)
		{
			if(modulenamelist[i].id!=menuname)
			{
				iconname=(modulenamelist[i].id).replace("menu_","icon_");
				$("#"+iconname).removeClass("icon-chevron-down");
				$("#"+iconname).addClass("icon-chevron-up");
				$("#"+modulenamelist[i].id).hide("fast");
			}
			
		}
	}
	else
	{
		if(modulenamelist.length>1)
		{
			$("#"+iconname).removeClass("icon-chevron-down");
			$("#"+iconname).addClass("icon-chevron-up");
			$("#"+menuname).hide("fast");
		}		
	}
});