var graphs = {
  load: function(){
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
      if (this.responseText == null) throw new Error("No graph data received"); //throw error if no response from API)
      //console.log(this.responseText)
      const res = JSON.parse(this.responseText); //parse JSON after confirming response not null
      console.log(res)
      if (res.error) throw new Error(res.error); //throw error if API returns error
      graphs.temp.res(res.data.ambient_temp, res.data.ground_temp)
      graphs.temp.create()
      graphs.wind.res(res.data.wind_speed, res.data.gust_speed)
      graphs.wind.create()
      graphs.windDir.res(res.data.wind_direction)
      graphs.windDir.create()
      graphs.windDir2.res(res.data.wind_direction)
      graphs.windDir2.create()
      graphs.pressureHumidity.res(res.data.pressure, res.data.humidity)
      graphs.pressureHumidity.create()
      graphs.rainfall.res(res.data.rainfall)
      graphs.rainfall.create()
      
    };
    xhttp.open("GET", "api/select/graphs/");
    xhttp.send();
  },
  temp: {
    res: function(res_ambient_temp, res_ground_temp){
      const resArrays = {
        ambient_temp: res_ambient_temp.split(","), //"1,2,3,4" to resArray ["1","2","3","4"]
        ground_temp: res_ground_temp.split(","), 
      }
      for (var x of resArrays.ambient_temp) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.temp.data.ambient_temp.push(parseFloat(x));
      }
      for (var x of resArrays.ground_temp) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.temp.data.ground_temp.push(parseFloat(x));
      }

      for (let hours = 0; hours < (graphs.temp.data.ground_temp.length / 60); hours++) {
        var strHours = hours.toString();
        if (strHours.length == 1) strHours = "0" + strHours;
        for (let mins = 0; mins < 60; mins++) {
          var strMins = mins.toString();
          if (strMins.length == 1) strMins = "0" + strMins;
          graphs.temp.labels.push(strHours + ":" + strMins);
        }
      }
    },
    create: function(){
      new Chart(
        'graph_temp',
        {
          type: 'line',
          data: {
            labels: graphs.temp.labels,
            datasets: [
              {
                label: 'Air Temperature',
                data: graphs.temp.data.ambient_temp,
                borderColor: 'rgb(80, 60, 255)',
                borderWidth: 2,
                backgroundColor: 'rgb(80, 60, 255)',
                radius: 0.5
              },
              {
                label: 'Ground Temperature',
                data: graphs.temp.data.ground_temp,
                borderColor: 'rgb(145, 117, 32)',
                borderWidth: 2,
                backgroundColor: 'rgb(145, 117, 32)',
                radius: 0.5
              }
            ]
          },
          options: {
            responsive: true,
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
              y: {
                display: true,
                beginAtZero: true,
                title: {
                  display: true,
                  text: "Temperature"
                },
                ticks: {
                  callback: function(val, index) {
                    return val + "°C"
                  }
                }
              }
            },
            plugins: {
              legend: {
                position: 'top',
              },
              title: {
                display: true,
                text: 'Temperature (Today)'
              }
            }
          },
        },
      ); 
    },
    data: {
      ambient_temp: [], //the array to push into
      ground_temp: []
    },
    labels: []
  },
  wind: {
    res: function(res_wind_speed, res_gust_speed){
      const resArray = {
        wind_speed: res_wind_speed.split(","), //"1,2,3,4" to resArray ["1","2","3","4"]
        gust_speed: res_gust_speed.split(",")
      }
      for (var x of resArray.wind_speed) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.wind.data.wind_speed.push(parseFloat(x));
      }
      for (var x of resArray.gust_speed) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.wind.data.gust_speed.push(parseFloat(x));
      }
      for (let hours = 0; hours < (graphs.wind.data.wind_speed.length / 60); hours++) {
        var strHours = hours.toString();
        if (strHours.length == 1) strHours = "0" + strHours;
        for (let mins = 0; mins < 60; mins++) {
          var strMins = mins.toString();
          if (strMins.length == 1) strMins = "0" + strMins;
          graphs.wind.labels.push(strHours + ":" + strMins);
        }
      }
    },
    create: function(){
      new Chart(
        'graph_wind',
        {
          type: 'line',
          data: {
            labels: graphs.wind.labels,
            datasets: [
              {
                label: 'Wind Speed',
                data: graphs.wind.data.wind_speed,
                borderColor: 'rgb(212, 237, 24)',
                borderWidth: 2,
                backgroundColor: 'rgb(212, 237, 24)',
                radius: 0.5
              },
              {
                label: 'Gust Speed',
                data: graphs.wind.data.gust_speed,
                borderColor: 'rgb(237, 56, 24)',
                borderWidth: 2,
                backgroundColor: 'rgb(237, 56, 24)',
                radius: 0.5
              }
            ]
          },
          options: {
            responsive: true,
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
              y: {
                display: true,
                beginAtZero: true,
                title: {
                  display: true,
                  text: "Wind Speed"
                },
                ticks: {
                  callback: function(val, index) {
                    return val + "km/h"
                  }
                }
              }
            },
            plugins: {
              legend: {
                position: "top"
              },
              title: {
                display: true,
                text: 'Wind Speed (Today)'
              }
            }
          },
        },
      ); 
    },
    data: {
      wind_speed: [],
      gust_speed: []
    },
    labels: []
  },
  windDir: {
    res: function(res_wind_direction){
      graphs.windDir.data = Object.values(res_wind_direction);
      graphs.windDir.labels = Object.keys(res_wind_direction);
    },
    create: function(){
      new Chart(
        'graph_windDir',
        {
          type: 'polarArea',
          data: {
            labels: graphs.windDir.labels,
            datasets: [
              {
                label: 'Wind Direction',
                data: graphs.windDir.data,
                borderColor: 'rgba(212, 237, 24, 0.5)',
                backgroundColor: 'rgba(212, 237, 24, 0.5)',
              }
            ]
          },
          options: {
            scales: {
              rad: {
                startAngle: -11.25,
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
                text: 'Wind Direction (Today) Chart Style Option 1'
              }
            }
          },
        }
      ); 
    },
    data: [],
    labels: []
  },
  windDir2: {
    res: function(res_wind_direction){
      graphs.windDir2.data = Object.values(res_wind_direction);
      graphs.windDir2.labels = Object.keys(res_wind_direction);
    },
    create: function(){
      new Chart(
        'graph_windDir2',
        {
          type: 'radar',
          data: {
            labels: graphs.windDir2.labels,
            datasets: [
              {
                label: 'Wind Direction',
                data: graphs.windDir2.data,
                borderColor: 'rgba(212, 237, 24, 0.5)',
                backgroundColor: 'rgba(212, 237, 24, 0.5)',
                tension: 0.4
              }
            ]
          },
          options: {
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
                text: 'Wind Direction (Today) Chart Style Option 2'
              }
            }
          },
        }
      ); 
    },
    data: [],
    labels: []
  },
  pressureHumidity: {
    res: function(res_pressure, res_humidity){
      const resArray = {
        pressure: res_pressure.split(","), //"1,2,3,4" to resArray ["1","2","3","4"]
        humidity: res_humidity.split(",")
      }
      for (var x of resArray.pressure) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.pressureHumidity.data.pressure.push(parseFloat(x));
      }
      for (var x of resArray.humidity) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.pressureHumidity.data.humidity.push(parseFloat(x));
      }
      for (let hours = 0; hours < (graphs.pressureHumidity.data.pressure.length / 60); hours++) {
        var strHours = hours.toString();
        if (strHours.length == 1) strHours = "0" + strHours;
        for (let mins = 0; mins < 60; mins++) {
          var strMins = mins.toString();
          if (strMins.length == 1) strMins = "0" + strMins;
          graphs.pressureHumidity.labels.push(strHours + ":" + strMins);
        }
      }
    },
    create: function(){
      new Chart(
        'graph_pressureHumidity',
        {
          type: 'line',
          data: {
            labels: graphs.pressureHumidity.labels,
            datasets: [
              {
                label: 'Pressure',
                data: graphs.pressureHumidity.data.pressure,
                borderColor: 'rgb(12, 204, 76)',
                borderWidth: 2,
                backgroundColor: 'rgb(12, 204, 76)',
                radius: 0.5,
                yAxisID: 'yPressure'
              },
              {
                label: 'Humidity',
                data: graphs.pressureHumidity.data.humidity,
                borderColor: 'rgb(70, 9, 184)',
                borderWidth: 2,
                backgroundColor: 'rgb(70, 9, 184)',
                radius: 0.5,
                yAxisID: 'yHumidity'
              }
            ]
          },
          options: {
            responsive: true,
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
              yPressure: {
                display: true,
                title: {
                  display: true,
                  text: "Pressure"
                },
                ticks: {
                  callback: function(val, index) {
                    return val + "mBar"
                  }
                }
              },
              yHumidity: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                  display: true,
                  text: "Humidity"
                },
                ticks: {
                  callback: function(val, index) {
                    return val + "%"
                  }
                },
                grid: {
                  drawOnChartArea: false, // only want the grid lines for one axis to show up
                },
              },
            },
            plugins: {
              legend: {
                position: "top"
              },
              title: {
                display: true,
                text: 'Pressure & Humidity (Today)'
              }
            }
          },
        },
      ); 
    },
    data: {
      pressure: [],
      humidity: []
    },
    labels: []
  },
  rainfall: {
    res: function(res_rainfall){
      const resArray = res_rainfall.split(","); //"1,2,3,4" to resArray ["1","2","3","4"]
      for (var x of resArray) { //takes each element of resArray and coverts it to point and pushes into the data array
        graphs.rainfall.data.push(parseFloat(x));
      }

      for (let hours = 0; hours < (graphs.rainfall.data.length / 60); hours++) {
        var strHours = hours.toString();
        if (strHours.length == 1) strHours = "0" + strHours;
        for (let mins = 0; mins < 60; mins++) {
          var strMins = mins.toString();
          if (strMins.length == 1) strMins = "0" + strMins;
          graphs.rainfall.labels.push(strHours + ":" + strMins);
        }
      }
    },
    create: function(){
      new Chart(
        'graph_rainfall',
        {
          type: 'line',
          data: {
            labels: graphs.rainfall.labels,
            datasets: [
              {
                label: 'Cumulative Rainfall',
                data: graphs.rainfall.data,
                borderColor: 'rgb(80, 60, 255)',
                borderWidth: 2,
                backgroundColor: 'rgb(80, 60, 255)',
                radius: 0.5
              }
            ]
          },
          options: {
            responsive: true,
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
              y: {
                display: true,
                beginAtZero: true,
                title: {
                  display: true,
                  text: "Rainfall"
                },
                ticks: {
                  callback: function(val, index) {
                    return val + "mm"
                  }
                }
              }
            },
            plugins: {
              legend: {
                position: 'top',
              },
              title: {
                display: true,
                text: 'Cumulative Rainfall (Today)'
              }
            }
          },
        },
      ); 
    },
    data: [],
    labels: []
  },
  utils: {

  }
}

graphs.load()