<?php

/* Shortcode for displaying the current song
 * 
 */
function station_shortcode_now_playing($atts) {
	$attr = shortcode_atts( array(
			'title' => '',
			'artist' => 1,
			'song' => 1,
			'album' => 0,
			'label' => 0,
			'comments' => 0
	), $atts );

	$artist = $attr['artist'];
	$song = $attr['song'];
	$album = $attr['album'];
	$label = $attr['label'];
	$comments = $attr['comments'];

	$most_recent = myplaylist_get_now_playing();
	$output = '';

	if($most_recent) {
		$class = '';
		if(isset($most_recent['playlist_entry_new']) && $most_recent['playlist_entry_new'] == 'on') {
			$class = ' class="new"';
		}

		$output .= '<div id="myplaylist-nowplaying"'.$class.'>';
		if($title != '') {
			$output .= '<h3>'.$title.'</h3>';
		}

		if($song == 1) {
			$output .= '<span class="myplaylist-song">'.$most_recent['playlist_entry_song'].'</span> ';
		}
		if($artist == 1) {
			$output .= '<span class="myplaylist-artist">'.$most_recent['playlist_entry_artist'].'</span> ';
		}
		if($album == 1) {
			$output .= '<span class="myplaylist-album">'.$most_recent['playlist_entry_album'].'</span> ';
		}
		if($label == 1) {
			$output .= '<span class="myplaylist-label">'.$most_recent['playlist_entry_label'].'</span> ';
		}
		if($comments == 1) {
			$output .= '<span class="myplaylist-comments">'.$most_recent['playlist_entry_comments'].'</span> ';
		}
		$output .= '<span class="myplaylist-link"><a href="'.$most_recent['playlist_permalink'].'">'.__('View Playlist', 'radio-station').'</a></span> ';
		$output .= '</div>';

	}
	else {
		echo 'No playlists available.';
	}
	return $output;
}
add_shortcode('now-playing', 'station_shortcode_now_playing');


/* Shortcode to fetch all playlists for a given show id
 */
function station_shortcode_get_playlists_for_show($atts) {
	$attr = shortcode_atts( array(
			'show' => '',
			'limit' => -1
	), $atts );

	$show = $attr['show'];
	$limit = $attr['limit'];

	//don't return anything if we don't have a show
	if($show == '') {
		return false;
	}

	$args = array(
			'numberposts' => $limit,
			'offset' => 0,
			'orderby' => 'post_date',
			'order' => 'DESC',
			'post_type' => 'playlist',
			'post_status' => 'publish',
			'meta_key' => 'playlist_show_id',
			'meta_value' => $show
	);

	$playlists = get_posts($args);

	$output = '';

	$output .= '<div id="myplaylist-playlistlinks">';
	$output .= '<ul class="myplaylist-linklist">';
	foreach($playlists as $playlist) {
		$output .= '<li><a href="';
		$output .= get_permalink($playlist->ID);
		$output .= '">'.$playlist->post_title.'</a></li>';
	}
	$output .= '</ul>';

	$playlist_archive = get_post_type_archive_link('playlist');
	$params = array( 'show_id' => $show );
	$playlist_archive = add_query_arg( $params, $playlist_archive );

	$output .= '<a href="'.$playlist_archive.'">'.__('More Playlists', 'radio-station').'</a>';

	$output .= '</div>';

	return $output;
}
add_shortcode('get-playlists', 'station_shortcode_get_playlists_for_show');

/* Shortcode for displaying a list of all shows
 * Since 2.0.0
 */
