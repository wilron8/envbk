/*
 * File: envitz/view/CloseProject.js
 *
 * This file was generated by Sencha Architect version 3.0.4.
 * http://www.sencha.com/products/architect/
 *
 * This file requires use of the Ext JS 4.2.x library, under independent license.
 * License of Sencha Architect does not include license for Ext JS 4.2.x. For more
 * details see http://www.sencha.com/license or contact license@sencha.com.
 *
 * This file will be auto-generated each and everytime you save your project.
 *
 * Do NOT hand edit this file.
 */

Ext.define('envitz.view.CloseProject', {
	extend: 'Ext.window.Window',

	requires: [
		'envitz.view.envitzEditor',
		'Ext.form.Panel',
		'Ext.slider.Single',
		'Ext.form.field.HtmlEditor',
		'Ext.button.Button',
		'Ext.form.field.Hidden'
	],

	height: 348,
	width: 428,
	layout: 'fit',
	title: 'Close Project',
	titleAlign: 'center',

	initComponent: function() {
		var me = this;

		Ext.applyIf(me, {
			items: [
				{
					xtype: 'form',
					border: false,
					header: false,
					title: 'My Form',
					items: [
						{
							xtype: 'container',
							padding: 20,
							style: {
								backgroundColor: '#fff'
							},
							defaults: {
								labelWidth: '75 px'
							},
							items: [
								{
									xtype: 'container',
									html: 'You have selected to close your project. Please report the result and output of your project.',
									margin: '0 0 15 0'
								},
								me.processProj_progress({
									xtype: 'slider',
									itemId: 'proj_progress',
									width: 374,
									fieldLabel: 'Result',
									name: 'proj_progress',
									value: 48
								}),
								{
									xtype: 'envitzEditor',
									height: 150,
									itemId: 'outcome',
									minHeight: 100,
									minWidth: 374,
									width: 374,
									fieldLabel: 'Outcome',
									name: 'outcome'
								},
								{
									xtype: 'container',
									margin: '15 0',
									style: {
										textAlign: 'center'
									},
									layout: {
										type: 'hbox',
										align: 'stretch'
									},
									items: [
										me.processCloseProject({
											xtype: 'button',
											itemId: 'closeProject',
											margin: '0 30 0 75',
											width: 100,
											text: 'Close Project'
										}),
										me.processCancel({
											xtype: 'button',
											handler: function(button, e) {
												this.close();
											},
											width: 75,
											text: 'Cancel'
										})
									]
								},
								me.processProj_id({
									xtype: 'hiddenfield',
									fieldLabel: 'Label',
									name: 'proj_id'
								})
							]
						}
					]
				}
			]
		});

		me.callParent(arguments);
	},

	processProj_progress: function(config) {
		config.value=this.proj_progress;
		return config;
	},

	processCloseProject: function(config) {
		config.scope = this;
		config.handler= this.closeProjectHandler;
		return config;
	},

	processCancel: function(config) {
		config.scope = this;
		return config;
	},

	processProj_id: function(config) {
		config.value = this.proj_id;
		return config;
	},

	closeProjectHandler: function(item, e) {
		var me = this,
			form = this.down('form').getForm();

		form.submit({
			waitMsg: "Processing....",
			url:'/project/close',
			success: function (response) {
				me.close();
				Ext.Msg.show({
					title: 'Information',
					msg: 'The project is successfully closed.',
					buttons: Ext.Msg.OK,
					icon: Ext.Msg.INFORMATION,
					fn: function () {
						window.location = "/project/view/" + me.proj_id;
					}
				});
			},
			failure: function () {
				Alert('Error', 'Network Operation Timed-out. Your connection to the server was lost. Please check your Internet connection and try again later.');
			}
		});
	}

});