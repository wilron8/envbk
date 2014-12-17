/*
 * File: envitz/controller/MessageMenu.js
 *
 */

Ext.define('envitz.controller.MessageMenu', {
	extend: 'Ext.app.Controller',

	onSelectMsgClick: function( btn, e, eOpts ) {
		
		 var selBtn = btn.text;

	    btn.setText( ( selBtn === 'Select All' ? "Unselect" : "Select" ) + " All");
	    Ext.select( '.chkbox-msg-frm' ).each( function( el ) {
	        el.dom.checked = !( btn.text === 'Select All' );
	    });

	    Ext.getCmp( 'deleteselectedmsg' ).setDisabled( btn.text === 'Select All' );
	    Ext.getCmp( 'exportselectedmsg' ).setDisabled( btn.text === 'Select All' );

		/*if(btn.text === 'Select All'){
            btn.setText('Unselect All');
            Ext.select('.chkbox-msg-frm').each(function(el){
            	el.dom.checked = true;                                            
            });
            Ext.getCmp('deleteselectedmsg').setDisabled(false);
            Ext.getCmp('exportselectedmsg').setDisabled(false);                                        
                                        
        } else if(btn.text === 'Unselect All'){                                        
            btn.setText('Select All');
            Ext.select('.chkbox-msg-frm').each(function(el){
                el.dom.checked = false;                                            
            });
            Ext.getCmp('deleteselectedmsg').setDisabled(true);
            Ext.getCmp('exportselectedmsg').setDisabled(true);
        }*/
	},

	onDeleteSelectedMsgClick: function( btn, e, eOpts ) {	

		var countChkMsg = 0;
		var totalChkboxMsgSelected = [];		

		Ext.select( '.chkbox-msg-frm' ).each( function( el ) {
			if( el.dom.checked == true ) {
				totalChkboxMsgSelected.push( el.dom.value );
				countChkMsg++;
			}
                                                        
        });

        var totalMsgToRemove = countChkMsg < 2 ? countChkMsg + ' message' : countChkMsg + ' messages';

		Ext.MessageBox.confirm('Remove Message', 'Are you sure you want to remove ' + totalMsgToRemove + '?', function( btn ) {
            if(btn === 'yes'){
                //TODO
                //Ext.Msg.alert('Response','Successfully removed '+countChkMsg+' messages!');
                var box = Ext.MessageBox.wait( 'Removing ' + totalMsgToRemove + '. Please wait...', '' );
                Ext.Ajax.request({                	
					url: "/message/remSelectedMsg",
					method: 'POST',
					params: {					
							msgid : totalChkboxMsgSelected.toString()
					},			
					success: function ( data ) {

						var responseData = Ext.JSON.decode( data.responseText );						
						//window.location.reload();
						Ext.select( '.message_box' ).each( function( el ){
							var currChkBox = el.dom.childNodes[1].childNodes[1].childNodes[1].childNodes[1];
							if( currChkBox.checked == true ) {
								el.dom.remove();
							}							
				                                                        
				        });
						box.hide();

						var totalMsgs = Ext.select('.message_box').elements.length;		                
						if(totalMsgs < 1) {
							Ext.fly('main-menu-message').dom.innerHTML = '';
							var currContent = Ext.fly('mainArea').dom.innerHTML;
							Ext.fly( 'mainArea' ).dom.innerHTML = '<p>You have no messages. There are many people full of ideas just waiting for you to message them!</p>' + currContent;
						}

					},
					failure: function( data ) {
						box.hide();	
						Ext.Msg.alert('Response','There was an error when trying to process data!');			
						console.log(data);

					}
				});

                
            }                                       
        });		

	},	

	onExportMsgClick: function( btn, e, eOpts ) {	
		
		var countChkMsg = 0;
		var totalChkboxMsgSelected = [];		

		Ext.select( '.chkbox-msg-frm' ).each( function( el ) {
			if( el.dom.checked == true ) {
				totalChkboxMsgSelected.push( el.dom.value );
				countChkMsg++;
			}
                                                        
        });

        var totalMsgToExport = countChkMsg < 2 ? countChkMsg + ' message' : countChkMsg + ' messages';

		Ext.MessageBox.confirm('Export Message', 'Are you sure you want to export ' + totalMsgToExport + '?', function( btn ) {

            if( btn === 'yes' ) {                
           		
           		var box = Ext.MessageBox.wait('Exporting ' + totalMsgToExport + '. Please wait...', '');
                Ext.Ajax.request({                	
					url: "/message/exportThread",
					method: 'POST',
					params: {					
							msgid : totalChkboxMsgSelected.toString()
					},			
					success: function ( data ) {

						var responseData = Ext.JSON.decode( data.responseText );	
						box.hide();
						Ext.Msg.alert('Response','Successfully exported '+totalMsgToExport+'!');
					},
					failure: function ( data ) {
						box.hide();				
						Ext.Msg.alert('Response','There was an error when trying to process data!');
						console.log(data);

					}
				});

            }                                       
        });		

	},

	init: function(application) {
		this.control({
			"#selectAllMsgItem": {
				click: this.onSelectMsgClick
			},
			"#deleteSelectedMsgItem": {
				click: this.onDeleteSelectedMsgClick
			},	
			"#exportSelectedMsgItem": {
				click: this.onExportMsgClick
			},		
		});
	}

});
