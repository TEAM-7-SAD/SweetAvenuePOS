<?php

include_once str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/../file-utilities.php');
include_once FileUtils::normalizeFilePath(__DIR__ . '/../error-reporting.php');
include_once FileUtils::normalizeFilePath(__DIR__ . '/../default-timezone.php');

$sales_data = array();
$weekly_sales = 0;

if(date('N') == 1) {

  $prev_week_start = date('Y-m-d', strtotime('last Monday'));
  $prev_week_end = date('Y-m-d', strtotime('last Saturday'));

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
          $date = $row['date'];
          $total_sales = $row['total_sales'];
          
          // Update sales data for the corresponding day
          $sales_data[] = [
              'date' => $date,
              'total_sales' => floatval($total_sales)
          ];
          $weekly_sales += $total_sales;
      }
  }

}

// Encode the data as JSON
$json_sales_data = json_encode($sales_data); 

?>

<!-- Styles -->
<style>
#weeklySales {
  width: 100%;
  height: 300px;
}
</style>

<!-- Resources -->
<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

<script>
am5.ready(function() {

  // Create root element
  var root = am5.Root.new("weeklySales");

  // Set themes
  root.setThemes([
    am5themes_Animated.new(root)
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
      minGridDistance: 30,
      minorLabelsEnabled: true
    }),
    tooltip: am5.Tooltip.new(root, {})
  }));

  var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
    renderer: am5xy.AxisRendererY.new(root, {})
  }));

  // Add series
  var series = chart.series.push(am5xy.LineSeries.new(root, {
    name: "Sales",
    xAxis: xAxis,
    yAxis: yAxis,
    valueYField: "total_sales",
    valueXField: "date",
    tooltip: am5.Tooltip.new(root, {
      labelText: "{valueY}"
    }),
    stroke: am5.color("#C57C47")
  }));

  // Add bullet
  series.bullets.push(function() {
    var bulletCircle = am5.Circle.new(root, {
      radius: 5,
      fill: am5.color("#88531E") 
    });
    return am5.Bullet.new(root, {
      sprite: bulletCircle
    });
  });

  // Add scrollbar
  chart.set("scrollbarX", am5.Scrollbar.new(root, {
    orientation: "horizontal"
  }));

  // Set data
  var salesData = <?php echo $json_sales_data; ?>;
  series.data.setAll(salesData.map(function(item) {
    return {
      date: new Date(item.date).getTime(),
      total_sales: item.total_sales
    };
  }));

  // Make stuff animate on load
  series.appear(1000);
  chart.appear(1000, 100);

}); // end am5.ready()
</script>
