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


// Create a new offerProductType form and add it to the
// end of the offerProductTypesWrapper
        offerProductTypesWrapper.append(offerProductTypeHtml);
    });
    // Remove offerProductType form when the "Remove" button is clicked
    $(document).on('click', '.remove-offer-product-type', function(e) {
        e.preventDefault();

        // Remove the offerProductType form
        $(this).closest('.offer-product-types-wrapper').remove();
    });
});
