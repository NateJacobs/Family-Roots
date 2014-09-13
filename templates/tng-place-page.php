<?php $place = new TNG_Place(get_query_var('tng_place_id')); ?>
<?php $utilities = new FamilyRootsUtilities(); ?>
<?php if($place->exists()): ?>
<div class="page-header">
	<h1><?php echo $place->get('place'); ?></h1>
</div>
<img class="img-responsive" src="http://maps.googleapis.com/maps/api/staticmap?markers=<?php echo $place->get('latitude').','.$place->get('longitude') ?>&zoom=12&size=600x200">
<div class="row">
	<div class="col-md-4">
		<h2>Births</h2>
		<?php $births = $place->get_births(); ?>
		<?php if($births): ?>
			<?php foreach($births as $person_id): ?>
				<?php $person = new TNG_Person($person_id->personID); ?>
				<p><a href="<?php echo $utilities->get_person_url($person); ?>"><?php echo $person->get('first_name').' '.$person->get('last_name'); ?></a></p>
			<?php endforeach; ?>
		<?php else: ?>
			<p class="lead">None</p>
		<?php endif; ?>
	</div>
	<div class="col-md-4">
		<h2>Deaths</h2>
		<?php $deaths = $place->get_deaths(); ?>
		<?php if($deaths): ?>
			<?php foreach($deaths as $person_id): ?>
				<?php $person = new TNG_Person($person_id->personID); ?>
				<p><a href="<?php echo $utilities->get_person_url($person); ?>"><?php echo $person->get('first_name').' '.$person->get('last_name'); ?></a></p>
			<?php endforeach; ?>
		<?php else: ?>
			<p class="lead">None</p>
		<?php endif; ?>
	</div>
	<div class="col-md-4">
		<h2>Burials</h2>
		<?php $burials = $place->get_burials(); ?>
		<?php if($burials): ?>
			<?php foreach($burials as $person_id): ?>
				<?php $person = new TNG_Person($person_id->personID); ?>
				<p><a href="<?php echo $utilities->get_person_url($person); ?>"><?php echo $person->get('first_name').' '.$person->get('last_name'); ?></a></p>
			<?php endforeach; ?>
		<?php else: ?>
			<p class="lead">None</p>
		<?php endif; ?>
	</div>
</div>
<?php else: ?>
<div class="page-header">
	<h1>Unknown location</h1>
</div>
<?php endif; ?>