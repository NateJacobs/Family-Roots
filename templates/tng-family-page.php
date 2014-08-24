<?php $family_id = get_query_var('tng_family_id'); ?>
<?php $family = new TNG_Family($family_id); ?>
<?php $utilities = new FamilyRootsUtilities() ?>

<h1>Family: <?php echo $family_id; ?></h1>

<?php echo '<pre>';
var_dump( $family );
echo '</pre>'; ?>