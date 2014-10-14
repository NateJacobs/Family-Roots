<?php get_header(); ?>
<?php $utilities = new FamilyRootsUtilities(); ?>
<?php $places = $utilities->get_all_places(); ?>
<div id="main-content" class="main-content">
	<div class="page-content">
		<h1>Places</h1>
		<?php if($places): ?>
			<ul class="list-inline">
			<?php foreach($places as $place): ?>
				<li><a href="<?php echo $utilities->get_place_url($place->ID); ?>"><?php echo $place->place; ?></a></li>
			<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</div>
<?php
get_sidebar();
get_footer();