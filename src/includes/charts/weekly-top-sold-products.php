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
strokeOpacity: 0,
fillOpacity: 0.8,
fill: am5.color("#C57C47")
});
// columnTemplate.adapters.add("fill", (fill, target) => {
// return chart.get("colors").getIndex(series.columns.indexOf(target));
// });

// columnTemplate.adapters.add("stroke", (stroke, target) => {
// return chart.get("colors").getIndex(series.columns.indexOf(target));
// });

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
