<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Highstock History</title>

		<style type="text/css">

		</style>
	</head>
	<body>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="highchart/code/highstock.js"></script>
<script src="highchart/code/modules/exporting.js"></script>

<div id="container" style="height: 400px; min-width: 310px"></div>


		<script type="text/javascript">

/**
 * (c) 2010-2017 Torstein Honsi
 *
 * License: www.highcharts.com/license
 * 
 * Dark theme for Highcharts JS
 * @author Torstein Honsi
 */


Highcharts.createElement('link', {
   href: 'https://fonts.googleapis.com/css?family=Open+Sans',
   rel: 'stylesheet',
   type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
   colors: ['#90ee7e', '#ff0000', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066', '#eeaaee',
      '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
   chart: {
      backgroundColor: {
         linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
         stops: [
            [0, '#2a2a2b'],
            [1, '#3e3e40']
         ]
      },
      style: {
         fontFamily: '\'Open Sans\', sans-serif'
      },
      plotBorderColor: '#606063'
   },
   title: {
      style: {
         color: '#E0E0E3',
         textTransform: 'uppercase',
         fontSize: '20px'
      }
   },
   subtitle: {
      style: {
         color: '#E0E0E3',
         textTransform: 'uppercase'
      }
   },
   xAxis: {
      gridLineColor: '#707073',
      labels: {
         style: {
            color: '#E0E0E3'
         }
      },
      lineColor: '#707073',
      minorGridLineColor: '#505053',
      tickColor: '#707073',
      title: {
         style: {
            color: '#A0A0A3'

         }
      }
   },
   yAxis: {
      gridLineColor: '#707073',
      labels: {
         style: {
            color: '#E0E0E3'
         }
      },
      lineColor: '#707073',
      minorGridLineColor: '#505053',
      tickColor: '#707073',
      tickWidth: 1,
      title: {
         style: {
            color: '#A0A0A3'
         }
      }
   },
   tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.85)',
      style: {
         color: '#F0F0F0'
      }
   },
   plotOptions: {
      series: {
         dataLabels: {
            color: '#B0B0B3'
         },
         marker: {
            lineColor: '#333'
         }
      },
      boxplot: {
         fillColor: '#505053'
      },
      candlestick: {
         lineColor: 'white'
      },
      errorbar: {
         color: 'white'
      }
   },
   legend: {
      itemStyle: {
         color: '#E0E0E3'
      },
      itemHoverStyle: {
         color: '#FFF'
      },
      itemHiddenStyle: {
         color: '#606063'
      }
   },
   credits: {
      style: {
         color: '#666'
      }
   },
   labels: {
      style: {
         color: '#707073'
      }
   },

   drilldown: {
      activeAxisLabelStyle: {
         color: '#F0F0F3'
      },
      activeDataLabelStyle: {
         color: '#F0F0F3'
      }
   },

   navigation: {
      buttonOptions: {
         symbolStroke: '#DDDDDD',
         theme: {
            fill: '#505053'
         }
      }
   },

   // scroll charts
   rangeSelector: {
      buttonTheme: {
         fill: '#505053',
         stroke: '#000000',
         style: {
            color: '#CCC'
         },
         states: {
            hover: {
               fill: '#707073',
               stroke: '#000000',
               style: {
                  color: 'white'
               }
            },
            select: {
               fill: '#000003',
               stroke: '#000000',
               style: {
                  color: 'white'
               }
            }
         }
      },
      inputBoxBorderColor: '#505053',
      inputStyle: {
         backgroundColor: '#333',
         color: 'silver'
      },
      labelStyle: {
         color: 'silver'
      }
   },

   navigator: {
      handles: {
         backgroundColor: '#666',
         borderColor: '#AAA'
      },
      outlineColor: '#CCC',
      maskFill: 'rgba(255,255,255,0.1)',
      series: {
         color: '#7798BF',
         lineColor: '#A6C7ED'
      },
      xAxis: {
         gridLineColor: '#505053'
      }
   },

   scrollbar: {
      barBackgroundColor: '#808083',
      barBorderColor: '#808083',
      buttonArrowColor: '#CCC',
      buttonBackgroundColor: '#606063',
      buttonBorderColor: '#606063',
      rifleColor: '#FFF',
      trackBackgroundColor: '#404043',
      trackBorderColor: '#404043'
   },

   // special colors for some of the
   legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
   background2: '#505053',
   dataLabelsColor: '#B0B0B3',
   textColor: '#C0C0C0',
   contrastTextColor: '#F0F0F3',
   maskColor: 'rgba(255,255,255,0.3)'
};

// Apply the theme
Highcharts.setOptions(Highcharts.theme);

var seriesOptions = [],
    seriesCounter = 0,
    names = ['Used', 'Avail'];
    
    
function createChart() {
        
    Highcharts.stockChart('container', {


        rangeSelector: {
            buttons: [{
                type: 'hour',
                count: 1,
                text: '1h'
            }, {
                type: 'day',
                count: 1,
                text: '1D'
            }, {
                type: 'day',
                count: 2,
                text: '2D'
            }, {
                type: 'day',
                count: 3,
                text: '3D'
            }, {
                type: 'day',
                count: 4,
                text: '4D'
            }, {
                type: 'day',
                count: 5,
                text: '5D'
            }, {
                type: 'week',
                count: 1,
                text: '1w'
            }, {
            	type: 'month',
            	count: 1,
            	text: '1m'
            }, {
            	type: 'month',
            	count: 3,
            	text: '3m'
            }, {
            	type: 'month',
            	count: 6,
            	text: '6m'
            }, {
            	type: 'ytd',
            	text: 'YTD'
            }, {
            	type: 'year',
            	count: 1,
            	text: '1y'
            }, {
            	type: 'all',
            	text: 'All'
            }],
            selected: 8
        },
        
        title: {
            text: '<?php echo $_GET['ticker']; ?>'
        },

        series: seriesOptions
        
    });

}



$.getJSON('getdata.php?ticker=<?php echo $_GET['ticker']; ?>', function (data) {
    // Create the chart
    
    seriesOptions[0] = {
            name: 'Used',
            data: data
        };
    
    $.getJSON('getsod.php?ticker=<?php echo $_GET['ticker']; ?>', function (data) {
    // Create the chart
    
        seriesOptions[1] = {
            name: 'Avail',
            data: data,
            tooltip: {
                valueDecimals: 0
            }
        };
        
        createChart();
    });
});





		</script>
	</body>
</html>
