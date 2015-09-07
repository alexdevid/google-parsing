$(function () {
    $('#search').on('click', function (e) {
        e.preventDefault();
        var $btn = $(this).addClass('active');

        var query = $('#keywords').val();
        //$('#results').load('/search/' + query, function () {
        //    $btn.removeClass('active');
        //});
        $.ajax({
            url: '/search/' + query,
            success: function (data) {
                $btn.removeClass('active');
                $('#results').html(data)
            }
        });
    });
});