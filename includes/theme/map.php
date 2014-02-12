<style>
    #map-canvas { 
        height: <?php echo $height_map; ?>; 
    }
    .list{
        height : <?php echo $height_map; ?>; 
    }
    .menu-wp-represent-map{
        height : <?php echo $height_map; ?>; 
    }
    #map-canvas{
        width: <?php echo $width_map; ?>; 
    }
</style>

<script>
    
    $ = jQuery.noConflict();

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
        
        // set map options
        var myOptions = {
            zoom: 12,
            center: new google.maps.LatLng(<?php if ( true === $all_map_items ) echo $lat_lng; else echo get_post_meta($posts[0]->ID, '_wp_represent_map_lat_lng', true) ?>),
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: false,
            mapTypeControl: true,
            panControl: false,
            zoomControl: true,
            zoomControlOptions: {
                <?php if ( true === $all_map_items ) : ?>
                    style: google.maps.ZoomControlStyle.LARGE,               
                <?php else : ?>
                    style: google.maps.ZoomControlStyle.SMALL,                   
                <?php endif; ?>
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
        
                if ( isset($post->type) ) {
                    $icon_type = $post->type;
                } else {
                    @$icon_type = $type;
                }
                
                if ( empty($icon_type) ) {
                    $icon_type = 'default';
                }
                
                $lat_lng = explode(',',get_post_meta($post->ID, '_wp_represent_map_lat_lng', true));
                $lat = $lat_lng[0];
                $lng = $lat_lng[1];
                
                echo "markers.push(['".$post->post_title."', '".$icon_type."', '".$lat."', '".$lng."', '".$post->post_title."', '".$post->post_title."', '".get_post_meta($post->ID, '_wp_represent_map_address', true)."']);";
                
            endforeach;
        endif; ?>
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
            var markerImage = new google.maps.MarkerImage("<?php echo home_url(); ?>/wp-content/uploads/map-icons/" + val[1] + ".png", null, null, null, iconSize);
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
                        "<div class='infowindow-content'><div class='marker_title'><h3>" + val[0] + "</h3></div>"
                            + "<div class='marker_uri'>"
                            + "<a target='_blank' href='" + markerURI + "'>" + markerURI_short + "</a></div>"
                            + "<br />"
                            + "<div class='marker_desc'>" + val[4] + "</div>"
                            + "<div class='marker_address'>" + val[6] + "</div></div>"
                        );
                infowindow.open(map, this);
            });

            // add marker label
                    var latLng = new google.maps.LatLng(val[2], val[3]);
                    var label = new Label({
                        map: map,
                        id: i
                    });
                    label.bindTo('position', marker);
                    label.set("text", '');
                    label.bindTo('visible', marker);
                    label.bindTo('clickable', marker);
                    label.bindTo('zIndex', marker);
                    
                    
                 // zoom to marker if selected in search typeahead list
        $('#search').typeahead({
          source: markerTitles, 
          onselect: function(obj) {
            marker_id = jQuery.inArray(obj, markerTitles);
            if(marker_id > -1) {
              map.panTo(gmarkers[marker_id].getPosition());
              map.setZoom(15);
              google.maps.event.trigger(gmarkers[marker_id], 'click');
            }
            $("#search").val("");
          }
        });   
        });

    }
    
     // zoom to specific marker
      function goToMarker(marker_id) {
        if(marker_id) {
          map.panTo(gmarkers[marker_id].getPosition());
          map.setZoom(15);
          google.maps.event.trigger(gmarkers[marker_id], 'click');
        }
      }

      // toggle (hide/show) markers of a given type (on the map)
      function toggle(type) {
        if($('#filter_'+type).is('.inactive')) {
          show(type); 
        } else {
          hide(type); 
        }
      }

      // hide all markers of a given type
      function hide(type) {
        for (var i=0; i<gmarkers.length; i++) {
          if (gmarkers[i].type == type) {
            gmarkers[i].setVisible(false);
          }
        }
        $("#filter_"+type).addClass("inactive");
      }

      // show all markers of a given type
      function show(type) {
        for (var i=0; i<gmarkers.length; i++) {
          if (gmarkers[i].type == type) {
            gmarkers[i].setVisible(true);
          }
        }
        $("#filter_"+type).removeClass("inactive");
      }
      
      // toggle (hide/show) marker list of a given type
      function toggleList(type) {
        $("#list .list-"+type).toggle();
      }


      // hover on list item
      function markerListMouseOver(marker_id) {
        $("#marker"+marker_id).css("display", "inline");
      }
      function markerListMouseOut(marker_id) {
        $("#marker"+marker_id).css("display", "none");
      }

    google.maps.event.addDomListener(window, 'load', initialize);
