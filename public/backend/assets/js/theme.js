$(document).ready(function () {
    // Check for saved theme in localStorage or use system preference
    const savedTheme = localStorage.getItem("theme");
    const prefersDark = window.matchMedia(
        "(prefers-color-scheme: dark)"
    ).matches;
    const defaultTheme = savedTheme || (prefersDark ? "dark" : "light");

    // Apply the theme
    document.documentElement.setAttribute("data-theme", defaultTheme);
    updateThemeIcon(defaultTheme);

    // Theme toggle button click handler
    $("#theme-toggle").on("click", function (e) {
        e.preventDefault();
        const currentTheme =
            document.documentElement.getAttribute("data-theme");
        const newTheme = currentTheme === "light" ? "dark" : "light";

        // Apply new theme
        document.documentElement.setAttribute("data-theme", newTheme);
        localStorage.setItem("theme", newTheme);
        updateThemeIcon(newTheme);
    });

    // Function to update the theme icon
    function updateThemeIcon(theme) {
        const themeIcon = $("#theme-icon");
        if (theme === "dark") {
            themeIcon.removeClass("bx-sun").addClass("bx-moon");
        } else {
            themeIcon.removeClass("bx-moon").addClass("bx-sun");
        }
    }

    console.log("Theme toggle initialized");
});
