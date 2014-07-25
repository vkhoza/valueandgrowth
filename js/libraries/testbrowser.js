var $buoop = {
    reminder: 0,                   // atfer how many hours should the message reappear
                                    // 0 = show all the time
    l: "en",                       // set a language for the message, e.g. "en"
                                    // overrides the default detection
    text: "Hi, we have detected that your browser is out of date, as a result most of the crucial functions on this website will not work.\
	<a href='http://browser-update.org/update-browser.html#14@value-and-growth.com' class='decorate'>Update your browser</a> for more security, \
	comfort and the best experience on this site.",                       // custom notification html text
};
$buoop.ol = window.onload; 
window.onload=function(){ 
 try {if ($buoop.ol) $buoop.ol();}catch (e) {} 
 var e = document.createElement("script"); 
 e.setAttribute("type", "text/javascript"); 
 e.setAttribute("src", "//browser-update.org/update.js"); 
 document.body.appendChild(e); 
} 
