<?php $utilities = new FamilyRootsUtilities(); ?>
<?php $places = $utilities->get_all_places(); ?>
<h1>Places</h1>
<?php if($places): ?>
	<ul class="list-inline">
	<?php foreach($places as $place): ?>
		<li><a href="<?php echo $utilities->get_place_url($place->ID); ?>"><?php echo $place->place; ?></a></li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>