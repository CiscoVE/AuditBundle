$( function()
{
    var viewIcon = '<i class="icon-eye-open" title="View"></i>';
    var hideIcon = '<i class="icon-eye-close" title="Hide"></i>';
    
    var viewBtn = '<i class="icon-eye-open"></i> View';
    var hideBtn = '<i class="icon-eye-close"></i> Hide';

    var sectionIsHidden = true;

    // bootstrap alert dismissal
    //$(".alert").alert();

    // hide description row if any
    $( '.cisco-audit-table' ).find( '.cisco-audit-desc-row' ).hide();

    // Toggle single Field
    $( '.cisco-audit-field-view' ).click( function()
    {
        var fieldRow = $( this ).closest( '.cisco-audit-field-row' );
        var descRows = fieldRow.nextUntil( '.cisco-audit-field-row', '.cisco-audit-desc-row' );
        
        if( $( fieldRow ).hasClass( 'cisco-audit-field-row' ))
        {
            var btn = $( this ).html();
            
            if( btn.trim() === viewIcon )
            {
                $( this ).html( hideIcon );
            }
            if( btn.trim() === hideIcon )
            {
                $( this ).html( viewIcon );
            }  
        }
        
        descRows.each( function()
        {
            if( $( this ).hasClass( 'cisco-audit-desc-row' ))
            {
                $( this ).toggle();
            }
        });
    });

    // Toggle All Fields for section
    $( '.cisco-audit-section-view' ).click( function()
    {
        var sectionRow = $( this ).closest( '.cisco-audit-section-row' );
        var fieldRows = sectionRow.nextUntil( '.cisco-audit-section-row' );
        
        if( sectionIsHidden === true )
        {
            $( this ).html( hideBtn );
            
            fieldRows.each( function()
            {
                if( $( this ).hasClass( 'cisco-audit-field-row' ))
                {
                    $( this ).find( '.cisco-audit-field-view' ).html( hideIcon );
                }
                if( $( this ).hasClass( 'cisco-audit-desc-row' ) && $( this ).is( ':hidden' ))
                {
                    $( this ).toggle();
                }
            });
        
            sectionIsHidden = false;
        }
        else if( sectionIsHidden === false )
        {
            $( this ).html( viewBtn );
            
            fieldRows.each( function()
            {
                if( $( this ).hasClass( 'cisco-audit-field-row' ))
                {
                    $( this ).find( '.cisco-audit-field-view' ).html( viewIcon );                    
                }                
                if( $( this ).hasClass( 'cisco-audit-desc-row' ) && $( this ).is( ':visible' ))
                {
                    $( this ).toggle();                    
                }
            });
            
            sectionIsHidden = true;
        }
    });

    // Add Section
    $( '.cisco-audit-section-add' ).click( function()
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
                $( '.cisco-audit-section-row' ).last( '.cisco-audit-section-row' ).after().load( loadUrl );
            },
            error: function( response )
            {
                //console.log( 'failure: ' + response );
            }
        });
        return false;
    });
    
    // Remove Section
    $( '.cisco-audit-section-remove' ).click( function()
    {
        var url = $( this ).attr( 'href' );
        var that = this;

        $.ajax(
        {
            url: url,
            type: "POST",
            success: function( response )
            {
                $( that ).closest( '.cisco-audit-section-row' ).nextUntil( '.cisco-audit-section-row' ).remove();
                $( that ).closest( '.cisco-audit-section-row' ).remove();
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
        var url = $( this ).attr( 'href' );
        var sectionTitle = $( this ).attr( 'title' );
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
                    title: sectionTitle,
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
    


//    table.find( '.cisco-audit-field-row' ).click( function()
//    {
//        $( this ).nextUntil( '.cisco-audit-field-row' ).fadeToggle( 500 );;
//    });




// test code in case needed
//$('.audit-section-row').next('.audit-form-field').css('background-color', 'yellow');
//$('.audit-section-row').next('.audit-section-row').prev().css('background-color', 'red');


    // Delete Field
    // need modal box to prompt for YES/NO confirmation message
});