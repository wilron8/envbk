/*
 * File: envitz/store/profileAddress.js
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

Ext.define('envitz.store.profileAddress', {
	extend: 'Ext.data.Store',

	requires: [
		'envitz.model.address',
		'Ext.data.proxy.Ajax',
		'Ext.data.reader.Json',
		'Ext.data.writer.Json'
	],

	constructor: function(cfg) {
		var me = this;
		cfg = cfg || {};
		me.callParent([Ext.apply({
			autoLoad: true,
			model: 'envitz.model.address',
			storeId: 'profileAddress',
			proxy: {
				type: 'ajax',
				url: '/people/address/api/get',
				reader: {
					type: 'json',
					root: 'data'
				},
				writer: {
					type: 'json'
				}
			}
		}, cfg)]);
	}
});