function station_shortcode_list_shows($atts) {
	$attr = shortcode_atts( array(
			'genre' => ''
	), $atts );

	$genre = $attr['genre'];

	//grab the published shows
	$args = array(
			'numberposts'     => -1,
			'offset'          => 0,
			'orderby'         => 'title',
			'order'           => 'ASC',
			'post_type'       => 'show',
			'post_status'     => 'publish',
			'meta_query' => array(
					array(
							'key' => 'show_active',
							'value' => 'on',
					)
			)
	);

	if($genre != '') {
		$args['genres'] = $genre;
	}

	$shows = get_posts($args);

	//if there are no shows saved, return nothing
	if(!$shows) {
		return false;
	}

	$output = '';

	$output .= '<div id="station-show-list">';
	$output .= '<ul>';
	foreach($shows as $show) {
		$output .= '<li>';
		$output .= '<span class="date">'.get_post_time().'</span> ';	
		$output .= ' "<a href="'.get_permalink($show->ID).'">'.get_the_title($show->ID);
		$current = get_post_meta($show->ID, 'show_user_list', true);
		$users = get_users($args);
		foreach($users as $i => $dj) {
    		if(in_array($dj->ID, $current)) {
		$output .= '<span>'.$users[$i].'</span>'; 
    		}
		}
		$output .= '<span>'.get_post_meta($show->ID, 'show_user_list', true).'</span>'; 
		$output .= '</a>';
		$output .= '</li>';
	}
	$output .= '</ul>';
	$output .= '</div>';
	return $output;
}
add_shortcode('list-shows', 'station_shortcode_list_shows');


//Dozie custom code
function shortcode_list_show($atts){
	// Modify get_posts args for your needs
$radio_shows = get_posts( array ( 'post_type' => 'show' ) );

if ( $radio_shows ) {
    echo '<ul class="R_show">';

    foreach ( $radio_shows as $radio_show ) {
	        $event_date = get_post_meta( $radio_show->ID, 'show_link', true );

	        $Djs_names = get_post_meta( $radio_show->ID, 'show_user_list', true );
	        foreach ($Djs_names as $Djs_name) {
	        	$user_info = get_userdata($Djs_name);
	        	$Djs_name = $user_info->display_name;
	        }
	        
	        $show_scheds = get_post_meta( $radio_show->ID, 'show_sched', true );
	        foreach ($show_scheds as $show_sched) {
	        	# code...
	        	$show_scheds_hrs = $show_sched['start_hour'];
	        	$show_scheds_min = $show_sched['start_min'];
	        	$show_scheds_meridian = $show_sched['start_meridian'];
	        }
	        $show_sched = $show_scheds_hrs.':'.$show_scheds_min.' '.$show_scheds_meridian;
	        echo '<li><span class="date">'.$show_sched.'</span> "'. get_the_title( $radio_show->ID ) . '" by  '. $Djs_name. '</li>';
	    }

    echo '</ul>';
}
}
add_shortcode('custom-list-shows', 'shortcode_list_show');


/* Shortcode function for current DJ on-air
 * Since 2.0.9
 */
