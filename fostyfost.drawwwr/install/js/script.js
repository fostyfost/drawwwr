$(function () {
    $('.ff-i>div').click(function (e) {
        e.preventDefault();

        $(this).toggleClass('ff-o');

        $(this).next().children().children('p').each(function () {
            if (
                $(this).is(':visible')
                && (parseInt($(this)[0].scrollWidth) - 20) > $(this).width()
                && $(this).next().length === 0
            ) {
                $(this).after('<a></a>');

                $(this).parent().css('cursor', 'pointer');

                $(this).parent().click(function (e) {
                    e.preventDefault();

                    $($(this).children('p')).toggleClass('ff-c');
                });
            }
        });
    });
});
