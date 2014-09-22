<?php $family_id = get_query_var('tng_family_id'); ?>
<?php $family = new TNG_Family($family_id); ?>
<?php $utilities = new FamilyRootsUtilities(); ?>

<?php if($family->exists()): ?>
	<div class="page-header">
		<h2>Family of <?php echo $family->get_father_name(); ?> and <?php echo $family->get_mother_name(); ?></h2>
	</div>
	<h3>Parents</h3>
	<dl>
		<?php $marriage_place_object = new TNG_Place(null, $family->marriage_place); ?>
		<?php $divorce_place_object = new TNG_Place(null, $family->divorce_place); ?>
		<?php $married = '0000-00-00' != $family->get('marriage_date') ? $utilities->get_date_for_display($family->get('marriage_date')).' &mdash; <a href="'.$utilities->get_place_url($marriage_place_object).'">'.$family->get('marriage_place').'</a>' : '' ?>
		<?php $divorced = '0000-00-00' != $family->get('divorce_date') ? $utilities->get_date_for_display($family->get('divorce_date')).' &mdash; <a href="'.$utilities->get_place_url($divorce_place_object).'">'.$family->get('divorce_place').'</a>' : '' ?>
		<?php if(!empty($married)): ?>
			<dt>Marriage</dt>
			<dd><?php echo $married; ?></dd>
		<?php endif; ?>
		<?php if(!empty($divorced)): ?>
			<dt>Divorce</dt>
			<dd><?php echo $divorced; ?></dd>
		<?php endif; ?>
	</dl>
	<!-- father -->
	<div class="row">
		<div class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><a href="<?php echo $utilities->get_person_url($family->get('father')); ?>"><?php echo $family->get_father_name(); ?></a> &ndash; Age: <?php echo $utilities->get_person_age($family->get('father')->birth_date, $family->get('father')->death_date); ?> <?php if(!$utilities->is_living($family->get('father')->living, $family->get('father')->birth_date)){echo '<small> Deceased</small>';} ?></h3>
				</div>
				<table class="table">
				    	<tr>
				    		<td>Birth:</td>
				    		<?php $birth_place_object = new TNG_Place(null, $family->get('father')->birth_place); ?>
				    		<?php $birth_place = !empty($family->get('father')->birth_place) ?  ' &mdash; <a href="'.$utilities->get_place_url($birth_place_object).'">'.$family->get('father')->birth_place.'</a>' : '';?>
						<td><?php echo $utilities->get_date_for_display($family->get('father')->birth_date).$birth_place; ?></td>
				    	</tr>
				    <?php if(!$utilities->is_living($family->get('father')->living, $family->get('father')->birth_date)): ?>
					<tr>
						<td>Death:</td>
						<?php $death_place_object = new TNG_Place(null, $family->get('father')->death_place); ?>
						<?php $death_place = !empty($family->get('father')->death_place) ?  ' &mdash; <a href="'.$utilities->get_place_url($death_place_object).'">'.$family->get('father')->death_place.'</a>' : '';?>
						<td><?php echo $utilities->get_date_for_display($family->get('father')->death_date).$death_place; ?></td>
						</tr>
					<?php endif; ?>
					<?php if(!$utilities->is_living($family->get('father')->living, $family->get('father')->birth_date)): ?>
					<tr>
						<td>Burial:</td>
						<?php $burial_place_object = new TNG_Place(null, $family->get('father')->burial_place); ?>
						<?php $burial_place = !empty($family->get('father')->burial_place) ?  ' &mdash; <a href="'.$utilities->get_place_url($birth_place_object).'">'.$family->get('father')->burial_place.'</a>' : '';?>
						<td><?php echo $utilities->get_date_for_display($family->get('father')->burial_date).$burial_place; ?></td>
					</tr>
					<?php endif; ?>
					<?php if($family->get('father')->has_parents()): ?>
					<tr>
						<td>Parents:</td>
						<td><?php echo $utilities->get_parent_template($family->get('father')); ?> &ndash; <a href="<?php echo $utilities->get_family_url($family->get('father')->family_id); ?>">Family Details</a></td>
					</tr>
					<?php endif; ?>
				</table>
				<div class="panel-footer">
					<a href="<?php echo $utilities->get_person_url($family->get('father')); ?>"><span class="glyphicon glyphicon-user"></span> Person Link</a> | <?php echo $utilities->get_sex_for_display($family->get('father')->get('sex')); ?>
				</div>
			</div>
		</div>
	</div>
	<!-- mother -->
	<div class="row">
		<div class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><a href="<?php echo $utilities->get_person_url($family->get('mother')); ?>"><?php echo $family->get_mother_name(); ?></a> &ndash; Age: <?php echo $utilities->get_person_age($family->get('mother')->birth_date, $family->get('mother')->death_date); ?> <?php if(!$utilities->is_living($family->get('mother')->living, $family->get('mother')->birth_date)){echo '<small> Deceased</small>';} ?></h3>
				</div>
				<table class="table">
				    	<tr>
				    		<td>Birth:</td>
				    		<?php $birth_place_object = new TNG_Place(null, $family->get('mother')->birth_place); ?>
				    		<?php $birth_place = !empty($family->get('mother')->birth_place) ?  ' &mdash; <a href="'.$utilities->get_place_url($birth_place_object).'">'.$family->get('mother')->birth_place.'</a>' : '';?>
						<td><?php echo $utilities->get_date_for_display($family->get('mother')->birth_date).$birth_place; ?></td>
				    	</tr>
				    <?php if(!$utilities->is_living($family->get('mother')->living, $family->get('mother')->birth_date)): ?>
					<tr>
						<td>Death:</td>
						<?php $death_place_object = new TNG_Place(null, $family->get('mother')->death_place); ?>
						<?php $death_place = !empty($family->get('mother')->death_place) ?  ' &mdash; <a href="'.$utilities->get_place_url($death_place_object).'">'.$family->get('mother')->death_place.'</a>' : '';?>
						<td><?php echo $utilities->get_date_for_display($family->get('mother')->death_date).$death_place; ?></td>
						</tr>
					<?php endif; ?>
					<?php if(!$utilities->is_living($family->get('mother')->living, $family->get('mother')->birth_date)): ?>
					<tr>
						<td>Burial:</td>
						<?php $burial_place_object = new TNG_Place(null, $family->get('mother')->burial_place); ?>
						<?php $burial_place = !empty($family->get('mother')->burial_place) ?  ' &mdash; <a href="'.$utilities->get_place_url($birth_place_object).'">'.$family->get('mother')->burial_place.'</a>' : '';?>
						<td><?php echo $utilities->get_date_for_display($family->get('mother')->burial_date).$burial_place; ?></td>
					</tr>
					<?php endif; ?>
					<?php if($family->get('mother')->has_parents()): ?>
					<tr>
						<td>Parents:</td>
						<td><?php echo $utilities->get_parent_template($family->get('mother')); ?> &ndash; <a href="<?php echo $utilities->get_family_url($family->get('mother')->family_id); ?>">Family Details</a></td>
					</tr>
					<?php endif; ?>
				</table>
				<div class="panel-footer">
					<a href="<?php echo $utilities->get_person_url($family->get('mother')); ?>"><span class="glyphicon glyphicon-user"></span> Person Link</a> | <?php echo $utilities->get_sex_for_display($family->get('mother')->get('sex')); ?>
				</div>
			</div>
		</div>
	</div>
	<?php if($family->has_children()): ?>
		<h3>Children</h3>
		<!-- children -->
		<?php foreach($family->get_children() as $child): ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><a href="<?php echo $utilities->get_person_url($child); ?>"><?php echo $child->get('first_name').' '.$child->get('last_name'); ?></a> &ndash; Age: <?php echo $utilities->get_person_age($child->get('birth_date'), $child->get('death_date')); ?> <?php if(!$utilities->is_living($child->get('living'), $child->get('birth_date'))){echo '<small> Deceased</small>';} ?></h3>
					</div>
					<table class="table">
				    		<tr>
					    		<td>Birth:</td>
					    		<?php $birth_place_object = new TNG_Place(null, $child->get('birth_place')); ?>
					    		<?php $birth_place = !empty($child->get('birth_place')) ?  ' &mdash; <a href="'.$utilities->get_place_url($birth_place_object).'">'.$child->get('birth_place').'</a>' : '';?>
							<td><?php echo $utilities->get_date_for_display($child->get('birth_date')).$birth_place; ?></td>
					    	</tr>
					    <?php if(!$utilities->is_living($child->get('living'), $child->get('birth_date'))): ?>
						<tr>
							<td>Death:</td>
							<?php $death_place_object = new TNG_Place(null, $child->get('death_place')); ?>
							<?php $death_place = !empty($child->get('death_place')) ?  ' &mdash; <a href="'.$utilities->get_place_url($death_place_object).'">'.$child->get('death_place').'</a>' : '';?>
							<td><?php echo $utilities->get_date_for_display($child->get('death_date')).$death_place; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$utilities->is_living($child->get('living'), $child->get('birth_date'))): ?>
						<tr>
							<td>Burial:</td>
							<?php $burial_place_object = new TNG_Place(null, $child->get('burial_place')); ?>
							<?php $burial_place = !empty($child->get('burial_place')) ?  ' &mdash; <a href="'.$utilities->get_place_url($birth_place_object).'">'.$child->get('burial_place').'</a>' : '';?>
							<td><?php echo $utilities->get_date_for_display($child->get('burial_date')).$burial_place; ?></td>
						</tr>
						<?php endif; ?>
						<?php if($child->has_partners()): ?>
						<tr>
							<td>Partner/Spouse:</td>
							<td>
								<?php $partners = $child->get_partners(); ?>
								<?php foreach($partners as $partner): ?>
									<?php if(!empty($partner->person_id)): ?>
										<?php $marriage_place_object = new TNG_Place(null, $partner->marriage_place); ?>
										<?php $partner_object = new TNG_Person($partner->person_id);?>
										<?php $married = '0000-00-00' != $partner->marriage_date ? ' &mdash; Married '.$utilities->get_date_for_display($partner->marriage_date).' &mdash; <a href="'.$utilities->get_place_url($marriage_place_object).'">'.$partner->marriage_place.'</a>' : '' ?>
										<?php $divorce_place_object = new TNG_Place(null, $partner->divorce_place); ?>
										<?php $divorced = '0000-00-00' != $partner->divorce_date ? ' &mdash; Divorced '.$utilities->get_date_for_display($partner->divorce_date).' &mdash; <a href="'.$utilities->get_place_url($divorce_place_object).'">'.$partner->divorce_place.'</a>' : '' ?>
										<a href="<?php echo $utilities->get_person_url($partner_object); ?>"><?php echo $partner_object->get('first_name').' '.$partner_object->get('last_name'); ?></a> &ndash; <a href="<?php echo $utilities->get_family_url($partner->family_id);?>">Family Details</a><?php echo $married.' '.$divorced; ?>
										<br>
									<?php endif; ?>
								<?php endforeach; ?>
							</td>
						</tr>
						<?php endif; ?>
					</table>
					<div class="panel-footer">
						<a href="<?php echo $utilities->get_person_url($child); ?>"><span class="glyphicon glyphicon-user"></span> Person Link</a> | <?php echo $utilities->get_sex_for_display($child->get('sex')); ?>
					</div>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
	<?php endif; ?>
<?php else: ?>
	<div class="page-header">
		<h1>Not a valid family ID number</h1>
		<p class="lead">Try a search here.</p>
	</div>
<?php endif; ?>