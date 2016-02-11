// Create a map object
var map = new YMap(document.getElementById('map'));
// Set map type to either of: YAHOO_MAP_SAT, YAHOO_MAP_HYB, YAHOO_MAP_REG
map.setMapType(YAHOO_MAP_REG);
// Display the map centered on a geocoded location
map.drawZoomAndCenter("San Francisco", 3);