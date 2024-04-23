<?php
require_once 'includes/db-connector.php';
require_once 'includes/session-handler.php';

if(isset($_SESSION['id'])) {

?>

    <!--Navbar-->
    <?php
    include 'includes/navbar.php';
    ?>

<!DOCTYPE html>
<html lang="en">

  <!--Head elements-->
  <?php
  include 'includes/head-element.php';
  ?>

  <body class="bg-timberwolf">
          <!-- Main Content -->
          <div class="col px-5">
            <!-- Content goes here -->
            <div class="col">
                <div class="row">
                    <!-- First Quarter: Mini Containers -->
                    <div class="col-md-6 px-4 mb-2 mt-4 mb-3"> <!-- Increased py-4 for more padding -->
                      <div class="col-md-12 bg-rose-white rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); height: 390px;"> <!-- Added box-shadow style for drop shadow -->
                          <!-- Larger Container 1 -->
                          <div class="p-2 mb-2 fw-medium" style="color: #5B5B5B;">
                            Weekly Sales
                          </div>
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
                            
                            series.columns.template.setAll({ cornerRadiusTL: 5, cornerRadiusTR: 5, strokeOpacity: 0 });
                            series.columns.template.adapters.add("fill", function (fill, target) {
                              return chart.get("colors").getIndex(series.columns.indexOf(target));
                            });
                            
                            series.columns.template.adapters.add("stroke", function (stroke, target) {
                              return chart.get("colors").getIndex(series.columns.indexOf(target));
                            });
                            
                            
                            // Set data
                            var data = [{
                              country: "Monday",
                              value: 2025
                            }, {
                              country: "Tuesday",
                              value: 1882
                            }, {
                              country: "Wednesday",
                              value: 1809
                            }, {
                              country: "Thursday",
                              value: 1322
                            }, {
                              country: "Friday",
                              value: 1122
                            }, {
                              country: "Saturday",
                              value: 1114
                            }, {
                              country: "Sunday",
                              value: 984
                            }];
                            
                            xAxis.data.setAll(data);
                            series.data.setAll(data);
                            
                            
                            // Make stuff animate on load
                            // https://www.amcharts.com/docs/v5/concepts/animations/
                            series.appear(1000);
                            chart.appear(1000, 100);
                            
                            }); // end am5.ready()
                            </script>
                            
                            <!-- HTML -->
                            <div id="chartdiv"></div>                      
                      </div>
                    </div>
                    <!-- Second Quarter: Larger Container -->
                    <div class="col-md-6 mt-4 mb-3">
                      <div class="row">
                        <div class="col-md-6 px-4" style="height: 191px;">
                          <div class="col-md-12 bg-rose-white rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); height: 175px;">
                            <!-- Mini Container 1 -->
                            <div class="text-center fw-medium py-2" style="color: #5B5B5B;">
                              Today's Sales
                              <div class="text-center py-4 d-flex justify-content-center" style="font-size: 40px; font-weight: 600;">940</div> 
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6 px-4">
                          <div class="col-md-12 bg-rose-white rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); height: 175px;">
                            <!-- Mini Container 2 -->
                            <div class="text-center fw-medium py-2" style="color: #5B5B5B;">
                              Today's Orders
                              <div class="text-center py-4 d-flex justify-content-center" style="font-size: 40px; font-weight: 600;">28</div> 
                            </div>
                          </div>
                        </div>  
                      </div>
                      <div class="row">
                        <div class="col-md-6 px-4" style="height: 192px;">
                          <div class="col-md-12 mt-4 bg-rose-white rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); height: 175px;">
                            <!-- Mini Container 3 -->
                            <div class="text-center fw-medium py-2" style="color: #5B5B5B;">
                              Weekly Sales
                              <div class="text-center py-4 d-flex justify-content-center" style="font-size: 40px; font-weight: 600;">4340</div> 
                            </div>
                        </div>
                        </div>
                        <div class="col-md-6 px-4">
                          <div class="col-md-12 mt-4 bg-rose-white rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); height: 175px;">
                            <!-- Mini Container 4 -->
                            <div class="text-center fw-medium py-2" style="color: #5B5B5B;">
                              Monthly Sales
                              <div class="text-center py-4 d-flex justify-content-center" style="font-size: 40px; font-weight: 600;">14340</div> 
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                    <!-- Third Quarter: Larger Container -->
                    <div class="col-md-6 px-4 mt-4"> <!-- Increased py-4 for more padding -->
                        <div class="col-md-12 mb-3 bg-rose-white rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); height: 390px;"> <!-- Added box-shadow style for drop shadow -->
                            <!-- Larger Container 2 -->
                            <div class="p-2 mb-2 fw-medium" style="color: #5B5B5B;">
                              Weekly Top Sold Products
                            </div>
                            <!-- Styles -->
                            <style>
                              #chartdiv1 {
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
                              var root = am5.Root.new("chartdiv1");
                              
                              
                              var myTheme = am5.Theme.new(root);
                              
                              myTheme.rule("Grid", ["base"]).setAll({
                                strokeOpacity: 0.1
                              });
                              
                              
                              // Set themes
                              // https://www.amcharts.com/docs/v5/concepts/themes/
                              root.setThemes([
                                am5themes_Animated.new(root),
                                myTheme
                              ]);
                              
                              
                              // Create chart
                              // https://www.amcharts.com/docs/v5/charts/xy-chart/
                              var chart = root.container.children.push(
                                am5xy.XYChart.new(root, {
                                  panX: false,
                                  panY: false,
                                  wheelX: "none",
                                  wheelY: "none",
                                  paddingLeft: 0
                                })
                              );
                              
                              
                              // Create axes
                              // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
                              var yRenderer = am5xy.AxisRendererY.new(root, {
                                minGridDistance: 30,
                                minorGridEnabled: true
                              });
                              yRenderer.grid.template.set("location", 1);
                              
                              var yAxis = chart.yAxes.push(
                                am5xy.CategoryAxis.new(root, {
                                  maxDeviation: 0,
                                  categoryField: "country",
                                  renderer: yRenderer
                                })
                              );
                              
                              var xAxis = chart.xAxes.push(
                                am5xy.ValueAxis.new(root, {
                                  maxDeviation: 0,
                                  min: 0,
                                  renderer: am5xy.AxisRendererX.new(root, {
                                    visible: true,
                                    strokeOpacity: 0.1,
                                    minGridDistance: 80
                                  })
                                })
                              );
                              
                              
                              // Create series
                              // https://www.amcharts.com/docs/v5/charts/xy-chart/series/
                              var series = chart.series.push(
                                am5xy.ColumnSeries.new(root, {
                                  name: "Series 1",
                                  xAxis: xAxis,
                                  yAxis: yAxis,
                                  valueXField: "value",
                                  sequencedInterpolation: true,
                                  categoryYField: "country"
                                })
                              );
                              
                              var columnTemplate = series.columns.template;
                              
                              columnTemplate.setAll({
                                draggable: true,
                                cursorOverStyle: "pointer",
                                tooltipText: "drag to rearrange",
                                cornerRadiusBR: 10,
                                cornerRadiusTR: 10,
                                strokeOpacity: 0
                              });
                              columnTemplate.adapters.add("fill", (fill, target) => {
                                return chart.get("colors").getIndex(series.columns.indexOf(target));
                              });
                              
                              columnTemplate.adapters.add("stroke", (stroke, target) => {
                                return chart.get("colors").getIndex(series.columns.indexOf(target));
                              });
                              
                              columnTemplate.events.on("dragstop", () => {
                                sortCategoryAxis();
                              });
                              
                              // Get series item by category
                              function getSeriesItem(category) {
                                for (var i = 0; i < series.dataItems.length; i++) {
                                  var dataItem = series.dataItems[i];
                                  if (dataItem.get("categoryY") == category) {
                                    return dataItem;
                                  }
                                }
                              }
                              
                              
                              // Axis sorting
                              function sortCategoryAxis() {
                                // Sort by value
                                series.dataItems.sort(function (x, y) {
                                  return y.get("graphics").y() - x.get("graphics").y();
                                });
                              
                                var easing = am5.ease.out(am5.ease.cubic);
                              
                                // Go through each axis item
                                am5.array.each(yAxis.dataItems, function (dataItem) {
                                  // get corresponding series item
                                  var seriesDataItem = getSeriesItem(dataItem.get("category"));
                              
                                  if (seriesDataItem) {
                                    // get index of series data item
                                    var index = series.dataItems.indexOf(seriesDataItem);
                              
                                    var column = seriesDataItem.get("graphics");
                              
                                    // position after sorting
                                    var fy =
                                      yRenderer.positionToCoordinate(yAxis.indexToPosition(index)) -
                                      column.height() / 2;
                              
                                    // set index to be the same as series data item index
                                    if (index != dataItem.get("index")) {
                                      dataItem.set("index", index);
                              
                                      // current position
                                      var x = column.x();
                                      var y = column.y();
                              
                                      column.set("dy", -(fy - y));
                                      column.set("dx", x);
                              
                                      column.animate({ key: "dy", to: 0, duration: 600, easing: easing });
                                      column.animate({ key: "dx", to: 0, duration: 600, easing: easing });
                                    } else {
                                      column.animate({ key: "y", to: fy, duration: 600, easing: easing });
                                      column.animate({ key: "x", to: 0, duration: 600, easing: easing });
                                    }
                                  }
                                });
                              
                                // Sort axis items by index.
                                // This changes the order instantly, but as dx and dy is set and animated,
                                // they keep in the same places and then animate to true positions.
                                yAxis.dataItems.sort(function (x, y) {
                                  return x.get("index") - y.get("index");
                                });
                              }
                              
                              // Set data
                              var data = [{
                                country: "Cappucino",
                                value: 2025
                              }, {
                                country: "Carbonara",
                                value: 1882
                              }, {
                                country: "Cheesecake",
                                value: 1809
                              }, {
                                country: "Ube Macapuno",
                                value: 1322
                              }, {
                                country: "Spanish Latte",
                                value: 1122
                              }];
                              
                              yAxis.data.setAll(data);
                              series.data.setAll(data);
                              
                              
                              // Make stuff animate on load
                              // https://www.amcharts.com/docs/v5/concepts/animations/
                              series.appear(1000);
                              chart.appear(1000, 100);
                              
                              }); // end am5.ready()
                              </script>
                              
                              <!-- HTML -->
                              <div id="chartdiv1"></div>  
                        </div>
                    </div>
                    <!-- Fourth Quarter: Larger Container -->
                    <div class="col-md-6 px-4 mt-4"> <!-- Increased py-4 for more padding -->
                        <div class="col-md-12 mb-3 bg-rose-white rounded-3 p-4" style="box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); height: 390px;"> <!-- Added box-shadow style for drop shadow -->
                            <!-- Larger Container 3 -->
                            <div class="p-2 mb-2 fw-medium" style="color: #5B5B5B;">
                              Weekly Top Sold Category
                            </div>
                            <!-- Styles -->
                            <style>
                              #chartdiv2 {
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
                              var root = am5.Root.new("chartdiv2");
                              
                              // Set themes
                              // https://www.amcharts.com/docs/v5/concepts/themes/
                              root.setThemes([
                                am5themes_Animated.new(root)
                              ]);
                              
                              // Create chart
                              // https://www.amcharts.com/docs/v5/charts/xy-chart/
                              var chart = root.container.children.push(am5xy.XYChart.new(root, {
                                panX: false,
                                panY: false,
                                wheelX: "none",
                                wheelY: "none",
                                paddingLeft: 0
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
                              
                              var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
                                maxDeviation: 0,
                                categoryField: "name",
                                renderer: xRenderer,
                                tooltip: am5.Tooltip.new(root, {})
                              }));
                              
                              xRenderer.grid.template.set("visible", false);
                              
                              var yRenderer = am5xy.AxisRendererY.new(root, {});
                              var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                                maxDeviation: 0,
                                min: 0,
                                extraMax: 0.1,
                                renderer: yRenderer
                              }));
                              
                              yRenderer.grid.template.setAll({
                                strokeDasharray: [2, 2]
                              });
                              
                              // Create series
                              // https://www.amcharts.com/docs/v5/charts/xy-chart/series/
                              var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                                name: "Series 1",
                                xAxis: xAxis,
                                yAxis: yAxis,
                                valueYField: "value",
                                sequencedInterpolation: true,
                                categoryXField: "name",
                                tooltip: am5.Tooltip.new(root, { dy: -25, labelText: "{valueY}" })
                              }));
                              
                              
                              series.columns.template.setAll({
                                cornerRadiusTL: 10,
                                cornerRadiusTR: 10,
                                strokeOpacity: 0
                              });
                              
                              series.columns.template.adapters.add("fill", (fill, target) => {
                                return chart.get("colors").getIndex(series.columns.indexOf(target));
                              });
                              
                              series.columns.template.adapters.add("stroke", (stroke, target) => {
                                return chart.get("colors").getIndex(series.columns.indexOf(target));
                              });
                              
                              // Set data
                              var data = [
                                {
                                  name: "Snacks & Rice Meals",
                                  value: 35654,
                                },
                                {
                                  name: "Pastries",
                                  value: 65456,
                                },
                                {
                                  name: "Drinks",
                                  value: 45724,
                                },
                                {
                                  name: "Froyo",
                                  value: 13654,
                                }
                              ];
                              
                              xAxis.data.setAll(data);
                              series.data.setAll(data);
                              
                              // Make stuff animate on load
                              // https://www.amcharts.com/docs/v5/concepts/animations/
                              series.appear(1000);
                              chart.appear(1000, 100);
                              
                              }); // end am5.ready()
                              </script>
                              
                              <!-- HTML -->
                              <div id="chartdiv2"></div>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>   
      </div>
    </div>

    <!--Bootstrap JavaScript-->
    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  </div>
</div>

  </body>
</html>

<?php 
  } else {
    header("location: login.php");
  } 
?>