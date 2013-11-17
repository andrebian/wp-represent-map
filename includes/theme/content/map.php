<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
<meta charset="utf-8">
<style>
    html, body, #map-canvas {
        height: <?php echo $height_map; ?>;
        width: 100%;
        margin: 0px;
        padding: 0px
    }
</style>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&language=pt_BR"></script>
<script>

    var map;
    var infowindow = null;
    var gmarkers = [];
    var markerTitles = [];
    var highestZIndex = 0;
    var agent = "default";
    var zoomControl = true;
    
    // detect browser agent
    $(document).ready(function(){
        if(navigator.userAgent.toLowerCase().indexOf("iphone") > -1 || navigator.userAgent.toLowerCase().indexOf("ipod") > -1) {
          agent = "iphone";
          zoomControl = false;
        }
        if (navigator.userAgent.toLowerCase().indexOf("ipad") > -1) {
            agent = "ipad";
            zoomControl = false;
        }
    });


    // initialize map
    function initialize() {
        // set map styles
        var mapStyles = [
            {
                featureType: "road",
                elementType: "geometry",
                stylers: [
                    {hue: "#8800ff"},
                    {lightness: 100}
                ]
            }, {
                featureType: "road",
                stylers: [
                    {visibility: "on"},
                    {hue: "#91ff00"},
                    {saturation: -62},
                    {gamma: 1.98},
                    {lightness: 45}
                ]
            }, {
                featureType: "water",
                stylers: [
                    {hue: "#005eff"},
                    {gamma: 0.72},
                    {lightness: 42}
                ]
            }, {
                featureType: "transit.line",
                stylers: [
                    {visibility: "off"}
                ]
            }, {
                featureType: "administrative.locality",
                stylers: [
                    {visibility: "on"}
                ]
            }, {
                featureType: "administrative.neighborhood",
                elementType: "geometry",
                stylers: [
                    {visibility: "simplified"}
                ]
            }, {
                featureType: "landscape",
                stylers: [
                    {visibility: "on"},
                    {gamma: 0.41},
                    {lightness: 46}
                ]
            }, {
                featureType: "administrative.neighborhood",
                elementType: "labels.text",
                stylers: [
                    {visibility: "on"},
                    {saturation: 33},
                    {lightness: 20}
                ]
            }
        ];

        // set map options
        var myOptions = {
            zoom: 13,
            center: new google.maps.LatLng(<?php echo $lat_lng; ?>),
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: false,
            mapTypeControl: false,
            panControl: false,
            zoomControl: true,
            //styles: mapStyles,
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.LARGE,
                position: google.maps.ControlPosition.LEFT_BOTTOM
            }
        };
        map = new google.maps.Map(document.getElementById('map-canvas'), myOptions);
        zoomLevel = map.getZoom();


        markers = new Array();

        //########################################
        // Here is the magical

        <?php if ( !empty($posts) ) :
            foreach($posts as $post) :
                $lat_lng = explode(',',get_post_meta($post->ID, '_wp_represent_map_lat_lng', true));
                $lat = $lat_lng[0];
                $lng = $lat_lng[1];
                echo "markers.push(['".$post->post_title."', '".$type."', '".$lat."', '".$lng."', '".$post->post_title."', '".$post->post_title."', '".get_post_meta($post->ID, '_wp_represent_map_address', true)."']);";
            endforeach;
        endif; ?>
//        markers.push(['Casa do Andre', 'outdoor', '-25.4447741', '-49.2291509', 'Teste', 'teste', 'Rua Miguel Caluf, 241, Cajuru']);
//
//        markers.push(['Trabalho do Andre', 'startup', '-25.495249', '-49.288399', 'Teste', 'teste', 'Rua José Cadilhe, 1283 Água Verde']);
//
//        markers.push(['Casa antiga', 'hackerspace', '-26.226199', '-51.098622', 'Teste', 'teste', 'Av Manoel Ribas 420, União da Vitória Paraná CEP 84600-000']);


        // Here is the magical
        //########################################

        // add markers
        jQuery.each(markers, function(i, val) {
            infowindow = new google.maps.InfoWindow({
                content: ""
            });

            // offset latlong ever so slightly to prevent marker overlap
            rand_x = Math.random();
            rand_y = Math.random();
            val[2] = parseFloat(val[2]) + parseFloat(parseFloat(rand_x) / 6000);
            val[3] = parseFloat(val[3]) + parseFloat(parseFloat(rand_y) / 6000);

            // show smaller marker icons on mobile
            if (agent == "iphone") {
                var iconSize = new google.maps.Size(16, 19);
            } else {
                iconSize = null;
            }

            // build this marker
            var markerImage = new google.maps.MarkerImage("<?php echo $url_base; ?>img/icons/" + val[1] + ".png", null, null, null, iconSize);
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(val[2], val[3]),
                map: map,
                title: '',
                clickable: true,
                infoWindowHtml: '',
                zIndex: 10 + i,
                icon: markerImage
            });
            marker.type = val[1];
            gmarkers.push(marker);

            // add marker hover events (if not viewing on mobile)
            if (agent == "default") {
                google.maps.event.addListener(marker, "mouseover", function() {
                    this.old_ZIndex = this.getZIndex();
                    this.setZIndex(9999);
                    $("#marker" + i).css("display", "inline");
                    $("#marker" + i).css("z-index", "99999");
                });
                google.maps.event.addListener(marker, "mouseout", function() {
                    if (this.old_ZIndex && zoomLevel <= 15) {
                        this.setZIndex(this.old_ZIndex);
                        $("#marker" + i).css("display", "none");
                    }
                });
            }

            // format marker URI for display and linking
            var markerURI = val[5];
            if (markerURI.substr(0, 7) != "http://") {
                markerURI = "http://" + markerURI;
            }
            var markerURI_short = markerURI.replace("http://", "");
            var markerURI_short = markerURI_short.replace("www.", "");

            // add marker click effects (open infowindow)
            google.maps.event.addListener(marker, 'click', function() {
                infowindow.setContent(
                        "<div class='marker_title'>" + val[0] + "</div>"
                        + "<div class='marker_uri'><a target='_blank' href='" + markerURI + "'>" + markerURI_short + "</a></div>"
                        + "<div class='marker_desc'>" + val[4] + "</div>"
                        + "<div class='marker_address'>" + val[6] + "</div>"
                        );
                infowindow.open(map, this);
            });

            // add marker label
//                    var latLng = new google.maps.LatLng(val[2], val[3]);
//                    var label = new Label({
//                        map: map,
//                        id: i
//                    });
//                    label.bindTo('position', marker);
//                    label.set("text", val[0]);
//                    label.bindTo('visible', marker);
//                    label.bindTo('clickable', marker);
//                    label.bindTo('zIndex', marker);
        });

    }

    google.maps.event.addDomListener(window, 'load', initialize);
</script>    
<div id="map-canvas"></div>
