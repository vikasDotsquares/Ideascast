
        // This page is part of an application that calls a web
        // service that returns response values with the most
        // appropriate status codes. We want those status codes
        // to trigger done/fail callbacks with parsed values.
        $.ajaxSetup(
            {
                normalizeForStatusCodes: true
            }
        );


        // Bind to the form submission error to handle it via AJAX
        // rather than through the standard HTTP request.
        form.submit(
            function( event ){

                // Prevent the default browser behavior.
                event.preventDefault();

                // Try to save the contact to the server. The
                // saveContact() method returnes a promise object
                // which will come back with a result eventually.
                // Depending on how it resolves, either the done()
                // or fail() event handlers will be invoked.
                //
                // NOTE: This return object can be chained; but for
                // clarity reasons, I am leaving these as one-offs.
                var saveAction = saveContact(
                    username.val(),
                    contactName.val(),
                    contactAge.val()
                );

                // Hook into the "success" outcome.
                saveAction.done(
                    function( response ){

                        // Output success message.
                        message.text(
                            "Contact " + response.data + " saved!"
                        );

                        // Show the message.
                        message.show();

                    }
                );

                // Hook into the "fail" outcome.
                saveAction.fail(
                    function( response ){

                        // Output fail message.
                        message.html(
                            "Please review the following<br />-- " +
                            response.errors.join( "<br />-- " )
                        );

                        // Show the message.
                        message.show();

                    }
                );

            }
        );


        // I save the contact data.
        function saveContact( username, name, age ){
            // Initiate the AJAX request. This will return an
            // AJAX promise object that maps (mostly) to the
            // standard done/fail promise interface.
            var request = $.ajax({
                type: "post",
                url: "./api.cfm",
                data: {
                    username: username,
                    name: name,
                    age: age
                }
            });

            // Return the jqXHR promise object.
            return( request );
        }


        // -------------------------------------------------- //
        // -------------------------------------------------- //
        // -------------------------------------------------- //
        // -------------------------------------------------- //
        // -------------------------------------------------- //
        // -------------------------------------------------- //


        // Here, we are providing a way to normalize AJAX responses
        // to web services make proper use of status codes when
        // returning request values. This will look for a
        // "normalizeForStatusCodes" option before altering the
        // jqXHR object.
        $.ajaxPrefilter(

            function( options, localOptions, jqXHR ){

                // Check to see if this request is going to require
                // a normalization based on status codes.
                if (options.normalizeForStatusCodes){

                    // The user wants the response status codes to
                    // be handled as part of the routing; augment the
                    // jqXHR object to parse "fail" responses.
                    normalizeAJAXRequestForStatusCodes( jqXHR );

                }

            }

        );


        // I take the AJAX request and return a new deferred object
        // that is able to normalize the response from the server so
        // that all of the done/fail handlers can treat the incoming
        // data in a standardized, unifor manner.
        function normalizeAJAXRequestForStatusCodes( jqXHR ){
            // Create an object to hold our normalized deferred.
            // Since AJAX errors don't get parsed, we need to
            // create a proxy that will handle that for us.
            var normalizedRequest = $.Deferred();

            // Bind the done/fail aspects of the original AJAX
            // request. We can use these hooks to resolve our
            // normalized AJAX request.
            jqXHR.then(

                // SUCCESS hook. ------ //
                // Simply pass this onto the normalized
                // response object (with a success-based resolve).
                normalizedRequest.resolve,

                // FAIL hook. -------- //
                function( jqXHR ){

                    // Check to see what the status code of the
                    // response was. A 500 response will represent
                    // an unexpected error. Anything else is simply
                    // a non-20x error that needs to be manually
                    // parsed.
                    if (jqXHR.status == 500){

                        // Normalize the fail() response.
                        normalizedRequest.rejectWith(
                            this,
                            [
                                {
                                    success: false,
                                    data: "",
                                    errors: [ "Unexpected error." ],
                                    statusCode: jqXHR.statusCode()
                                },
                                "error",
                                jqXHR
                            ]
                        );

                    } else {

                        // Normalize the non-500 "failures." These
                        // are actually valid responses that require
                        // actions on the part of the user.
                        normalizedRequest.rejectWith(
                            this,
                            [
                                $.parseJSON( jqXHR.responseText ),
                                "success",
                                jqXHR
                            ]
                        );

                    }

                }

            );


            // We can't actually return anything meaningful from this
            // function; but, we can augment the incoming jqXHR
            // object. Right now, the incoming jqXHR promise methods
            // reference their original settings; however, we can
            // copy the locally-created deferred object methods into
            // the existing jqXHR. This will keep the jqXHR objec the
            // same reference -- but, it will essentially change all
            // the meaningful bindingds.
            jqXHR = normalizedRequest.promise( jqXHR );

            // At this point, the promise-based methods of the jqXHR
            // are actually the locally-declared ones. Now, we just
            // have to point the sucecss and error methods (AJAX
            // related) to the done and fail methods (promise
            // related).
            jqXHR.success = jqXHR.done;
            jqXHR.error = jqXHR.fail;

            // NOTE: No need to return anything since the jqXHR
            // object is being passed by reference.
        }
