<?php
/**
 * The Template for displaying all single playlist posts.  Based on TwentyEleven.
 */

get_header(); ?>
		<div>
			<div id="content" role="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<?php $show = get_post_meta($post->ID, 'playlist_show_id', true); ?>
						<h1 class="entry-title"><?php the_title(); ?></h1>
						<h2><a href="<?php echo get_permalink($show); ?>"><?php echo get_the_title($show); ?></a></h2>
					</header><!-- .entry-header -->
					
					<div class="entry-content">
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'radio-station' ) . '</span>', 'after' => '</div>' ) ); ?>
					
						
						<!-- custom playlist output : This portion can be edited or inserted into your own theme files -->
						
						<?php $playlist = get_post_meta($post->ID, 'playlist', true); ?>
						
						<?php if($playlist): ?>
						<div class="myplaylist-playlist-entires">
							<table>
							<tr>
								<th><?php _e('Artist', 'radio-station'); ?></th>
								<th><?php _e('Song', 'radio-station'); ?></th>
								<th><?php _e('Album', 'radio-station'); ?></th>
								<th><?php _e('Record Label', 'radio-station'); ?></th>
								<th><?php _e('DJ Comments', 'radio-station'); ?></th>
							</tr>
							<?php foreach($playlist as $entry): ?>
								<?php if($entry['playlist_entry_status'] == 'played'): ?>
									<?php $myplaylist_class=''; if(isset($entry['playlist_entry_new']) && $entry['playlist_entry_new'] == 'on') { $myplaylist_class=' class="new"';} ?>
									<tr<?php echo $myplaylist_class; ?>>
										<td><?php echo $entry['playlist_entry_artist']; ?></td>
										<td><?php echo $entry['playlist_entry_song']; ?></td>
										<td><?php echo $entry['playlist_entry_album']; ?></td>
										<td><?php echo $entry['playlist_entry_label']; ?></td>
										<td><?php echo $entry['playlist_entry_comments']; ?></td>
									</tr>
								<?php endif; ?>
							<?php endforeach; ?>
							</table>
						</div>
						<? else: ?>
						<div class="myplaylist-no-entries">
							<?php _e('No entries for this playlist', 'radio-station'); ?>
						</div>
						<?php endif; ?>
						
						<!-- /custom playlist output -->
						
						
					</div><!-- .entry-content -->
					</article>

				<?php endwhile; // end of the loop. ?>

			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>