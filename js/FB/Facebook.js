$.require('http://connect.facebook.net/en_US/all.js', function() {
  $(Facebook.init);
});

var Facebook =
{
  STATUS_CONNECTED: 'connected',
  EVENT_TYPE_LOGIN: 'login',
  EVENT_TYPE_LOGOUT: 'logout',
  
  status: null,  // 'connected': When user logs in facebook and authenticates your app
  user: {},
  
  // TODO: Before subscribing to the events, check the facebook session and populate
  //       Facebook.status and Facebook.user.
  init: function() {
    $('body').append($('<div id="fb-root"></div>'));
    FB.init(
    {
      appId: FB_APP_ID,
      status: true,
      cookie: true,
      xfbml: true,
      oauth: true
    });
    
    FB.getLoginStatus(function(data) {
      if (data.authResponse) {
        Facebook.userLoggedIn(data);
      } else {
        Facebook.userLoggedOut(data);
      }
    });
    FB.Event.subscribe('auth.login', function(data) {
      Facebook.userLoggedIn(data);
    });
    
    FB.Event.subscribe('auth.logout', function(data) {
      Facebook.userLoggedOut(data);
    });
  },
  
  userLoggedIn: function(data) {
    if(data.status != Facebook.STATUS_CONNECTED) return;
      
    FB.api('/me', function(response) {
      Facebook.status = data.status;
      Facebook.user.id = data.authResponse.userId;
      Facebook.user.name = response.name;
      Facebook.user.username = response.username;
      Facebook.user.obj = response;
      
      Facebook.trigger(Facebook.EVENT_TYPE_LOGIN);
    });
  },
  
  userLoggedOut: function(data) {
    Facebook.trigger(Facebook.EVENT_TYPE_LOGOUT);
  },
  
  requestLogin: function()
  {
    FB.login();
  },
  
  requestLogout: function() {
    FB.logout();
  },
  
  // TODO: Show user display pic along with the name of the user
  panel: {
    template: 
      '<span class="facebook-panel-button hidden">' +
        '<div class="fb-login-button inline">Connect</div>' +
      '</span>'+
      '<span class="facebook-panel-user hidden">' +
        '<strong class="facebook-panel-user-name"></strong> | ' +
        '<a href="javascript:Facebook.requestLogout()">Logout</a>' +
      '</span>',
    render: function(sel) {
      $(Facebook.panel.template).appendTo(sel);
      
      if(Facebook.status == Facebook.STATUS_CONNECTED) {
        Facebook.panel.showUser();
      } else {
        Facebook.panel.showButton();
      }
      
      Facebook.on('logout', Facebook.panel.showButton);
      Facebook.on('login', Facebook.panel.showUser);
    },
    showButton: function() {
      $('.facebook-panel-button').show();
      $('.facebook-panel-user')
        .hide()    
        .find('.facebook-panel-user-name').text(' ');
    },
    showUser: function() {
      $('.facebook-panel-button').hide();
      $('.facebook-panel-user')
        .show()    
        .find('.facebook-panel-user-name').text(Facebook.user.name);
    }
  },
  
  // TODO: try to use the new jquery binders .on() and .off() for binding
  //       and unbinding the events.
  on: function(eventType, handler) {
    $(window).bind('facebook:'+eventType, handler);
  },
  
  off: function(eventType, handler) {
    $(window).unbind('facebook:'+eventType, handler);
  },
  
  trigger: function(eventType) {
    $(window).triggerHandler('facebook:'+eventType);
  }
};