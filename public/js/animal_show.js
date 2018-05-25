$(document).ready(function() {
    $('.js-like-animal').on('click', function(e) {
        e.preventDefault();

        var $link = $(e.currentTarget);
        var animal_id = {{animal.id}};
        var user_id = {{app.user.id}};

        $link.toggleClass('fa-heart-o').toggleClass('fa-heart');

        $.ajax({
            method: 'POST',
            url: $link.attr('href'),
            data: {animal_id: animal_id, user_id: user_id}
        }).done(function(data) {
            $('.js-like-animal-count').html(data.hearts);
        })
    });
});