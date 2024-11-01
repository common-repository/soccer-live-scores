jQuery(document).ready(function($) {

    'use strict';

    window.DAEXTSOLISC = {};

    $('#match-id').select2();
    $('#team').select2();
    $('#event-type-id').select2();

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

});