
(function($)
{
  $.require = function(src, callback)
  {
	  var settings = 
    {
      url: src,
      async: false,
      crossDomain: true,
      dataType: 'script',
      cache: true,
      complete: function(jqXHR, status)
      {
    	 //alert('['+status+'] '+src);
       if(callback) callback();
      }
    };
    $.ajax(settings);
  };
})(jQuery);

$.require("/G87/config/siteConfig.json?js=1");
$.require("/G87/js/jquery.template.js");

//<h3>Console Hacks</h3>
//This file checks if the browser has console object defined. If not, it
//simply defines it with empty functions for console.log() and console.debug() 
//so that the browser does not throw any console related error.

if(typeof(console) == "undefined")
{
  console = {};
}

if(typeof(console.log) == "undefined")
{
  console.log = function(str)
  {
  };
}

if(typeof(console.debug) == "undefined")
{
  console.debug = function(str)
  {
  };
}

// <h2>Date prototype functions</h2>

// <h3>Date.getMonthString()</h3>
// This function returns the name of the month in string format like - 'January'.
Date.prototype.getMonthString = function()
{
  var monthInd = this.getMonth();
  switch(monthInd)
  {
    case 0: return 'January';
    case 1: return 'February';
    case 2: return 'March';
    case 3: return 'April';
    case 4: return 'May';
    case 5: return 'June';
    case 6: return 'July';
    case 7: return 'August';
    case 8: return 'September';
    case 9: return 'October';
    case 10: return 'November';
    case 11: return 'December';
    default: return 'Unknown';
  }
};

// <h3>Date.getDiffString(refDate)</h3>
// This function returns string with respect to the refDate object passed.
// The strings it returns are as follows
// <ul>
//   <li>Today, Yesterday - If the given date is one or two days away from refDate.</li>
//   <li>Like 28 Jan - If the difference is nore than two days.</li>
//   <li>Like 2009 - If the given date and refDate fall in different years.</li>
// </ul>
Date.prototype.getDiffString = function(refDate)
{
  if(typeof(refDate) == 'string')
  { 
    refDate = new Date(refDate);
  }
  
  if(this.getFullYear() == refDate.getFullYear())
  {
    if(this.getMonth() == refDate.getMonth())
    {
      if(this.getDate() == refDate.getDate())
      {
        return 'Today';
      }
      
      if((refDate.getDate() - this.getDate()) == 1)
      {
        return 'Yesterday';
      }
    }
    
    return this.getDate()+' '+this.getMonthString().substring(0,3);
  }
  
  return this.getDate()+' '+this.getMonthString().substring(0,3)+' '+this.getFullYear();
};

//<h3>Date.toXString()</h3>
//This function is like toString() for date except that it returns the string 
//which is cross browser compatible.
Date.prototype.toXString = function()
{
  var time = this;
  var timeStr = 
    (time.getMonth()+1)+"/"+
    time.getDate()+"/"+
    time.getFullYear()+" "+
    (time.getHours()<10?"0"+time.getHours():time.getHours())+":"+
    (time.getMinutes()<10?"0"+time.getMinutes():time.getMinutes());
  
  return timeStr;
};

//<h2>Array prototype functions</h2>

//<h3>Array.has(key)</h3>
//This function is to check if the 'key' is present in the given array. 
//It returns true if found and false otherwise.

Array.prototype.has = function(key)
{
  var array = this;
  for(x in array)
  {
    if(array[x] === key)
    {
      return true;
    }
    
    return false;
  }
};

//<h3>Array.sortByField(fieldName, [fieldType])</h3>
//If the given array is an array of (similar) objects, then use this function to sort it with 
//respect to one of the fields of the object 'fieldName'. Optionally, you can give the 'fieldType'
//otherwise, this function might treat it as a string or number by default. <b>NOTE:</b> 
//Currently 'fieldType' only supports 'Date'.

Array.prototype.sortByField = function(fieldName, fieldType)
{
  var array = this;
  var swap = function(i,j)
  {
    var x = array[i];
    array[i] = array[j];
    array[j] = x;
  };
  
  for(var i=0; i<array.length; i++)
  {
    for(var j=array.length-1; j>i; j--)
    {  
      var needle1, needle2;
      needle1 = array[j-1][fieldName];
      needle2 = array[j][fieldName];
      if(fieldType == 'Date')
      {
        needle1 = new Date(needle1);
        needle2 = new Date(needle2); 
      }
      if(needle1 > needle2)
        swap(j-1, j);
    }
  }
};