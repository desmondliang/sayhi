$(document).ready(function(){
    var campaign_id  = $('#campaign_info').data('campaign-id');

    $.getJSON('/api/c/get_members/' + campaign_id, function(data){
    //load members of this campaign
        $("#list-members tbody").empty();

        $.each( data, function( key, val ) {
            $("#list-members tbody").append(
                "<tr>" +
                    "<td><a href='/admin/member/"+campaign_id+"/"+val.member_id+"'>"+val.firstname+" "+val.lastname+"</a></td>" +
                    "<td>"+val.email+"</td>" +
                    "<td>"+val.last_checkin+"</td>"+
                "</tr>"
            );
        });


        $.getJSON('/api/c/get_checkins/'+$('#campaign_info').data('campaign-id'), function(data){
            $("#list-checkins tbody").empty();

            $.each( data, function( key, val ) {
                $("#list-checkins tbody").append(
                    "<tr>" +
                        "<td><a href='/admin/member/"+campaign_id+"/"+val.member_id+"'>"+val.firstname+" "+val.lastname+"</a></td>" +
                        "<td>"+val.email+"</td>" +
                        "<td>"+val.last_checkin+"</td>"+
                    "</tr>"
                );
            });
        });
    });
});


$(window).load(function(){


    $.getJSON('/api/c/get_checkin_chart_dataset/'+$('#campaign_info').data('campaign-id')+'/day', function(data_checkins){

        var chart_color = '95,193,254';

        //preparing data for chart
        var data_labels = new Array();
        var data_numbers = new Array();

        $(data_checkins.data).each(function(i, val){
            data_labels[data_labels.length] = val.label;
            data_numbers[data_numbers.length] = val.num;
        });

        //creating chart
        var chart_data = {
            labels: data_labels,
            datasets: [
                {
                    label:     "Check-in Trends",
                    fillColor: "rgba("+chart_color+",0.2)",
                    strokeColor: "rgba("+chart_color+",1)",
                    pointColor: "rgba("+chart_color+",1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba("+chart_color+",1)",
                    data: data_numbers
                }
            ]
        };


        var ctx_chartCheckInTrends = document.getElementById("chartCheckInTrends").getContext("2d");
        var chartCheckInTrends = new Chart(ctx_chartCheckInTrends).Line(chart_data, {
            responsive: true
        });

    });

    $.getJSON('/api/c/get_new_members_chart_dataset/'+$('#campaign_info').data('campaign-id')+'/day', function(data_new_memebers){

        var chart_color = '141,222,20';

        var data_labels = new Array();
        var data_numbers = new Array();

        $(data_new_memebers.data).each(function(i, val){
            data_labels[data_labels.length] = val.label;
            data_numbers[data_numbers.length] = val.num;
        });

        var chart_data = {
            labels: data_labels,
            datasets: [
                {
                    label:     "New Members Trends",
                    fillColor: "rgba("+chart_color+",0.2)",
                    strokeColor: "rgba("+chart_color+",1)",
                    pointColor: "rgba("+chart_color+",1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba("+chart_color+",1)",
                    data: data_numbers
                }
            ]
        };


        var ctx_chartNewMembersTrends = document.getElementById("chartNewMembersTrends").getContext("2d");
        var chartNewMembersTrends = new Chart(ctx_chartNewMembersTrends).Line(chart_data, {
            responsive: true
        });

    });
});
