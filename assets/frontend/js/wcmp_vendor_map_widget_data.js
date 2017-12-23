/* global google */

// Load the Visualization API and the piechart package.
google.charts.load('current', {'packages': ['corechart']});
// Set a callback to run when the Google Visualization API is loaded.
google.charts.setOnLoadCallback(drawChart);
var chart;
function drawChart() {
    chart = new google.visualization.PieChart(document.getElementById('donutchart'));
    jQuery("#wcmp_visitor_stats_date_filter").change(function (e) {
        var data = {
            action: 'wcmp_vendor_dashboard_visitor_stats',
            stats_date: jQuery(this).val()
        }
        jQuery.post(woocommerce_params.ajax_url, data, function (response) {
            var country_pin = Array();
            var colors = Array();
            var gchart_data = [];
            var gchart_color = [];
            jQuery.each(response.stats_data_visitors, function (key, val) {
                country_pin[key] = val.hits_count + ' visitors';
                colors[key] = val.color;
                gchart_data.push([key.toUpperCase(), val.hits_count]);
                gchart_color.push(val.color);
            });

            jQuery('#vmap').replaceWith("<div id='vmap' style='height: 270px;''></div>");
            var jQuerymap = jQuery('#vmap');
            jQuerymap.vectorMap(
                    {
                        map: 'world_en',
                        backgroundColor: false,
                        colors: colors,
                        hoverOpacity: 0.7, // opacity for :hover
                        hoverColor: false,
                        onLabelShow: function (element, label, code) {
                            if (country_pin[code] !== undefined) {
                                label.html(label.html() + ' - ' + country_pin[code]);
                            } else {
                                label.html(label.html() + ' [0]');
                            }
                        }
                    });
            if (typeof google !== "undefined") {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Region');
                data.addColumn('number', 'Hits Percentage');
                data.addRows((gchart_data));
                var options = {
                    colors: gchart_color,
                    legend: {position: 'left'},
                    pieSliceText: 'none',
                    pieHole: 0.7,
                    chartArea: {left: 10, top: 50, width: '100%', height: '100%'},
                    animation: {duration: 800, easing: 'in'}
                };
                if (typeof chart === "undefined") {
                } else {
                    chart.draw(data, options);
                }
            }
        });
    }).change();
}