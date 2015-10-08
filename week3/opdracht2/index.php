
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title> - Slider demo</title>

        <script type='text/javascript' src='//code.jquery.com/jquery-2.1.0.js'></script>

        <link rel="stylesheet" type="text/css" href="css/slider.css">
    </head>
    <body>

        <!--- DISPLAY CONTAINER --->
        <div id="h_container"><!-- OUTTER WRAPPER -->
            <div id="h_wrapper"><!-- SLIDE 1 -->
                <div id="slide0" class="h_slide"></div>
                <!-- SLIDE 2 -->
                <div id="slide1" class="h_slide"></div>
                <!-- SLIDE 3 -->
                <div id="slide2" class="h_slide"></div>
            </div>
        </div>
        <!--- NAVIGATION BUTTONS -->
        <table style="width:200px; padding: 0 10px 0 10px;">
            <tbody>
                <tr>
                    <td align="left"><a href="javascript:void(0);" id="PreviousButton" style="display:none">&laquo; Previous</a></td>
                    <td align="right"><a href="javascript:void(0);" id="NextButton">Next &raquo;</a></td>
                </tr>
            </tbody>
        </table>
        <script type='text/javascript' src='js/flickr.js'></script>
        <script type='text/javascript' src='js/slider.js'></script>
        <script type='text/javascript' src='js/main.js'></script>
    </body>
</html>