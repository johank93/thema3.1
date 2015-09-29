<canvas id="myChart" width="400" height="200"></canvas>
<script>

    $.get("/inc/device-information.php?device=HAHA8000&comparison_type=1&region_type=1", function(data) {

        var json = JSON.decode(data);

        var labels = [];
        var valuesAVG = [];
        var valuesMine = [];

        for (var i = 0; i < json.length; i++) {
            var obj = json[i];
            labels[i] = obj.tijd.substring(0, 5);
            valuesAVG[i] = parseFloat(obj.gemiddeldewaarde);
            valuesMine[i] = parseFloat(obj.gemiddeldewaarde)+parseFloat(1);
        }
        labels.reverse();
        valuesAVG.reverse();
        valuesMine.reverse();

        var ctx = document.getElementById("myChart").getContext("2d");
        var options = {
            ///Boolean - Whether grid lines are shown across the chart
            scaleShowGridLines: true,
            //String - Colour of the grid lines
            scaleGridLineColor: "rgba(0,0,0,.05)",
            //Number - Width of the grid lines
            scaleGridLineWidth: 1,
            //Boolean - Whether to show horizontal lines (except X axis)
            scaleShowHorizontalLines: true,
            //Boolean - Whether to show vertical lines (except Y axis)
            scaleShowVerticalLines: true,
            //Boolean - Whether the line is curved between points
            bezierCurve: true,
            //Number - Tension of the bezier curve between points
            bezierCurveTension: 0.4,
            //Boolean - Whether to show a dot for each point
            pointDot: true,
            //Number - Radius of each point dot in pixels
            pointDotRadius: 4,
            //Number - Pixel width of point dot stroke
            pointDotStrokeWidth: 1,
            //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
            pointHitDetectionRadius: 20,
            //Boolean - Whether to show a stroke for datasets
            datasetStroke: true,
            //Number - Pixel width of dataset stroke
            datasetStrokeWidth: 2,
            //Boolean - Whether to fill the dataset with a colour
            datasetFill: true,
            //String - A legend template
            multiTooltipTemplate: "<%= value %> - <%= datasetLabel %>"
        };

        var chartdata = {
            labels: labels,
            datasets: [
                {
                    label: "Mijn apparaat",
                    fillColor: "rgba(220,220,220,0.2)",
                    strokeColor: "rgba(220,220,220,1)",
                    pointColor: "rgba(220,220,220,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: valuesMine
                },
                {
                    label: "Prov. gem.",
                    fillColor: "rgba(151,187,205,0.2)",
                    strokeColor: "rgba(151,187,205,1)",
                    pointColor: "rgba(151,187,205,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(151,187,205,1)",
                    data: valuesAVG
                }
            ]
        };
        new Chart(ctx).Line(chartdata, options);

    });
</script>


