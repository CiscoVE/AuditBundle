$( function()
{
    var viewIcon = '<i class="icon-eye-open" title="View"></i>';
    var hideIcon = '<i class="icon-eye-close" title="Hide"></i>';
    
    var viewBtn = '<i class="icon-eye-open"></i> View';
    var hideBtn = '<i class="icon-eye-close"></i> Hide';

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
     * Add Section to Form
     * Add Section Object to Form Object
     * Remove Section from drop down menu
     */
    $( '.cisco-audit-section-add' ).click( function()
    {
        var url = $( this ).attr( 'href' );
//        $( this ).closest( 'tr' ).prev().after().load( url, function(response, status, xhr) {
//            if (status == "error") {
//                var msg = "Sorry but there was an error: ";
//                $("#error").html(msg + xhr.status + " " + xhr.statusText);
//            }
//            else
//            {
//                $( '#success' ).html( response );
//            }
//        });
        $( this ).closest( 'tr' ).prev().after().load( url );
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
     * Remove seleced section 
     * Reload the list of unassigned sections after the table
     */
    $( '.cisco-audit-section-remove' ).click( function()
    {
        var url = $( this ).attr( 'href' );
        var that = this;
        var usections = $( this ).closest( 'form' ).next( '.cisco-audit-orphan-section' );

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
     * Remove Field from section
     * Remove Field Object from Section Object (saved to db)
     * Add field back to drop drown menu without refreshing the page
     */
    $( '.cisco-audit-field-remove' ).click( function()
    {
        var url = $( this ).attr( 'href' );
        var that = this;
//        var sectionId = $( this ).attr( 'data-section-id' );
        var row = $( this ).closest( 'tr' );
        var sectionRow = $( row ).prevAll( '.cisco-audit-section-row' ).first();
        var menuField = $( sectionRow ).find( '.cisco-audit-orphan-field' );
        
        console.log( menuField );
        
        $.ajax(
        {
            url: url,
            datatype: "HTML",
            success: function( response )
            {
                console.log( 'success' );
                $( that ).closest( '.cisco-audit-field-row' ).remove();
                $( menuField ).html( response );
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
    
    $( '.test' ).click( function()
    {
        var row = $( this ).closest( 'tr' );
        var form = $( this ).closest( 'form' );
        var usections = $( this ).closest( 'form' ).next( '.cisco-audit-orphan-section' );
//        var usections = $( this ).closest( '.cisco-audit-orphan-section' );
//        var parent = $( row ).parents();
//        var usections = $( row ).parent().closest( '.cisco-audit-orphan-section' );
        
        
        console.log( row );
        console.log( form );
        console.log( usections );
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