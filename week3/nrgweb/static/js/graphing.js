/**
 * Get data and make into a graph
 * 
 * Based on demo code at http://g.raphaeljs.com/linechart.html
 * 
 * @param url for own data
 * @param url_avg average data
 */
function getData(url, url_avg) {
	
	$('#holder').empty();
	
	$.get( url, function( data ) {
		var measurements = JSON.parse(data); // Parse the JSON measurement data
		var x = [9,10,11,12,13,14,15,16,17,18];
		var y1 =[0,0,0,0,0,0,0,0,0,0];
		var y2 =[0,0,0,0,0,0,0,0,0,0];

		for (i in measurements) {
			pos = measurements[i].time.substring(0,2).replace(/^0+/, '')-9; // Get timelabel, parse to array position
        	y1[pos] = measurements[i].value;
		}
              
    	$.get( url_avg, function( data ) {
    		var measurements = JSON.parse(data); // Parse the JSON measurement data
    		for (i in measurements) {
    			pos = measurements[i].time.substring(0,2).replace(/^0+/, '')-9; // Get timelabel, parse to array position
            	y2[pos] = measurements[i].value;
    		}
    		
    		drawGraph(x,y1,y2);
    	});        
    });
}

/**
 * Draws the graph
 * @param array with x values
 * @param array with y values
 * @param 2nd array with y values
 */
function drawGraph(x,y1,y2) {
    var r = Raphael("holder"), // create holder for graph
        txtattr = { font: "12px sans-serif" };
    r.text(25, 13, "kWh").attr(txtattr);

    // Draw graph
    var lines = r.linechart(20, 20, 800, 300, x, [y1, y2], {
        nostroke: false, axis: "0 0 1 1", symbol: "circle", axisxstep: 9, axisystep: 9 }).hoverColumn(function () {
            this.tags = r.set();
            for (var i = 0, ii = this.y.length; i < ii; i++) {
                this.tags.push(r.tag(this.x, this.y[i], this.values[i], 160, 10).insertBefore(this).attr([{ fill: "#fff" }, { fill: this.symbols[i].attr("fill") }]));
            }
        }, function () {
            this.tags && this.tags.remove();
        });

    // Replace x-axis labels with time labels. This trick was borrowed from https://gist.github.com/boazsender/447379
    var xText = lines.axis[0].text.items;
    for (var i in xText) { // Iterate through the array of dom elems, the current dom elem will be i
        var oldLabel = (xText[i].attr('text') + "").split('.'), // Get the current dom elem in the loop, and split it on the decimal
        newLabel = oldLabel[0] + ":00"; // Format the result into time strings
        xText[i].attr({'text': newLabel}); // Set the text of the current elem with the result
    }
}
