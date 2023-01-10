
// new TomSelect("#select-state",{
//     create: false,
//     sortField: {
//         field: "text",
//         direction: "asc"
//     }
// });
// new TomSelect("# tabelPaket tr td:nth-child(3) select",{
//     create: false,
//     sortField: {
//         field: "text",
//         direction: "asc"
//     }
// });


function filterFunction(that, event) {
    // console.log(that);
    // console.log(event);
    let container, input, filter, li, input_val;
    container = $(that).closest(".searching-select");
    // console.log(container);
    input_val = container.find("input").val().toUpperCase();

    if (["ArrowDown", "ArrowUp", "Enter"].indexOf(event.key) != -1) {
        keyControl(event, container)
    } else {
        li = container.find("ul li");
        li.each(function (i, obj) {
            if ($(this).text().toUpperCase().indexOf(input_val) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        container.find("ul li").removeClass("selected");
        setTimeout(function () {
            container.find("ul li:visible").first().addClass("selected");
        }, 100)
    }
}

function filterFunction2(that, event) {
    // console.log(that);
    // console.log(event);
    let container, input, filter, li, input_val;
    container = $(that).closest(".searching-select2");
    // console.log(container);
    input_val = container.find("input").val().toUpperCase();

    if (["ArrowDown", "ArrowUp", "Enter"].indexOf(event.key) != -1) {
        keyControl(event, container)
    } else {
        li = container.find("ul li");
        li.each(function (i, obj) {
            if ($(this).text().toUpperCase().indexOf(input_val) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        container.find("ul li").removeClass("selected");
        setTimeout(function () {
            container.find("ul li:visible").first().addClass("selected");
        }, 100)
    }
}

function filterFunction3(that, event) {
    // console.log(that);
    // console.log(event);
    let container, input, filter, li, input_val;
    container = $(that).closest(".searching-select3");
    // console.log(container);
    input_val = container.find("input").val().toUpperCase();

    if (["ArrowDown", "ArrowUp", "Enter"].indexOf(event.key) != -1) {
        keyControl(event, container)
    } else {
        li = container.find("ul li");
        li.each(function (i, obj) {
            if ($(this).text().toUpperCase().indexOf(input_val) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        container.find("ul li").removeClass("selected");
        setTimeout(function () {
            container.find("ul li:visible").first().addClass("selected");
        }, 100)
    }
}

function keyControl(e, container) {
    if (e.key == "ArrowDown") {

        if (container.find("ul li").hasClass("selected")) {
            if (container.find("ul li:visible").index(container.find("ul li.selected")) + 1 < container.find("ul li:visible").length) {
                container.find("ul li.selected").removeClass("selected").nextAll().not('[style*="display: none"]').first().addClass("selected");
            }

        } else {
            container.find("ul li:first-child").addClass("selected");
        }

    } else if (e.key == "ArrowUp") {

        if (container.find("ul li:visible").index(container.find("ul li.selected")) > 0) {
            container.find("ul li.selected").removeClass("selected").prevAll().not('[style*="display: none"]').first().addClass("selected");
        }
    }
    // else if (e.key == "Enter") {
        // container.find("input").val(container.find("ul li.selected").text()).blur();
        // container.find("input").val(container.find("ul li.selected").text());
        // $(container).closest(".searching-select").find("input").val($(container).text());
        // onSelect(container.find("ul li.selected").text())
    // }

    container.find("ul li.selected")[0].scrollIntoView({
        behavior: "smooth",
    });
}

// function onSelect(val) {
//     // alert(val)
// }

// $(".searching-select input").focus(function () {
//     console.log(this);
//     // $(this).closest(".searching-select").find("ul").css('display', 'initial');
//     $(this).closest(".searching-select ul").show();
//     // $(this).closest(".searching-select").find("ul").show();
//     // $(this).closest(".searching-select").find("ul li").css('display', 'initial');
//     $(this).closest(".searching-select").show();
//     // $(this).closest(".searching-select").find("ul li").show();
// });
// $(".searching-select input").blur(function () {
//     let that = this;
//     setTimeout(function () {
//         $(that).closest(".searching-select").find("ul").hide();
//     }, 300);
// });

$(document).on('focus', '.searching-select input', function () {
    $(this).closest(".searching-select").find("ul").show();
    $(this).closest(".searching-select").find("ul li").show();
});

$(document).on('blur', '.searching-select input', function () {
    let that = this;
    setTimeout(function () {
        $(that).closest(".searching-select").find("ul").hide();
    }, 300);
});

$(document).on('click', '.searching-select ul li', function () {
    // $(this).closest(".searching-select").find("input").val($(this).text()).blur();
    $(this).closest(".searching-select").find("input").val($(this).text());
    $(this).closest(".searching-select").find("ul li.selected").removeClass("selected");
    $(this).addClass("selected");
    // change_paket2(this);
    change_paket(this);
    // onSelect($(this).text())
});

$(document).on('hover', '.searching-select ul li', function () {
    $(this).closest(".searching-select").find("ul li.selected").removeClass("selected");
    $(this).addClass("selected");
});

//Searching Select 2

$(document).on('focus', '.searching-select2 input', function () {
    $(this).closest(".searching-select2").find("ul").show();
    $(this).closest(".searching-select2").find("ul li").show();
});

$(document).on('blur', '.searching-select2 input', function () {
    let that = this;
    setTimeout(function () {
        $(that).closest(".searching-select2").find("ul").hide();
    }, 300);
});

$(document).on('click', '.searching-select2 ul li', function () {
    // $(this).closest(".searching-select").find("input").val($(this).text()).blur();
    $(this).closest(".searching-select2").find("input").val($(this).text());
    $(this).closest(".searching-select2").find("ul li.selected").removeClass("selected");
    $(this).addClass("selected");
    change_item(this);
    // change_paket2(this);
    // change_paket(this);
    // onSelect($(this).text())
});

$(document).on('hover', '.searching-select2 ul li', function () {
    $(this).closest(".searching-select2").find("ul li.selected").removeClass("selected");
    $(this).addClass("selected");
});

//Searching Select 3

$(document).on('focus', '.searching-select3 input', function () {
    $(this).closest(".searching-select3").find("ul").show();
    $(this).closest(".searching-select3").find("ul li").show();
});

$(document).on('blur', '.searching-select3 input', function () {
    let that = this;
    setTimeout(function () {
        $(that).closest(".searching-select3").find("ul").hide();
    }, 300);
});

$(document).on('click', '.searching-select3 ul li', function () {
    // $(this).closest(".searching-select").find("input").val($(this).text()).blur();
    $(this).closest(".searching-select3").find("input").val($(this).text());
    $(this).closest(".searching-select3").find("ul li.selected").removeClass("selected");
    $(this).addClass("selected");
    change_item_with_paket(this);
    // change_paket2(this);
    // change_paket(this);
    // onSelect($(this).text())
});

$(document).on('hover', '.searching-select3 ul li', function () {
    $(this).closest(".searching-select3").find("ul li.selected").removeClass("selected");
    $(this).addClass("selected");
});
