// Get Coordinate location
const latInput = $("#latitude");
const lngInput = $("#longitude");
$("#alertNotFound").addClass("d-none");

const map = L.map('map', {
  scrollWheelZoom: false,
  gestureHandling: true,
  gestureHandlingOptions: {
    duration: 1000, //1 secs
  },
}).setView([-6.211311604567863, 106.83123239131747], 12);

const tiles = L.tileLayer(
  'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
  attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, ' +
    'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
  id: 'mapbox/streets-v11',
  tileSize: 512,
  zoomOffset: -1,
}).addTo(map);


const curLocation = [-6.211311604567863, 106.83123239131747];
map.attributionControl.setPrefix(false);

const marker = new L.marker(curLocation, {
  // draggable: 'true', // Disable drag
})

map.addLayer(marker);

// Search Method 1 (Free + Depracted) (Change Script on Header)
const searchControl = new L.esri.Controls.Geosearch().addTo(map);

const results = new L.LayerGroup().addTo(map);

searchControl.on('results', function (data) {
  results.clearLayers();
  for (let i = data.results.length - 1; i >= 0; i--) {
    results.addLayer(marker.setLatLng(data.results[i].latlng));
    latInput.val(data.results[i].latlng.lat);
    lngInput.val(data.results[i].latlng.lng);
  }
});

map.on("click", function (e) {
  const lat = e.latlng.lat;
  const lng = e.latlng.lng;
  if (!marker) {
    marker = L.marker(e.latlng).addTo(map)
  } else {
    marker.setLatLng(e.latlng);
  }
  latInput.val(lat);
  lngInput.val(lng);
})

latInput.on("keyup", function (e) {
  const lat = e.target.value;
  let lng = lngInput.val();
  if (!lng) {
    lng = curLocation[1];
  }
  const location = {
    lat,
    lng
  }
  if (!marker) {
    marker = L.marker(location).addTo(map)
  } else {
    marker.setLatLng(location);
  }
})

lngInput.on("keyup", function (e) {
  const lng = e.target.value;
  let lat = latInput.val();
  if (!lat) {
    lat = curLocation[0];
  }
  const location = {
    lat,
    lng
  }
  if (!marker) {
    marker = L.marker(location).addTo(map)
  } else {
    marker.setLatLng(location);
  }
})

$("#subdis_id").on("change", function (e) {
  map.spin(true);
  const location = $('#subdis_id option:selected').text();
  const data = fetch(
    '//nominatim.openstreetmap.org/search?format=json&q=' + location
  ).then(response => response.json().then(data => {
    marker.setLatLng({
      lat: data[0].lat,
      lng: data[0].lon
    })
    latInput.val(data[0].lat);
    lngInput.val(data[0].lon);
    map.spin(false);
  })).catch(() => {
    $("#alertNotFound").removeClass("d-none");
    latInput.val(null);
    lngInput.val(null);
    map.spin(false);
  })
})

marker.on('move', function () {
  $("#alertNotFound").addClass("d-none");
  map.setView(this.getLatLng())
});