jQuery(document).ready(function ($) {
    // Confirm before clearing the trending search list
  $(".fp-clear-trending-confirm a").on("click", function (event) {
    var confirmAction = confirm("Are you sure you want to clear the trending search list?");
    if (!confirmAction) event.preventDefault();
  });
});
