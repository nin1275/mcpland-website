$('#ordersSelectAll').click(function(event) {
  if(this.checked) {
    // Iterate each checkbox
    $(':checkbox').each(function() {
      $(this).prop('checked', true);
    });
  } else {
    $(':checkbox').each(function() {
      $(this).prop('checked', false);
    });
  }
});
