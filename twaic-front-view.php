<?php
// Shortcode
function twaic_shortcode($atts, $content = null) {
		// Set default shortcode attributes
	$options = get_option( 'twaic_settings' );
	if(!$options){
		twaic_set_options ();
		$options = get_option( 'twaic_settings' );
	}
	$options['id'] = '';

	// Parse incomming $atts into an array and merge it with $defaults
	$atts = shortcode_atts($options, $atts);

	return twaic_frontend($atts);
}
add_shortcode('twaic-carousel', 'twaic_shortcode');

// Display carousel
function twaic_frontend($atts){

	// Build the attributes
	$id = rand(0, 999); // use a random ID so that the CSS IDs work with multiple on one page
	if(!isset($atts['image_size'])) $atts['image_size'] = 'standard_resolution';
	if(!isset($atts['image_count'])) $atts['image_count'] = 20;
	$photo_count = $atts['image_count'];
	if(!isset($atts['before_wrapper'])) $atts['before_wrapper'] = '<ul>';
	if(!isset($atts['after_wrapper'])) $atts['after_wrapper'] = '</ul>';
	if(!isset($atts['before_image_listing'])) $atts['before_image_listing'] = '<li>';
	if(!isset($atts['after_image_listing'])) $atts['after_image_listing'] = '</li>';
	
	if(!isset($atts['before_caption'])) $atts['before_caption'] = '<p>';
	if(!isset($atts['after_caption'])) $atts['after_caption'] = '</p>';
	
	if(!isset($atts['theme'])) $atts['theme'] = 'theme1';
	if(!isset($atts['show_user_details'])) $atts['show_user_details'] = '1';
	
	if(isset($atts['access_token'])){
		$access_token=$atts['access_token'];
		/*Profile Details*/
		$profile_details = '';
		if($atts['show_user_details']==1){
			$json_link="https://api.instagram.com/v1/users/self/?";
			$json_link.="access_token={$access_token}";
			$json = file_get_contents($json_link);
			$obj = json_decode(preg_replace('/("\w+"):(\d+)/', '\\1:"\\2"', $json), true);
		
			$username= $obj['data']['username'];
			$profile_picture=$obj['data']['profile_picture'];
			$full_name=$obj['data']['full_name'];
			$followed_by=$obj['data']['counts']['followed_by'];
			$follows=$obj['data']['counts']['follows'];
			$post_count = $obj['data']['counts']['media'];
			$profile_details .='<div class="twic-profile-details">'; 
			
			
			
			$profile_details .= '<div class="twic-profile-pic"><img alt="'.$full_name.'" src="'.$profile_picture.'">
			<p class="twic-username"><a target="_blank" href="https://www.instagram.com/'.$username.'/">@'.$username.'</a></p>
			</div>';
			$profile_details .= '<div class="twic-post">
			<p class="twic-count"><a target="_blank" href="https://www.instagram.com/'.$username.'/">'.$post_count.'</a></p>
			<p class="twic-name"><a target="_blank" href="https://www.instagram.com/'.$username.'/">Posts</a></p>
			</div>';
			$profile_details .= '<div class="twic-followers">
			<p class="twic-count"><a target="_blank" href="https://www.instagram.com/'.$username.'/followers/">'.$followed_by.'</a></p>
			<p class="twic-name"><a target="_blank" href="https://www.instagram.com/'.$username.'/followers/">Followers</a></p>
			</div>';
			$profile_details .= '<div class="twic-following">
			<p class="twic-count"><a target="_blank" href="https://www.instagram.com/'.$username.'/following/">'.$follows.'</a></p>
			<p class="twic-name"><a target="_blank" href="https://www.instagram.com/'.$username.'/following/">Following</a></p>
			</div>';
		
			$profile_details .='<div class="twic-clear"></div>';
			$profile_details .='</div>'; //twic-profile-details
		}
		
		/*Instagram Feeds*/
		$json_link="https://api.instagram.com/v1/users/self/media/recent/?";
		$json_link.="access_token={$access_token}&count={$photo_count}";
		
		$json = file_get_contents($json_link);
		
		$obj = json_decode(preg_replace('/("\w+"):(\d+)/', '\\1:"\\2"', $json), true);
		
		$output = '';
		
		//Wrapper Start
		echo '<div class="twic-instagram-container">';
		echo $profile_details;
		echo $atts['before_wrapper'];
		foreach ($obj['data'] as $post) {
			/*echo '<pre>';
			print_r($post);
			echo '</pre>';*/
			$image_size = $atts['image_size'];
			$pic_text=$post['caption']['text'];
			$pic_link=$post['link'];
			$pic_like_count=$post['likes']['count'];
			$pic_comment_count=$post['comments']['count'];
			$pic_src=str_replace("http://", "https://", $post['images'][$image_size]['url']);
			$pic_created_time=date("F j, Y", $post['caption']['created_time']);
			$pic_created_time=date("F j, Y", strtotime($pic_created_time . " +1 days"));
			
			//Image Listing Start
			echo $atts['before_image_listing'];        
			echo "<a href='{$pic_link}' target='_blank'>";
				echo '<div class="insta-overlay">';
				echo "<img class='img-responsive photo-thumb' src='{$pic_src}' alt='{$pic_text}'>";
				echo $atts['before_caption']."{$pic_text}".$atts['after_caption'];
				echo '<div class="insta-image-meta">';
				echo '<i class="fa fa-heart fa-fw"></i>'.$pic_like_count.'&nbsp;&nbsp;<i class="fa fa-comment fa-fw"></i>'.$pic_comment_count;
				echo '</div>';
				echo '</div>';
			echo "</a>";
			echo $atts['after_image_listing'];
			//Image Listing End
		}
		echo $atts['after_wrapper'];
		echo '</div>';
		//Wrapper End
		wp_enqueue_style( 'twaic-fontawesome', 'https://use.fontawesome.com/releases/v4.6.3/css/font-awesome-css.min.css', array(), '4.6.3' );
		if($atts['theme']=="theme1"){
			wp_enqueue_style( 'twaic-theme1', plugins_url('asset/css/theme1.css',__FILE__ ), array(), TWAIC_VERSION );
			
		}
	}
	else{
		echo '<div>Please, configure your Instagram settings first.</div>';
	}
	
	
	

	// Collect the output
	$output = ob_get_contents();
	ob_end_clean();
	
	
	return $output;
}

