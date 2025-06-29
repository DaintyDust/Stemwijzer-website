function ScrollCommentsToBottom() {
  const commentsContainer = document.getElementById("comments-container");
  if (commentsContainer) {
    commentsContainer.scrollTo(0, commentsContainer.scrollHeight);
  }
}

function SetupPostCommentButton() {
  const commentForm = document.getElementById("comment-form");
  if (commentForm) {
    commentForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      //   formData.append("action", "delete");
      //   formData.append("commentId", commentId);
      //   formData.append("authorId", authorId);
      const textarea = this.querySelector('textarea[name="comment"]');
      fetch("private/comment.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            textarea.value = "";
            location.reload();
          } else {
            alert("Er is een fout opgetreden bij het plaatsen van de reactie.");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Er is een fout opgetreden bij het plaatsen van de reactie.");
        });
    });
  }
}

function SetupDeleteCommentButtons() {
  const deleteButtons = document.querySelectorAll(".delete-comment");
  deleteButtons.forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault();

      if (confirm("Weet je zeker dat je deze reactie wilt verwijderen?")) {
        const commentElement = this.closest(".comment");
        const commentId = commentElement.getAttribute("data-comment-id");
        const authorId = commentElement.getAttribute("data-author-id");

        const formData = new FormData();
        formData.append("action", "delete");
        formData.append("commentId", commentId);
        formData.append("authorId", authorId);

        fetch("private/comment.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              location.reload();
            } else {
              alert("Er is een fout opgetreden bij het verwijderen van de reactie: " + (data.message || "Onbekende fout"));
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            alert("Er is een fout opgetreden bij het verwijderen van de reactie.");
          });
      }
    });
  });
}

function SetupEditCommentButtons() {
  const editButtons = document.querySelectorAll(".edit-comment");
  editButtons.forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault();

      const commentElement = this.closest(".comment");
      const commentId = commentElement.getAttribute("data-comment-id");
      const authorId = commentElement.getAttribute("data-author-id");

      const commentTextElement = commentElement.querySelector(".comment-text");

      commentTextElement.classList.add("editing");
      commentElement.querySelector(".comment-actions").classList.add("editing");
      commentTextElement.setAttribute("data-original-text", commentTextElement.innerText);
      console.log(commentTextElement.innerText);
      commentTextElement.innerHTML = `
      <form class="edit-comment-form" data-comment-id="${commentId}" data-author-id="${authorId}">
        <textarea type="text" placeholder="Bewerk de reactie..." required>${commentTextElement.innerText}</textarea>
        <button type="submit"><img src="images/send-message.svg" alt="Verzend bericht"></button>
      </form>
      `;
      const textarea = commentTextElement.querySelector("textarea");
      textarea.focus();

      function adjustTextareaHeight() {
        textarea.style.height = "";
        textarea.style.height = textarea.scrollHeight + "px";
      }
      adjustTextareaHeight();

      textarea.oninput = function () {
        adjustTextareaHeight();
      };

      commentElement.querySelector(".cancel-comment-edit").addEventListener("click", function (e) {
        e.preventDefault();
        commentTextElement.classList.remove("editing");
        commentElement.querySelector(".comment-actions").classList.remove("editing");
        const originalText = commentTextElement.getAttribute("data-original-text");
        commentTextElement.outerHTML = `<p class="comment-text">${originalText.replace(/\n/g, '<br>')}</p>`;
      });

      const updateCommentButton = commentTextElement.querySelector("button[type='submit']");
      updateCommentButton.addEventListener("click", function (e) {
        e.preventDefault();

        if (textarea.value.trim() === "") {
          e.preventDefault();
          alert("De reactie mag niet leeg zijn.");
          return;
        }

        const formData = new FormData();
        formData.append("action", "edit");
        formData.append("commentId", commentId);
        formData.append("authorId", authorId);
        formData.append("commentText", textarea.value);

        fetch("private/comment.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              location.reload();
            } else {
              alert("Er is een fout opgetreden bij het verwijderen van de reactie: " + (data.message || "Onbekende fout"));
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            alert("Er is een fout opgetreden bij het verwijderen van de reactie.");
          });
      });
    });
  });
}

function SetupReplyCommentButtons() {
  const replyButtons = document.querySelectorAll(".reply-comment");
  replyButtons.forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault();

      const commentElement = this.closest(".comment");
      const author = commentElement.querySelector(".comment-author").innerText;
      const textarea = document.getElementById("comment-form").querySelector('textarea[name="comment"]');

      const currentText = textarea.value.trim();

      textarea.value = `@${author} ${currentText}`;
      textarea.focus();

    });
  });
}

document.addEventListener("DOMContentLoaded", function () {
  SetupPostCommentButton();
  ScrollCommentsToBottom();
  SetupDeleteCommentButtons();
  SetupEditCommentButtons();
  SetupReplyCommentButtons();
});
