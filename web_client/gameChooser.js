$(function(){
    $('.list-group button').click(function(e) {
        e.preventDefault()
        $that = $(this);
        $that.parent().find('button').removeClass('active');
        $that.addClass('active');
    });
})