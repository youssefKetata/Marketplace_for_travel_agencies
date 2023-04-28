
// $(document).ready(function() {
//     // Get the div that holds the collection of offerProductTypes
//     let offerProductTypesWrapper = $('.offer-product-types-wrapper');
//
//     // Add new offerProductType form when the "Add New OfferProductType" button is clicked
//     $('.add-offer-product-typeForm').click(function(e) {
//         e.preventDefault();
//
//         // Get the data-prototype for the offerProductType form
//         let offerProductTypePrototype = offerProductTypesWrapper.data('prototype');
//
//         // Replace '__name__' in the prototype HTML with a unique index
//         let newIndex = offerProductTypesWrapper.children().length;
//         //   console.log(offerProductTypePrototype);
//         let offerProductTypeHtml = offerProductTypePrototype.replace(/__name__/g, newIndex);
//
//         offerProductTypesWrapper.append(offerProductTypeHtml);
//     });
//     $(document).on('click', '.remove-offer-product-type', function(e) {
//         e.preventDefault();
//         $(this).closest('li').remove();
//     });
// });


$(document).ready(function() {
    // Get the div that holds the collection of offerProductTypes
    let offerProductTypesWrapper = $('.offer-product-types-wrapper');

    // Add new offerProductType form when the "Add New OfferProductType" button is clicked
    $('.add-offer-product-typeForm').click(function(e) {
        e.preventDefault();

        // Get the data-prototype for the offerProductType form
        let offerProductTypePrototype = offerProductTypesWrapper.data('prototype');

        // Replace '__name__' in the prototype HTML with a unique index
        let newIndex = offerProductTypesWrapper.children().length;
        //   console.log(offerProductTypePrototype);
        let offerProductTypeHtml = offerProductTypePrototype.replace(/__name__/g, newIndex);

        // Add a remove button to the offerProductType form
        //svg is just an icon
        offerProductTypeHtml += '<button type="button" class="remove-offer-product-type btn btn-danger"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash-fill " viewBox="0 0 16 16"><path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z" /></svg>Remove</button>';

        offerProductTypesWrapper.append('<li style="list-style-type: none;">'+offerProductTypeHtml+'</li>');

    });
    $(document).on('click', '.remove-offer-product-type', function(e) {
        e.preventDefault();
        $(this).closest('li').remove();
    });
});

