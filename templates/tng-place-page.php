<?php $place = new TNG_Place(get_query_var('tng_place_id')); ?>
<?php $utilities = new FamilyRootsUtilities(); ?>
<?php if($place->exists()): ?>
<div class="page-header">
	<h1><?php echo $place->get('place'); ?></h1>
</div>
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
<div class="row">
	<div class="col-md-6">
		<h2>Marriages</h2>
		<?php $marriages = $place->get_marriages(); ?>
		<?php if($marriages): ?>
			<?php foreach($marriages as $person_id): ?>
				<?php $husband = new TNG_Person($person_id->husband); ?>
				<?php $wife = new TNG_Person($person_id->wife); ?>
				<p><a href="<?php echo $utilities->get_person_url($husband); ?>"><?php echo $husband->get('first_name').' '.$husband->get('last_name'); ?></a> and <a href="<?php echo $utilities->get_person_url($wife); ?>"><?php echo $wife->get('first_name').' '.$wife->get('last_name'); ?></a></p>
			<?php endforeach; ?>
		<?php else: ?>
			<p class="lead">None</p>
		<?php endif; ?>
	</div>
	<div class="col-md-6">
		<h2>Divorces</h2>
		<?php $divorces = $place->get_divorces(); ?>
		<?php if($divorces): ?>
			<?php foreach($divorces as $person_id): ?>
				<?php $person = new TNG_Person($person_id->personID); ?>
				<p><a href="<?php echo $utilities->get_person_url($person); ?>"><?php echo $person->get('first_name').' '.$person->get('last_name'); ?></a></p>
			<?php endforeach; ?>
		<?php else: ?>
			<p class="lead">None</p>
		<?php endif; ?>
	</div>
</div>
<div class="row">
	<?php $events = $place->get_events(); ?>
	<?php if($events): ?>
		<?php $event_iterator = 0; ?>
		<?php foreach($events as $type => $event): ?>
			<div class="col-xs-12 col-md-4">
				<h2><?php echo $type; ?></h2>
				<?php foreach($event as $single): ?>
				<?php $person = new TNG_Person($single->person_id); ?>
					<p><a href="<?php echo $utilities->get_person_url($person); ?>"><?php echo $person->get('first_name').' '.$person->get('last_name'); ?></a></p>
				<?php endforeach; ?>
			</div>
			<?php $event_iterator++; ?>
			<?php if( $event_iterator % 3 == 0 ): ?>
				<div class="clearfix"></div>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
<?php else: ?>
<div class="page-header">
	<h1>Unknown location</h1>
</div>
<?php endif; ?>