function station_shortcode_dj_on_air($atts) {
	$attr = shortcode_atts( array(
			'title' => '',
			'display_djs' => 1,
			'show_avatar' => 1,
			'show_link' => 0,
			'default_name' => '',
			'time' => '12',
			'show_sched' => 1,
			'show_playlist' => 0,
			'show_all_sched' => 1,
			'show_desc' => 0
	), $atts );

	$title = $attr['title'];
	$display_djs = $attr['display_djs'];
	$show_avatar = $attr['show_avatar'];
	$show_link = $attr['show_link'];
	$default_name = $attr['default_name'];
	$time = $attr['time'];
	$show_sched = $attr['show_sched'];
	$show_playlist = $attr['show_playlist'];
	$show_all_sched = $attr['show_all_sched'];
	$show_desc = $attr['show_desc'];

	//find out which DJ(s) are currently scheduled to be on-air and display them
	$djs = dj_get_current();
	$playlist = myplaylist_get_now_playing();

	$dj_str = '';

	$dj_str .= '<div class="on-air-embedded dj-on-air-embedded">';
	if($title != '') {
		$dj_str .= '<h3>'.$title.'</h3>';
	}
	$dj_str .= '<ul class="on-air-list">';

	//echo the show/dj currently on-air
	if($djs['type'] == 'override') {

		$dj_str .= '<li class="on-air-dj">';
		if($show_avatar) {
			if(has_post_thumbnail($djs['all'][0]['post_id'])) {
				$dj_str .= '<span class="on-air-dj-avatar">'.get_the_post_thumbnail($djs['all'][0]['post_id'], 'thumbnail').'</span>';
			}
		}
		
		$dj_str .= $djs['all'][0]['title'];
			
		//display the override's schedule if requested
		if($show_sched) {

			if($time == 12) {
				$dj_str .= '<span class="on-air-dj-sched">'.$djs['all'][0]['sched']['start_hour'].':'.$djs['all'][0]['sched']['start_min'].' '.$djs['all'][0]['sched']['start_meridian'].'-'.$djs['all'][0]['sched']['end_hour'].':'.$djs['all'][0]['sched']['end_min'].' '.$djs['all'][0]['sched']['end_meridian'].'</span><br />';
			}
			else {
				$djs['all'][0]['sched'] = station_convert_schedule_to_24hour($djs['all'][0]['sched']);

				$dj_str .= '<span class="on-air-dj-sched">'.$djs['all'][0]['sched']['start_hour'].':'.$djs['all'][0]['sched']['start_min'].' '.'-'.$djs['all'][0]['sched']['end_hour'].':'.$djs['all'][0]['sched']['end_min'].'</span><br />';
			}

			$dj_str .= '</li>';
		}
	}
	else {
		if(isset($djs['all']) && count($djs['all']) > 0) {
			foreach($djs['all'] as $dj) {
				$dj_str .= '<li class="on-air-dj">';
				if($show_avatar) {
					$dj_str .= '<span class="on-air-dj-avatar img_large">'.get_the_post_thumbnail($dj->ID, 'large').'</span>';
				}

				$dj_str .= '<span class="on-air-dj-title">';
				if($show_link) {
					$dj_str .= '<a href="';
					$dj_str .= get_permalink($dj->ID);
					$dj_str .= '">';
					$dj_str .= $dj->post_title.'</a>';
				}
				else {
					$dj_str .= $dj->post_title;
				}
				$dj_str .= '</span>';

				if($display_djs) {
					$names = get_post_meta($dj->ID, 'show_user_list', true);
					$count = 0;
				
					if($names) {
						$dj_str .= '<div class="on-air-dj-names">';
						foreach($names as $name) {
							$count ++;
							$user_info = get_userdata($name);
				
							$dj_str .= $user_info->display_name;
				
							if( ($count == 1 && count($names) == 2) || (count($names) > 2 && $count == count($names)-1) ) {
								$dj_str .= ' and ';
							}
							elseif($count < count($names) && count($names) > 2) {
								$dj_str .= ', ';
							}
							else {
								//do nothing
							}
						}
						$dj_str .= '</div>';
					}
				}
				
				if($show_desc) {
					$desc_string = station_shorten_string(strip_tags($dj->post_content), 20);
					$dj_str .= '<span class="on-air-show-desc">'.$desc_string.'</span>';
				}
				
				if($show_playlist) {
					$dj_str .= '<span class="on-air-dj-playlist"><a href="'.$playlist['playlist_permalink'].'">'.__('View Playlist', 'radio-station').'</a></span>';
				}

				$dj_str .= '<span class="radio-clear"></span>';

				if($show_sched) {
					$scheds = get_post_meta($dj->ID, 'show_sched', true);
					if($show_all_sched) { //if we only want the schedule that's relevant now to display...
						$current_sched = station_current_schedule($scheds);
							
					if($current_sched) {
					  if($time == 12) {
						$dj_str .= '<span class="on-air-dj-sched"> '.$current_sched['start_hour'].':'.$current_sched['start_min'].' '.$current_sched['start_meridian'].'-'.$current_sched['end_hour'].':'.$current_sched['end_min'].' '.$current_sched['end_meridian'].' '.__($current_sched['day'], 'radio-station').'</span><br />';
							}
							else {
								$current_sched = station_convert_schedule_to_24hour($current_sched);
					
								$dj_str .= '<span class="on-air-dj-sched">'.__($current_sched['day'], 'radio-station').', '.$current_sched['start_hour'].':'.$current_sched['start_min'].' '.'-'.$current_sched['end_hour'].':'.$current_sched['end_min'].' '.__($current_sched['day'], 'radio-station').'</span><br />';
							}
						}
							
					}
					else {
						
						foreach($scheds as $sched) {
							if($time == 12) {
								$dj_str .= '<span class="on-air-dj-sched">'.__($sched['day'], 'radio-station').', '.$sched['start_hour'].':'.$sched['start_min'].' '.$sched['start_meridian'].'-'.$sched['end_hour'].':'.$sched['end_min'].' '.$sched['end_meridian'].'</span><br />';
							}
							else {
								$sched = station_convert_schedule_to_24hour($sched);
									
								$dj_str .= '<span class="on-air-dj-sched">'.__($sched['day'], 'radio-station').', '.$sched['start_hour'].':'.$sched['start_min'].' '.'-'.$sched['end_hour'].':'.$sched['end_min'].'</span><br />';
							}
						}
					}
				}

				$dj_str .= '</li>';
			}
		}
		else {
			$dj_str .= '<li class="on-air-dj default-dj">'.$default_name.'</li>';
		}
	}

	$dj_str .= '</ul>';
	$dj_str .= '</div>';

	return $dj_str;

}
add_shortcode( 'dj-widget', 'station_shortcode_dj_on_air');

