
/*
 * File: envitz/view/MessageSubMenu.js
 * 
 */

Ext.define('envitz.view.MessageSubMenu', {
    extend: 'Ext.button.Button',

    requires: [
        'Ext.menu.Menu',
        'Ext.menu.Item'
    ],

    ideaId: 0,
    frame: false,
    arrowCls: '  ',
    iconAlign: 'top',
    iconCls: 'icon-dropdown',
    menuAlign: 'tr-br?',
    scale: 'medium',
    text: '',

    initComponent: function() {
        var me = this;

        Ext.applyIf(me, {
            menu: {
                xtype: 'menu',
                width: 140,
                items: [
                        { 
                    
                            xtype: 'menuitem',                            
                            /*listeners : {
                                click: function(btn, e, eOpts) {
                                    Ext.MessageBox.confirm('Remove Message', 'Are you sure you want to remove this message?', function(btn){
                                       if(btn === 'yes'){
                                           //TODO
                                           Ext.Msg.alert('Response','Successfully removed message!');
                                       }                                       
                                    });
                                }
                            },*/
                            itemId: 'removeMsgItem',
                            text: 'Remove'
                        
                    
                        }
                    
                ]
            },
            listeners: {
                mouseout: {
                    fn: me.onButtonMouseOut,
                    scope: me
                },
                mouseover: {
                    fn: me.onButtonMouseOver,
                    scope: me
                }
            }
        });

        me.callParent(arguments);
    },
    
    onButtonMouseOut: function(button, e, eOpts) {
        if (button.hasVisibleMenu()) {
            //button.hideMenu();
        }
    },

    onButtonMouseOver: function(button, e, eOpts) {
        if (!button.hasVisibleMenu()) {
            button.showMenu();
        }

        //console.log(e);
    },

    /*removeMessage: function() {
        var me = this;

        Ext.Ajax.request({
            url: LApp.ideaMenu.remove.url,
            success: function (response) {
                var svrMsg = Ext.JSON.decode(response.responseText);
                if (svrMsg.success === true) {
                    Ext.Msg.alert('Information','The message has been successfuly removed.', function(){
                        window.location = LApp.basePath + "/idea";
                    });
                }else{
                    Ext.Msg.alert('There is a problem removing this idea.');
                }
            },
            failure: function () {
                Alert('Error','Network Operation Timed-out. Your connection to the server was lost. Please check your Internet connection and try again later.');
            }

        });
    },*/

    

});