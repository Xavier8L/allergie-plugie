jQuery(document).ready(($) => {

    $checkbox = $('.filter-btn');

    // check box klick voor de allgies name krijg en stuur naar php daarna later zie de products html in wordpress
    $(".filter-btn").click(function (e) { 
        var chkArray = [];
        chkArray = $.map($checkbox, function (el) {
            if (el.checked) { return el.id };
        });
        console.log(chkArray);
        let jsonString = JSON.stringify(chkArray);
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'post',
            data: {
                action: 'my_filter',
                data: chkArray
            },
            success: (result) => {
                $("#filter-result").html(result);
                console.log("ja")
            },
            error: (error) => {
                console.warn(error);
            }
        })   
    });

    // show all button klick voor alles products late zie
    $("#all-filter").click(function (e) { 

       $.ajax({
           url: ajax_object.ajax_url,
           type: 'post',
           data: {
               action: 'my_filter',
               keyword:  null
           },
           success: (result) => {
               $("#filter-result").html(result);
               $checkbox.prop( "checked", false );
           },
           error: (error) => {
               console.warn(error);
           }
       })   
   });

})


