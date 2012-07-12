$.require('/G87/js/remoteScript.php?src='+escape('http://www.google.com/jsapi'));
google.load('friendconnect', '0.8');

$(function()
{
  $('head').append('<link rel="stylesheet" href="/G87/js/GFC/GFC.css" />');
  GFC.init();
});
var GFC = 
{

  // GFC Events
  SIGN_IN_EVENT: 'GFC.signInEvent',
  SIGN_OUT_EVENT: 'GFC.signOutEvent',
  
  // GFC Globals
  //   user - is a simplified object for the logged in user.
  //   viewer - is a GFC object for the logged in user.
  //   members - is an array of GFC objects of all site members.
  user: null,
  viewer: null,
  members: null,
  
  init: function()
  {
    google.friendconnect.container.loadOpenSocialApi(
    {
      site: GFC_SITE_ID,
      onload: function(securityToken) {
        secTok = securityToken;
        // Create a request to grab the current viewer.
        var req = opensocial.newDataRequest();
        req.add(req.newFetchPersonRequest('VIEWER'), 'viewer');
        
        var idSpec = opensocial.newIdSpec({'userId': 'OWNER', 'groupId': 'FRIENDS'});
        req.add(req.newFetchPeopleRequest(idSpec), 'members');
        
        // Sent the request
        req.send(function(resp)
        {
          viewerResp = resp.get('viewer');
          if(viewerResp.hadError())
          {
            // User has signed out
           
            // Update the user status as guest
            GFC.user = {guest: true};
            
            // call all the signOut handlers
            if(GFC.afterSignOut)
            {
              GFC.afterSignOut();
              GFC.afterSignOut = null;
            }
            
            /*for(var i=0; i<GFC.signOutHandlers.length; i++)
            {
              var callback = GFC.signOutHandlers[i];
              callback();
            }*/
            
            $(window).trigger(GFC.SIGN_OUT_EVENT);
          }
          else
          {
            // User is signed in
            
            // Update the user status with user details
            var viewer = viewerResp.getData();
            var user = 
            {
              thumbnailUrl: viewer.getField('thumbnailUrl'),
              name: viewer.getDisplayName(),
              id: viewer.getId()
            };
            
            GFC.user = user;
            GFC.viewer = viewer;
            
            // Call all the signIn handlers
            if(GFC.afterSignIn)
            {
              GFC.afterSignIn();
              GFC.afterSignIn = null;
            }
            
            /*for(var i=0; i<GFC.signInHandlers.length; i++)
            {
              var callback = GFC.signInHandlers[i];
              callback();
            }*/
            
            $(window).trigger(GFC.SIGN_IN_EVENT);
            
          }
          
          var membersResp = resp.get('members');
          if(!membersResp.hadError())
          {
            var people = membersResp.getData();
            GFC.members = new Array();
            people.each(function(member)
            {
              GFC.members[member.getId().toString()] = member;
            });
          }
          
        });
      }
    });
  },
  
  signInHandlers: [],
  signOutHandlers: [],
  afterSignIn: null,
  aftgerSignOut: null,
  
  onSignOut: function(callback)
  {
    //GFC.signOutHandlers.push(callback);
    
    $(window).bind(GFC.SIGN_OUT_EVENT, callback);
    
    // In case at the time of binding if it is already known that user is signed out, 
    // then call the callback.
    if(GFC.user && GFC.user.guest)
    {
      callback();
    }
  },
  onSignIn: function(callback)
  {
    //GFC.signInHandlers.push(callback);
    
    $(window).bind(GFC.SIGN_IN_EVENT, callback);
    
    // In case at the time of binding if it is already known that the user is signed in,
    // then call the callback.
    if(GFC.user && GFC.user.id)
    {
      callback();
    }
  },
  
  renderSignInButton: function(container)
  {
    container = $(container);
    
    GFC.onSignIn(function()
    {
      GFC.showUserPanel(container);
    });
    GFC.onSignOut(function()
    {
      GFC.showSignInButton(container);
    });
  },
  userSignedIn: function(viewer)
  {
    var user = 
    {
      thumbnailUrl: viewer.getField('thumbnailUrl'),
      name: viewer.getDisplayName(),
      id: viewer.getId()
    };
    
    GFC.user = user;
    GFC.showUserPanel();
  },
  showSignInButton: function(container)
  {
    var loginButtonTemplate = 
      '<div class="GFC-loginButton">' +
        '<button onclick="GFC.requestSignIn()"><img src="http://www.google.com/favicon.ico" /> Connect</button>' +
      '</div>';
    container.html('');
    container.append(loginButtonTemplate);
  },
  showUserPanel: function(container)
  {
    var userPanelTemplate = 
      '<div class="GFC-userPanel">' +
          '<img class="GFC-userPanel-pic" src="${thumbnailUrl}" onclick="google.friendconnect.showMemberProfile(GFC.user.id)"/>' +
          '<div class="GFC-userPanel-name">' +
            '<nobr><strong class="GFC-userPanel-name">${name}</strong></nobr>' +
          '</div>' +
          '<div class="GFC-userPanel-options"><nobr>' +
            '<a href="javascript:;" onclick="google.friendconnect.requestSettings()">Settings</a> | ' +
            '<a href="javascript:;" onclick="GFC.requestSignOut()">Sign out</a>' +
          '</nobr></div>' +
      '</div>';
    
    container.html('');
    container.append($.template(userPanelTemplate), GFC.user);
  },
  requestSignIn: function(callback)
  {
    if(callback)
    {
      //GFC.afterSignIn = callback;
      var callbackFunction = function()
      {
        callback();
        $(window).unbind(GFC.SIGN_IN_EVENT, callbackFunction);
      };
      
      $(window).bind(GFC.SIGN_IN_EVENT, callbackFunction);
    }
    //GFC.performAction(GFC.SIGN_IN_ACTION);
    google.friendconnect.requestSignIn();
  },
  requestSignOut: function()
  {
    //GFC.performAction(GFC.SIGN_OUT_ACTION);
    google.friendconnect.requestSignOut();
  },  
  
  requestSettings: function()
  {
    google.friendconnect.requestSettings();
  },
  checkUserSignInStatus: function(signedIn, signedOut)
  { 
    GFC.onSignIn(signedIn);
    GFC.onSignOut(signedOut);
  },
  
  getUsers: function(userIds, callback)
  {
    var req = opensocial.newDataRequest();
    req.add(req.newFetchPersonRequest('09679680591901462645'), 'person');
    req.send(function(resp)
    {
      resp = resp.get('person');
      if(resp.hadError())
      {
      }
      else
      {
        var person = resp.getData();
        alert(person.getDisplalyName()); 
      }
    });
    /*if(!callback || !userIds)
      return;
      
    var req = opensocial.newDataRequest();
    for(var i=0; i<userIds.length; i++)
    {
      req.add(req.newFetchPersonRequest('VIEWER'), 'viewer');
    }*/
  },
  
  getMembers: function(userIds)
  {
    if(GFC.members == null)
      return null;
      
    var members = new Array();
    
    for(var i=0; i<userIds.length; i++)
    {
      var userId = userIds[i];
      
      for(x in GFC.members)
      {
        if(x == userId)
        {
          members.push(GFC.members[x]);
        }
      }
    }
    
    return members;
  }
};