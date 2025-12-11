function autoScrollToToday(tableInstance, yearMonth, selectedYearMonth) {
    var today = new Date().getDate(); // Get today's date (1-31)

    var $tableContainer = $(tableInstance.table().container()).find(
        ".dataTables_scrollBody"
    );

    if (selectedYearMonth === yearMonth) {
        var columnIndex = findColumnIndex(tableInstance, today);
        if (columnIndex !== -1) {
            var column = tableInstance.column(columnIndex).nodes();
            var $targetCell = $(column[0]);

            if ($targetCell.length) {
                var fixedColumnsWidth = getFixedColumnsWidth($tableContainer); // Get fixed columns width
                var scrollLeft =
                    $targetCell.position().left +
                    $tableContainer.scrollLeft() -
                    fixedColumnsWidth -
                    20; // Adjust scroll position

                $tableContainer.animate({ scrollLeft: scrollLeft }, 800); // Smooth scroll
            }
        }
    }
}

function findColumnIndex(tableInstance, today) {
    var headers = $(tableInstance.table().container()).find("thead th");
    var todayFormatted = ("0" + today).slice(-2) + " "; // Format date as "22 "

    for (var i = 0; i < headers.length; i++) {
        var thHtml = $(headers[i]).html();
        if (thHtml.includes(todayFormatted)) {
            return i;
        }
    }
    return -1;
}

function getFixedColumnsWidth($tableContainer) {
    var width = 0;
    var $fixedColumns = $tableContainer
        .closest(".dataTables_wrapper")
        .find(
            ".dataTables_scroll.dtfc-has-left .dataTables_scrollHead .dataTables_scrollHeadInner > table > thead > tr > th.dtfc-fixed-left"
        );

    if ($fixedColumns.length === 0) {
        console.warn(
            "No fixed columns found for this table! Check your selector."
        );
        return 0;
    }

    $fixedColumns.each(function () {
        width += $(this).outerWidth(true);
    });

    return width;
}

// function autoScrollToToday() {
//     var yearMonth = "<?= date('Y-m') ?>";
//     var selectedYearMonth = "<?= request()->get('yearmonth', date('Y-m')) ?>";
//     var today = new Date().getDate();
//     if (selectedYearMonth === yearMonth) {
//         var columnIndex = findColumnIndex(today);
//         if (columnIndex !== -1) {
//             var column = attendance_table.column(columnIndex).nodes();
//             var $tableContainer = $('.dataTables_scrollBody');
//             var $targetCell = $(column[0]);

//             if ($targetCell.length) {
//                 var fixedColumnsWidth = getFixedColumnsWidth($tableContainer);
//                 console.log("fixedColumnsWidth", fixedColumnsWidth);
//                 var scrollLeft = $targetCell.position().left + $tableContainer.scrollLeft() - fixedColumnsWidth - 20;

//                 $tableContainer.animate({
//                     scrollLeft: scrollLeft
//                 }, 800);
//             }
//         }
//     }
// }

// function findColumnIndex(today) {
//     var headers = $('#attendance_table thead th');
//     var todayFormatted = ("0" + today).slice(-2) + " ";

//     for (var i = 0; i < headers.length; i++) {
//         var thHtml = $(headers[i]).html();
//         if (thHtml.includes(todayFormatted)) {
//             return i;
//         }
//     }
//     return -1;
// }

// function getFixedColumnsWidth($tableContainer) {
//     var width = 0;
//     var $fixedColumns = $tableContainer.closest('.dataTables_wrapper')
//         .find('.dataTables_scroll.dtfc-has-left .dataTables_scrollHead .dataTables_scrollHeadInner > table > thead > tr > th.dtfc-fixed-left');

//     if ($fixedColumns.length === 0) {
//         console.warn("No fixed columns found for this table! Check your selector.");
//         return 0;
//     }

//     $fixedColumns.each(function() {
//         width += $(this).outerWidth(true);
//     });

//     return width;
// }