/* Shortcode for displaying upcoming DJs/shows
 * Since 2.0.9
*/
function station_shortcode_coming_up($atts) {
	$attr = shortcode_atts( array(
			'title' => '',
			'display_djs' => 0,
			'show_avatar' => 0,
			'show_link' => 0,
			'limit' => 1,
			'time' => '12',
			'show_sched' => 1
	), $atts );
	$title = $attr['title'];
	$display_djs = $attr['display_djs'];
	$show_avatar = $attr['show_avatar'];
	$show_link = $attr['show_link'];
	$time = $attr['time'];
	$show_sched = $attr['show_sched'];

	//find out which DJ(s) are coming up today
	$djs = dj_get_next($limit);
	//print_r($djs);
	
	$now = strtotime(current_time("mysql"));
	$curDate = date('Y-m-d', $now);

	$dj_str = '';

	$dj_str .= '<div class="on-air-embedded dj-coming-up-embedded">';
	if($title != '') {
		$dj_str .= '<h3>'.$title.'</h3>';
	}
	$dj_str .= '<ul class="on-air-list">';

	//echo the show/dj coming up
	if(isset($djs['all']) && count($djs['all']) > 0) {
		foreach($djs['all'] as $showtime => $dj) {
				
			if(is_array($dj) && $dj['type'] == 'override') {
				echo '<li class="on-air-dj">';
				
				if($show_avatar) {
					if(has_post_thumbnail($dj['post_id'])) {
						$dj_str .= '<span class="on-air-dj-avatar">'.get_the_post_thumbnail($dj['post_id'], 'thumbnail').'</span>';
					}
				}
				
				echo $dj['title'];
				if($show_sched) {

					if($time == 12) {
						$dj_str .= '<span class="on-air-dj-sched">'.$dj['sched']['start_hour'].':'.$dj['sched']['start_min'].' '.$dj['sched']['start_meridian'].'-'.$dj['sched']['end_hour'].':'.$dj['sched']['end_min'].' '.$dj['sched']['end_meridian'].'</span><br />';
					}
					else {
						$dj['sched'] = station_convert_schedule_to_24hour($dj['sched']);
							
						$dj_str .= '<span class="on-air-dj-sched">'.$dj['sched']['start_hour'].':'.$dj['sched']['start_min'].' '.'-'.$dj['sched']['end_hour'].':'.$dj['sched']['end_min'].'</span><br />';
							
					}
				}
				echo '</li>';
			}
			else {
				$dj_str .= '<li class="on-air-dj">';
				if($show_avatar) {
					$dj_str .= '<span class="on-air-dj-avatar">'.get_the_post_thumbnail($dj->ID, 'thumbnail').'</span>';
				}else{
					$dj_str .= '<span class="on-air-dj-avatar img_large">'.get_the_post_thumbnail($dj->ID, 'large').'</span>';
				}

				$dj_str .= '<span class="on-air-dj-title">';
				if($show_link) {
					$dj_str .= '<a href="';
					$dj_str .= get_permalink($dj->ID);
					$dj_str .= '">';
					$dj_str .= $dj->post_title.'</a>';
				}
				else {
					$dj_str .= $dj->post_title;
				}
				$dj_str .= '</span>';
				
				if(!$display_djs) {
					$names = get_post_meta($dj->ID, 'show_user_list', true);
					$count = 0;
				
					if($names) {
						$dj_str .= '<div class="on-air-dj-names"> ';
						foreach($names as $name) {
							$count ++;
							$user_info = get_userdata($name);
				
							$dj_str .= $user_info->display_name;
				
							if( ($count == 1 && count($names) == 2) || (count($names) > 2 && $count == count($names)-1) ) {
								$dj_str .= ' and ';
							}
							elseif($count < count($names) && count($names) > 2) {
								$dj_str .= ', ';
							}
							else {
								//do nothing
							}
						}
						$dj_str .= '</div>';
					}
				}

				$dj_str .= '<span class="radio-clear"></span>';
				
				if($show_sched) {
					$showtimes = explode("|", $showtime);
					if($time == 12) {
						$dj_str .= '<span class="on-air-dj-sched">'.date('g:i a', $showtimes[0]).' - '.date('g:i a', $showtimes[1]).'<span class="on-air-dj-sched-day"> '.__(date('l', $showtimes[0]), 'radio-station').'</span></span><br />';
					}
					else {
						$dj_str .= '<span class="on-air-dj-sched">'.date('H:i', $showtimes[0]).' - '.date('H:i', $showtimes[1]).' <span class="on-air-dj-sched-day"> '.__(date('l', $showtimes[0]), 'radio-station').', </span></span><br />';
					}
				}
					
				$dj_str .= '</li>';
			}
		}
	}
	else {
		$dj_str .= '<li class="on-air-dj default-dj">'.__('None Upcoming', 'radio-station').'</li>';
	}

	$dj_str .= '</ul>';
	$dj_str .= '</div>';

	return $dj_str;

}
add_shortcode( 'dj-coming-up-widget', 'station_shortcode_coming_up');

