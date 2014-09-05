<?php $lastname = get_query_var('tng_lastname_id'); ?>
<?php $utilities = new FamilyRootsUtilities() ?>
<?php $people = $utilities->get_people_from_last_name($lastname); ?>
<div class="page-header">
	<h1><?php echo $lastname; ?></h1>
</div>
<p class="lead">All the people with the last name of <?php echo $lastname; ?>.</p>
<?php if(!empty($people)): ?>
<table class="table">
	<thead>
		<th>Name</th>
		<th>Age</th>
		<th>Date of Birth</th>
		<th>Place of Birth</th>
	</thead>
	<tbody>
		<?php foreach($people as $person): ?>
			<tr>
				<td><a href="<?php echo $utilities->get_person_url($person->person_id); ?>"><?php echo $person->first_name; ?> <?php echo $person->last_name; ?></a></td>
				<td><?php echo $utilities->get_person_age($person->birth_date, $person->death_date); ?>
				<td><?php echo $utilities->get_date_for_display($person->birth_date); ?></td>
				<td><?php echo $person->birth_place; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<p>There is no one with that last name.</p>
<?php endif; ?>