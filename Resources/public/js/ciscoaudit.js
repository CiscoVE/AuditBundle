
/**
 * calculate section's score and audit score on field's score change
 */
$( document ).on( 'change', '.cisco-audit-score-selector', function()
{
    // var flagLabel declared in /views/Audit/add.html.twig

    var url = $( this ).attr( 'href' );
    var row = $( this ).closest( 'tr' );

    var prevRows = $( row ).prevUntil( '.cisco-audit-section-row', '.cisco-audit-field-row' );
    var nextRows = $( row ).nextUntil( '.cisco-audit-section-score-row', '.cisco-audit-field-row' );
    var scoreRow = $( row ).nextUntil( '.cisco-audit-section-row', '.cisco-audit-section-score-row' );
    var rows = $.merge( $.merge( prevRows, row ), nextRows );

    var scores = [];
    var index = 0;
    var flag = false;
    var flaggedArray = [];

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
            if( $( this ).hasClass( 'cisco-audit-field-weight' ) && $( this ).text().trim() === flagLabel )
            {
                flaggedArray.push( score[1] );
            }
        });

        scores[index] = score;
        index += 1;
    });

    ( $.inArray( 'N', flaggedArray ) > -1) ? flag = true: flag = false;

    var sectionScore = $( scoreRow ).children().next( '.cisco-audit-section-score' );
    var finalRow = $( row ).siblings( ':last' );
    var auditScore = $( finalRow ).children().next( '.cisco-audit-score' );
    var auditWeight = $( finalRow ).children().next( '.cisco-audit-weight' );

    if( flag )
    {
        $( sectionScore ).text( flagLabel );
        $( sectionScore ).css( 'background-color', 'red' );
        $( sectionScore ).css( 'color', 'white' );
        $( auditScore ).text( flagLabel );
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
                var sectionWeight = $( scoreRow ).children().next( '.cisco-audit-section-weight' );
                var prevSectionRows = $( scoreRow ).prevAll( '.cisco-audit-section-score-row' );
                var nextSectionRows = $( scoreRow ).nextAll( '.cisco-audit-section-score-row' );
                var sectionRows = $.merge( prevSectionRows, nextSectionRows );
                var sectionTempScore = parseFloat( response ) * $( sectionWeight ).text();

                sectionRows.each( function()
                {
                    var tempScore = 0;
                    var tempWeight = 0;

                    $( this ).children().each( function()
                    {
                        if( $( this ).hasClass( 'cisco-audit-section-score' ))
                        {
                            if( $( this ).text() === flagLabel )
                            {
                                flag = true;
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

                if( !flag )
                {
                    $( sectionScore ).text( Math.round( 100*response )/100 );
                    $( sectionScore ).css( 'background-color', $( sectionScore ).parent().css( 'background-color' ));
                    $( sectionScore ).css( 'color', $( sectionScore ).parent().css( 'color' ));
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


$( function()
{
    // Delete Field
    // need modal box to prompt for YES/NO confirmation message

//    $( '.test' ).live( 'click', function()
//    {
//        var div = $( this ).parent();
//        var table = $( this ).closest('table');
//        var table1 = $( div ).prev('table');
//        var lastRow = $( table ).children().children().last();
//        var lastRow1 = $( table1 ).children().children().last();
//
////        console.log( div );
//        console.log( lastRow );
//        console.log( lastRow1 );
////        console.log( lastRow );
////        console.log( $( lastRow ).html() );
//
////        $( '<tr>THIS IS BULLSHIT</tr>' ).insertAfter( lastRow );
//        var temp = $('<tr><td colspan="4">THIS IS NOT WORKING</td><td><a class="btn btn-mini test"><i class="icon-warning-sign"></i> Oy !</td></tr>');
//        if( table1 !== null ) $( table1 ).append( temp );
//        else if( table !== null ) $( table ).append( temp );
////        $( lastRow ).next().show();
//    });


    /**
     * testing function to color
     * @param {type} e
     * @param {type} color
     * @returns {undefined}
     */
//    function colorMe( e, color )
//    {
//        switch( color )
//        {
//            case 'yellow':
//                $( e ).css( 'background-color', 'yellow' );
//                break;
//            case 'green':
//                $( e ).css( 'background-color', 'green' );
//              break;
//            case 'red':
//                $( e ).css( 'background-color', 'red' );
//                break;
//            case 'blue':
//                $( e ).css( 'background-color', 'blue' );
//                break;
//            default:
//                var parent = $( e ).parent();
//                $( e ).css( 'background-color', $( parent ).css( 'background-color' ));
//        }
//    };
});


/**
 * Remove selected field
 * Reload the list of unassigned fields after the table
 */
$( document ).on( 'click', '.cisco-audit-field-remove', function()
{
    var emptyFieldRow = ( '<tr class="warning-empty"><td colspan="6"><i class="icon-warning-sign"></i> No field assigned to this section.</td></tr>' );
    var btn = this;
    var ufields = $( this ).closest( 'table' ).next( '.cisco-audit-orphan-field' );
    var tbody = $( this ).closest( 'tbody' );

    $.get( $( this ).attr( 'href' ), function( data )
    {
        var siblings = $( btn ).closest( 'tr' ).siblings( '.cisco-audit-field-row' );
        $( btn ).closest( 'tr' ).nextUntil( '.cisco-audit-field-row, .cisco-audit-section-row' ).remove();
        $( btn ).closest( 'tr' ).closest( '.cisco-audit-field-row' ).remove();

        if( $( siblings ).length === 0)
        {
            $( tbody ).html( emptyFieldRow );
        }

        $( ufields ).replaceWith( data );
    });

    return false;
});

/**
 * Remove selected section
 * Reload the list of unassigned sections after the table
 */
$( document ).on( 'click', '.cisco-audit-section-remove', function()
{
    var emptySectionRow = ( '<tr class="warning-empty"><td colspan="6"><i class="icon-warning-sign"></i> No section assigned to this form.</td></tr>' );
    var btn = this;
    var usections = $( this ).closest( 'table' ).next( '.cisco-audit-orphan-section' );
    var tbody = $( this ).closest( 'tbody' );

    $.get( $( this ).attr( 'href' ), function( data )
    {
        var siblings = $( btn ).closest( 'tr' ).siblings( '.cisco-audit-section-row' );
        var sectionRow = $( btn ).closest( '.cisco-audit-section-row' );
        var fieldRows = sectionRow.nextUntil( '.cisco-audit-section-row' );

        fieldRows.each( function()
        {
            if( $( this ).hasClass( 'cisco-audit-field-row' ) || $( this ).hasClass( 'cisco-audit-desc-row' ))
            {
                $( this ).remove();
            }
        });

        if( sectionRow.next().hasClass( 'warning-empty' ) ) sectionRow.next().remove();

        sectionRow.remove();

        if( $( siblings ).length === 0)
        {
            $( tbody ).html( emptySectionRow );
        }

        $( usections ).replaceWith( data );
    });

    return false;
});

/**
 * Add selected Section to Form
 * Remove button for selected section from orphan sections
 */
$( document ).on( 'click', '.cisco-audit-section-add', function()
{
    var btn = this;
    var table = $( this ).parent().prev('table');

    $.get( $( this ).attr( 'href' ), function( data )
    {
        if( $( table ).find( 'tbody' ).children().first().hasClass( 'warning-empty' ))
        {
            $( table ).find( 'tbody' ).children().remove();
        }
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
$( document ).on( 'click', '.cisco-audit-field-add', function()
{
    var btn = this;
    var table = $( this ).parent().prev('table');

    $.get( $( this ).attr( 'href' ), function( data )
    {
        if( $( table ).find( 'tbody' ).children().hasClass( 'warning-empty' ) )
        {
            $( table ).find( 'tbody' ).children().remove();
        }
        $( table ).append( data );
        $( btn ).remove();
    });

    return false;
});

/**
 * variable for the next 2 functions
 * @type String
 */
var viewIcon = '<i class="icon-eye-open" title="View"></i>';
var hideIcon = '<i class="icon-eye-close" title="Hide"></i>';

/**
 * Toggle show/hide single Field
 */
$( document ).on( 'click', '.cisco-audit-field-view', function()
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
$( document ).on( 'click', '.cisco-audit-section-view', function()
{
    var sectionRow = $( this ).closest( '.cisco-audit-section-row' );
    var fieldRows = sectionRow.nextUntil( '.cisco-audit-section-row' );

    if( $( this ).children().hasClass( 'icon-eye-open' ) )
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
    }
    else if(  $( this ).children().hasClass( 'icon-eye-close' ) )
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
    }
});

/**
 * show menu btn group on row being hovered
 */
//$( document ).on(
//{
//    mouseenter: function()
//    {
//        console.log( 'entering: ' + $( this ).parent().html() );
//        $( this ).prop( 'disabled', false );
//        $( this ).removeClass( 'disabled' );
//    },
//    mouseleave: function()
//    {
//        console.log( 'leaving: ' + $( this ).parent().html() );
//        $( this ).prop( 'disabled', true );
//        $( this ).addClass( 'disabled' );
//    }
//}, 'a:.btn' );
//



//        'hover', '.btn', function()
//{
//    console.log( this );
//    console.log( 'foo' );
//});

//    $( 'tr' ).hover( function()
//$( document ).on( 'hover', 'tr', function()
//{
//    $( this ).children().find( '.btn-group' ).children().prop( 'disabled', false );
//    $( this ).children().find( '.btn-group' ).children().removeClass( 'disabled' );
////    alert( 'foo' );
//},
//function()
//{
//    $( this ).children().find( '.btn-group' ).children().prop( 'disabled', true );
//    $( this ).children().find( '.btn-group' ).children().addClass( 'disabled' );
////    alert( 'bar' );
//});