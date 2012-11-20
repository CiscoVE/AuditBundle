$( function()
{
    // Add Section
    $( '.wgaudit-section-add' ).click( function()
    {
        var url = $( this ).attr( 'href' );
        var sectionId = $( this ).attr( 'data-section-id' );
        
        loadUrl = routePatternLoad.replace( '{id}', sectionId );

        $.ajax(
        {
            url: url,
            type: "POST",
            success: function( response )
            {
                $( '.audit-section-row' ).last( '.audit-section-row' ).after().load( loadUrl );
            },
            error: function( response )
            {
                //console.log( 'failure: ' + response );
            }
        });
        return false;
    });
    
    // Remove Section
    $( '.wgaudit-section-remove' ).click( function()
    {
        var url = $( this ).attr( 'href' );
        var that = this;

        $.ajax(
        {
            url: url,
            type: "POST",
            success: function( response )
            {
                $( that ).closest( '.audit-section-row' ).nextUntil( '.audit-section-row' ).remove();
                $( that ).closest( '.audit-section-row' ).remove();
            },
            error: function( response )
            {
                //console.log( 'failure: ' + response );
            }

        });
        return false;
    });

    // Edit Section
    $( '.wgaudit-section-edit' ).click( function()
    {
        // TODO: pop a new row bellow the current one and populate with the EditSectionForm
        // OR: get current row and replace by value of the EditSectionForm
        return false;
    });

    // Add Field
    $( '.wgaudit-field-add' ).click( function()
    {
        var url = $( this ).attr( 'href' );
        var fieldId = $( this ).attr( 'data-field-id' );

        loadUrl = routePatternLoadField.replace( '{id}', fieldId );

        $.ajax(
        {
            url: url,
            type: "POST",
            success: function( response )
            {
                $( '.wgaudit-field-row' ).last( '.wgaudit-field-row' ).after().load( loadUrl );
            },
            error: function( response )
            {
                //console.log( 'failure: ' + response );
            }
        });
        return false;
    });

    // Remove Field
    $( '.wgaudit-field-remove' ).click( function()
    {
        var url = $( this ).attr( 'href' );
        var that = this;
        $.ajax(
        {
            url: url,
            type: "POST",
            success: function( response )
            {
                $( that ).closest( '.wgaudit-field-row' ).remove();
            },
            error: function( response )
            {
                //console.log( 'failure: ' + response );
            }
        });
        return false;
    });
});