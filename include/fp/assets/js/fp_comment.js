jQuery(document).ready(function ($) {
  $("body").on("click", ".comment-reply-link", function (e) {
    e.preventDefault();
    var commentID = $(this).data("commentid");
    $("#new-comment-area").hide();
    $(".comment-reply-form").remove();
    var form = $("#commentform").clone(true);
    form.addClass("comment-reply-form");
    form.find("#comment_parent").val(commentID);
    form.find(".form-submit").find("button[type='submit']").text("Reply");
    form.find(".form-submit").find("button[type='submit']").addClass("fp_reply_comment");
    form.find(".form-submit").append('<button type="button" class="cancel-reply" style="">Cancel</button>');

    // form.append('<div style="display: flex; justify-content: center; align-items: center;"><button type="button" class="cancel-reply" style="margin-left: 10px; background-color: red; padding: 0.5rem 0.3rem; margin-top:5px; color: #fff; font-weight: 600">Cancel</button></div>');

    $("#comment-" + commentID).after(form);

    form.find("#comment").trigger("focus");
  });

  // Handle Cancel Reply Button Click
  $("body").on("click", ".cancel-reply", function () {
    $(".comment-reply-form").remove();
    $("#new-comment-area").show();
  });

  // Handle Form Submission via AJAX
  $("body").on("submit", "#commentform.comment-reply-form", function (e) {
    e.preventDefault();

    let comment = $(this).find("textarea[name='comment']").val().trim();
    let author = $(this).find("input[name='author']").val().trim();
    let email = $(this).find("input[name='email']").val().trim();

    // Check if any required fields are empty
    if (comment === "" || author === "" || email === "") {
        alert("Please fill out all required fields.");
        return;
    }

    let fData = $(this).serialize();
    fData += "&security=" + fp_ajax_comments.nonce + "&action=fp_ajax_submit_comment";

    // var formData = $(this).serialize() + "&security=" + fp_ajax_comments.nonce + "&action=fp_ajax_submit_comment";

    $.ajax({
      url: fp_ajax_comments.ajax_url,
      type: "POST",
      data: fData,
      beforeSend: function () {
        $("#commentform").find('button[type="submit"]').prop("disabled", true);
      },
      success: function (response) {
        $("#commentform").find('button[type="submit"]').prop("disabled", false);

        if (response.success) {
          appendCommentToDOM(response.data);
          $(".comment-reply-form").remove();
          $("#new-comment-area").show();
          $("#commentform")[0].reset();
        } else {
          alert(response.data.message);
        }
      },
      error: function () {
        $("#commentform").find('button[type="submit"]').prop("disabled", false);
        alert("There was an error submitting your comment.");
      },
    });
  });

  function appendCommentToDOM(commentData) {
    var commentHtml = `
        <li class="comment byuser comment-author-${commentData.user_id} ${commentData.comment_parent ? "parent" : ""}" id="comment-${
      commentData.comment_id
    }" style="display: flex; align-items: flex-start; margin-bottom: 20px; margin-left: ${
      commentData.depth > 1 ? (commentData.depth - 1) * 20 + "px" : "0"
    };">
            <div class="comment-avatar" style="margin-right: 15px;">
                <img src="${commentData.avatar}" alt="${commentData.comment_author}" class="avatar avatar-32 photo" height="32" width="32">
            </div>
            <div class="comment-content-wrapper" style="display: flex; flex: 1; justify-content: start; align-items: center; flex-direction: column;">
                <div class="comment-content" style="background-color: #333; color: #f5f5f5; padding: 15px; border-radius: 5px; width: 100%;">
                    <div class="comment-header" style="margin-bottom: 10px; display: flex; align-items: center; flex-wrap: wrap;">
                        <span class="comment-author" style="font-weight: 600; margin-right: 5px;">
                            ${commentData.comment_author}:
                        </span>
                        <span class="comment-text">
                            ${commentData.comment_content}
                        </span>
                    </div>
                    <div class="comment-meta" style="display: flex; flex-direction: column; font-size: 0.9em; color: #aaa;">
                        <span class="comment-date" style="margin-bottom: 5px;">
                            ${commentData.comment_date} at ${commentData.comment_time}
                        </span>
                        <div style="display: flex; justify-content: start; align-items: center; gap: 1rem;">
                            <span class="comment-reply">
                                <a href="#" class="comment-reply-link" data-commentid="${commentData.comment_id}" data-postid="${
      commentData.comment_post_ID
    }" data-belowelement="comment-${commentData.comment_id}" data-respondelement="respond" data-replyto="Reply to ${
      commentData.comment_author
    }">Reply</a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </li>`;

    // If it's a reply, append the comment to the parent's reply list (ol)
    if (commentData.comment_parent > 0) {
      // Check if the parent has a nested list for replies
      let parentComment = $("#comment-" + commentData.comment_parent);
      let replyList = parentComment.children("ol.children");
      if (replyList.length === 0) {
        // If not, create a new one
        replyList = $('<ol class="children" style="list-style-type:none; padding-left:20px;"></ol>');
        parentComment.after(replyList);
      }
      replyList.append(commentHtml);
    } else {
      // If it's a new comment, append it directly to the comment list
      $(".comment-list").append(commentHtml);
    }
  }
});
