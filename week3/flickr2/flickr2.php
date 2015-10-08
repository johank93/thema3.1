
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <title> - jsFiddle demo</title>
  
  
  <script type='text/javascript' src='//code.jquery.com/jquery-2.1.0.js'></script>
  
  <style type='text/css'>
    
  </style>
  



<script type='text/javascript'>//<![CDATA[
$(window).load(function(){
function getQueryStringVar(name){
    var qs = window.location.search.slice(1);
    var props = qs.split("&");
    for (var i=0 ; i < props.length;i++){
        var pair = props[i].split("=");
        if(pair[0] === name) {
            return decodeURIComponent(pair[1]);
        }
    }
}

function getLetterImage(tag, theNumber){

var flickerAPI = "https://www.flickr.com/services/feeds/photos_public.gne?jsoncallback=?";

            return $.getJSON( flickerAPI, {
                tags: tag,
                tagmode: "all",
                format: "json"
            })
            .then(function (flickrdata) {
                //console.log(flickrdata);
                var i = Math.floor(Math.random() * flickrdata.items.length);
                var item = flickrdata.items[i];
                var url = item.media.m;
                $("#img"+theNumber).html("<img src="+ url + "></img>");
                }); 
}



$(document).ready(function() {
        var name = getQueryStringVar("name") || "Lol";

            var str = "letter,";
            var searchtags = new Array()
            for (var i = 0; i < name.length; i++) {
                searchtags[i] = str.concat(name.charAt(i));
            }
            for (var j = 0; j < name.length; j++){
                getLetterImage(searchtags[j], j);

            }


});
});//]]> 

</script>

</head>
<body>
  <body>
    <div id="img0">Loading</div>
    <div id="img1">Loading</div>
    <div id="img2">Loading</div>
    <div id="img3">Loading</div>
    <div id="img4">Loading</div>
        <div id="img5">Loading</div>
        <div id="img6">Loading</div>

  </body>
  
</body>

</html>

