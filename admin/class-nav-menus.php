<?php

/** 
 *	Manages and builds the meta boxes used in the nav-menu page.
 *
 *	@author		Nate Jacobs
 *	@date		11/7/14
 *	@since		1.0
 */
class NavMenus {
	/** 
	 *	Hook into WordPress and prepare all the methods as necessary.
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/7/14
	 *	@since		1.0
	 */
	public function __construct() {
		add_action('admin_init', [$this, 'add_family_roots_menus']);
	}

	/** 
	 *	Add a meta box to the nav-menu admin page.
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/7/14
	 *	@since		1.0
	 */
	public function add_family_roots_menus() {
		add_meta_box(
			'family_roots_nav_menu_pages',
			__( 'Family Roots Pages', 'family_roots' ),
			[$this, 'render_family_roots_pages_meta_box'],
			'nav-menus',
			'side'
		);
	}
	
	/** 
	 *	Add the two pages available for Family Roots: Last names and Places.
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/7/14
	 *	@since		1.0
	 */
	public function render_family_roots_pages_meta_box() {
		$boxes = [
			'lastnames' => [
				'id' => -1,
				'label' => 'Last Names',
				'url' => 'lastnames',
				'title' => 'Last Names'
			],
			'places' => [
				'id' => -2,
				'label' => 'Places',
				'url' => 'places',
				'title' => 'Places'
			]
		];
		$boxes = apply_filters('family_roots_nav_menu_pages_meta_box', $boxes, count($boxes));
		?>
		<div id="family-roots-pages" class="posttypediv">
        		<div id="tabs-panel-family-roots-pages" class="tabs-panel tabs-panel-active">
        			<ul id ="family-roots-pages-checklist" class="categorychecklist form-no-clear">
        				<?php foreach($boxes as $box): ?>
	        				<li>
	        					<label class="menu-item-title">
	        						<input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo $box['id']; ?>][menu-item-object-id]" value="<?php echo $box['id']; ?>"> <?php echo $box['label']; ?>
	        					</label>
	        					<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $box['id']; ?>][menu-item-type]" value="custom">
	        					<input type="hidden" class="menu-item-title" name="menu-item[<?php echo $box['id']; ?>][menu-item-title]" value="<?php echo $box['title']; ?>">
	        					<input type="hidden" class="menu-item-url" name="menu-item[<?php echo $box['id']; ?>][menu-item-url]" value="<?php echo home_url(); ?>/genealogy/<?php echo $box['url']; ?>">
	        				</li>
        				<?php endforeach; ?>
        			</ul>
        		</div>
        		<p class="button-controls">
        			<span class="list-controls">
        				<a href="<?php echo admin_url(); ?>/nav-menus.php?page-tab=all&amp;selectall=1#family-roots-pages" class="select-all">Select All</a>
        			</span>
        			<span class="add-to-menu">
        				<input type="submit" class="button-secondary submit-add-to-menu right" value="Add to Menu" name="add-post-type-menu-item" id="submit-family-roots-pages">
        				<span class="spinner"></span>
        			</span>
        		</p>
        	</div>
        	<?php
	}
}

$family_roots_nav_menu = new NavMenus();