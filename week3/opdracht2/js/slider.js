var SlideSpeed = 500;
var SlideWidth = 200;
var time = 5000;
    
function createslider() {   
    var index = 0;
    var count = $('#h_wrapper').children().length; // dynamically value
    setInterval(next, time); // create the interval
    
    // move to next slide
    function next() {
        index = (index + 1) % count;
        goto(index);
    }

    // move to previous slide
    function previous() {
        index = (index + count - 1) % count;
        goto(index);
    }
    
    // go to slide x
    function goto(x) {
        var margin = index * SlideWidth; // set offset by index + width
        $('#h_wrapper').stop().animate({ // stop cancels any running animations
            'margin-left': -margin
        }, SlideSpeed, function() {
            SetNavigationDisplay();
        });
    }
    
       // set click handlers
    $("#NextButton").click(next);
    $("#PreviousButton").click(previous);
    
    //$("#NextButton").click(function(e) { 
    //    e.preventDefault();
    //    if ( $("#h_wrapper").is(':not(:animated)') && $("#NextButton").is(':not(:animated)') ) {
    //        var newMargin = CurrentMargin() - SlideWidth;
    //        $("#h_wrapper").animate({ marginLeft: newMargin }, SlideSpeed, function () { SetNavigationDisplay() }); 
    //    }
    //});
    //
    //$("#PreviousButton").click(function(e) { 
    //    e.preventDefault();
    //    if ( $("#h_wrapper").is(':not(:animated)') && $("#PreviousButton").is(':not(:animated)') ) {
    //        var newMargin = CurrentMargin() + SlideWidth;
    //        $("#h_wrapper").animate({ marginLeft: newMargin }, SlideSpeed, function () { SetNavigationDisplay() });
    //    }
    //});

    function CurrentMargin() {
      // get current margin of slider
      var currentMargin = $("#h_wrapper").css("margin-left");

      // return the current margin to the function as an integer
      return parseInt(currentMargin);
    }

    function SetNavigationDisplay() {
      // get current margin
      var currentMargin = CurrentMargin();

      // if current margin is at 0, then we are at the beginning, hide previous
      if (currentMargin == 0) {
        $("#PreviousButton").fadeOut();
      }
      else {
        $("#PreviousButton").fadeIn();
      }

      // get wrapper width
      var wrapperWidth = $("#h_wrapper").width();

      // turn current margin into postive number and calculate if we are at last slide, if so, hide next button
      if ((currentMargin * -1) == (wrapperWidth - SlideWidth)) {
        $("#NextButton").fadeOut();
      }
      else {
        $("#NextButton").fadeIn();
      }
    }
};