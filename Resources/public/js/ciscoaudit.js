
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
    var thisScoreRow = $( row ).nextUntil( '.cisco-audit-section-row', '.cisco-audit-section-score-row' );
    var rows = $.merge( $.merge( prevRows, row ), nextRows );

//    console.log( scoreRow );

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
            if( $( this ).hasClass( 'cisco-audit-field-weight' ) )
            {
                score[2] = $( this ).find( 'div' ).attr( 'data-value' );
                if( $( this ).find( 'div' ).attr( 'data-field' ) === flagLabel)
                {
                    flaggedArray.push( score[1] );
                    if( score[1] === 'N' )
                    {
                        $( this ).find( 'div' ).text( flagLabel );
                        $( this ).addClass( 'alert alert-error' );
                    }
                    else
                    {
                        $( this ).find( 'div' ).text( score[2] );
                        $( this ).removeClass( 'alert alert-error' );
                    }
                }
            }
        });

        scores[index] = score;
        index += 1;
    });

    ( $.inArray( 'N', flaggedArray ) > -1) ? flag = true: flag = false;
    $( thisScoreRow ).attr( 'data-flag', flag );


// need to recalculate the score value held in the value attribute of the SectionScore.
    var sectionScore = $( thisScoreRow ).find( '.cisco-audit-section-score' );
    var sectionWeight = $( thisScoreRow ).find( '.cisco-audit-section-weight' );
//    var sectionScore = $( thisScoreRow ).children().next( '.cisco-audit-section-score' );
    var finalRow = $( row ).siblings( ':last' );
    var auditScore = $( finalRow ).find( '.cisco-audit-score' );
    var auditWeight = $( finalRow ).find( '.cisco-audit-weight' );

    $.ajax(
    {
        url: url,
        type: "POST",
        data: { scores: scores },
        dataType: 'text',
        success: function( response )
        {
            // set this section's score value
            $( sectionScore ).attr( 'value', parseFloat( response ).toFixed( 2 ) );
            var sectionTempScore = 0;

            // get all the score rows and merge them in order
            var prevScoreRows = $( thisScoreRow ).prevAll( '.cisco-audit-section-score-row' );
            var nextScoreRows = $( thisScoreRow ).nextAll( '.cisco-audit-section-score-row' );
            var sectionRows = $.merge( $.merge( prevScoreRows, thisScoreRow), nextScoreRows );
            var sectionFlag = $( thisScoreRow ).attr( 'data-flag' );
            var auditFlag = false;

            sectionRows.each( function()
            {
                var sRow = $( this );

                if( $( sRow ).attr( 'data-flag' ) === 'true' )
                {
                    auditFlag = true;
                }

                var sScore = parseFloat( $( sRow ).find( '.cisco-audit-section-score' ).attr( 'value' ));
                var sWeight = parseFloat( $( sRow ).find( '.cisco-audit-section-weight' ).attr( 'value' ));
                sectionTempScore += ( sScore * sWeight );
            });

            var newSectionScore = ( Math.round( response * 100 ) / 100 ).toFixed( 2 );
            var globalScore = sectionTempScore / $( auditWeight ).attr( 'value' );
            var newAuditScore = ( Math.round( globalScore * 100 ) / 100 ).toFixed( 2 );

            $( sectionScore ).attr( 'value', newSectionScore );
            $( auditScore ).attr( 'value', newAuditScore );
//            console.log( 'auditFlag: ' + auditFlag );
            if( sectionFlag === 'true' )
            {
                $( sectionScore ).text( flagLabel );
                $( sectionScore ).addClass( 'alert alert-error' );
            }
            else
            {
                $( sectionScore ).text( newSectionScore + ' %' );
                $( sectionScore ).removeClass( 'alert alert-error' );
            }

            if( auditFlag === true )
            {
                $( auditScore ).text( flagLabel );
                $( auditScore ).addClass( 'alert alert-error' );
            }
            else
            {
                $( auditScore ).text( newAuditScore + ' %' );
                $( auditScore ).removeClass( 'alert alert-error' );
            }
        },
        error: function( response )
        {
            console.log( 'can not do it .....' );
        }
    });
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