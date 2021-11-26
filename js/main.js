var ui = {
    monitor: {
        update: function(weather){
            document.getElementById("datetime").innerHTML = weather.datetime;
            document.getElementById("version").innerHTML = weather.version;
            document.getElementById("comment").innerHTML = weather.comment;
            document.getElementById("wind_speed").innerHTML = weather.wind_speed;
            document.getElementById("gust_speed").innerHTML = weather.gust_speed;
            document.getElementById("wind_direction").innerHTML = weather.wind_direction;
            document.getElementById("rainfall").innerHTML = weather.rainfall;
            document.getElementById("ambient_temp").innerHTML = weather.ambient_temp;
            document.getElementById("ground_temp").innerHTML = weather.ground_temp;
            document.getElementById("humidity").innerHTML = weather.humidity;
            document.getElementById("pressure").innerHTML = weather.pressure;
            document.getElementById("datetime").classList.add("bg-green-fade");
            setTimeout(function(){ document.getElementById("datetime").classList.remove("bg-green-fade"); }, 2000);
        },
        empty: function(){
            var empty = {
                datetime: "n/a",
                version: "n/a",
                comment: "n/a",
                wind_speed: "n/a",
                gust_speed: "n/a",
                wind_direction: "n/a",
                rainfall: "n/a",
                ambient_temp: "n/a",
                ground_temp: "n/a",
                humidity: "n/a",
                pressure: "n/a",
            };
            this.update(empty);
        }
    },
    log: {
        add: function(weather){
            var log = document.getElementById("log");
            var output = "<strong>Datetime: " + weather.datetime + " - Version: " + weather.version + " - Comment: " + weather.comment + "</strong><br>";
            output += "Wind Speed: " + weather.wind_speed + "km/h (Gusting " + weather.gust_speed + "km/h) - Wind Direction: " + weather.wind_direction + " - Rainfall: " + weather.rainfall + "mm - Temp: " + weather.ambient_temp + "&#176;C(air) " + weather.ground_temp + "&#176;C(gnd) - Humidity: " + weather.humidity + "% - Pressure: " + weather.pressure + "mBar";
            output += "<hr>";
            output += log.innerHTML;
            log.innerHTML = output;
        },
        empty: function(){
            document.getElementById("log").innerHTML = "";
        }
    },
    reload: {
        toggle: function(){
            if (this.on) this.setOff();
            else this.setOn();
        },
        setOn: function(){
            this.on = true;
            document.getElementById("toggleAutoReload").innerHTML = "Auto-reload: ON";
            document.getElementById("toggleAutoReload").classList.add("btn-success");
            document.getElementById("toggleAutoReload").classList.remove("btn-secondary");
        },
        setOff: function(){
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
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                try{
                    if (this.responseText == null) throw new Error("No weather received"); //throw error if no response from API
                    const weather = JSON.parse(this.responseText); //parse JSON after confirming response not null
                    if (weather.error) {
                        throw new Error(weather.error); //throw error if API returns error
                    }
                    if (weather.datetime == api.select.previousDT) return; //don't update if no new entry in database
                    ui.log.add(weather);
                    ui.monitor.update(weather);
                    api.select.previousDT = weather.datetime;
                } catch (e) {
                    alert(e);
                    console.log(e);
                    ui.monitor.empty();
                    ui.reload.setOff();
                }
            }
            xhttp.open("GET", "api/select/latest/");
            xhttp.send();
        },
        previousDT: ""
    },
    delete: {
        all: function(){
            var key = prompt("Enter key:");
            if (key == null) return;
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                try{
                    if (this.responseText == null) throw new Error("No response from api/delete/all"); //throw error if no response from API
                    const res = JSON.parse(this.responseText); //parse JSON after confirming response not null
                    if (res.error) {
                        throw new Error(res.error); //throw error if API returns error
                    } else {
                        ui.reload.setOff();
                    } 
                    ui.monitor.empty();
                    ui.log.empty();
                    if (res.msg) alert(res.msg);
                } catch (e) {
                    alert(e);
                    console.log(e);
                }
            }
            xhttp.open("GET", "api/delete/all/?key="+key);
            xhttp.send();
        }
    }
}

api.select.latest();
window.setInterval(function() {
    if (ui.reload.on) api.select.latest();
}, 2000);