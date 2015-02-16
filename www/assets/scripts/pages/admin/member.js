$(document).ready(function(){

    var member_id = $('#member-info').data('member-id');

    $.getJSON('/api/m/get_responses/' + member_id, function(data){

        //load responses by this member
        $("#list-responses tbody").empty();

        $.each( data, function( key, val ) {
            $("#list-responses tbody").append(
                "<tr class='row'>" +
                    "<td class='col-xs-6'>"+val.question+"</td>" +
                    "<td class='col-xs-3'>"+val.response+"</td>" +
                    "<td class='col-xs-3'>"+val.created_datetime+"</td>"+
                "</tr>"
            );
        });


        $.getJSON('/api/m/get_checkins/'+member_id, function(data){
            $("#list-checkins tbody").empty();
            var counter = 1;

            $.each( data, function( key, val ) {
                $("#list-checkins tbody").append(
                    "<tr class='row'>" +
                        "<td class='col-xs-1'>"+counter+"</td>"+
                        "<td class='col-xs-11'>"+val.datetime+"</td>"+
                    "</tr>"
                );

                counter++;
            });
        });

    });
});