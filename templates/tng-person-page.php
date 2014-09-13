<?php $person_id = get_query_var('tng_person_id'); ?>
<?php $person = new TNG_Person($person_id); ?>
<?php $utilities = new FamilyRootsUtilities(); ?>
<?php $pedigree = new TNG_Pedigree($person) ?>

<div class="media">
	<span class="pull-left" href="#">
		<?php if(!family_roots_get_person_photo($person)): ?>
			<img class="media-object img-rounded" src="http://0.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=200" alt="<?php echo $person->get('first_name').' '.$person->get('last_name'); ?>">
		<?php else: ?>
			<img class="media-object img-rounded" src="<?php echo family_roots_get_person_photo($person); ?>" alt="<?php echo $person->get('first_name').' '.$person->get('last_name'); ?>">
		<?php endif; ?>
	</span>
	<div class="media-body">
		<h2 class="media-heading"><?php echo $person->get('first_name').' '.$person->get('last_name'); ?></h2>
		<h4>Age: <?php echo $utilities->get_person_age($person->get('birth_date'), $person->get('death_date')); ?> <?php if(!$utilities->is_living($person->get('living'), $person->get('birth_date'))){echo '<small> Deceased</small>';} ?></h4>
		<table class="table">
			<tr>
				<td>Birth:</td>
				<?php $birth_place_object = new TNG_Place(null, $person->get('birth_place')); ?>
				<?php $birth_place = !empty($person->get('birth_place')) ?  ' &mdash; <a href="'.$utilities->get_place_url($birth_place_object).'">'.$person->get('birth_place').'</a>' : '';?>
				<td><?php echo $utilities->get_date_for_display($person->get('birth_date')).$birth_place; ?></td>
			</tr>
			<?php if(!$utilities->is_living($person->get('living'), $person->get('birth_date'))): ?>
			<tr>
				<td>Death:</td>
				<?php $death_place_object = new TNG_Place(null, $person->get('death_place')); ?>
				<?php $death_place = !empty($person->get('death_place')) ?  ' &mdash; <a href="'.$utilities->get_place_url($death_place_object).'">'.$person->get('death_place').'</a>' : '';?>
				<td><?php echo $utilities->get_date_for_display($person->get('death_date')).$death_place; ?></td>
			</tr>
			<tr>
				<td>Burial:</td>
				<?php $burial_place_object = new TNG_Place(null, $person->get('burial_place')); ?>
				<?php $burial_place = !empty($person->get('burial_place')) ?  ' &mdash; <a href="'.$utilities->get_place_url($burial_place_object).'">'.$person->get('burial_place').'</a>' : '';?>
				<td><?php echo $utilities->get_date_for_display($person->get('burial_date')).$burial_place; ?></td>
			</tr>
			<?php endif; ?>
			<tr>
				<td>Gender:</td>
				<td><?php echo $utilities->get_sex_for_display($person->get('sex')); ?></td>
			</tr>
			<?php if($person->has_parents()): ?>
			<tr>
				<td>Parents:</td>
				<td><?php echo $utilities->get_parent_template($person); ?></td>
			</tr>
			<?php endif; ?>
			<?php if($person->has_partners()): ?>
			<tr>
				<td>Partner/Spouse:</td>
				<td>
					<?php $partners = $person->get_partners(); ?>
					<?php foreach($partners as $partner): ?>
						<?php if(!empty($partner->person_id)): ?>
							<?php $partner_object = new TNG_Person($partner->person_id);?>
							<?php $marriage_place_object = new TNG_Place(null, $partner->marriage_place); ?>
							<?php $divorce_place_object = new TNG_Place(null, $partner->divorce_place); ?>
							<?php $married = '0000-00-00' != $partner->marriage_date ? ' &mdash; Married '.$utilities->get_date_for_display($partner->marriage_date).' &mdash; <a href="'.$utilities->get_place_url($marriage_place_object).'">'.$partner->marriage_place.'</a>' : '' ?>
							<?php $divorced = '0000-00-00' != $partner->divorce_date ? ' &mdash; Divorced '.$utilities->get_date_for_display($partner->divorce_date).' &mdash; <a href="'.$utilities->get_place_url($divorce_place_object).'">'.$partner->divorce_place.'</a>' : '' ?>
							<a href="<?php echo $utilities->get_person_url($partner_object); ?>"><?php echo $partner_object->get('first_name').' '.$partner_object->get('last_name'); ?></a> &ndash; <a href="<?php echo $utilities->get_family_url($partner->family_id); ?>">Family Details</a><?php echo $married.' '.$divorced; ?>
							<br>
						<?php endif; ?>
					<?php endforeach; ?>
				</td>
			</tr>
			<?php endif; ?>
			<?php if($person->has_children()): ?>
			<tr>
				<td>Children:</td>
				<td>
					<?php $children = $person->get_children(); ?>
					<?php foreach($children as $child): ?>
						<?php $child_object = new TNG_Person($child);?>
						<a href="<?php echo $utilities->get_person_url($child_object); ?>"><?php echo $child_object->get('first_name').' '.$child_object->get('last_name'); ?></a> 
						(Age <?php echo $utilities->get_person_age($child_object->get('birth_date'), $child_object->get('death_date')); ?>) <?php if(!$utilities->is_living($child_object->get('living'), $child_object->get('birth_date'))){echo '<small> Deceased</small>';} ?>
						<br>
					<?php endforeach; ?>
				</td>
			</tr>
			<?php endif; ?>
			<?php if($person->has_siblings()): ?>
			<tr>
				<td>Siblings:</td>
				<td>
					<?php $siblings = $person->get_siblings(); ?>
					<?php foreach($siblings as $sibling): ?>
						<?php $sibling_object = new TNG_Person($sibling);?>
						<a href="<?php echo $utilities->get_person_url($sibling_object); ?>"><?php echo $sibling_object->get('first_name').' '.$sibling_object->get('last_name'); ?></a>
						(Age <?php echo $utilities->get_person_age($sibling_object->get('birth_date'), $sibling_object->get('death_date')); ?>) <?php if(!$utilities->is_living($sibling_object->get('living'), $sibling_object->get('birth_date'))){echo '<small> Deceased</small>';} ?>
						<br>
					<?php endforeach; ?>
				</td>
			</tr>
			<?php endif; ?>
		</table>
	</div>
