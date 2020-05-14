$('#collapsingLeftSideBar').click(function () {
    let leftSideBar = $('#sidebar');
    leftSideBar.css('margin-left') === '-250px' ? leftSideBar.css({"margin-left": '0'}) : leftSideBar.css({"margin-left": '-250px'});
});