</script>    
<div id="map-canvas">&nbsp;</div>

<?php if ( true == $all_map_items ) : ?>
    <div class="menu-wp-represent-map" id="menu">
      <ul class="list" id="list">
        <?php
          
          foreach($categories as $mark) : ?>
               
              <li class='category'>
                    <div class='category_item'>
                        <div class='category_toggle' 
                             onClick="toggle('<?php echo $mark->slug; ?>')" 
                             id="filter_<?php echo $mark->slug; ?>">
                        </div>
                        <a href='#' onClick="toggleList('<?php echo $mark->slug; ?>');" class="category_info category_info_parent" 
                           id="<?php echo $mark->slug; ?>_child"><?php echo $mark->name; ?>
                            <img src="<?php echo home_url(); ?>/wp-content/uploads/map-icons/<?php echo $mark->slug; ?>.png" alt="" />
                            <div class="span">    
                                <?php if ( !empty($mark->children) ) : ?>
                                <img width="12" src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))); ?>assets/img/icons/down.png"/>
                                <?php endif; ?>
                            </div>
                            <span class="total"> ( <?php echo $mark->count; ?> )</span>
                        </a>
                    </div>
                  
              <?php if ( !empty($mark->children) ) :?>
                  <ul class="subcategories <?php echo $mark->slug; ?>_child" style="display:none;">
                      <?php $total_posts = ''; ?>
                      <?php foreach($mark->children as $child) : ?>
                        
                            
                        
                            <div class='category_item'>
                                <div class='category_toggle' 
                                     onClick="toggle('<?php echo $mark->slug . '-' . $child->slug; ?>')" 
                                     id="filter_<?php echo $mark->slug . '-' . $child->slug; ?>">
                                </div>
                                
                                <a href='#' onClick="toggleList('<?php echo $mark->slug . '-' . $child->slug; ?>');" class="category_info">
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <img src="<?php echo home_url(); ?>/wp-content/uploads/map-icons/<?php echo $mark->slug . '-' . $child->slug; ?>.png" alt="" />
                                    <div class="span"><?php echo $child->name; ?></div>
                                    <span class="total"> ( <?php echo $child->count; ?> )</span>
                                </a>
                            </div>
                      <?php endforeach; ?>
                  </ul>
                
                  
                <?php endif; ?>  
                
              </li>
            
            <?php 
            /*
            foreach($item_to_map as $itm) {
                //foreach()
                echo "
                  <li class='".$marker[type]."'>
                    <a href='#' onMouseOver=\"markerListMouseOver('".$marker_id."')\" onMouseOut=\"markerListMouseOut('".$marker_id."')\" onClick=\"goToMarker('".$marker_id."');\">".$marker[title]."</a>
                  </li>
              ";
            }
            while($item = mysql_fetch_assoc($markers)) {
              echo "
                  <li class='".$marker[type]."'>
                    <a href='#' onMouseOver=\"markerListMouseOver('".$marker_id."')\" onMouseOut=\"markerListMouseOut('".$marker_id."')\" onClick=\"goToMarker('".$marker_id."');\">".$marker[title]."</a>
                  </li>
              ";
              $marker_id++;
            }
            echo "
                </ul>
              </li>
            ";
             * 
             */
          endforeach; 
          
        ?>
              <li class="attribution">
                      <!-- per our license, you may not remove this line -->

                      <?php echo __('Created with ', 'wp-represent-map'); ?>
					  <a target="_blank" href="https://github.com/abenzer/represent-map">
						  RepresentMap
					  </a>&nbsp;
					  <?php echo __('by', 'wp-represent-map'); ?><br>
                      <a target="_blank" href="http://www.twitter.com/abenzer">@abenzer</a> +
                      <a target="_blank" href="http://www.twitter.com/tara">@tara</a> +
                      <a target="_blank" href="http://www.twitter.com/seanbonner">@seanbonner</a>
              </li>
      </ul>
    </div>
<?php endif; ?>

<script>
    $('.category_info_parent').on('click', function(){
        var toShow = $(this).attr('id');
        $('.'+toShow).slideToggle('slow');
        return false;
    });
    $('.category_info').on('click', function(){
        return false;
    });
</script>
