<?php

$today = date("Y-m-d");
$args = array(
    'post_type' => 'wj-events',
    'post_status' => 'publish',
    'post__in' => $id,
    'meta_key' => 'wj_events_date',
    'orderby' => 'meta_value',
    'meta_type' => 'DATE',
    'order' => 'ASC',
    'meta_query' => array(
        array(
            'key' => 'wj_events_date',
            'compare' => '>=',
            'value' => $today,
            'type' => 'DATE'
        )
    )
);

$query = new WP_Query( $args );
?>
<?php
$displayFilter = isset( WJ_Events_Settings::$options['wj_events_filter'] ) ? 'show' : 'hide';
?>
<div class="js-filter filter-<?php echo $displayFilter ?>">
    <form class="events-filter-form">
        <?php if( $terms = get_terms( [ 'taxonomy' => 'event_cat' ] ) ) : ?>
        <select id="cat" name="cat">
                <option value="">Select Category</option>
                <?php foreach( $terms as $term ) : ?>
                <option value="<?php echo $term->name; ?>"><?php echo $term->name; ?></option>
                <?php endforeach; ?>
        </select>
        <?php  endif; ?>
        <?php if( $tags = get_terms( [ 'taxonomy' => 'event_tags' ] ) ) : ?>
            <?php foreach ( $tags as $tag ) : ?>
                <input type="checkbox" id="<?php echo $tag->slug ?>" name="events-keywords[<?php echo $tag->term_id; ?>]" value="<?php echo $tag->slug; ?>">
                <label for="<?php echo $tag->slug; ?>"><?php echo $tag->name; ?></label>
            <?php endforeach ;?>
        <?php endif; ?>
        <button>Filter</button>
        <input type="hidden" name="action" value="filter">
    </form>
</div>
<div class="wj-events">
    <?php 
    if( $query->have_posts() ) :
        while ( $query->have_posts() ) :
            $query->the_post();
            $event_date = get_post_meta( get_the_ID(), 'wj_events_date', true );
            $event_place = get_post_meta( get_the_ID(), 'wj_events_place', true );
            $event_ticket_url = get_post_meta( get_the_ID(), 'wj_events_ticket_url', true );
    ?>
    <div class="wje-container">
        <?php
        if( has_post_thumbnail() ) : ?>
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail( 'medium', array( 'class' => 'img-fluid' ) ); ?>
            </a>
        <?php endif; ?>
        <div class="event-title">
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        </div>
        <?php

            $terms = get_the_terms( get_the_ID(), 'event_cat' );
            echo join( ', ', wp_list_pluck( $terms, 'name') );
            echo '<br/>';
            $tags = get_the_terms( get_the_ID(), 'event_tags' );
            echo join( ', ', wp_list_pluck( $tags, 'name') );
        ?>
        <div class="event-description">
            <div class="event-meta">
                <?php echo esc_html( $event_date ) ?> <?php esc_html_e( ' in ', 'wj-events' ); ?>
                <b><?php echo esc_html( $event_place ); ?></b> </br>
            </div>
            <div class="description">
                <?php the_content(); ?>
            </div>
            <button><a href="<?php echo esc_url($event_ticket_url) ?>"><?php esc_html_e( 'Book Now', 'wj-events' ) ?></a></button>
        </div>
    </div>
    <?php endwhile; wp_reset_postdata(); endif; ?>
    <button><a href="<?php echo site_url( '/past-events/' ) ?>"><?php esc_html_e( 'Our Past Events', 'wj-events' ); ?></a></button>
</div>