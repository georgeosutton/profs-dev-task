jQuery(document).ready(function($) {
    $('.like-button').on('click', function() {
        var button = $(this);
        var postId = button.data('post-id');
        var likesCountSpan = button.siblings('.likes-count');

        $.ajax({
            url: likePostData.ajaxurl,
            type: 'POST',
            data: {
                action: 'like_post',
                nonce: likePostData.nonce,
                post_id: postId,
            },
            success: function(response) {
                if (response.success) {
                    likesCountSpan.text('(' + response.data.likes + ' likes)');
                    button.text(likePostData.liked_text ? likePostData.liked_text : 'Liked');
                    button.prop('disabled', true);
                } else {
                    console.error('Error liking post:', response.data);
                    alert('There was an error processing your like.');
                }
            },
            error: function(errorThrown) {
                console.error('AJAX error:', errorThrown);
                alert('There was a problem connecting to the server.');
            }
        });
    });
});