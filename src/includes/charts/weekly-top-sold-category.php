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
    cornerRadiusTL: 5, 
    cornerRadiusTR: 5, 
    strokeOpacity: 0,
    fillOpacity: 0.8,
    fill: am5.color("#C57C47")
});

// series.columns.template.adapters.add("fill", (fill, target) => {
// return chart.get("colors").getIndex(series.columns.indexOf(target));
// });

// series.columns.template.adapters.add("stroke", (stroke, target) => {
// return chart.get("colors").getIndex(series.columns.indexOf(target));
// });

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