/* 
 *  @author    Carlos Cámara - GWE Systems
 *  @copyright Copyright (C) GWE Systems
 *  @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 *  @link http://www.jevents.net GWE Systems
 */

var  JEVENTS = JEVENTS || {};

JEVENTS.gmaps = {
	urlRoot:	"",
	locations:	[],
	mapId:	'',
	map:	'',
	maps: [],
	mapBounds:	'',
	mapStyle: {},
	mapOptions:	{},
	options: {},

	addLocation:	function(location)
					{
						if (!(location.mapId in this.maps))
						{
							var map = {};
							map.mapId = location.mapId;
							map.minMap = "";
							map.maxMap = "";
							this.maps[location.mapId] =  map;
						}
						this.locations.push(location);
					},

	addPoint:	function (location)
				{
					// Create our marker icon
					var markerIcon = new google.maps.MarkerImage(this.urlRoot + location.icon,
									// This marker is 32 pixels wide by 32 pixels tall.
									new google.maps.Size(32, 32),
									// The origin for this image is 0,0 within a sprite
									new google.maps.Point(0,0),
									// The anchor for this image is the base of the flagpole at 0,32.
									new google.maps.Point(16, 32));

					// Set up our GMarkerOptions object
					var point = new google.maps.LatLng(location.latitude,location.longitude);
					
					if (!("map" in this.maps[location.mapId]))
					{
						this.maps[location.mapId].map = new google.maps.Map(document.getElementById(location.mapId),this.mapOptions );
					}

					var map = this.maps[location.mapId].map;
					var markerOptions = { icon: markerIcon, draggable: false , map: map, position: point};
					var myMarkerMulti = new google.maps.Marker(markerOptions);
					var infowindow = new google.maps.InfoWindow({content: location.infoLayout});

					google.maps.event.addListener(myMarkerMulti, "mouseover", function(e) {
						  infowindow.open(map,myMarkerMulti);
					});

					google.maps.event.addListener(myMarkerMulti, "mouseout", function(e) {
						  infowindow.close(map,myMarkerMulti);
					});

					google.maps.event.addListener(myMarkerMulti, "click", function(e) {
						// use for event detail page
						window.open(location.url);
					});
				},
	init:		function()
				{
					var thisObject = this;
					
					this.setMapOptions(this.options);

					jQuery.each(thisObject.locations, function(index,location){
						thisObject.addPoint(location);
					});

					//thisObject.setMapBounds(this.maps[mapId]);
				},

	setIconsBaseUrl:	function(url)
						{
							this.urlRoot = url;
						},
	setMapId:	function(id)
				{
					this.mapId = id;
				},
	setMapOptions:	function(options)
					{
						var mapType = ""
						switch(options.mapType)
						{
							case "hybrid":
								mapType= google.maps.MapTypeId.HYBRID;
								break;
							case "satellite":
								mapType= google.maps.MapTypeId.SATELLITE;
								break;
							case "terrain":
								mapType= google.maps.MapTypeId.TERRAIN;
								break;
							default:
								mapType= google.maps.MapTypeId.ROADMAP;
						}						
						var dcenter = JSON.parse(options.center);
						
						this.mapOptions = {center: dcenter, mapTypeId: mapType, zoom: options.zoom, styles: this.mapStyle};
					},
	setMapStyle:	function(style)
					{
						if(style.length > 0)
							this.mapStyle = style;
					},
	setMapBounds:	function()
					{
						this.mapBounds = new google.maps.LatLngBounds(new google.maps.LatLng(this.mapMin.lat, this.mapMin.lng), new google.maps.LatLng(this.mapMax.lat,this.mapMax.lng));
						this.map.fitBounds(this.mapBounds);
					},
	setBounds:	function(mapMin, mapMax)
				{
					this.mapMin = mapMin;
					this.mapMax = mapMax;

				},
	setOptions:	function(options)
				{
					this.options = options;
				}

};
