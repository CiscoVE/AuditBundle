$( function()
{
    var viewIcon = '<i class="icon-eye-open" title="View"></i>';
    var hideIcon = '<i class="icon-eye-close" title="Hide"></i>';
    
    var viewBtn = '<i class="icon-eye-open"></i> View';
    var hideBtn = '<i class="icon-eye-close"></i> Hide';

    var sectionIsHidden = true;
    
//    var routePatternCalculateScore = '{{ routePatternCalculateScore }}';

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
        
        $.ajax(
        {
            url: url,
            type: "POST",
            success: function( response )
            {
                $( '.cisco-audit-section-row' ).last( '.cisco-audit-section-row' ).after().load( url );
            },
            error: function( response )
            {
                //console.log( 'failure: ' + response );
            }
        });
        return false;
    });

    // Add Field
    $( '.cisco-audit-field-add' ).click( function()
    {
        var url = $( this ).attr( 'href' );

        $.ajax(
        {
            url: url,
            datatype:  'HTML',
            success: function( response )
            {
                $( '.cisco-audit-field-row' ).last( '.cisco-audit-field-row' ).after().load( response );
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

    // Remove Field
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
});