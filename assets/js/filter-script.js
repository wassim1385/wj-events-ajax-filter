jQuery( function( $ )  {

    $(document).ready( function() {

        $(document).on('submit', '.events-filter-form',function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            console.log('Data: ' + data);
            $.ajax({
                url:VARS.ajax_url,
                type:'POST',
                data:data,
                success:function(response) {
                    $('.wj-events').html(response)
                    console.log(response);
                }
            })
        } );

    });
});