<?php get_header(); ?>
<?php
$requested_location = [
	get_query_var('tng_place_id'),
	get_query_var('tng_locality_1'),
	get_query_var('tng_locality_2')
];
?>
<?php $locality = new TNG_Locality( $requested_location ); ?>
<?php $utilities = new FamilyRootsUtilities(); ?>
<div id="main-content" class="main-content">
	<div class="page-content">
		<?php if($locality->exists()): ?>
			<div class="entry-header">
				<h1 class="entry-title"><?php echo $locality->get('place'); ?> <small>(<?php echo sprintf( _n( '1 place', '%s localities or places', $locality->get('count'), 'family-roots' ), $locality->get('count') ); ?>)</small></h1>
			</div>
			<table class="table">
				<thead>
					<th><?php _e('Locality or Place', 'family-roots'); ?></th>
					<th><?php _e('Count', 'family-roots'); ?></th>
				</thead>
				<tbody>
					<?php foreach($locality->locations as $locations): ?>
					<tr>
						<td><a href="<?php echo $locations->place_url; ?>"><?php echo $locations->place_name; ?></a></td>
						<td><?php echo $locations->place_count; ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>	
		<?php else: ?>
			<div class="page-header">
				<h1><?php _e( 'Unknown locality', 'family-roots' ); ?></h1>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php
get_sidebar();
get_footer();