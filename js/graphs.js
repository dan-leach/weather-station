var graphs = {
  load: function(){
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
      if (this.responseText == null) throw new Error("No graph data received"); //throw error if no response from API
      const res = JSON.parse(this.responseText); //parse JSON after confirming response not null
      if (res.error) throw new Error(res.error); //throw error if API returns error
      graphs.temp.res(res)
      graphs.temp.create()
    };
    xhttp.open("GET", "api/select/graphs/");
    xhttp.send();
  },
  temp: {
    res: function(res){
      const resArrays = {
        ambient_temp: res.data.ambient_temp.split(","), //converts res.data: "1,2,3,4" to an resArray ["1","2","3","4"]
        ground_temp: res.data.ground_temp.split(","), 
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
        'graph_Temp',
        {
          type: 'line',
          data: {
            labels: graphs.temp.labels,
            datasets: [
              {
                label: 'Air Temperature',
                data: graphs.temp.data.ambient_temp,
                borderColor: 'rgb(80, 60, 255)',
                borderWidth: 3,
                backgroundColor: 'rgb(80, 60, 255)',
                radius: 0.5
              },
              {
                label: 'Ground Temperature',
                data: graphs.temp.data.ground_temp,
                borderColor: 'rgb(145, 117, 32)',
                borderWidth: 3,
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
  utils: {

  }
}

graphs.load()