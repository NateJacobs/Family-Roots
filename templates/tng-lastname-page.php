<?php $lastname = get_query_var('tng_lastname_id'); ?>
<?php $utilities = new FamilyRootsUtilities() ?>
<?php $number = 25; ?>
<?php $current_page = (int) get_query_var('tng_page'); ?>
<?php $offset = $current_page !== 0 ? $current_page*$number-$number : 0; ?>
<?php $people = family_roots_get_people_from_last_name(['search' => rawurldecode($lastname), 'number' => $number, 'offset' => $offset]); ?>
<div class="page-header">
	<h1><?php echo rawurldecode($lastname); ?></h1>
</div>
<p class="lead">All the people with the last name of <?php echo rawurldecode($lastname); ?>.</p>
<?php if(!empty($people->get_results())): ?>
<table class="table">
	<thead>
		<th>Name</th>
		<th>Age</th>
		<th>Date of Birth</th>
		<th>Place of Birth</th>
	</thead>
	<tbody>
		<?php foreach($people->get_results() as $person): ?>
			<tr>
				<td><a href="<?php echo $utilities->get_person_url($person); ?>"><?php echo $person->get('first_name'); ?> <?php echo $person->get('last_name'); ?></a><?php if(!$utilities->is_living($person->get('living'), $person->get('birth_date'))){echo '<small> Deceased</small>';} ?></td>
				<td><?php echo $utilities->get_person_age($person->get('birth_date'), $person->get('death_date')); ?>
				<td><?php echo $utilities->get_date_for_display($person->get('birth_date')); ?></td>
				<td><?php echo $person->get('birth_place'); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php $utilities->tng_pagination($current_page, $number, $offset, $people->get_total()); ?>
<?php else: ?>
<p>There is no one with that last name.</p>
<?php endif; ?>