/*custom widget by lucas*/
function dz_station_shortcode_coming_up($atts) {
	$attr = shortcode_atts( array(
			'title' => '',
			'display_djs' => 0,
			'show_avatar' => 0,
			'show_link' => 0,
			'limit' => 1,
			'time' => '12',
			'show_sched' => 1
	), $atts );

	$title = $attr['title'];
	$display_djs = $attr['display_djs'];
	$show_avatar = $attr['show_avatar'];
	$show_link = $attr['show_link'];
	$limit = $attr['limit'];
	$time = $attr['time'];
	$show_sched = $attr['show_sched'];

	//find out which DJ(s) are coming up today
	$djs = dj_get_next($limit);
	//print_r($djs);
	
	$now = strtotime(current_time("mysql"));
	$curDate = date('Y-m-d', $now);

	$dj_str = '';

	$dj_str .= '<div class="on-air-embedded dj-coming-up-embedded">';
	if($title != '') {
		$dj_str .= '<h3>'.$title.'</h3>';
	}
	$dj_str .= '<ul class="on-air-list">';

	//echo the show/dj coming up
	if(isset($djs['all']) && count($djs['all']) > 0) {
		foreach($djs['all'] as $showtime => $dj) {
				
			if(is_array($dj) && $dj['type'] == 'override') {
				echo '<li class="on-air-dj">';
				
				if($show_avatar) {
					if(has_post_thumbnail($dj['post_id'])) {
						$dj_str .= '<span class="on-air-dj-avatar">'.get_the_post_thumbnail($dj['post_id'], 'thumbnail').'</span>';
					}
				}
				
				echo $dj['title'];
				if($show_sched) {

					if($time == 12) {
						$dj_str .= '<span class="on-air-dj-sched">'.$dj['sched']['start_hour'].':'.$dj['sched']['start_min'].' '.$dj['sched']['start_meridian'].'-'.$dj['sched']['end_hour'].':'.$dj['sched']['end_min'].' '.$dj['sched']['end_meridian'].'</span><br />';
					}
					else {
						$dj['sched'] = station_convert_schedule_to_24hour($dj['sched']);
							
						$dj_str .= '<span class="on-air-dj-sched">'.$dj['sched']['start_hour'].':'.$dj['sched']['start_min'].' '.'-'.$dj['sched']['end_hour'].':'.$dj['sched']['end_min'].'</span><br />';
							
					}
				}
				echo '</li>';
			}
			else {
				$dj_str .= '<li class="on-air-dj">';
				if(!$show_avatar) {
					$dj_str .= '<span class="on-air-dj-avatar rh_hide">'.get_the_post_thumbnail($dj->ID, 'thumbnail').'</span>';
				}

				$dj_str .= '<span class="on-air-dj-titl" style="font-size:13px;">';
				if($show_link) {
					$dj_str .= '<a href="';
					$dj_str .= get_permalink($dj->ID);
					$dj_str .= '">';
					$dj_str .= $dj->post_title.'</a>';
				}
				else {
					$dj_str .= $dj->post_title;
				}
				$dj_str .= '</span>';
				
				if(!$display_djs) {
					$names = get_post_meta($dj->ID, 'show_user_list', true);
					$count = 0;
				
					if($names) {
						$dj_str .= '<div class="rh-on-air-dj-names show-oap">';
						foreach($names as $name) {
							$count ++;
							$user_info = get_userdata($name);
				
							$dj_str .= $user_info->display_name;
				
							if( ($count == 1 && count($names) == 2) || (count($names) > 2 && $count == count($names)-1) ) {
								$dj_str .= ' and ';
							}
							elseif($count < count($names) && count($names) > 2) {
								$dj_str .= ', ';
							}
							else {
								//do nothing
							}
						}
						$dj_str .= '</div>';
					}
				}

				$dj_str .= '<span class="radio-clear"></span>';
				
				if($show_sched) {
					$showtimes = explode("|", $showtime);
					if($time == 12) {
						$dj_str .= '<span class="on-air-dj-sched rh_hide"><span class="on-air-dj-sched-day">'.__(date('l', $showtimes[0]), 'radio-station').', </span>'.date('g:i a', $showtimes[0]).'-'.date('g:i a', $showtimes[1]).'</span><br />';
					}
					else {
						$dj_str .= '<span class="on-air-dj-sched"><span class="on-air-dj-sched-day">'.__(date('l', $showtimes[0]), 'radio-station').', </span>'.date('H:i', $showtimes[0]).'-'.date('H:i', $showtimes[1]).'</span><br />';
					}
				}
					
				$dj_str .= '</li>';
			}
		}
	}
	else {
		$dj_str .= '<li class="on-air-dj default-dj">'.__('None Upcoming', 'radio-station').'</li>';
	}

	$dj_str .= '</ul>';
	$dj_str .= '</div>';

	return $dj_str;

}
add_shortcode( 'dz-coming-up-widget', 'dz_station_shortcode_coming_up');
?>