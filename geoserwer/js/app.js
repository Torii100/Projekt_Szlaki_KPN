var geoserverUrl = "http://localhost:8080/geoserver";
var selectedPoint = null;

var source = null;
var target = null;

// initialize our map
var map = L.map("mymap", {
  center: [52.31, 20.58],
  zoom: 12, //set the zoom level
});

//add openstreet map baselayer to the map
var OpenStreetMap = L.tileLayer(
  "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
  {
    maxZoom: 19,
    attribution:
      '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
  }
).addTo(map);

// empty geojson layer for the shortes path result
var pathLayer = L.geoJSON(null);

// draggable marker for starting point. Note the marker is initialized with an initial starting position
var sourceMarker = L.marker([52.31, 20.58], {
  draggable: true,
})
  .on("dragend", function (e) {
    selectedPoint = e.target.getLatLng();
    getVertex(selectedPoint);
    getRoute();
  })
  .setZIndexOffset(666)
  .addTo(map);

// draggbale marker for destination point.Note the marker is initialized with an initial destination positon
var targetMarker = L.marker([52.31, 20.58], {
  draggable: true,
})
  .on("dragend", function (e) {
    selectedPoint = e.target.getLatLng();
    getVertex(selectedPoint);
    getRoute();
  })
  .setZIndexOffset(666)
  .addTo(map);

// function to get nearest vertex to the passed point
function getVertex(selectedPoint) {
  var url = `${geoserverUrl}
	/wfs?service=WFS&version=1.0.0&request=GetFeature&typeName=routing:nearest_vertex&outputformat=application/json&viewparams=x:${selectedPoint.lng};y:${selectedPoint.lat};`;
  $.ajax({
    url: url,
    async: false,
    success: function (data) {
      loadVertex(
        data,
        selectedPoint.toString() === sourceMarker.getLatLng().toString()
      );
    },
    error: function (err) {
      console.log("AJAX error in request: " + JSON.stringify(err, null, 2));
    },
  });
}

// function to update the source and target nodes as returned from geoserver for later querying
function loadVertex(response, isSource) {
  var features = response.features;
  map.removeLayer(pathLayer);
  if (isSource) {
    source = features[0].properties.id;
  } else {
    target = features[0].properties.id;
  }
}

// function to get the shortest path from the give source and target nodes
function getRoute() {
  var url = `${geoserverUrl}/wfs?service=WFS&version=1.0.0&request=GetFeature&
	typeName=routing:shortest_path&outputformat=application/json&viewparams
	=source:${source};target:${target};`;

  $.getJSON(url, function (data) {
    map.removeLayer(pathLayer);
    pathLayer = L.geoJSON(data);
    map.addLayer(pathLayer);
  });
}

getVertex(sourceMarker.getLatLng());
getVertex(targetMarker.getLatLng());
getRoute();
