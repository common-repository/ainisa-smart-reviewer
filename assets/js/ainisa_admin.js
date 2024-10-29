jQuery(document).ready(function($) {

    const translations = AINISA_SMART_REVIEWER_OPTIONS.translations;
    /**
     * Set fake data for creating new user
     */
    function setFakeData() {
        const fakeNames = AINISA_SMART_REVIEWER_OPTIONS.fakeNames;
        const randomUserFullnameNumber = Math.floor(Math.random() * 999);
        const randomFullname = fakeNames[randomUserFullnameNumber];
        const fullnameArray = randomFullname.split(' ');
        const firstname = fullnameArray[0];
        const lastname = fullnameArray[1];
        $('#ainisa_smart_reviewer_firstname').val(firstname);
        $('#ainisa_smart_reviewer_lastname').val(lastname);
        $('#ainisa_smart_reviewer_email').val('fakemail_'+Math.floor(Math.random() * 99999)+'@fakemail.com');

    }

    /**
     * Create error string from response
     * @param res
     * @param breaker
     * @returns {string}
     */
    function errorMaker(res, breaker = "\n") {
        var errorsString = '';
        if(res.data !== undefined && res.data.errors !== undefined) {
            var errors = res.data.errors;
            $.each(errors, function (key,value) {
                errorsString += value + breaker;
            });
        } else {
            errorsString = res.message;
        }

        return errorsString;
    }

    function showLoader(parentElement, loaderType) {
        if(loaderType === 'show') {
            parentElement.removeClass('ainisa_hidden').addClass('fa-spin');
        } else {
            parentElement.removeClass('fa-spin').addClass('ainisa_hidden');
        }
    }

    const ainisaClass = $('.ainisa');
    const ainisaSmartReviewerPostId = $('#ainisa_smart_reviewer_post_id');
    const getPosts = $('#ainisa_get_posts_get');
    let postsOptions = '<option value="0">'+ translations.select +'</option>';
    const ainisaGetPostsForm = $('.ainisa_get_posts_form');
    let postId = 0
    let postTitle = '';

    /**
     * Set post title for prompt when it is changed
     */
    ainisaSmartReviewerPostId.change(function () {
        postId = $(this).find(':selected').val();
        postTitle = $(this).find(':selected').text();
    })

    /**
     * Get posts which needs review/comment
     */
    getPosts.on('click', function (e) {
        e.preventDefault();
        const ainisaHiddenIconOfButton = $(this).children('.ainisa_hidden_icon');
        const postData = ainisaGetPostsForm.serializeArray();
        $.ajax({
            url: '/wp-json/ainisa-smart-reviewer-api/v1/get-posts',
            method: 'POST',
            data: postData,
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader( 'X-WP-Nonce', wpApiSettings.nonce );
                showLoader(ainisaHiddenIconOfButton, 'show');
            },
            complete: function () {
                showLoader(ainisaHiddenIconOfButton, 'hide');
            },
            success: function (response) {
                const posts = response.posts;
                let newPostOptions = postsOptions;
                if(posts.length > 0) {
                    posts.forEach(post => {
                        newPostOptions += "<option value="+post.ID+">"+post.post_title+"</option>";
                    });
                }

                toastr.success(response.message);
                ainisaSmartReviewerPostId.children('option').remove();
                ainisaSmartReviewerPostId.append(newPostOptions);
            },
            error: function (response) {
                const responseJSON = response.responseJSON;
                const errorsString = errorMaker(responseJSON, "<br>");
                toastr.error(errorsString);
                showLoader(ainisaHiddenIconOfButton, 'hide');
            },
            fail: function () {
                toastr.error_scope('Problem happened');
                showLoader(ainisaHiddenIconOfButton, 'hide');
            }
        });

        return false;

    });


    /**
     * Ajax for creating review with ai
     */
    const getGptReviewButton = $('#ainisa_get_gpt_review');
    getGptReviewButton.on('click', function (e) {
        e.preventDefault();
        const ainisaHiddenIconOfButton = $(this).children('.ainisa_hidden_icon');
        showLoader(ainisaHiddenIconOfButton, 'show');
        if(postTitle.length === 0 || postId === 0) {
            toastr.error(translations['Please select post']);
            showLoader(ainisaHiddenIconOfButton, 'hide');
            return ;
        }
        const prompt = ($('#ainisa_smart_review_prompt').text()).replace('[post_title]', postTitle);
        const postData = {
            ainisa_smart_review_prompt:prompt
        };
        $.ajax({
            url: '/wp-json/ainisa-smart-reviewer-api/v1/create-review',
            method: 'POST',
            data: postData,
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader( 'X-WP-Nonce', wpApiSettings.nonce );
            },
            complete: function () {
                showLoader(ainisaHiddenIconOfButton, 'hide');
            },
            success: function (response) {
                try {
                    const array = response.content;
                    $('#ainisa_smart_reviewer_title').val(array[0]);
                    $('#ainisa_smart_reviewer_review').text(array[1]);
                } catch (e) {
                    $('#ainisa_smart_reviewer_review').text(response.content);
                }
                const success = Boolean(response.success);
                if(success === true) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }

            },
            error: function (response) {
                const responseJSON = response.responseJSON;
                const errorsString = errorMaker(responseJSON, "<br>");
                toastr.error(errorsString);
                showLoader(ainisaHiddenIconOfButton, 'hide');
            },
            fail: function () {
                toastr.error('Problem happened');
                showLoader(ainisaHiddenIconOfButton, 'hide');
            }
        });

        return false;

    });


    /**
     * Ajax for creating new user and review
     */
    const getSaveReviewButton = $('#ainisa_save_review');
    getSaveReviewButton.on('click', function (e) {
        e.preventDefault();
        const ainisaReviewForm = $('.ainisa_review_form');
        const ainisaHiddenIconOfButton = $(this).children('.ainisa_hidden_icon');
        const postData = ainisaReviewForm.serializeArray();
        $.ajax({
            url: '/wp-json/ainisa-smart-reviewer-api/v1/add-review',
            method: 'POST',
            data: postData,
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader( 'X-WP-Nonce', wpApiSettings.nonce );
                showLoader(ainisaHiddenIconOfButton, 'show');
            },
            complete: function () {
                showLoader(ainisaHiddenIconOfButton, 'hide');
            },
            success: function (response) {
                toastr.success(response.message);
                setFakeData();
            },
            error: function (response) {
                const responseJSON = response.responseJSON;
                const errorsString = errorMaker(responseJSON, "<br>");
                toastr.error(errorsString);
                showLoader(ainisaHiddenIconOfButton, 'hide');
            },
            fail: function () {
                toastr.error('Problem happened');
                showLoader(ainisaHiddenIconOfButton, 'hide');
            }
        });

        return false;

    });

    /**
     * Set fake data when page is loaded
     */
    setFakeData();
});