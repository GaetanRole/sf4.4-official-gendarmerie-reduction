$('#collapsingLeftSideBar').click(function () {
    let leftSideBar = $('#sidebar');
    leftSideBar.css('margin-left') === '-250px' ? leftSideBar.css({"margin-left": '0'}) : leftSideBar.css({"margin-left": '-250px'});
});

$(function () {
    $("#brand-table").tablesorter();
    $("#category-index-table").tablesorter();
    $("#user-index-table").tablesorter();
    $("#article-index-table").tablesorter();
});
