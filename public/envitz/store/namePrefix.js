/*
 * File: envitz/store/namePrefix.js
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

Ext.define('envitz.store.namePrefix', {
	extend: 'Ext.data.Store',
	alias: 'store.namePrefix',

	requires: [
		'envitz.model.simpleCombo',
		'Ext.data.proxy.Memory',
		'Ext.data.reader.Array'
	],

	constructor: function(cfg) {
		var me = this;
		cfg = cfg || {};
		me.callParent([Ext.apply({
			model: 'envitz.model.simpleCombo',
			storeId: 'namePrefixSID',
			data: [
				{
					id: 1,
					text: 'Mr.'
				},
				{
					id: 2,
					text: 'Ms.'
				},
				{
					id: 3,
					text: 'Dr.'
				},
				{
					id: 4,
					text: '(N/A)'
				}
			],
			proxy: {
				type: 'memory',
				reader: {
					type: 'array'
				}
			}
		}, cfg)]);
	}
});