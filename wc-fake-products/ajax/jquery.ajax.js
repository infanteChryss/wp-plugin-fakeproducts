jQuery.noConflict();
( ($)=> {
    $( ()=> {
        $(document).ready( ()=> {
        
            $('#wcfakeproducts_import_button').click( (e)=> {
                e.preventDefault();
                $('#wcfakeproducts_import_button').attr('disabled', true);
                $('#wcfakeproducts_import_button').html('<p>Importing products...please wait.</p>' + '<svg xmlns="http://www.w3.org/2000/svg" class="lds-curve-bars" width="30px" height="30px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><g transform="translate(50,50)"><circle cx="0" cy="0" r="8.333333333333334" fill="none" stroke="#ffffcb" stroke-width="4" stroke-dasharray="26.179938779914945 26.179938779914945" transform="rotate(308.129)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="0" repeatCount="indefinite"/></circle><circle cx="0" cy="0" r="16.666666666666668" fill="none" stroke="#fac090" stroke-width="4" stroke-dasharray="52.35987755982989 52.35987755982989" transform="rotate(360)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.2" repeatCount="indefinite"/></circle><circle cx="0" cy="0" r="25" fill="none" stroke="#ff7c81" stroke-width="4" stroke-dasharray="78.53981633974483 78.53981633974483" transform="rotate(51.8709)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.4" repeatCount="indefinite"/></circle><circle cx="0" cy="0" r="33.333333333333336" fill="none" stroke="#c0f6d2" stroke-width="4" stroke-dasharray="104.71975511965978 104.71975511965978" transform="rotate(135.238)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.6" repeatCount="indefinite"/></circle><circle cx="0" cy="0" r="41.666666666666664" fill="none" stroke="#dae4bf" stroke-width="4" stroke-dasharray="130.89969389957471 130.89969389957471" transform="rotate(224.762)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.8" repeatCount="indefinite"/></circle></g></svg>');
                $.post ( ajax_object.ajaxurl, {
                    action: 'import_products',
                    nonce: ajax_object.nonce
                }).done( (data) => {
                    if(data=='success') {
                        alert('Import complete!');
                    }
                    $('#wcfakeproducts_import_button').html('Import');
                    $('#wcfakeproducts_import_button').removeAttr('disabled', !true);
                }).fail( (err) => {
                    console.log(err);
                    alert('An error occured while processing your request.');
                    $('#wcfakeproducts_import_button').html('Import');
                });
            });

        });

    });
})(jQuery);