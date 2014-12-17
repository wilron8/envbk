/*Ext.create('Ext.menu.Menu', {
    width: 100,
    height: 110,
    floating: false,  // usually you want this set to True (default)
    renderTo: Ext.getBody(),  // usually rendered by it's containing component
    items: [{
        xtype: 'menucheckitem',
        text: 'select all'
    },{
        xtype: 'menucheckitem',
        text: 'select specific'
    },{
        iconCls: 'add16',
        text: 'icon item'
    },{
        text: 'regular item'
    }]
});*/

/*
 * File: envitz/view/MessageMenu.js
 *
 */

Ext.define('envitz.view.MessageMenu', {
    extend: 'Ext.button.Button',

    requires: [
        'Ext.menu.Menu',
        'Ext.menu.Item'
    ],

    itemId: 'msgMenuForm',
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
                            itemId: 'selectAllMsgItem',
                            id: 'selectallmsg',
                            text: 'Select All'
                      }, 
                      {  
                    
                            xtype: 'menuitem',                           
                            /*listeners : {
                                click: function(btn, e, eOpts) {
                                    Ext.Msg.alert('Response','Successfully removed messages!');
                                }
                            },*/
                            itemId: 'deleteSelectedMsgItem',
                            id: 'deleteselectedmsg',
                            text: 'Remove',
                            disabled: true
                      },
                      {  
                    
                            xtype: 'menuitem', 
                            itemId: 'exportSelectedMsgItem',
                            id: 'exportselectedmsg',
                            text: 'Export to Email',
                            disabled: true
                      },                    
                    
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

    processSelectAllMsgItem: function(config) {
        /*if (!LApp.ideaMenu.unfollow) {
            config.hidden = true;
        }*/
        config.scope = this;
        return config;
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

    /*removeIdea: function() {
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

    loadWait: function() {
        try {
            Ext.getBody().mask("Please wait... ロード中です。お待ちくださませ。");
        } catch (e) {
            Ext.Error.raise(' Message menu failure\n' + e.description);
        }
    }

});