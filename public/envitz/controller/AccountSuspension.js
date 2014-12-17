/*
 * File: envitz/controller/AccountSuspension.js
 *
 */

Ext.define('envitz.controller.AccountSuspension', {
	extend: 'Ext.app.Controller',
	
	onSuspendedClick: function( button, e, eOpts ) {	

		var svrMsg;
		var reason = Ext.getCmp('reasonTxt').getValue();
		if (reason.length < 4) {

			Alert("Deactivate: error", "Please provide a reason.");

		} else {

			button.setText('Processing...');
			button.setDisabled(true);

			Ext.Ajax.request({
				url: '/people/suspend',
				params: {
					reason: reason
				},
				success: function (response) {
					svrMsg = Ext.JSON.decode(response.responseText);
					if (svrMsg.success === true) {
						/*
						Ext.Msg.show({
							title: 'Confirmation',
							msg: 'Account termination is successful',
							minWidth: 300,
							icon: Ext.Msg.INFO,
							buttons: Ext.Msg.OK,
							fn: function() {
								window.location = "/logout";
							}
						});
						*/
					}else{
						Alert('Confirmation', "Account Deactivation failed. Please check your connection and try again.");
					}
				},
				failure: function () {
					Msg('Connection Error', 'Please check your connection.');
				},
				callback: function () {
					button.setText('Deactivate');
					button.setDisabled(false);
				}
			});
		}

	},	
	

	init: function(application) {
		this.control({
			"#btnDeactivate": {
				click: this.onSuspendedClick
			}		
		});
	}

});
