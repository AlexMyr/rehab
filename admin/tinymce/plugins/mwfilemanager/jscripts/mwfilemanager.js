function mwFileManager(){
	this.filed2Update="";
	this.openWindow=null;
	this.prevInstance='';
	mwFileManager.prototype.browserCallback=function(field_name, url, type, win){		
		this.openWindow=win
		this.field2Update=field_name;
		this.prevIinstance=tinyMCE.windowArgs['editor_id'];
			var template = new Array();
            var editor=tinyMCE.getInstanceById(tinyMCE.getWindowArg('editor_id'));   
			template['file']   = '../../plugins/mwfilemanager/index.php?type='+type;
			template['width']  = 710;
			template['height'] = 535;
		    template['close_previous']='no';			
//			template['scrollbars']=true;
			//debugger;
			tinyMCE.openWindow(template, {editor_id : editor, inline : "yes",'scrollbars': 'no'});

	//console.log(tinyMCE);
	};
	mwFileManager.prototype.updateField=function(url){
	
		this.openWindow.document.forms[0].elements[this.field2Update].value=url;
		try {
//			debugger;
			this.openWindow.document.forms[0].elements[this.field2Update].onchange();
			this.openWindow.tinyMCE.windowArgs.editor_id=this.prevIinstance;
		} catch (e) {
			// Skip it
		}	
	};
};//end object
var mwwFileManager = new mwFileManager();
