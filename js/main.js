var ui = {
    monitor: {
        update: function(weather){
            //console.log('ui.monitor.update');
            document.getElementById("status").innerHTML = "Updated: " + moment(weather.latest.datetime, 'YYYY-MM-DD hh:mm:ss').format('DD/MM/YY HH:mm');

            document.getElementById("ambient_temp").innerHTML = weather.latest.ambient_temp.toFixed(1);
            document.getElementById("ambient_temp_min").innerHTML = weather.minMax.ambient_temp.min.toFixed(1);
            document.getElementById("ambient_temp_max").innerHTML = weather.minMax.ambient_temp.max.toFixed(1);

            document.getElementById("ground_temp").innerHTML = weather.latest.ground_temp.toFixed(1);
            document.getElementById("ground_temp_min").innerHTML = weather.minMax.ground_temp.min.toFixed(1);
            document.getElementById("ground_temp_max").innerHTML = weather.minMax.ground_temp.max.toFixed(1);

            document.getElementById("pressure").innerHTML = weather.latest.pressure.toFixed();
            document.getElementById("pressure_min").innerHTML = weather.minMax.pressure.min.toFixed();
            document.getElementById("pressure_max").innerHTML = weather.minMax.pressure.max.toFixed();

            document.getElementById("humidity").innerHTML = weather.latest.humidity.toFixed();
            document.getElementById("humidity_min").innerHTML = weather.minMax.humidity.min.toFixed();
            document.getElementById("humidity_max").innerHTML = weather.minMax.humidity.max.toFixed();

            document.getElementById("wind_speed").innerHTML = weather.latest.wind_speed.toFixed(1);
            document.getElementById("gust_speed").innerHTML = weather.latest.gust_speed.toFixed(1);
            document.getElementById("gust_speed_max").innerHTML = weather.minMax.gust_speed.max.toFixed(1);
            
            document.getElementById("wind_direction").innerHTML = weather.latest.wind_direction;

            document.getElementById("rainfall").innerHTML = weather.cum.rainfall.toFixed(1);

            document.getElementById("status").classList.add("bg-green-fade");
            setTimeout(function(){ document.getElementById("status").classList.remove("bg-green-fade"); }, 10000);
        },
        empty: function(){
            console.log('ui.monitor.empty');
            var empty = {
                latest: {
                    datetime: moment(),
                    wind_speed: 0,
                    gust_speed: 0,
                    wind_direction: 0,
                    ambient_temp: 0,
                    ground_temp: 0,
                    humidity: 0,
                    pressure: 0,
                },
                cum: {
                    rainfall: 0,
                },
                minMax: {
                    ambient_temp: {
                        min: 0,
                        max: 0,
                    },
                    ground_temp: {
                        min: 0,
                        max: 0,
                    },
                    pressure: {
                        min: 0,
                        max: 0,
                    },
                    humidity: {
                        min: 0,
                        max: 0,
                    }
                }
            }
            this.update(empty);
        }
    },
    log: {
        add: function(weather){
            console.log("ui.log.add");
            var table = document.getElementById("log");
            
            var row = table.insertRow(2);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            var cell6 = row.insertCell(5);
            var cell7 = row.insertCell(6);
            var cell8 = row.insertCell(7);
            var cell9 = row.insertCell(8);
            
            cell1.innerHTML = "<strong>" + moment(weather.latest.datetime, 'YYYY-MM-DD hh:mm:ss').format('HH:mm') + "</strong>";
            cell2.innerHTML = weather.latest.wind_speed.toFixed(1);
            cell3.innerHTML = weather.latest.gust_speed.toFixed(1);
            cell4.innerHTML = weather.latest.wind_direction;
            cell5.innerHTML = weather.cum.rainfall.toFixed(1);
            cell6.innerHTML = weather.latest.ambient_temp.toFixed(1);
            cell7.innerHTML = weather.latest.ground_temp.toFixed(1);
            cell8.innerHTML = weather.latest.humidity.toFixed();
            cell9.innerHTML = weather.latest.pressure.toFixed();
        },
        empty: function(){
            console.log("ui.log.empty");
            var table = document.getElementById("log");
            var rowCount = table.rows.length;
            for (var i = rowCount - 1; i > 1; i--) {
                table.deleteRow(i);
            }
        }
    },
    reload: {
        toggle: function(){
            console.log("ui.reload.toggle");
            if (this.on) this.setOff();
            else this.setOn();
        },
        setOn: function(){
            console.log("ui.reload.setOn");
            this.on = true;
            document.getElementById("toggleAutoReload").innerHTML = "Auto-reload: ON";
            document.getElementById("toggleAutoReload").classList.add("btn-success");
            document.getElementById("toggleAutoReload").classList.remove("btn-secondary");
        },
        setOff: function(){
            console.log("ui.reload.setOff");
            this.on = false;
            document.getElementById("toggleAutoReload").innerHTML = "Auto-reload: OFF";
            document.getElementById("toggleAutoReload").classList.remove("btn-success");
            document.getElementById("toggleAutoReload").classList.add("btn-secondary");
        },
        on: true
    }
}

