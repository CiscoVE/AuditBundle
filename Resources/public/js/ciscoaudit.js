$( function()
{
    var viewIcon = '<i class="icon-eye-open" title="View"></i>';
    var hideIcon = '<i class="icon-eye-close" title="Hide"></i>';
    
    var sectionIsHidden = true;
    
    // bootstrap alert dismissal
    //$(".alert").alert();

    /**
     * hide description row if any on page load
     */
    $( '.cisco-audit-table' ).find( '.cisco-audit-desc-row' ).hide();

    /**
     * hide menu btn group in table's row
     */
    $( 'tr' ).children().find( '.btn-group' ).children().prop( 'disabled', true );
    $( 'tr' ).children().find( '.btn-group' ).children().addClass( 'disabled' );

    /**
     * show menu btn group on row being hovered
     */
    $( 'tr' ).hover( function()
    {
        $( this ).children().find( '.btn-group' ).children().prop( 'disabled', false );
        $( this ).children().find( '.btn-group' ).children().removeClass( 'disabled' );
    },
    function()
    {
        $( this ).children().find( '.btn-group' ).children().prop( 'disabled', true );
        $( this ).children().find( '.btn-group' ).children().addClass( 'disabled' );
    });   

    /**
     * Toggle show/hide single Field 
     */
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

    /**
     * Toggle show/hide All Fields for Section
     */
    $( '.cisco-audit-section-view' ).click( function()
    {
        var sectionRow = $( this ).closest( '.cisco-audit-section-row' );
        var fieldRows = sectionRow.nextUntil( '.cisco-audit-section-row' );
        
        if( sectionIsHidden === true )
        {
            $( this ).html( hideIcon );
            
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
            $( this ).html( viewIcon );
            
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

    /**
     * Add selected Section to Form
     * Remove button for selected section from orphan sections
     */
    $( '.cisco-audit-section-add' ).click( function()
    {
        alert( 'I am here' );
        
        var btn = this;
        
        $.get( $( this ).attr( 'href' ), function( data )
        {
            $( btn ).parent().prev('table').append( data );
            $( btn ).remove();
        });        
        
        return false;
    });

    /**
     * Add Field to Section
     * Add Field Object to Section Object
     * Remove Field from drop down menu
     */
    $( '.cisco-audit-field-add' ).click( function()
    {
        var url = $( this ).attr( 'href' );
        $( '.cisco-audit-field-row' ).last( '.cisco-audit-field-row' ).after().load( url );
        return false;
    });

    /**
     * Remove selected section 
     * Reload the list of unassigned sections after the table
     */
    $( '.cisco-audit-section-remove' ).click( function()
    {
        var url = $( this ).attr( 'href' );
        var that = this;
        var usections = $( this ).closest( 'table' ).next( '.cisco-audit-orphan-section' );

        $.ajax(
        {
            url: url,
            type: "POST",
            success: function( response )
            {
                var sectionRow = $( that ).closest( '.cisco-audit-section-row' );
                var fieldRows = sectionRow.nextUntil( '.cisco-audit-section-row' );
                $( usections ).replaceWith( response );
                
                fieldRows.each( function()
                {
                    if( $( this ).hasClass( 'cisco-audit-field-row' ) || $( this ).hasClass( 'cisco-audit-desc-row' ))
                    {
                        $( this ).remove();
                    }
                });
                sectionRow.remove();
            },
            error: function( response )
            {
                //console.log( 'failure: ' + response );
            }
        });
        return false;
    });

    /**
     * Remove selected field
     * Reload the list of unassigned fields after the table
     */
    $( '.cisco-audit-field-remove' ).click( function()
    {
        var url = $( this ).attr( 'href' );
        var that = this;
        var ufields = $( this ).closest( 'table' ).next( '.cisco-audit-orphan-field' );
        
        console.log( ufields );
        
        $.ajax(
        {
            url: url,
            datatype: "HTML",
            success: function( response )
            {
                console.log( 'success' );
                $( that ).closest( '.cisco-audit-field-row' ).remove();
                $( ufields ).replaceWith( response );
            },
            error: function( response )
            {
                //console.log( 'failure: ' + response );
            }
        });
        return false;
    });

    /**
     * calculate section's score and audit score on field's score change
     */
    $( '.cisco-audit-score-selector' ).change( function()
    {
        var url = $( this ).attr( 'href' );
        var row = $( this ).closest( 'tr' );
        
        var prevRows = $( row ).prevUntil( '.cisco-audit-section-row', '.cisco-audit-field-row' );
        var nextRows = $( row ).nextUntil( '.cisco-audit-section-score-row', '.cisco-audit-field-row' );
        var scoreRow = $( row ).nextUntil( '.cisco-audit-section-row', '.cisco-audit-section-score-row' );
        var rows = $.merge( $.merge( prevRows, row ), nextRows );

        var scores = [];
        var index = 0;
        var failed = false;
        var failedArray = [];
        
        rows.each( function()
        {
            var score = [];
            score[0] = $( this ).attr( 'field-id' );
            
            $( this ).children().each( function()
            {                
                if( $( this ).hasClass( 'cisco-audit-field-score' ))
                {
                    score[1] = $( this ).children().val();
                }
                if( $( this ).hasClass( 'cisco-audit-field-weight' ) && $( this ).text().trim() === 'Involve Mgr' )
                {
                    failedArray.push( score[1] );
                }
            });
        
            scores[index] = score;
            index += 1;
        });
    
        ( $.inArray( 'N', failedArray ) > -1) ? failed = true: failed = false;
        
        var finalRow = $( row ).siblings( ':last' );
        var auditScore = $( finalRow ).children().next( '.cisco-audit-score' );
        var auditWeight = $( finalRow ).children().next( '.cisco-audit-weight' );

        if( failed )
        {
            $( sectionScore ).text( 'FAILED' );
            $( sectionScore ).css( 'background-color', 'red' );
            $( sectionScore ).css( 'color', 'white' );
            $( auditScore ).text( 'FAILED' );
            $( auditScore ).css( 'background-color', 'red' );
            $( auditScore ).css( 'color', 'white' );
        }        
        else
        {
            $.ajax(
            {
                url: url,
                type: "POST",
                data: { scores: scores },
                dataType: 'text',
                success: function( response )
                {
                    $( sectionScore ).text( Math.round( 100*response )/100 );
                    $( sectionScore ).css( 'background-color', $( sectionScore ).parent().css( 'background-color' ));
                    $( sectionScore ).css( 'color', $( sectionScore ).parent().css( 'color' ));
                    
                    var sectionScore = $( scoreRow ).children().next( '.cisco-audit-section-score' );
                    var sectionWeight = $( scoreRow ).children().next( '.cisco-audit-section-weight' );
                    var prevSectionRows = $( scoreRow ).prevAll( '.cisco-audit-section-score-row' );
                    var nextSectionRows = $( scoreRow ).nextAll( '.cisco-audit-section-score-row' );
                    var sectionRows = $.merge( prevSectionRows, nextSectionRows );
                    var sectionTempScore = parseFloat( response ) * $( sectionWeight ).text();

                    sectionRows.each( function()
                    {
                        var tempScore;
                        var tempWeight;
                        
                        $( this ).children().each( function()
                        {
                            if( $( this ).hasClass( 'cisco-audit-section-score' ))
                            {
                                if( $( this ).text() === 'FAILED' )
                                {
                                    failed = true;
                                }
                                else
                                {
                                    tempScore = parseInt( $( this ).text());
                                }
                            }
                            if( $( this ).hasClass( 'cisco-audit-section-weight' ))
                            {
                                tempWeight = parseInt( $( this ).text());
                            }
                        });
                        sectionTempScore += tempScore * tempWeight;
                    });
                    
                    if( !failed )
                    {
                        var globalScore = sectionTempScore / $( auditWeight ).text();
                        $( auditScore ).text( Math.round( 100*globalScore )/100 );
                        $( auditScore ).css( 'background-color', $( auditScore ).parent().css( 'background-color' ));
                        $( auditScore ).css( 'color', $( auditScore ).parent().css( 'color' ));
                    }
                },
                error: function( response )
                {
                    console.log( 'can not do it .....' );
                }
            });
        }
    });

// test code in case needed
//$('.audit-section-row').next('.audit-form-field').css('background-color', 'yellow');
//$('.audit-section-row').next('.audit-section-row').prev().css('background-color', 'red');


    // Delete Field
    // need modal box to prompt for YES/NO confirmation message
    
//    $( '.test' ).click( function()
    $( '.test' ).live( 'click', function()
    {
        var div = $( this ).parent();
        var table = $( this ).closest('table');
        var table1 = $( div ).prev('table');
        var lastRow = $( table ).children().children().last();
        var lastRow1 = $( table1 ).children().children().last();
        
//        console.log( div );
        console.log( lastRow );
        console.log( lastRow1 );
//        console.log( lastRow );
//        console.log( $( lastRow ).html() );
        
//        $( '<tr>THIS IS BULLSHIT</tr>' ).insertAfter( lastRow );
        var temp = $('<tr><td colspan="4">THIS IS BULLSHIT</td><td><a class="btn btn-mini test"><i class="icon-warning-sign"></i> Oy !</td></tr>');
        if( table1 !== null ) $( table1 ).append( temp );
        else if( table !== null ) $( table ).append( temp );
//        $( lastRow ).next().show();
    });
 
    
    /**
     * testing function to color 
     * @param {type} e
     * @param {type} color
     * @returns {undefined}
     */
    function colorMe( e, color )
    {
        switch( color )
        {
            case 'yellow':
                $( e ).css( 'background-color', 'yellow' );
                break;
            case 'green':
                $( e ).css( 'background-color', 'green' );
              break;
            case 'red':
                $( e ).css( 'background-color', 'red' );
                break;
            case 'blue':
                $( e ).css( 'background-color', 'blue' );
                break;
            default:
                var parent = $( e ).parent();
                $( e ).css( 'background-color', $( parent ).css( 'background-color' ));
        }
    };    
});