</div>
<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
	<li class="active"><a href="#events" role="tab" data-toggle="tab">Events</a></li>
	<li><a href="#media" role="tab" data-toggle="tab">Media</a></li>
	<li><a href="#pedigree" role="tab" data-toggle="tab">Ancestors</a></li>
</ul>
<!-- Tab panes -->
<div class="tab-content">
	<div class="tab-pane active" id="events">
		<div class="row">
			<br>
			<?php $events = $person->get_events(); ?>
			<?php $event_iterator = 0; ?>
			<?php foreach($events as $event): ?>
				<?php if($event->display != '_UID'): ?>
				<div class="col-xs-12 col-md-4">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><?php echo $event->display; ?></h3>
						</div>
						<div class="panel-body">
							<p><?php echo $event->info ?></p>
							<p>
								<?php if('0000-00-00' != $event->event_date): ?>
									<?php echo $utilities->get_date_for_display($event->event_date); ?>
								<?php endif; ?>
							</p>
							<p><?php echo $event->note; ?></p>
							<p><?php echo $event->event_place; ?></p>
						</div>
					</div>
				</div>
				<?php endif; ?>
				<?php $event_iterator++; ?>
				<?php if( $event_iterator % 3 == 0 ): ?>
					<div class="clearfix"></div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="tab-pane" id="media">
		<div class="row">
			<br>
			<?php $media_iterator = 0; ?>
			<?php foreach($person->get_media() as $media): ?>
				<?php if($media->media_type == 'photos'): ?>
				<div class="col-xs-6 col-md-3">
					<div class="thumbnail">
						<img src="<?php echo family_roots_get_photo_url($media->media_path); ?>" alt="...">
					</div>
					<?php if(!empty($media->description)): ?>
					<figure class="bg-warning caption">
						<figcaption class="wp-caption-text"><?php echo $media->description; ?></figcaption>
					</figure>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<?php $event_iterator++; ?>
				<?php if( $event_iterator % 4 == 0 ): ?>
					<div class="clearfix"></div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="tab-pane" id="pedigree">
		<span id="person-pedigree-json" hidden><?php echo $pedigree->get_pedigree_json(); ?></span>
		<div id="pedigree-chart"></div>
	</div>
</div>
<br>
<h4>Notes</h4>
<div class="row">
	<?php $notes = $person->get_notes(); ?>
	<?php foreach($notes as $note): ?>
		<?php if(empty($note->eventID)): ?>
			<div class="col-sm-12">
				<div class="panel panel-default">
					<div class="panel-body">
						<?php echo wpautop($note->note); ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
</div>