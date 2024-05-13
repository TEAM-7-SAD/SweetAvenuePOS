<?php

// Calculate the timestamp for the beginning of May 7th, 2024
$prev_week_start = date('Y-m-d', strtotime('2024-05-07'));

// Calculate the timestamp for the end of May 12th, 2024
$prev_week_end = date('Y-m-d', strtotime('2024-05-12'));

// Fetching sales data from the database
$sql = "SELECT DATE(timestamp) AS date, SUM(total_amount) AS total_sales 
        FROM transaction 
        WHERE timestamp >= '$prev_week_start' AND timestamp <= '$prev_week_end'
        GROUP BY DATE(timestamp)";
$result = $db->query($sql);

$sales_data = array();

$weekly_sales = 0;

// Process the query result
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
      // Extract date and total sales from the row
      $date = date('l', strtotime($row['date']));
      $total_sales = $row['total_sales'];
      
      // Update sales data for the corresponding day
      $sales_data[$date] = $total_sales;
      $weekly_sales += $total_sales;
  }
}

// Prepare data for the graph
$data = [];
foreach ($sales_data as $day => $sales) {
    $data[] = [
        'country' => $day,
        'value' => floatval($sales)
    ];
}

?>

<!-- Styles -->
<style>
#chartdiv {
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
// https://www.amcharts.com/docs/v5/getting-started/#Root_element
var root = am5.Root.new("chartdiv");

// Set themes
// https://www.amcharts.com/docs/v5/concepts/themes/
root.setThemes([
  am5themes_Animated.new(root)
]);

// Create chart
// https://www.amcharts.com/docs/v5/charts/xy-chart/
var chart = root.container.children.push(am5xy.XYChart.new(root, {
  panX: true,
  panY: true,
  wheelX: "panX",
  wheelY: "zoomX",
  pinchZoomX: true,
  paddingLeft:0,
  paddingRight:1
}));

// Add cursor
// https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
cursor.lineY.set("visible", false);


// Create axes
// https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
var xRenderer = am5xy.AxisRendererX.new(root, { 
  minGridDistance: 30, 
  minorGridEnabled: true
});

xRenderer.labels.template.setAll({
  rotation: -90,
  centerY: am5.p50,
  centerX: am5.p100,
  paddingRight: 15
});

xRenderer.grid.template.setAll({
  location: 1
})

var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
  maxDeviation: 0.3,
  categoryField: "country",
  renderer: xRenderer,
  tooltip: am5.Tooltip.new(root, {})
}));

var yRenderer = am5xy.AxisRendererY.new(root, {
  strokeOpacity: 0.1
})

var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
  maxDeviation: 0.3,
  renderer: yRenderer
}));

// Create series
// https://www.amcharts.com/docs/v5/charts/xy-chart/series/
var series = chart.series.push(am5xy.ColumnSeries.new(root, {
  name: "Series 1",
  xAxis: xAxis,
  yAxis: yAxis,
  valueYField: "value",
  sequencedInterpolation: true,
  categoryXField: "country",
  tooltip: am5.Tooltip.new(root, {
    labelText: "{valueY}"
  })
}));

series.columns.template.setAll({ 
    cornerRadiusTL: 5, 
    cornerRadiusTR: 5, 
    strokeOpacity: 0,
    fillOpacity: 0.8,
    fill: am5.color("#C57C47")
});

// series.columns.template.adapters.add("fill", function (fill, target) {
//   return chart.get("colors").getIndex(series.columns.indexOf(target));
// });

// series.columns.template.adapters.add("stroke", function (stroke, target) {
//   return chart.get("colors").getIndex(series.columns.indexOf(target));
// });


// Set data
var data = <?php echo json_encode($data); ?>;

xAxis.data.setAll(data);
series.data.setAll(data);


// Make stuff animate on load
// https://www.amcharts.com/docs/v5/concepts/animations/
series.appear(1000);
chart.appear(1000, 100);

}); // end am5.ready()
</script>