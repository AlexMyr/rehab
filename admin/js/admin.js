$(document).ready(function(){
  $('#count_exercise_select').change(function(){
    var new_count = $(this).val();
    var cur_count = $('.exercise_row_counter').length;

    if(new_count > cur_count)
    {
      $.ajax({
        type: "POST",
        url: "/admin/index.php?pag=programs_add_generate_row",
        data: {cur_count: cur_count, new_count: new_count},
        dataType: "html",
        success: function(response){
          $(response).insertAfter('.exercise_row_counter:last');
        }
      });
    }
    else if(new_count < cur_count)
    {
      //hide rows
      var i=0;
      $('.exercise_row_counter').each(function(){
        if(i>new_count-1)
          $(this).remove();
        i++;
      });
    }
  });
  
  $('.section_us_display').live('change', function(){
    $(this).parent().parent().parent().parent().find('.section_us').toggleClass('section_hide');
  });
  
});