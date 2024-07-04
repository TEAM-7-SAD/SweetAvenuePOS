<?php

include_once str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/../file-utilities.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/../db-connector.php');
include_once FileUtils::normalizeFilePath(__DIR__ . '/../error-reporting.php');
include_once FileUtils::normalizeFilePath(__DIR__ . '/../default-timezone.php');

function fetchAndProcessSalesData($db) {
  $prev_week_start = date('Y-m-d', strtotime('monday last week'));
  $prev_week_end = date('Y-m-d', strtotime('saturday last week'));

  $sql = "SELECT DATE(timestamp) AS date, SUM(total_amount) AS total_sales 
          FROM transaction 
          WHERE timestamp >= ? AND timestamp <= ?
          GROUP BY DATE(timestamp)";
  $stmt = $db->prepare($sql);
  $stmt->bind_param('ss', $prev_week_start, $prev_week_end);
  $stmt->execute();
  $result = $stmt->get_result();

  $sales_data = array();
  $weekly_sales = 0;

  // Process the query result
  if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
          $date = $row['date'];
          $total_sales = floatval($row['total_sales']);

          $sales_data[] = [
              'date' => $date,
              'total_sales' => $total_sales
          ];
          $weekly_sales += $total_sales;
      }
  }

  $stmt->close();

  $json_sales_data = json_encode($sales_data); 
  $json_file = __DIR__ . '/weekly_sales_data.json';
  file_put_contents($json_file, $json_sales_data);

  return $json_sales_data;
}

// Check if today is monday
if(date('N') == 1) {
  $json_sales_data = fetchAndProcessSalesData($db);
} else {
  $json_file = __DIR__ . '/weekly_sales_data.json';
  $json_sales_data = file_get_contents($json_file);
}

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
