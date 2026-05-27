<?php
$urltrimmedtab = remove_query_arg( array('page', 'deleterev','_wpnonce', 'taction', 'tid', 'sortby', 'sortdir', 'opt','settings-updated', 'wpyelp_skip') );

$urlreviewlist = esc_url( add_query_arg( 'page', 'wp_yelp-reviews',$urltrimmedtab ) );
$urltemplateposts = esc_url( add_query_arg( 'page', 'wp_yelp-templates_posts',$urltrimmedtab ) );
$urlgetpro = esc_url( add_query_arg( 'page', 'wp_yelp-get_yelp',$urltrimmedtab ) );
$urlforum = esc_url( add_query_arg( 'page', 'wp_yelp-get_pro',$urltrimmedtab ) );
$urlwelcome = esc_url( add_query_arg( 'page', 'wp_yelp-welcome',$urltrimmedtab ) );
?>	
	<div class="w3-bar w3-border w3-white">
	<a href="<?php echo $urlwelcome; ?>" class="w3-bar-item w3-button <?php if($_GET['page']=='wp_yelp-welcome'){echo 'w3-greentrip';} ?>"><i class="fa fa-home"></i> <?php _e('Welcome', 'wp-yelp-reviews'); ?></a>
	<a href="<?php echo $urlgetpro; ?>" class="w3-bar-item w3-button <?php if($_GET['page']=='wp_yelp-get_yelp'){echo 'w3-greentrip';} ?>"><i class="fa fa-search"></i> <?php _e('Get Yelp Reviews', 'wp-yelp-reviews'); ?></a>
	<a href="<?php echo $urlreviewlist; ?>" class="w3-bar-item w3-button <?php if($_GET['page']=='wp_yelp-reviews'){echo 'w3-greentrip';} ?>"><i class="fa fa-list"></i> <?php _e('Review List', 'wp-yelp-reviews'); ?></a>
	<a href="<?php echo $urltemplateposts; ?>" class="w3-bar-item w3-button <?php if($_GET['page']=='wp_yelp-templates_posts'){echo 'w3-greentrip';} ?>"><i class="fa fa-commenting-o"></i> <?php _e('Templates', 'wp-yelp-reviews'); ?></a>
	<a href="https://wpreviewslider.com/" target="_blank" class="goprohbtntrip w3-bar-item w3-button <?php if($_GET['page']=='wp_yelp-get_pro'){echo 'w3-greentrip';} ?>"><i class="fa fa-external-link-square" aria-hidden="true"></i> <?php _e('Get Pro Version!', 'wp-yelp-reviews'); ?></a>

	</div>
