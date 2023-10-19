
$(document).ready(function() {

    var page = 2;
    var counter = 2;
    // Function to handle AJAX request
    function makeAjaxRequest(typeFilter,categoryFilter,dateFilter,load_more) {

        if(!load_more){ 
          page = 1;
          counter=1;
        }
        var data = {
            'action': 'my_action',
            'type':typeFilter,
            'category':categoryFilter,
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
        const contentDiv = document.getElementById("publication-section");       
    if(load_more)
      $('#card_container').append(data);
    else
        contentDiv.innerHTML = data; // Assuming the server returns HTML content
    
        }


    // Event handlers for select boxes
    $('#select_1, #select_2, #select_3').change(function() {
        const typeFilter = document.getElementById("select_1").value;
        const categoryFilter = document.getElementById("select_2").value;
        const dateFilter = document.getElementById("select_3").value;

        makeAjaxRequest(typeFilter, categoryFilter, dateFilter,false);

    });


    // Load more posts
    document.getElementById('load-more-button').addEventListener('click', function () {
        const typeFilter = document.getElementById("select_1").value;
        const categoryFilter = document.getElementById("select_2").value;
        const dateFilter = document.getElementById("select_3").value;
        load_more=true;
        document.getElementById('loader').style.display="block";
        document.getElementById('load-more-button').style.display="none";
        makeAjaxRequest(typeFilter, categoryFilter, dateFilter,load_more);      
    });
});


