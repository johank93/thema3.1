//$(document).ready(function() {
function flickrPics() {

    var tag = 'solar panels';
    var count = $('#h_wrapper').children().length; // dynamically value

    for (var j = 0; j < count; j++){
        randomImage(j);
    }

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

    function randomImage(theNumber){

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
            
            var mydate = new Date(item.date_taken);
            var day = mydate.getDay();
            var month = mydate.getMonth();
            var year = mydate.getFullYear();
            var str = day + '/' + month + '/' + year;
            
            $("#slide"+theNumber).html("<a href="+ url+" target='_blank' ><img src="+ url + "></a></img><br><b>Titel:</b>"+item.title+"<br><b>Date:</b>"+str+"");
            }); 
    }
    
    
};