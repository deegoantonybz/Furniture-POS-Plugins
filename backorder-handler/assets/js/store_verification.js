// store_verification.js
function displayStoreMessage() {
    var message = '<div class="store-message">' + storeVerification.message + ' ' + storeVerification.storeName + '</div>';
    var formElement = document.querySelector('.elementor-widget-container form.cart');
    var productCategoriesDiv = document.querySelector('.wd-product-cats');
    var addToCartButton = document.querySelector('.single_add_to_cart_button');
    var buyNowButton = document.querySelector('.wd-buy-now-btn');
    var addToCartLink = document.querySelector('a.add_to_cart_button');
    
    if (formElement) {
        formElement.insertAdjacentHTML('beforeend', message);
    }

    if (productCategoriesDiv) {
        productCategoriesDiv.insertAdjacentHTML('afterend', message);
    }

    if (addToCartButton) {
        addToCartButton.disabled = true;
        addToCartButton.addEventListener('click', function(event) {
            event.preventDefault();
            alert('This product cannot be added to the cart.');
            // You can also display a custom modal instead of the alert
        });
    }

    if (buyNowButton) {
        buyNowButton.disabled = true;
        buyNowButton.addEventListener('click', function(event) {
            event.preventDefault();
            alert('This product cannot be purchased.');
            // You can also display a custom modal instead of the alert
        });
    }

    if (addToCartLink) {
        addToCartLink.disabled = true;
        addToCartLink.addEventListener('click', function(event) {
            event.preventDefault();
            alert('This product cannot be added to the cart.');
            // You can also display a custom modal instead of the alert
        });
    }
}
