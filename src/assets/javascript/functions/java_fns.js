// JavaScript Document
<!--

var ival = 0;
var active_cutoff = 20 + 1;

function updateTime(){
	
	var currentTime = new Date()
	var gmhour = currentTime.getUTCHours()
	var gmminute = currentTime.getUTCMinutes()
	
	if(gmminute < 10){
		gmminute = "0" + gmminute 
	}
	
	document.getElementById("jstime").innerHTML = "(" + gmhour + " : " + gmminute + " Zulu)"
	
	setTimeout("updateTime()", 1000) 
	
}





function getUrl(url,fn) {
  if (url && fn) {
	  
	//change the news item we are calling. 
	if(ival < 4){
		ival++;
	}
	else{
		ival = 0;	
	}
	
	var tmpurl = url+"/"+ival;
	
	//document.getElementById("jspilotnews").innerHTML = "<b>" + tmpurl + "</b> <br /> ";
	  
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", tmpurl, true);
    xmlhttp.onreadystatechange = function() {
      if (xmlhttp.readyState == 4) {
        	fn(xmlhttp.responseXML, url);
      }
    };
    xmlhttp.send(null);
  } else {
    //alert('url or function not specified!');
  }
}
 
//  trim functions
//  will trim whitespace from strings
String.prototype.trim=function(){
  return this.replace(/^\\s*|\\s*$/g,'');
}
String.prototype.ltrim=function(){
  return this.replace(/^\\s*/g,'');
}
String.prototype.rtrim=function(){
  return this.replace(/\\s*$/g,'');
}
 
//  CheckResultIsOk
//  Will check the XML data you've return for <error_code>x</error_code>
//  and returns the error to the user if found.
function checkResultIsOk(xml) {
  if (!xml) {
    //xml data was empty. Result was not ok.
	//recall XML
	
	//alert('!XML');  
    return false;
  }
  if (xml.getElementsByTagName('error_code')[0].firstChild.data &&
    Math.abs(xml.getElementsByTagName('error_code')[0].firstChild.data.trim()) > 0) {
    //error has been supplied
    //alert('Error ' + xml.getElementsByTagName('error_code')[0].firstChild.data + ': ' +
     // xml.getElementsByTagName('error')[0].firstChild.data.trim());
    return false;
  } else {
    //no errors!
    return true;
  }
}

function processPilot(xml, url) {
  //check result is ok
  //alert(xml);
  if (checkResultIsOk(xml)) {
    //get the item count
    var title = xml.getElementsByTagName('title')[0].firstChild.data.trim();
	var bod = xml.getElementsByTagName('bod')[0].firstChild.data.trim();
	
	if(active_cutoff > 0){
		document.getElementById("jspilotnews").innerHTML = "<b>" + title + "</b> <br /> " + bod;
	}
	else{
		//display inactive message
		document.getElementById("jspilotnews").innerHTML = "<b>Inactive</b> <br /> We notice that you haven't been active for a while, so we turned off the information feed to save bandwidth - refresh the page to reactivate it.";
	}
	
	//fade in
	fade('jspilotnews', url);
	 
	
  }
  else{
	//alert('XML data failed validation!');  
	//fade in
	fade('jspilotnews', url);
  }
}

function processPilotlong(xml) {
  //check result is ok
  if (checkResultIsOk(xml)) {
    //get the item count
    var item_count = Math.abs(xml.getElementsByTagName('item_count')[0].firstChild.data.trim());
 
    //get the items
    var items = xml.getElementsByTagName('items');
    for (i=0;i<items.length;i++) {
    //send an alert of the data received
      alert (i + "  Data recieved:\\n" +
        "id: " + items[i].getElementsByTagName('id')[0].firstChild.data.trim() + "\\n" +
        "name: " + items[i].getElementsByTagName('name')[0].firstChild.data.trim() + "\\n" +
        "price: " + items[i].getElementsByTagName('price')[0].firstChild.data.trim() + "\\n");
    } //end i for		
  } //end check result
} //end function 




function getPilotNews(url){
	
	//initiate fade out 
	fade('jspilotnews', url);
	
	//grab updated data
	//getUrl(url,processPilot);
	
	//fade('jspilotnews');
	//initiate fade in in 2 seconds
	//setTimeout("fade('jspilotnews')", 2000)
	
	newPilotObject = new callPilotNews();
	newPilotObject.url = url;
	
	//reduce inactive count
	if(active_cutoff > 0){
		active_cutoff--;
	}
	   
	//callself if inactive not at 0;
	if(active_cutoff > 0){
		setTimeout("newPilotObject.callSelf()", 15000);
	}
	
	
}

function callPilotNews(){

	this.url = "ajax/pilotnews";
	this.callSelf = function()
	{
		getPilotNews(this.url);	
	}

}


var TimeToFade = 1000.0;

function fade(eid, url)
{
  var element = document.getElementById('jspilotnews');
  if(element == null)
    return;
   
  if(element.FadeState == null)
  {
    if(element.style.opacity == null
        || element.style.opacity == ''
        || element.style.opacity == '1')
    {
      element.FadeState = 2;
    }
    else
    {
      element.FadeState = -2;
	  
    }
  }
   
  if(element.FadeState == 1 || element.FadeState == -1)
  {
    element.FadeState = element.FadeState == 1 ? -1 : 1;
    element.FadeTimeLeft = TimeToFade - element.FadeTimeLeft;
	
  }
  else
  {
    element.FadeState = element.FadeState == 2 ? -1 : 1;
    element.FadeTimeLeft = TimeToFade;
    setTimeout("animateFade(" + new Date().getTime() + ",'" + url + "')", 33);
  }  
}

function animateFade(lastTick, url)
{  
  var curTick = new Date().getTime();
  var elapsedTicks = curTick - lastTick;
 
  var element = document.getElementById('jspilotnews');
 
  if(element.FadeTimeLeft <= elapsedTicks)
  {
    element.style.opacity = element.FadeState == 1 ? '1' : '0';
    element.style.filter = 'alpha(opacity = '
        + (element.FadeState == 1 ? '100' : '0') + ')';
    element.FadeState = element.FadeState == 1 ? 2 : -2;
	if(element.FadeState == -2){
	getUrl(url,processPilot);
	}
    return;
  }
 
  element.FadeTimeLeft -= elapsedTicks;
  var newOpVal = element.FadeTimeLeft/TimeToFade;
  if(element.FadeState == 1)
    newOpVal = 1 - newOpVal;

  element.style.opacity = newOpVal;
  element.style.filter = 'alpha(opacity = ' + (newOpVal*100) + ')';
 
  setTimeout("animateFade(" + curTick + ",'" + url + "')", 33);
}

//-->
