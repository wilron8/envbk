/*
 * File: envitz/view/CommentInsert.js
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

Ext.define('envitz.view.CommentInsert', {
	extend: 'Ext.form.Panel',

	requires: [
		'envitz.view.envitzEditor',
		'Ext.Img',
		'Ext.form.field.Hidden',
		'Ext.form.field.HtmlEditor',
		'Ext.button.Button',
		'envitz.view.ReLogin'
	],

	border: false,
	minHeight: 200,
	layout: 'hbox',
	anchorSize: 1,
	method: 'POST',
	timeout: 15,

	initComponent: function() {
		var me = this;

		me.initialConfig = Ext.apply({
			method: 'POST',
			timeout: 15
		}, me.initialConfig);

		Ext.applyIf(me, {
			items: [
				{
					xtype: 'container',
					padding: 5,
					style: {
						textAlign: 'center'
					},
					layout: 'anchor',
					items: [
						me.processCommentUser({
							xtype: 'image',
							height: 64,
							itemId: 'commentUser',
							width: 64,
							src: '/images/photo001.png'
						}),
						me.processDisplayName({
							xtype: 'container',
							html: '{LApp.user.displayName}',
							margin: '5 0 0 0'
						})
					]
				},
				{
					xtype: 'container',
					items: [
						{
							xtype: 'hiddenfield',
							id: 'iComm_ideaId',
							itemId: 'iComm_ideaId',
							fieldLabel: 'Label',
							name: 'id',
							inputId: 'iComm_ideaId'
						},
						{
							xtype: 'envitzEditor',
							height: 100,
							id: 'commentEditor',
							minHeight: 100,
							name: 'comment'
						},
						{
							xtype: 'button',
							itemId: 'btnComment',
							maxWidth: 700,
							minWidth: 400,
							width: 600,
							text: 'Comment',
							listeners: {
								click: {
									fn: me.onCommentClick,
									scope: me
								}
							}
						}
					]
				}
			]
		});

		me.callParent(arguments);
	},

	processCommentUser: function(config) {
		if (LApp.user.photo !== false){
			config.src = LApp.user.photo;
		}

		return config;
	},

	processDisplayName: function(config) {
		if (LApp.user.displayName){
			config.html = LApp.user.displayName;
		}

		return config;
	},

	onCommentClick: function(button, e, eOpts) {
		var newComm = "<!-- Begin comment block -->",
			//timeStamp = new Date(),
			commForm = this.getForm(),
			commBtn = this.down('[itemId=btnComment]'),
			userComm = this.down('#commentEditor'),
			// Remove excessive carriage returns that some users like to add...
			comValue = userComm.getValue().replace(/(<br>)+/g, '<br>');
			// Insert rel="nofollow" and target="_blank" in anchor element
			comValue = elemInsertAttr(userComm.getValue(),['rel="nofollow"','target="_blank"']);
			//comValue = userComm.getValue().replace(/<a [^>]+>([^<]+)<\/a>/g, "<a href=\"$1\" rel=\"nofollow\">$1</a>");
			//console.log(comValue);
			//filterAnchor = comValue.split(" ");
			//var regEx = /<a [^>]+>([^<]+)<\/a>/g;
			//console.log(filterAnchor.test(regEx));
			//var i;
			//for (i = 0; i < filterAnchor.length; i++) {
			//	if(filterAnchor.search(regEx) !=-1){
			//		console.log(filterAnchor[i]);
			//	}
			    //filterAnchor[i].setAttribute("rel", "nofollow");
			//}

			/*
			var x = document.getElementById("commentEditor");
			var y = x.getElementsByTagName("a");
			var i;
			for (i = 0; i < y.length; i++) {
			    y[i].setAttribute("rel", "nofollow");
			}
			console.log(y);*/

			//document.getElementsByTagName("a")[0].setAttribute("rel", "nofollow");
			//document.getElementById("commentEditor").setAttribute("rel", "nofollow");

		if (Ext.util.Format.stripTags(comValue).length < 3) {
			Alert("User Comments", "Please provide a comment longer than 3 characters.");

		} else {

			commBtn.setText("Sending...");
			commBtn.setDisabled(true); //prevent rabbit clickers.

			commForm.submit({
				success: function (bForm, o) {
					//filterAnchor = comValue;
					//var regEx = /<a [^>]+>([^<]+)<\/a>/g;
					/*if(filterAnchor.search(regEx) !=-1){

					}*/				
					

					newComm += "<div id='userComm" + o.result.id + "' data-isCommenter=\"1\" data-ideaId=\"" + LApp.thisPage.mainID + "\" data-commentId=\"" + o.result.id + "\">";
					newComm += "<div class=\"res_photo\"><img style=\"height:32px;width:32px;\" src=\"" + LApp.user.photo + "\" alt=\"" + LApp.user.displayName + "\" title=\"" + LApp.user.displayName + "\"></div>";
					//Ext.Date.format(timeStamp, Ext.Date.patterns.ISO8601Long)
					newComm += "<DL><dt><span class=\"commenter\"><a href=\"/people/" + LApp.user.id + "\" target=\"_blank\">" + LApp.user.displayName + "</a></span><span class=\"time\">" + o.result.dateTime + "</span></dt>";
					newComm += "<DD class='body'>" + comValue + "</DD></DL>";
					newComm += "<DIV id=\"navi_" + o.result.id + "\" class='navi'></div>";
					newComm += "</div><!-- //END comment block -->";

					Ext.get("userCommsEnd").insertHtml('beforeBegin', newComm);

					LApp.createNAVI(o.result.id);
					userComm.reset();
					commBtn.setDisabled(false);
					commBtn.setText("Comment");

					//document.getElementsByClassName("body").setAttribute("rel", "nofollow");
					//var x = document.getElementsByClassName("body");
					//var y = x.getElementsByTagName("a");
					//var i;
					/*for (i = 0; i < y.length; i++) {
					    y[i].setAttribute("rel", "nofollow");
					}*/
					//console.log(x);

				},
				failure: function (bForm, action) {
					commBtn.setDisabled(false);
					commBtn.setText("Comment");
					try { //FireFox goes here on error
						if (action && action.result) {
							if(Ext.isGecko){ console.log("'Comment Manager'", action.result.error);}
							console.log(action.result);
							if(action.result.relogin == 1){
								//Ext.require('envitz.view.ReLogin'); add this in require section at the top
								reloginForm = Ext.create('envitz.view.ReLogin');
                				reloginForm.show();
                				//Alert('Comment Manager', action.result.error);
							} else {
								Alert('Comment Manager', action.result.error);
							}
							
						} else {
							if(Ext.isGecko){ console.log("'Comment Manager' : check connectivity");}
							Alert('Comment Manager', "Network Operation Timed-out. Your connection to the server was lost. Please check your Internet connection and try again later.");
						}
					} catch(e) { Ext.Error.raise(' Comment save failure\n' + e.description); }
				}
			});
		}
	}

});