var api = {
    select: {
        latest: function(){
            //console.log("api.select.latest");
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                try{
                    if (this.responseText == null) throw new Error("No weather received"); //throw error if no response from API
                    const weather = JSON.parse(this.responseText); //parse JSON after confirming response not null
                    if (weather.error) {
                        throw new Error(weather.error); //throw error if API returns error
                    }
                    if (weather.latest.datetime == api.select.previousDT) return; //don't update if no new entry in database
                    ui.log.add(weather);
                    ui.monitor.update(weather);
                    api.select.previousDT = weather.latest.datetime;
                } catch (e) {
                    api.error.new(e);
                }
            };
            xhttp.open("GET", "api/select/latest/");
            xhttp.send();
        },
        previousDT: ""
    },
    delete: {
        all: function(){
            (async () => {
                const { value: key } = await Swal.fire({
                  title: 'Enter key to delete all weather data',
                  input: 'password',
                  inputLabel: 'Key',
                  inputPlaceholder: 'Enter key',
                  inputAttributes: {
                    maxlength: 10,
                    autocapitalize: 'off',
                    autocorrect: 'off'
                  },
                  showCancelButton: true,
                  showConfirmButton: true,
                  confirmButtonText: "Delete all weather data",
                  confirmButtonColor: "#ff1100",
                  reverseButtons: true
                })
                if (key) {
                    const xhttp = new XMLHttpRequest();
                    xhttp.onload = function() {
                        try{
                            if (this.responseText == null) throw new Error("No response from api/delete/all"); //throw error if no response from API
                            const res = JSON.parse(this.responseText); //parse JSON after confirming response not null
                            if (res.error) throw new Error(res.error); //throw error if API returns error
                            if (res.failMsg) Swal.fire({
                                text: res.failMsg,
                                icon: "error"
                            });
                            if (res.passMsg) {
                                Swal.fire({
                                    text: res.passMsg,
                                    icon: "success"
                                });
                                ui.reload.setOff();
                                ui.monitor.empty();
                                ui.log.empty();
                            } 
                        } catch (e) {
                            api.error.new(e);
                        }
                    }
                    xhttp.open("GET", "api/delete/all/?key="+key);
                    xhttp.send();
                }
            })();
        }
    },
    error: {
        new: function(e){
            console.log("api.error.new: ", e);
            ui.monitor.empty();
            document.getElementById("status").innerHTML = "Error: see console.log";
            document.getElementById("status").classList.add("bg-red-fade");
            setTimeout(function(){ document.getElementById("status").classList.remove("bg-red-fade"); }, 10000);
            ui.reload.setOff();
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                console.log(this.responseText);
            }
            xhttp.open("GET", "api/error/new/?source=client&stack=" + e.stack);
            xhttp.send();
        },
        check: function(){
            console.log("api.error.check");
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                try{
                    if (this.responseText == null) throw new Error("No response from api/error/check"); //throw error if no response from API
                    const res = JSON.parse(this.responseText); //parse JSON after confirming response not null
                    document.getElementById("error").innerHTML = ""; //clear error div before populating
                    for (err in res) {
                        if (res[err].length > 0) {
                            console.log("Log of " + err + " errors found");
                            document.getElementById("error").innerHTML += '<button class="btn btn-sm btn-warning" onClick="api.error.show(' + "'" + err + "'" + ')">' + err.charAt(0).toUpperCase() + err.slice(1) + ' Errors</button>&nbsp;';
                            api.error.content[err] = atob(res[err]); //atob decodes base64 into plain string
                        }
                    }
                } catch (e) {
                    api.error.new(e);
                }
            }
            xhttp.open("GET", "api/error/check/");
            xhttp.send();
        },
        content: {
            client: "",
            server: "",
            station: ""
        },
        show: function(source){
            Swal.fire({
                title: source.charAt(0).toUpperCase() + source.slice(1) + " Errors",
                html: this.content[source],
                icon: "warning",
                showConfirmButton: true,
                confirmButtonText: "Clear " + source + " error log",
                confirmButtonColor: "#ff1100",
                showCancelButton: true,
                reverseButtons: true,
            }).then((clear) => {
                if (clear.isConfirmed) {
                    (async () => {
                        const { value: key } = await Swal.fire({
                          title: 'Enter key',
                          input: 'password',
                          inputLabel: 'Key',
                          inputPlaceholder: 'Enter key',
                          inputAttributes: {
                            maxlength: 10,
                            autocapitalize: 'off',
                            autocorrect: 'off'
                          }
                        })
                        if (key) {
                            api.error.clear(source, key);
                        }
                    })();
                    
                }
            }) 
        },
        clear: function(source, key){
            console.log("api.error.clear: " + source + " errors");
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                try{
                    if (this.responseText == null) throw new Error("No response from api/error/clear"); //throw error if no response from API
                    const res = JSON.parse(this.responseText); //parse JSON after confirming response not null
                    if (res.error) throw new Error(res.error);
                    if (res.failMsg) Swal.fire({
                        text: res.failMsg,
                        icon: "error"
                    });
                    if (res.passMsg) Swal.fire({
                        text: res.passMsg,
                        icon: "success"
                    });
                    api.error.check();
                } catch (e) {
                    api.error.new(e);
                }
            }
            xhttp.open("GET", "api/error/clear/?source="+source+"&key="+key);
            xhttp.send();
        }
    }
}

try {
    api.error.check();
    api.select.latest();
    window.setInterval(function() {
        console.log('Reload');
        if (ui.reload.on) api.select.latest();
    }, 60000);
} catch (e) {
    api.error.new(e);
}
