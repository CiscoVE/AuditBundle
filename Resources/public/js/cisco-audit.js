$( function()
{
    // bootstrap alert dismissal
    $(".alert").alert();

    // Add Section
    $( '.cisco_audit-section-add' ).click( function()
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
    $( '.cisco_audit-section-remove' ).click( function()
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
    $( '.cisco-audit-section-edit' ).click( function()
    {
        // TODO: pop a new row bellow the current one and populate with the EditSectionForm
        // OR: get current row and replace by value of the EditSectionForm
        return false;
    });

    // Add Field
    $( '.cisco-audit-field-add' ).click( function()
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
                $( '.cisco-audit-field-row' ).last( '.cisco-audit-field-row' ).after().load( loadUrl );
            },
            error: function( response )
            {
                //console.log( 'failure: ' + response );
            }
        });
        return false;
    });

    // View Field
    // load up _view.html.twig in a modal box
    $( '.cisco-audit-field-view' ).click( function()
    {
        var url = $( this ).attr( 'href' );
        var content = $( 'body' ).find( '#dialog-modal' );
        
        $.ajax(
        {
            url: url,
            type: "POST",
            success: function( response )
            {
                var replacement = '<div class="modal-content">' + response + '</div>';
                
                $( content ).find( '.modal-content' ).replaceWith( replacement );
        
                $( "#dialog-modal" ).dialog({
                    height: 320,
                    width: 960,
                    modal: true
                }).show();

            },
            error: function( response )
            {
                console.log( 'can not do it .....' );
            }
        });
        return false;
    });

    // Edit Field
    // load up _edit.html.twig in a modal box
    $( '.cisco-audit-field-edit' ).click( function()
    {
        var url = $( this ).attr( 'href' );
        var content = $( 'body' ).find( '#dialog-modal' );
        
        $.ajax(
        {
            url: url,
            type: "POST",
            success: function( response )
            {
                var replacement = '<div class="modal-content">' + response + '</div>';
                
                $( content ).find( '.modal-content' ).replaceWith( replacement );
        
                $( "#dialog-modal" ).dialog(
                {
                    autoOpen: false,
                    height: 520,
                    width: 760,
                    position: [250,100],
                    modal: true,
                    buttons: 
                    {
                        "Save": function()
                        {
                            $( this ).find( 'form' ).submit();
                            $( this ).dialog( "close" );                            
                        },
                        Cancel: function() 
                        {
                            $( this ).dialog( "close" );
                        }
                    }
                });
            
                $( "#dialog-modal" ).dialog( "open" );
            },
            error: function( response )
            {
                console.log( 'can not do it .....' );
            }
        });
        return false;        
    });

    // Remove Field
    $( '.cisco-audit-field-remove' ).click( function()
    {
        var url = $( this ).attr( 'href' );
        var that = this;
        $.ajax(
        {
            url: url,
            type: "POST",
            success: function( response )
            {
                $( that ).closest( '.cisco-audit-field-row' ).remove();
            },
            error: function( response )
            {
                //console.log( 'failure: ' + response );
            }
        });
        return false;
    });
    

// test code in case needed
//$('.audit-section-row').next('.audit-form-field').css('background-color', 'yellow');
//$('.audit-section-row').next('.audit-section-row').prev().css('background-color', 'red');


    // Delete Field
    // need modal box to prompt for YES/NO confirmation message
});