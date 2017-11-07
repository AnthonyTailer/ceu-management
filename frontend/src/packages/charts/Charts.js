import Highcharts from 'highcharts'

export default function (Vue) {
  Vue.charts = {

    pieChart (dataChart) {
      // Radialize the colors
      Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function (color) {
        return {
          radialGradient: {
            cx: 0.5,
            cy: 0.3,
            r: 0.7
          },
          stops: [
            [0, color],
            [1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
          ]
        };
      });

// Build the chart
      Highcharts.chart(dataChart.id, {
        chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: 'pie'
        },
        title: {
          text: dataChart.title
        },
        tooltip: {
          pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
          pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
              enabled: true,
              format: '<b>{point.name}</b>: {point.percentage:.1f} %',
              style: {
                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
              },
              connectorColor: 'silver',
            },
            showInLegend: true
          }
        },
        series: [{
          name: 'Brands',
          colorByPoint: true,
          data: dataChart.data
        }]
      });
    },

    barChart(dataChart) {
      Highcharts.chart(dataChart.id, {
        chart: {
          type: 'column'
        },
        title: {
          text: dataChart.title
        },
        xAxis:{
          categories: ['']
        },
        yAxis: {
          min: 0,
        },
        tooltip: {
          headerFormat: '<table>',
          pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
          '<td style="padding:0"><b>{point.y:0f}</b></td></tr>',
          footerFormat: '</table>',
          shared: true,
          useHTML: true
        },
        plotOptions: {
          column: {
            pointPadding: 0.2,
            borderWidth: 0
          }
        },
        series: dataChart.data
      });
    }

  }

  Object.defineProperties(Vue.prototype, {
    $charts: {
      get () {
        return Vue.charts
      }
    }
  })
}



