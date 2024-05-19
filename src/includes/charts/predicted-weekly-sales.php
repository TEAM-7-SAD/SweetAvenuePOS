<?php 

// Read the JSON file
$json_file = 'sales-prediction-algorithm/sales_prediction.json';
$json_data = file_get_contents($json_file);

// Decode JSON into array
$predictions = json_decode($json_data, true);

$sales_sum = 0;

// Check if decoding is scucessful
if($predictions != NULL) {
    // Extract predictions
    $prediction_data = $predictions['predictions'];
    $sales_sum = $predictions['sales_sum'];
}

?>

<style>
    #predictedWeeklySales {
    width: 100%;
    height: 300px;
    }
</style>

<!-- Resources -->
<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

<!-- Chart code -->
<script>
am5.ready(function() {

// Create root element
var root = am5.Root.new("predictedWeeklySales");

const myTheme = am5.Theme.new(root);

// Move minor label a bit down
myTheme.rule("AxisLabel", ["minor"]).setAll({
  dy: 1
});

// Tweak minor grid opacity
myTheme.rule("Grid", ["minor"]).setAll({
  strokeOpacity: 0.08
});

// Set themes
root.setThemes([
  am5themes_Animated.new(root),
  myTheme
]);

// Create chart
var chart = root.container.children.push(am5xy.XYChart.new(root, {
  panX: false,
  panY: false,
  wheelX: "panX",
  wheelY: "zoomX",
  paddingLeft: 0
}));

// Add cursor
var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
  behavior: "zoomX"
}));
cursor.lineY.set("visible", false);

// Create axes
var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
  maxDeviation: 0,
  baseInterval: {
    timeUnit: "day",
    count: 1
  },
  renderer: am5xy.AxisRendererX.new(root, {
    minorGridEnabled: true,
    minGridDistance: 50,    
    minorLabelsEnabled: true
  }),
  tooltip: am5.Tooltip.new(root, {})
}));

xAxis.set("minorDateFormats", {
  day: "dd",
  month: "MM"
});

var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
  renderer: am5xy.AxisRendererY.new(root, {})
}));

// Add series
var series = chart.series.push(am5xy.LineSeries.new(root, {
  name: "Series",
  xAxis: xAxis,
  yAxis: yAxis,
  valueYField: "sales_prediction",
  valueXField: "date",
  tooltip: am5.Tooltip.new(root, {
    labelText: "{valueY}"
  }),
  stroke: am5.color("#C57C47")
}));

// Actual bullet
series.bullets.push(function () {
  var bulletCircle = am5.Circle.new(root, {
    radius: 5,
    fill: am5.color("#88531E") 
  });
  return am5.Bullet.new(root, {
    sprite: bulletCircle
  })
})

// Add scrollbar
chart.set("scrollbarX", am5.Scrollbar.new(root, {
  orientation: "horizontal"
}));

// Use prediction data from PHP
var data = <?php echo json_encode(array_map(function($item) {
    return [
        'date' => strtotime($item['date']) * 1000,
        'sales_prediction' => $item['sales_prediction']
    ];
}, $prediction_data)); ?>;

series.data.setAll(data);

// Make stuff animate on load
series.appear(1000);
chart.appear(1000, 100);

}); // end am5.ready()
</script>