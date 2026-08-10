import $ from "jquery";
window.jQuery = window.$ = $;
import jQueryConfirm from "jquery-confirm";
window.jQuery.alert = window.$.alert = jQueryConfirm;
window.jQuery.confirm = window.$.confirm = jQueryConfirm;

export function cAlert(type, title, content, reload, redirect) {
    if (type == "green") {
        var icon = "fa-solid fa-check";
    } else if (type == "orange" || type == "red" || type == "blue") {
        var icon = "fa-solid fa-triangle-exclamation";
    } else {
        var icon = "fa-solid fa-check";
    }

    var alertOptions = {
        type: type,
        icon: icon,
        title: title,
        content: content,
        useBootstrap: false,
        buttons: {
            ok: {
                text: "OK",
                keys: ["enter", "esc"]
            }
        }
    };

    if (reload === true) {
        alertOptions.onDestroy = function () {
            location.reload();
        };
    } else if (redirect) {
        alertOptions.onDestroy = function () {
            window.location.href = redirect;
        };
    }
    
    $.alert(alertOptions);
}

export function oAlert(type, title, content) {
    if (type == "green") {
        var icon = "fa-solid fa-check";
    } else if (type == "orange" || type == "red" || type == "blue") {
        var icon = "fa-solid fa-triangle-exclamation";
    } else {
        var icon = "fa-solid fa-check";
    }
    $.alert({
        type: type,
        icon: icon,
        title: title,
        content: content,
        useBootstrap: false,
        buttons: {
            ok: {
                text: "OK",
                keys: ["enter", "esc"]
            }
        }
    });
}

export function cConfirm(title, content, cFunction) {
    var icon = "fa-solid fa-triangle-exclamation";
    $.confirm({
        type: "orange",
        icon: icon,
        title: title,
        content: content,
        autoClose: "cancel|8000",
        useBootstrap: false,
        buttons: {
            ok: {
                text: "Confirm",
                keys: ["enter"],
                action: function () {
                    cFunction();
                },
            },
            cancel: {
                text: "Cancel",
                keys: ["esc"],
            },
        },
    });
}
