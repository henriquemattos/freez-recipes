<?php
/**
 * Redeem gift card template.
 *
 * This template can be overriden by copying this file to your-theme/woocommerce-plugin-templates/redeem-gift-card.php
 *
 * @author  Freez
 * @package Freez_Recipes
 * @version 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Don't allow direct access

get_header(); ?>

<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
				/* Start the Loop */
				while ( have_posts() ) : the_post();

					// get_template_part( 'template-parts/post/content', get_post_format() );
          ?>
          <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
          	<?php
          		// if ( is_sticky() && is_home() ) :
          			// echo twentyseventeen_get_svg( array( 'icon' => 'thumb-tack' ) );
          		// endif;
          	?>
          	<header class="entry-header">
          		<?php
          			if ( is_single() ) {
          				the_title( '<h1 class="entry-title">', '</h1>' );
          			} elseif ( is_front_page() && is_home() ) {
          				the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
          			} else {
          				the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
          			}
          		?>
          	</header><!-- .entry-header -->

          	<?php if('' !== get_the_post_thumbnail() && !is_single()) : ?>
          		<div class="post-thumbnail">
          			<a href="<?php the_permalink(); ?>">
          				<?php the_post_thumbnail('post-thumbnail'); ?>
          			</a>
          		</div><!-- .post-thumbnail -->
          	<?php endif; ?>

          	<div class="entry-content">
          		<?php
          			/* translators: %s: Name of current post */
          			the_content(sprintf(
          				__('Continue lendo<span class="screen-reader-text"> "%s"</span>'),
          				get_the_title()
          			) );

          			wp_link_pages( array(
          				'before'      => '<div class="page-links">' . __('Páginas:'),
          				'after'       => '</div>',
          				'link_before' => '<span class="page-number">',
          				'link_after'  => '</span>',
          			) );
          		?>
          	</div><!-- .entry-content -->
						<?php
						$postmeta = json_decode(get_post_meta($post->ID, 'freez_recipes_ingredients', true));
						if(count($postmeta) > 0) : ?>
            <section class="freez-recipes ingredients">
              <table>
                <thead>
                  <tr>
                    <th>Ingrediente</th>
                    <th>Quantidade</th>
                  </tr>
                </thead>
                <tfoot></tfoot>
                <tbody>
                <?php foreach($postmeta as $ingredient) : ?>
                  <tr>
                    <td><?php echo $ingredient->ingredient; ?></td>
                    <td><?php echo $ingredient->amount . ' ' . $ingredient->measure; ?></td>
									</tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </section>
						<?php endif; ?>

          </article><!-- #post-## -->
          <?php
					// If comments are open or we have at least one comment, load up the comment template.
					// if ( comments_open() || get_comments_number() ) :
						// comments_template();
					// endif;

					the_post_navigation( array(
						'prev_text' => '<span class="screen-reader-text">' . __('Receita Anterior') . '</span><span aria-hidden="true" class="nav-subtitle">' . __('Anterior') . '</span> <span class="nav-title"><span class="nav-title-icon-wrapper dashicons dashicons-arrow-left-alt"></span>%title</span>',
						'next_text' => '<span class="screen-reader-text">' . __('Próxima Receita') . '</span><span aria-hidden="true" class="nav-subtitle">' . __('Próxima') . '</span> <span class="nav-title">%title<span class="nav-title-icon-wrapper dashicons dashicons-arrow-right-alt"></span></span>',
					) );

				endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
	<?php get_sidebar(); ?>
</div><!-- .wrap -->

<?php get_footer();
