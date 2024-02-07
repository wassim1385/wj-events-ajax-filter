<?php
$event_date = get_post_meta( $post->ID, 'wj_events_date', true );
$event_place = get_post_meta( $post->ID, 'wj_events_place', true );
$event_ticket_url = get_post_meta( $post->ID, 'wj_events_ticket_url', true );
?>
<table class="form-table wj-events-metabox">
<input type="hidden" name="wj_events_nonce" value="<?php echo wp_create_nonce( "wj_events_nonce" ); ?>">
    <tr>
        <th>
            <label for="wj_events_date">Event Date</label>
        </th>
        <td>
            <input 
                type="date" 
                name="wj_events_date" 
                id="wj_events_date" 
                class="regular-text link-text"
                value="<?php echo $event_date ?>"
                required
            >
        </td>
    </tr>
    <tr>
        <th>
            <label for="wj_events_place">Place</label>
        </th>
        <td>
            <input 
                type="text" 
                name="wj_events_place" 
                id="wj_events_place" 
                class="regular-text link-text"
                value="<?php echo $event_place ?>"
                required
            >
        </td>
    </tr> 
    <tr>
        <th>
            <label for="wj_events_ticket_url">Link for Tickets</label>
        </th>
        <td>
            <input 
                type="url" 
                name="wj_events_ticket_url" 
                id="wj_events_ticket_url" 
                class="regular-text link-url"
                value="<?php echo $event_ticket_url ?>"
            >
        </td>
    </tr>               
</table>