
!function ($) {
    "use strict";

    function setStyleHref(elementId, hrefValue) {
        var styleEl = document.getElementById(elementId);
        if (!styleEl) {
            return;
        }

        if (styleEl.getAttribute("href") != hrefValue) {
            styleEl.setAttribute("href", hrefValue);
        }
    }

    if (window.sessionStorage) {
        var alreadyVisited = sessionStorage.getItem("is_visited");
        if (alreadyVisited) {
            switch (alreadyVisited) {
                case "light-mode-switch":
                    document.documentElement.removeAttribute("dir");
                    setStyleHref("bootstrap-style", "build/css/bootstrap.min.css");
                    setStyleHref("app-style", "build/css/app.min.css");
                    document.documentElement.setAttribute("data-bs-theme", "light");
                    break;
                case "dark-mode-switch":
                    document.documentElement.removeAttribute("dir");
                    setStyleHref("bootstrap-style", "build/css/bootstrap.min.css");
                    setStyleHref("app-style", "build/css/app.min.css");
                    document.documentElement.setAttribute("data-bs-theme", "dark");
                    break;
                case "rtl-mode-switch":
                    setStyleHref("bootstrap-style", "build/css/bootstrap-rtl.min.css");
                    setStyleHref("app-style", "build/css/app-rtl.min.css");
                    document.documentElement.setAttribute("dir", "rtl");
                    document.documentElement.setAttribute("data-bs-theme", "light");
                    break;
                case "dark-rtl-mode-switch":
                    setStyleHref("bootstrap-style", "build/css/bootstrap-rtl.min.css");
                    setStyleHref("app-style", "build/css/app-rtl.min.css");
                    document.documentElement.setAttribute("dir", "rtl");
                    document.documentElement.setAttribute("data-bs-theme", "dark");
                    break;
                default:
                    console.log("Something wrong with the layout mode.");
            }
        }
    }
}(window.jQuery);
