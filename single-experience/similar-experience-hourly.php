<?php
global $homey_local;
$experiences_layout = homey_option('similer_experiences_layout');
$similer_count = homey_option('similer_experiences_count');


$tax_query = Array ();
$term_ids = Array ();
$city_ids = Array ();
$terms = get_the_terms(get_the_ID(), 'experience_type', 'string');
$experience_city = get_the_terms( get_the_ID(), 'experience_city', 'string' );

if ( !empty( $terms ) ) :

	$term_ids = wp_list_pluck($terms, 'term_id');
	$tax_query[] = array(
		'taxonomy' => 'experience_type',
		'field' => 'id',
		'terms' => $term_ids,
		'operator' => 'IN' //Or 'AND' or 'NOT IN'
	);

endif;

if ( !empty( $experience_city ) ) :

	$city_ids = wp_list_pluck( $experience_city, 'term_id' );
	$tax_query[] = array(
		'taxonomy' => 'experience_city',
		'field' => 'id',
		'terms' => $city_ids,
		'operator' => 'IN' //Or 'AND' or 'NOT IN'
	);

endif;

$tax_count = count( $tax_query );

if ($tax_count > 1) :

    $tax_query['relation'] = 'AND';

endif;

$second_query = array(
	'post_type' => 'experience',
	'tax_query' => $tax_query,
	'posts_per_page' => $similer_count,
	'meta_key' => 'homey_booking_type',
	'meta_value' => 'per_hour',
	'orderby' => 'rand',
	'post__not_in' => array(get_the_ID())
);

query_posts( $second_query );

if (have_posts()) :
?>
	<div id="similar-experience-section" class="similar-experience-section">
		<h2 class="title"><?php echo esc_attr(homey_option('experience_sn_similar_label')); ?></h2>
		<div class="item-row item-<?php echo esc_attr($experiences_layout); ?>-view">
			<?php
			while (have_posts()) : the_post();

				if($experiences_layout == 'card') {
					get_template_part('template-parts/experience/experience-card');
				} else {
					get_template_part('template-parts/experience/experience-item');
				}

			endwhile;
			?>	
		</div>
	</div>
<?php
endif;
wp_reset_query();
?>