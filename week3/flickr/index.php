
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <title> - jsFiddle demo</title>
  
  
  <script type='text/javascript' src='js/flickr.js'></script>

  
  <style type='text/css'>
    #random { display:block; height: 400px; background: #ccc; }
  </style>
  



<script type='text/javascript'>//<![CDATA[
$(window).load(function(){
function getPicture(tags, cb) {
    var apiKey = "fa214b1215cd1a537018cfbdfa7fb9a6"; // replace this with your API key

    // get an array of random photos
    var hoi = $.getJSON(
        "https://api.flickr.com/services/rest/?jsoncallback=?", {
            method: 'flickr.photos.search',
            tags: tags,
            api_key: apiKey,
            format: 'json',
            nojsoncallback: 1,
            per_page: 10 // you can increase this to get a bigger array
        },
        
        
        function(data) {

            // if everything went good
            if (data.stat == 'ok') {
                // get a random id from the array
                var photo = data.photos.photo[Math.floor(Math.random() * data.photos.photo.length)];

                // now call the flickr API and get the picture with a nice size
                $.getJSON(
                    "https://api.flickr.com/services/rest/?jsoncallback=?", {
                        method: 'flickr.photos.getSizes',
                        api_key: apiKey,
                        photo_id: photo.id,
                        format: 'json',
                        nojsoncallback: 1
                    },
                    function(response) {
                        if (response.stat == 'ok') {
                            var the_url = response.sizes.size[5].source;
                            cb(the_url);
                        } else {
                            console.log(" The request to get the picture was not good :\ ")
                        }
                    }
                );
        
            } else {
                console.log(" The request to get the array was not good :( ");
            }
        }
    );
    alert(JSON.stringify(hoi));
};

getPicture('httyd', function(url) {
    $("#random").attr("src", url);
});
});//]]> 

</script>

</head>
<body>
  <img id="random"></img>
  
</body>

</html>

