
$(document).ready(function() {

    var page = 2;
    var counter = 2;
    const selectedOptions = [];
    // Function to handle AJAX request
    function makeAjaxRequest(dateFilter,load_more) {

        if(!load_more){ 
          page = 1;
          counter=1;
        }
        var data = {
            'action': 'my_action',
            'filters': JSON.stringify(selectedOptions),
            'order':dateFilter,
            'page':page
        };
        // We can also pass the url value separately from ajaxurl for front end AJAX implementations
        jQuery.post(ajax_object.ajax_url, data, function(response) {
            updatePageContent(response.html,load_more);
            if(counter==response.max_page_number) {
                document.getElementById('load-more-button').style.display="none";        
              }
            else {
                document.getElementById('load-more-button').style.display="block";
              }  
            page++;
            counter++;
            document.getElementById('loader').style.display="none";
        },"json");
        
    
    }

    function updatePageContent(data,load_more) {
        const contentDiv = document.getElementById("blogs-section");       
    if(load_more)
      $('#card_container').append(data);
    else
        contentDiv.innerHTML = data; // Assuming the server returns HTML content
    
        }


    // Event handlers for select boxes
    $('#select_3').change(function() {
        const dateFilter = document.getElementById("select_3").value;
        makeAjaxRequest(dateFilter,false);
    });


    // Get all your checkboxes (replace 'input[type="checkbox"]' with a more specific selector)
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');

    // Attach change event listener to each checkbox
    checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
    if (this.checked) {
        // If checkbox is checked, add its value to the array
        selectedOptions.push(this.id);
        const dateFilter = document.getElementById("select_3").value;
        makeAjaxRequest(dateFilter,false);        
    } else {
        // If checkbox is unchecked, remove its value from the array
        const index = selectedOptions.indexOf(this.id);
        if (index !== -1) {
        selectedOptions.splice(index, 1);
        const dateFilter = document.getElementById("select_3").value;
        makeAjaxRequest(dateFilter,false);        
        }
    }

    // Debug: Print the current array
    console.log(myArray);
    });
    });


    // Load more posts
    document.getElementById('load-more-button').addEventListener('click', function () {
        const dateFilter = document.getElementById("select_3").value;
        load_more=true;
        document.getElementById('loader').style.display="block";
        document.getElementById('load-more-button').style.display="none";
        makeAjaxRequest(dateFilter,load_more);      
    });
});


