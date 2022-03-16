var chart_today = {
  update: function(){ return null; }
};

var graphs = {
  load: function(){
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
      if (this.responseText == null) throw new Error("No graph data received"); //throw error if no response from API)
      //console.log(this.responseText)
      const res = JSON.parse(this.responseText); //parse JSON after confirming response not null
      console.log(res)
      if (res.error) throw new Error(res.error); //throw error if API returns error
      graphs.today.res(res.data.today.ambient_temp, res.data.today.ground_temp, res.data.today.wind_speed, res.data.today.gust_speed, res.data.today.pressure, res.data.today.humidity, res.data.today.rainfall, res.data.today.power)
      graphs.today.create()
      graphs.windDir.res(res.data.today.wind_direction)
      graphs.windDir.create()
      graphs.week.res(res.data.week.ambient_temp, res.data.week.ground_temp, res.data.week.wind_speed, res.data.week.gust_speed, res.data.week.pressure, res.data.week.humidity, res.data.week.rainfall, res.data.week.power, res.data.week.labels)
      graphs.week.create()
    };
    xhttp.open("GET", "api/select/graphs/");
    xhttp.send();
  },
  today: {
    res: function(res_ambient_temp, res_ground_temp, res_wind_speed, res_gust_speed, res_pressure, res_humidity, res_rainfall, res_power){     
      const resArrays = {
        ambient_temp: res_ambient_temp.split(","), //"1,2,3,4" to resArray ["1","2","3","4"]
        ground_temp: res_ground_temp.split(","),
        wind_speed: res_wind_speed.split(","),
        gust_speed: res_gust_speed.split(","),
        pressure: res_pressure.split(","),
        humidity: res_humidity.split(","),
        rainfall: res_rainfall.split(","),
        power: res_power.split(",")
      }
      for (var x of resArrays.ambient_temp) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.today.data.ambient_temp.push(parseFloat(x));
      }
      for (var x of resArrays.ground_temp) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.today.data.ground_temp.push(parseFloat(x));
      }
      for (var x of resArrays.wind_speed) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.today.data.wind_speed.push(parseFloat(x));
      }
      for (var x of resArrays.gust_speed) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.today.data.gust_speed.push(parseFloat(x));
      }
      for (var x of resArrays.pressure) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.today.data.pressure.push(parseFloat(x));
      }
      for (var x of resArrays.humidity) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.today.data.humidity.push(parseFloat(x));
      }
      for (var x of resArrays.rainfall) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.today.data.rainfall.push(parseFloat(x));
      }
      for (var x of resArrays.power) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.today.data.power.push(parseFloat(x));
      }
      for (let hours = 0; hours < (graphs.today.data.ground_temp.length / 60); hours++) {
        var strHours = hours.toString();
        if (strHours.length == 1) strHours = "0" + strHours;
        for (let mins = 0; mins < 60; mins++) {
          var strMins = mins.toString();
          if (strMins.length == 1) strMins = "0" + strMins;
          graphs.today.labels.push(strHours + ":" + strMins);
        }
      }
    },
    create: function(){
      new Chart(
        'graph_today',
        {
          type: 'line',
          data: {
            labels: graphs.today.labels,
            datasets: [
              {
                label: 'Wind Speed (km/h)',
                data: graphs.today.data.wind_speed,
                borderColor: 'rgb(212, 237, 24)',
                borderWidth: 2,
                backgroundColor: 'rgb(212, 237, 24)',
                radius: 0.5,
                spanGaps: true,
                segment: {
                  borderColor: ctx => graphs.utils.skipped(ctx, 'rgb(0,0,0,0.2)'),
                  borderDash: ctx => graphs.utils.skipped(ctx, [6, 6]),
                },
                fill: true,
                yAxisID: 'yWind'
              },
              {
                label: 'Gust Speed (km/h)',
                data: graphs.today.data.gust_speed,
                borderColor: 'rgb(237, 56, 24)',
                borderWidth: 2,
                backgroundColor: 'rgb(237, 56, 24)',
                radius: 0.5,
                spanGaps: true,
                segment: {
                  borderColor: ctx => graphs.utils.skipped(ctx, 'rgb(0,0,0,0.2)'),
                  borderDash: ctx => graphs.utils.skipped(ctx, [6, 6]),
                },
                fill: true,
                yAxisID: 'yWind'
              },
              {
                label: 'Power (kW)',
                data: graphs.today.data.power,
                borderColor: 'rgb(245, 155, 0)',
                borderWidth: 2,
                backgroundColor: 'rgb(245, 155, 0)',
                radius: 0.5,
                yAxisID: 'yPower',
                spanGaps: true,
                segment: {
                  borderColor: ctx => graphs.utils.skipped(ctx, 'rgb(0,0,0,0.2)'),
                  borderDash: ctx => graphs.utils.skipped(ctx, [6, 6]),
                }
              },
              {
                label: 'Cumulative Rainfall (mm)',
                data: graphs.today.data.rainfall,
                borderColor: 'rgb(80, 60, 255)',
                borderWidth: 2,
                backgroundColor: 'rgb(80, 60, 255)',
                radius: 0.5,
                yAxisID: 'yRainfall',
                spanGaps: true,
                segment: {
                  borderColor: ctx => graphs.utils.skipped(ctx, 'rgb(0,0,0,0.2)'),
                  borderDash: ctx => graphs.utils.skipped(ctx, [6, 6]),
                }
              },
              {
                label: 'Pressure (mbar)',
                data: graphs.today.data.pressure,
                borderColor: 'rgb(12, 204, 76)',
                borderWidth: 2,
                backgroundColor: 'rgb(12, 204, 76)',
                radius: 0.5,
                yAxisID: 'yPressure',
                spanGaps: true,
                segment: {
                  borderColor: ctx => graphs.utils.skipped(ctx, 'rgb(0,0,0,0.2)'),
                  borderDash: ctx => graphs.utils.skipped(ctx, [6, 6]),
                }
              },
              {
                label: 'Humidity (%)',
                data: graphs.today.data.humidity,
                borderColor: 'rgb(252, 3, 232)',
                borderWidth: 2,
                backgroundColor: 'rgb(252, 3, 232)',
                radius: 0.5,
                yAxisID: 'yHumidity',
                spanGaps: true,
                segment: {
                  borderColor: ctx => graphs.utils.skipped(ctx, 'rgb(0,0,0,0.2)'),
                  borderDash: ctx => graphs.utils.skipped(ctx, [6, 6]),
                }
              },
              {
                label: 'Air Temperature (°C)',
                data: graphs.today.data.ambient_temp,
                borderColor: 'rgb(0,0,0)',
                borderWidth: 2,
                backgroundColor: 'rgb(0,0,0)',
                radius: 0.5,
                spanGaps: true,
                segment: {
                  borderColor: ctx => graphs.utils.skipped(ctx, 'rgb(0,0,0,0.2)'),
                  borderDash: ctx => graphs.utils.skipped(ctx, [6, 6]),
                },
                yAxisID: 'yTemp'
              },
              {
                label: 'Ground Temperature (°C)',
                data: graphs.today.data.ground_temp,
                borderColor: 'rgb(145, 117, 32)',
                borderWidth: 2,
                backgroundColor: 'rgb(145, 117, 32)',
                radius: 0.5,
                spanGaps: true,
                segment: {
                  borderColor: ctx => graphs.utils.skipped(ctx, 'rgb(0,0,0,0.2)'),
                  borderDash: ctx => graphs.utils.skipped(ctx, [6, 6]),
                },
                yAxisID: 'yTemp'
              }
            ]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              x: {
                display: true,
                title: {
                  display: true,
                  text: "Time"
                },
                ticks: {
                  callback: function(val, index) {
                    var label = this.getLabelForValue(val)
                    if (label.endsWith('00')) {
                      return this.getLabelForValue(val)
                    } else {
                      return null //don't show label if not on the hour
                    }
                  }
                }
              },
              yWind: {
                display: true,
                beginAtZero: true,
                ticks: {
                  callback: function(val, index) {
                    return val
                  }
                },
                stack: 'today',
                stackWeight: 1
              },
              yPower: {
                display: true,
                beginAtZero: true,
                ticks: {
                  callback: function(val, index) {
                    return val
                  }
                },
                stack: 'today',
                stackWeight: 1,
                offset: true
              },
              yRainfall: {
                display: true,
                beginAtZero: true,
                ticks: {
                  callback: function(val, index) {
                    return val
                  }
                },
                stack: 'today',
                stackWeight: 1,
                offset: true
              },
              yPressure: {
                display: true,
                ticks: {
                  callback: function(val, index) {
                    return val
                  }
                },
                stack: 'today',
                stackWeight: 1,
                offset: true
              },
              yBlank3: { //do not display but keeps down humidity scale
                display: false,
                position: 'right',
                stack: 'today',
                stackWeight: 1
              },
              yHumidity: {
                type: 'linear',
                display: true,
                position: 'right',
                ticks: {
                  callback: function(val, index) {
                    return val
                  }
                },
                grid: {
                  drawOnChartArea: false, // only want the grid lines for one axis to show up
                },
                stack: 'today',
                stackWeight: 1
              },
              yBlank: { //do not display but lifts up humidity scale
                display: false,
                position: 'right',
                stack: 'today',
                stackWeight: 1
              },
              yBlank2: { //do not display but lifts up humidity scale
                display: false,
                position: 'right',
                stack: 'today',
                stackWeight: 1
              },
              yBlank2: { //do not display but lifts up humidity scale
                display: false,
                position: 'right',
                stack: 'today',
                stackWeight: 1
              },
              yTemp: {
                display: true,
                ticks: {
                  callback: function(val, index) {
                    return val
                  }
                },
                stack: 'today',
                stackWeight: 1
              }
            },
            plugins: {
              legend: {
                position: 'top',
              },
              title: {
                display: true,
                text: 'Weather Today'
              }
            }
          },
        },
      ); 
    },
    data: { //the arrays to push into
      ambient_temp: [], 
      ground_temp: [],
      wind_speed: [],
      gust_speed: [],
      pressure: [],
      humidity: [],
      rainfall: [],
      power: []
    },
    labels: []
  },
  windDir: {
    res: function(res_wind_direction){
      graphs.windDir.data = Object.values(res_wind_direction);
      graphs.windDir.labels = Object.keys(res_wind_direction);
    },
    chartObj: {},
    create: function(){
      chart_today = new Chart(
        'graph_windDir',
        {
          type: 'radar',
          data: {
            labels: graphs.windDir.labels,
            datasets: [
              {
                label: 'Wind Direction',
                data: graphs.windDir.data,
                borderColor: 'rgba(212, 237, 24, 0.5)',
                backgroundColor: 'rgba(212, 237, 24, 0.5)',
                tension: 0.4
              }
            ]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              rad: {
                ticks: {
                  display: false
                }
              }
            },
            plugins: {
              legend: {
                display: false
              },
              title: {
                display: true,
                text: 'Wind Direction (Today)'
              }
            }
          },
        }
      ); 
    },
    data: [],
    labels: []
  },
  week: {
    res: function(res_ambient_temp, res_ground_temp, res_wind_speed, res_gust_speed, res_pressure, res_humidity, res_rainfall, res_power, res_labels){
      const resArrays = {
        ambient_temp: res_ambient_temp.split(","), //"1,2,3,4" to resArray ["1","2","3","4"]
        ground_temp: res_ground_temp.split(","),
        wind_speed: res_wind_speed.split(","),
        gust_speed: res_gust_speed.split(","),
        pressure: res_pressure.split(","),
        humidity: res_humidity.split(","),
        rainfall: res_rainfall.split(","),
        power: res_power.split(","),
        labels: res_labels.split(",")
      }
      for (var x of resArrays.ambient_temp) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.week.data.ambient_temp.push(parseFloat(x));
      }
      for (var x of resArrays.ground_temp) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.week.data.ground_temp.push(parseFloat(x));
      }
      for (var x of resArrays.wind_speed) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.week.data.wind_speed.push(parseFloat(x));
      }
      for (var x of resArrays.gust_speed) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.week.data.gust_speed.push(parseFloat(x));
      }
      for (var x of resArrays.pressure) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.week.data.pressure.push(parseFloat(x));
      }
      for (var x of resArrays.humidity) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.week.data.humidity.push(parseFloat(x));
      }
      for (var x of resArrays.rainfall) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.week.data.rainfall.push(parseFloat(x));
      }
      for (var x of resArrays.power) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.week.data.power.push(parseFloat(x));
      }
      for (var x of resArrays.labels) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.week.labels.push(x);
      }
    },
    create: function(){
      new Chart(
        'graph_week',
        {
          type: 'line',
          data: {
            labels: graphs.week.labels,
            datasets: [
              {
                label: 'Wind Speed (km/h)',
                data: graphs.week.data.wind_speed,
                borderColor: 'rgb(212, 237, 24)',
                borderWidth: 2,
                backgroundColor: 'rgb(212, 237, 24)',
                radius: 0.5,
                spanGaps: true,
                segment: {
                  borderColor: ctx => graphs.utils.skipped(ctx, 'rgb(0,0,0,0.2)'),
                  borderDash: ctx => graphs.utils.skipped(ctx, [6, 6]),
                },
                fill: true,
                yAxisID: 'yWind'
              },
              {
                label: 'Gust Speed (km/h)',
                data: graphs.week.data.gust_speed,
                borderColor: 'rgb(237, 56, 24)',
                borderWidth: 2,
                backgroundColor: 'rgb(237, 56, 24)',
                radius: 0.5,
                spanGaps: true,
                segment: {
                  borderColor: ctx => graphs.utils.skipped(ctx, 'rgb(0,0,0,0.2)'),
                  borderDash: ctx => graphs.utils.skipped(ctx, [6, 6]),
                },
                fill: true,
                yAxisID: 'yWind'
              },
              {
                label: 'Power (kW)',
                data: graphs.week.data.power,
                borderColor: 'rgb(245, 155, 0)',
                borderWidth: 2,
                backgroundColor: 'rgb(245, 155, 0)',
                radius: 0.5,
                yAxisID: 'yPower'
              },
              {
                label: 'Cumulative Rainfall (mm)',
                data: graphs.week.data.rainfall,
                borderColor: 'rgb(80, 60, 255)',
                borderWidth: 2,
                backgroundColor: 'rgb(80, 60, 255)',
                radius: 0.5,
                yAxisID: 'yRainfall'
              },
              {
                label: 'Pressure (mbar)',
                data: graphs.week.data.pressure,
                borderColor: 'rgb(12, 204, 76)',
                borderWidth: 2,
                backgroundColor: 'rgb(12, 204, 76)',
                radius: 0.5,
                yAxisID: 'yPressure',
                spanGaps: true,
                segment: {
                  borderColor: ctx => graphs.utils.skipped(ctx, 'rgb(0,0,0,0.2)'),
                  borderDash: ctx => graphs.utils.skipped(ctx, [6, 6]),
                }
              },
              {
                label: 'Humidity (%)',
                data: graphs.week.data.humidity,
                borderColor: 'rgb(252, 3, 232)',
                borderWidth: 2,
                backgroundColor: 'rgb(252, 3, 232)',
                radius: 0.5,
                yAxisID: 'yHumidity',
                spanGaps: true,
                segment: {
                  borderColor: ctx => graphs.utils.skipped(ctx, 'rgb(0,0,0,0.2)'),
                  borderDash: ctx => graphs.utils.skipped(ctx, [6, 6]),
                }
              },
              {
                label: 'Air Temperature (°C)',
                data: graphs.week.data.ambient_temp,
                borderColor: 'rgb(0,0,0)',
                borderWidth: 2,
                backgroundColor: 'rgb(0,0,0)',
                radius: 0.5,
                spanGaps: true,
                segment: {
                  borderColor: ctx => graphs.utils.skipped(ctx, 'rgb(0,0,0,0.2)'),
                  borderDash: ctx => graphs.utils.skipped(ctx, [6, 6]),
                },
                yAxisID: 'yTemp'
              },
              {
                label: 'Ground Temperature (°C)',
                data: graphs.week.data.ground_temp,
                borderColor: 'rgb(145, 117, 32)',
                borderWidth: 2,
                backgroundColor: 'rgb(145, 117, 32)',
                radius: 0.5,
                spanGaps: true,
                segment: {
                  borderColor: ctx => graphs.utils.skipped(ctx, 'rgb(0,0,0,0.2)'),
                  borderDash: ctx => graphs.utils.skipped(ctx, [6, 6]),
                },
                yAxisID: 'yTemp'
              }
            ]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              x: {
                display: true,
                title: {
                  display: true,
                  text: "Time"
                },
                ticks: {
                  callback: function(val, index) {
                    var label = this.getLabelForValue(val)
                    if (label.endsWith('12:00') || label.endsWith('00:00')) {
                      return this.getLabelForValue(val)
                    } else {
                      return null //don't show label if not on the hour
                    }
                  }
                }
              },
              yWind: {
                display: true,
                beginAtZero: true,
                ticks: {
                  callback: function(val, index) {
                    return val
                  }
                },
                stack: 'week',
                stackWeight: 1
              },
              yPower: {
                display: true,
                beginAtZero: true,
                ticks: {
                  callback: function(val, index) {
                    return val
                  }
                },
                stack: 'week',
                stackWeight: 1,
                offset: true
              },
              yRainfall: {
                display: true,
                beginAtZero: true,
                ticks: {
                  callback: function(val, index) {
                    return val
                  }
                },
                stack: 'week',
                stackWeight: 1,
                offset: true
              },
              yPressure: {
                display: true,
                ticks: {
                  callback: function(val, index) {
                    return val
                  }
                },
                stack: 'week',
                stackWeight: 1,
                offset: true
              },
              yBlank3: { //do not display but keeps down humidity scale
                display: false,
                position: 'right',
                stack: 'week',
                stackWeight: 1
              },
              yHumidity: {
                type: 'linear',
                display: true,
                position: 'right',
                ticks: {
                  callback: function(val, index) {
                    return val
                  }
                },
                grid: {
                  drawOnChartArea: false, // only want the grid lines for one axis to show up
                },
                stack: 'week',
                stackWeight: 1
              },
              yBlank: { //do not display but lifts up humidity scale
                display: false,
                position: 'right',
                stack: 'week',
                stackWeight: 1
              },
              yBlank2: { //do not display but lifts up humidity scale
                display: false,
                position: 'right',
                stack: 'week',
                stackWeight: 1
              },
              yBlank4: { //do not display but lifts up humidity scale
                display: false,
                position: 'right',
                stack: 'week',
                stackWeight: 1
              },
              yTemp: {
                display: true,
                ticks: {
                  callback: function(val, index) {
                    return val
                  }
                },
                stack: 'week',
                stackWeight: 1
              }
            },
            plugins: {
              legend: {
                position: 'top',
              },
              title: {
                display: true,
                text: 'Weather This Week'
              }
            }
          },
        },
      ); 
    },
    data: { //the arrays to push into
      ambient_temp: [], 
      ground_temp: [],
      wind_speed: [],
      gust_speed: [],
      pressure: [],
      humidity: [],
      rainfall: [],
      power: []
    },
    labels: []
  },
  utils: {
    skipped: function(ctx, value){
      if (ctx.p0.skip || ctx.p1.skip) return value;
    }
  }
}

graphs.load()