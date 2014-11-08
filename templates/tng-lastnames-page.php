<?php get_header(); ?>
<?php $utilities = new FamilyRootsUtilities() ?>
<?php $settings = get_option('family_roots_settings'); ?>
<div id="main-content" class="main-content">
	<div class="page-content">
		<div class="entry-header">
			<h1 class="entry-title">Last names in the <?php echo ucwords(strtolower($settings['default_tree'])); ?> family tree</h1>
		</div>
		<p class="lead">Top 15 last names in the tree</p>
		<?php echo family_roots_get_lastname_cloud(); ?>
		<hr>
		<?php $last_names = family_roots_unique_last_names(); ?>
		<ul class="list-inline">
			<?php foreach($last_names as $name): ?>
				<li><a href="<?php echo trailingslashit(home_url('genealogy/lastname/').rawurlencode($name->lastname)); ?>"><?php echo $name->lastname; ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php
get_sidebar();
get_footer();