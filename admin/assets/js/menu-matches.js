jQuery(document).ready(function($) {

    'use strict';

    window.DAEXTSOLISC = {};

    $('#live').select2();
    $('#additional-score-mode').select2();

    //Dialog Confirm ---------------------------------------------------------------------------------------------------

    /**
     * Original Version (not compatible with pre-ES5 browser)
     */
    $(function() {

        'use strict';

        $('#dialog-confirm').dialog({
            autoOpen: false,
            resizable: false,
            height: 'auto',
            width: 340,
            modal: true,
            buttons: {
                [objectL10n.deleteText]: function() {
                    $('#form-delete-' + window.DAEXTSOLISC.itemToDelete).submit();
                },
                [objectL10n.cancelText]: function() {
                    $(this).dialog('close');
                },
            },
        });

    });

    //Click event handler on the delete button
    $(document.body).on('click', '.menu-icon.delete' , function(){

        'use strict';

        event.preventDefault();
        window.DAEXTSOLISC.itemToDelete = $(this).prev().val();
        $('#dialog-confirm').dialog('open');

    });

    remove_border_last_cell_chart();

    //.group-trigger -> click - EVENT LISTENER
    $(document.body).on('click', '.group-trigger' , function(){

        //open and close the various sections of the chart area
        let target = $(this).attr('data-trigger-target');
        $('.' + target).toggle(0);
        $(this).find('.expand-icon').toggleClass('arrow-down');

        remove_border_last_cell_chart();

    });

    /*
     Remove the bottom border on the cells of the last row of the chart section
     */
    function remove_border_last_cell_chart(){
        $('table.daext-form tr > *').css('border-bottom-width', '1px');
        $('table.daext-form tr:visible:last > *').css('border-bottom-width', '0');
    }


});