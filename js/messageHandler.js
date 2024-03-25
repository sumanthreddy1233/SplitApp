        window.onload = function() {
            // Function to get URL query parameters
            function getQueryParam(param) {
                var search = window.location.search.substring(1);
                var vars = search.split('&');
                for (var i = 0; i < vars.length; i++) {
                    var pair = vars[i].split('=');
                    if (pair[0] == param) {
                        return decodeURIComponent(pair[1]);
                    }
                }
                return false;
            }
    
            // Get the 'message' query parameter
            var message = getQueryParam('message');
            if (message) {
                alert(message); // Display the message as an alert
            }
        }
    