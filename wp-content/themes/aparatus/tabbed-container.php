<div id="tabbed-container">

	<div id="myTabs" class="mootabs">
				<ul class="mootabs_title">
					<li title="Popular">Popular</li>
					<li title="RecentComments">Recent Comments</li>
					<li title="Tags">Tags</li>
				</ul>
				
				<div id="Popular" class="mootabs_panel">
				<ul>
        			<?php echo popularPosts(10); ?>
    			</ul>
				</div><!--popular-->				
				
				<div id="RecentComments" class="mootabs_panel">
				
				<?php my_rec_comments(7); ?>
					
				</div>
				<div id="Tags" class="mootabs_panel">

				<?php wp_tag_cloud(); ?> 
				</div>
			</div>
</div><!--tabbed-container-->
