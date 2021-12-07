const xhttp = new XMLHttpRequest();
xhttp.onload = function() {
  if (this.responseText == null) throw new Error("No graph data received"); //throw error if no response from API
  const res = JSON.parse(this.responseText); //parse JSON after confirming response not null
  if (res.error) throw new Error(res.error); //throw error if API returns error
  const resArrays = {
    ambient_temp: res.data.ambient_temp.split(";"), //converts res.data: "1,2;1,2;1,2" to an resArray ["1,2","1,2","1,2"]
    ground_temp: res.data.ground_temp.split(";"), //converts res.data: "1,2;1,2;1,2" to an resArray ["1,2","1,2","1,2"]
  }
  const data = {
    ambient_temp: [], //the array to push into
    ground_temp: []
  }
  for (var xy of resArrays.ambient_temp) { //takes each element of resArray and coverts it to point and pushes into the data array
    const x = parseFloat(xy.substring(0,xy.indexOf(',')));
    const y = parseFloat(xy.substring(xy.indexOf(',')+1));
    data.ambient_temp.push({x: x, y: y});
  }
  for (var xy of resArrays.ground_temp) { //takes each element of resArray and coverts it to point and pushes into the data array
    const x = parseFloat(xy.substring(0,xy.indexOf(',')));
    const y = parseFloat(xy.substring(xy.indexOf(',')+1));
    data.ground_temp.push({x: x, y: y});
  }
  const airTemp = new Chart(
    'graph_airTemp',
    {
      type: 'scatter',
      data: {
        datasets: [
          {
            label: 'Air Temperature',
            data: data.ambient_temp,
            backgroundColor: 'rgb(80, 60, 255)'
          },
          {
            label: 'Ground Temperature',
            data: data.ground_temp,
            backgroundColor: 'rgb(80, 60, 0)'
          }
        ],
      },
      options: {
        scales: {
          x: {
            title: {
              text: "Time (hrs)",
              display: true
            },
            ticks: {
              callback: function(value, index, values) {
                return value + ":00";
              }
            },
            type: 'linear',
            position: 'bottom',
            min: 0,
            max: 24
          },
          y: {
            title: {
              text: "Temp (C)",
              display: true
            }
          }
        }
      }
    }
  );  
};
xhttp.open("GET", "api/select/temperature/dayGraph.php");
xhttp.send();