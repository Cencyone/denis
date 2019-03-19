/*
 * Adding new comment
 */
$(document).on('submit', '[data-comments-form]', function (e) {
    e.preventDefault();

    var form = $(this);
    var context = form.closest('[data-comments]');
    var formType = form.attr('data-comments-form');
    var currentForm = '[data-comments-form="' + formType + '"]';
    var successMessage = currentForm + ' [data-comments-success]';
    var errorMessageFrame = currentForm + ' [data-comments-error-frame]';
    var errorMessageList = currentForm + ' [data-comments-error-list]';

    /* Request to submit form */
    $.ajax({
        url: form.attr('data-comments-form-url'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        beforeSend: function () {
            /* Show loader before ajax response */
            $.mlsAjax.preloaderShow({
                type: 'frame',
                frame: context
            });
        },
        success: function (data) {
            if (data.answer == 'error') {
                /* Error Message */
                context.find(successMessage).addClass('hidden');
                context.find(errorMessageList).html(data.validation_errors);
                context.find(errorMessageFrame).removeClass('hidden');
                /* Change captcha image */
                context.find(currentForm + ' [data-captcha-img]').html(data.cap_image);
            } else {
                /* Request to update comments list*/
                $.ajax({
                    url: form.attr('data-comments-form-list-url'),
                    method: 'post',
                    dataType: 'json',
                    success: function (data) {
                        /* Insert response into document */
                        context.html(data.comments);
                        /* Success Message */
                        context.find(errorMessageFrame).addClass('hidden');
                        context.find(successMessage).removeClass('hidden');
                    }
                });
            }
        }
    });
});

/*
 * Reply for existing comment
 */
$(document).on('click', '[data-comments-reply-link]', function (e) {
    e.preventDefault();

    var replyLink = $(this);
    var insertFrame = replyLink.closest('[data-comments-post]').find('[data-comments-form-wrapper]');
    var insertForm = replyLink.closest('[data-comments]').find('[data-reply-form] [data-comments-form="reply"]').clone();
    var formId = replyLink.closest('[data-comments-post]').attr('data-comments-post');

    insertForm.find('[data-comments-success], [data-comments-error-frame]').addClass('hidden');
    insertForm.find('[data-comments-parent]').val(formId);
    insertFrame.toggleClass('hidden').html(insertForm);

});

/*
 * Rate comment
 */
$(document).on('click', '[data-comments-vote-url]', function (e) {
    e.preventDefault();

    var voteLink = $(this);
    var href = voteLink.attr('data-comments-vote-url');
    var commentId = voteLink.closest('[data-comments-post]').attr('data-comments-post');
    var voteValue = voteLink.find('[data-comments-vote-value]');

    $.ajax({
        url: href,
        type: 'post',
        data: {comid: commentId},
        dataType: 'json',
        success: function (data) {
            voteValue.html(data.y_count ? data.y_count : data.n_count);
        }
    });
});