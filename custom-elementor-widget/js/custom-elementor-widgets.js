// custom-elementor-widgets.js

document.addEventListener('DOMContentLoaded', function() {
    const storeButton = document.querySelector('.store-selector button');
    const storeDetails = document.querySelector('.store-selector .store-details');
    const overlay = document.querySelector('.store-selector .wd-fill');

    storeButton.addEventListener('click', function() {
        storeDetails.classList.toggle('hidden');
        overlay.classList.toggle('wd-close-side-opened');
    });

    const stores = document.querySelectorAll('.store-selector .store');

    stores.forEach(function(store) {
        const storeButton = store.querySelector('.button-store');
        storeButton.addEventListener('click', function() {
          const storeName = store.querySelector('h4').textContent;
          const storeId = store.getAttribute('data-store-id');
          const storeNameSpan = document.querySelector('.sr-only');
          storeNameSpan.textContent = storeName;
    
          // Store the selected store ID and name in the session
          sessionStorage.setItem('selectedStoreId', storeId);
          sessionStorage.setItem('selectedStoreName', storeName);
          
          // Redirect to the same page after selecting the store
          window.location.reload();

          // Call function to send selectedStoreId to server
          // storeSelectedStoreIdInPHP(storeId);
    
        });
      });
    
      // Display the selected store name on page load
      const selectedStoreId = sessionStorage.getItem('selectedStoreId');
      const selectedStoreName = sessionStorage.getItem('selectedStoreName');
      const storeNameSpan = document.querySelector('.sr-only');

    // console.log(selectedStoreId);
      if (selectedStoreId && selectedStoreName) {
        // Display the selected store name in the button
        storeNameSpan.textContent = selectedStoreName;
      }
      
      // Prevent closing the widget when interacting with the input field
    const inputField = document.getElementById('zipCode');
    inputField.addEventListener('click', function(event) {
        event.stopPropagation(); // Prevent the click event from propagating
    });
    const searchField = document.querySelector('.searchStores');
    searchField.addEventListener('click', function(event) {
        event.stopPropagation(); // Prevent the click event from propagating
    });
      const searchStoresLink = document.querySelector('.searchStores');

    searchStoresLink.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default action of the link
        
        const zipCode = document.getElementById('zipCode').value;
        let foundStore = false;
        // console.log(zipCode);
        // Loop through each store
        stores.forEach(function(store) {
          const storeZipCode = store.querySelector('p[style="color: orange;"]').textContent.split(' ')[1]; // Assuming the zip code is in a <p> element with the specified style
          if (storeZipCode !== zipCode) {
              // Hide stores that don't match the entered zip code
              store.style.display = 'none';
          } else {
              // Display the store that matches the entered zip code
              store.style.display = 'block';
              foundStore = true;
          }
      });

      // Show/hide message based on whether a store is found
      const messageElement = document.getElementById('noStoreMessage');
      if (!foundStore) {
          messageElement.style.display = 'block'; // Show the message if no store is found
      } else {
          messageElement.style.display = 'none'; // Hide the message if a store is found
      }
        
    });
        
});


document.addEventListener('DOMContentLoaded', function() {
    // Retrieve the selected store ID and name from session storage
    const selectedStoreId = sessionStorage.getItem('selectedStoreId');
    const selectedStoreName = sessionStorage.getItem('selectedStoreName');
    console.log(selectedStoreId);

    if (selectedStoreId && selectedStoreName) {

        jQuery.ajax({
          type: 'POST',
          url: custom_ajax_obj.ajaxurl,
          data: {
              action: 'save_selected_store_id',
              store_id: selectedStoreId,
          },
          success: function(response) {
            console.log('Selected store ID successfully saved to the table');
            // Handle success response if needed
          },
          error: function(error) {
              console.error('Error sending selected store ID to server:', error.statusText);
              // Handle error if needed
          }
      });

        // Find the table element where you want to append the store details
        const cartTable = document.querySelector('.woocommerce-cart-form__contents tbody');

        if (cartTable) {
            // Create a new row for the selected store details
            const newRow = document.createElement('tr');
            newRow.classList.add('selected-store');

            // Add columns for the selected store details
            newRow.innerHTML = `
                <td class="product-remove">&nbsp;</td>
                <td class="product-thumbnail">&nbsp;</td>
                <td class="product-name" colspan="3"><strong>Selected Store:</strong> ${selectedStoreName}</td>
                <td class="product-subtotal">&nbsp;</td>
            `;

            // Append the new row to the cart table
            cartTable.appendChild(newRow);
        }
    }

    // Create an AJAX request to send the selected store ID to the server
    // const xhr = new XMLHttpRequest();
    // xhr.open('POST', '/wordpress/wp-content/plugins/custom-elementor-widget/js/endpoint.php');
    // // xhr.open('POST', '/wp-content/plugins/custom-elementor-widget/js/endpoint.php');
    // // xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    // xhr.setRequestHeader('Content-Type', 'application/json'); // Change to application/json

    // // const data = `selectedStoreId=${selectedStoreId}`;
    // const data = {
    //   selectedStoreId: selectedStoreId
    // };
    
    // console.log('Sending JSON data:', JSON.stringify(data));
    // xhr.send(JSON.stringify(data));
    // // xhr.send(data);

    // // Handle the response from the server
    // xhr.onload = function() {
    //   if (xhr.status === 200) {
    //     console.log('Selected store ID successfully sent to server');
    //     // location.reload();
    //   } else {
    //     console.error('Error sending selected store ID to server:', xhr.statusText);
    //   }
    // };
    
});


