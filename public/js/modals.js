function fillFormEditComment(id) {
    document.getElementById("editCommentFormCommentId").value = id;
    document.getElementById("editCommentFormCommentContent").innerHTML = document.getElementById("commentContent"+id).innerText;
}

function fillFormDeleteComment(id) {
    document.getElementById("deleteCommentFormCommentId").value = id;
}

function fillFormDeletePost(id) {
    document.getElementById("deletePostFormPostId").value = id;
}
