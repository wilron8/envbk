/*
 * File: envitz/controller/AccountTermination.js
 *
 */

Ext.define('envitz.controller.AccountTermination', {
	extend: 'Ext.app.Controller',
	
	onTerminateClick: function( button, e, eOpts ) {	

		var svrMsg;
		var reason = Ext.getCmp('txtReason').getValue();
		if (reason.length < 10) {

			Alert("TERMINATE: error", "Please provide a reason.");

		} else {

			button.setText('Processing...');
			button.setDisabled(true);

			Ext.Ajax.request({
				url: '/people/terminate',
				params: {
					reason: reason
				},
				success: function (response) {
					svrMsg = Ext.JSON.decode(response.responseText);
					if (svrMsg.success === true) {
						Ext.Msg.show({
							title: 'Confirmation',
							msg: 'Account was successfully terminated. /n You will be logged out.',
							minWidth: 300,
							icon: Ext.Msg.INFO,
							buttons: Ext.Msg.OK,
							fn: function() {
								window.location = "/logout";
							}
						});
					}else{
						Alert('Confirmation', "Account termination failed. Please check your connection and try again.");
					}
				},
				failure: function () {
					Msg('Connection Error', 'Please check your connection.');
				},
				callback: function () {
					button.setText('Terminate Account');
					button.setDisabled(false);
				}
			});
		}

	},	
	

	init: function(application) {
		this.control({
			"#btnTerminate": {
				click: this.onTerminateClick
			}		
		});
	}

});
