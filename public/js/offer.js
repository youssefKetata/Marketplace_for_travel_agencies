
$(document).ready(function() {
    // Get the div that holds the collection of offerProductTypes
    let offerProductTypesWrapper = $('.offer-product-types-wrapper');

    // Add new offerProductType form when the "Add New OfferProductType" button is clicked
    $('.add-offer-product-typeForm').click(function(e) {
        e.preventDefault();

        // Get the data-prototype for the offerProductType form
        var offerProductTypePrototype = offerProductTypesWrapper.data('prototype');

        // Replace '__name__' in the prototype HTML with a unique index
        var newIndex = offerProductTypesWrapper.children().length;
        //   console.log(offerProductTypePrototype);
        var offerProductTypeHtml = offerProductTypePrototype.replace(/__name__/g, newIndex);

        // Add a remove button to the offerProductType form
        offerProductTypeHtml += '<button type="button" class="remove-offer-product-type btn btn-sm btn-outline-danger mt-2" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; transition: all 0.2s ease-in-out;">Remove</button>';
        //offerProductTypeHtml += '<button type="button" class="remove-offer-product-type btn-danger">Remove</button>';


        // Create a new offerProductType form and add it to the
        // end of the offerProductTypesWrapper
        offerProductTypesWrapper.append('<li style="list-style-type: none;">'+offerProductTypeHtml+'</li>');

    });

    $(document).on('click', '.remove-offer-product-type', function(e) {
        e.preventDefault();

        // Remove the offerProductType form
        $(this).closest('li').remove();
    });
});
